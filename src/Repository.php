<?php

/**
 * Part of the Composite Config package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Composite Config
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\CompositeConfig;

use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository as BaseRepository;
use Illuminate\Database\Connection as DatabaseConnection;

class Repository extends BaseRepository
{
    /**
     * The database instance.
     *
     * @var Illuminate\Database\Connection
     */
    protected $database;

    /**
     * The config database table.
     *
     * @var string
     */
    protected $databaseTable;

    /**
     * Cache instance.
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * The config repository instance.
     *
     * @var Illuminate\Config\Repository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct(array $items = [], CacheManager $cache = null)
    {
        $this->cache = $cache;

        parent::__construct($items);
    }

    /**
     * Cached database items.
     *
     * @var array
     */
    protected $cachedConfigs = [];

    /**
     * Returns the config value.
     *
     * @param  string  $key
     * @return string
     */
    public function get($key, $default = null)
    {
        return $this->retrieve($key) ?: parent::get($key, $default);
    }

    /**
     * Retrieves a value from the database.
     *
     * @param  string  $key
     * @return string|null
     */
    protected function retrieve($key)
    {
        if (isset($this->cachedConfigs[$key])) {
            return $this->cachedConfigs[$key];
        }
    }

    /**
     * Load the given configuration group.
     *
     * @return array
     */
    public function load()
    {
        $query = $this->database->table($this->databaseTable);

        foreach ($this->cachedConfigs as $key => $value) {
            parent::set($key, $value);
        }
    }

    /**
     * Persist the given configuration to the database.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function persist($key, $value = null)
    {
        // If there is no databse, we'll not persist anything which will make
        // the configuration act as if this package was not installed.
        if (! isset($this->database)) {
            return;
        }

        $query = $this->database->table($this->databaseTable)
            ->where('item', '=', $key);

        // Firstly, we'll see if the configuration exists
        $existing = $query->first();

        if ($existing) {
            if (isset($value)) {
                // We'll update an existing record
                $query->update(['value' => $this->prepareValue($value)]);
            } else {
                $query->delete();
            }
        } elseif (isset($value)) {
            // Prepare our data
            $data = [
                'item'  => $key,
                'value' => $this->prepareValue($value),
            ];

            $this
                ->database->table($this->databaseTable)
                ->insert($data);
        }

        $this->removeCache();
    }

    /**
     * Returns the database connection.
     *
     * @return Illuminate\Database\Connection
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Sets the database connection.
     *
     * @param  Illuminate\Database\Connection  $database
     * @return void
     */
    public function setDatabase(DatabaseConnection $database)
    {
        $this->database = $database;
    }

    /**
     * Set the repository instance on the composite loader.
     *
     * @param  Illuminate\Config\Repository  $repository
     * @return void
     */
    public function setRepository(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Sets the database table used by the
     * loaded.
     *
     * @param  string  $databaseTable
     */
    public function setDatabaseTable($databaseTable)
    {
        $this->databaseTable = $databaseTable;
    }

    /**
     * Cache all configurations.
     *
     * @return void
     */
    public function cacheConfigs()
    {
        $configs = $this->cache->rememberForever('cartalyst.config', function () {
            return $this->database->table($this->databaseTable)->get();
        });

        $cachedConfigs = [];

        foreach ($configs as $key => $config) {
            $cachedConfigs[$config->item] = $this->parseValue($config->value);
        }

        $this->cachedConfigs = $cachedConfigs;
    }

    /**
     * Parses a value from the database and attempts to return it's
     * JSON decoded value.
     *
     * @param  string  $json
     * @return mixed
     */
    protected function parseValue($value)
    {
        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $value;
        }

        return $decoded;
    }

    /**
     * Prepares a value to be persisted in the database.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function prepareValue($value)
    {
        // We will always JSON encode the value. This allows us to store "null", "true"
        // and "false" values in the database (as an example), which may mean completely
        // different things.
        return json_encode($value);
    }

    /**
     * Removes the repository cache for the given item
     * enforcing a database reload.
     *
     * @return void
     */
    protected function removeCache()
    {
        $this->cache->forget('cartalyst.config');
    }
}
