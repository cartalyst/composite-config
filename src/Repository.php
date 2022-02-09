<?php

/*
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
 * @version    6.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2022, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\CompositeConfig;

use Illuminate\Support\Arr;
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
     * The Illuminate Cache Manager instance.
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param array                          $items
     * @param \Illuminate\Cache\CacheManager $cache
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
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return $this->retrieve($key) ?: parent::get($key, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param array|string $key
     * @param mixed|null   $value
     *
     * @return void
     */
    public function set($key, $value = null): void
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            Arr::set($this->cachedConfigs, $key, $value);
            Arr::set($this->items, $key, $value);
        }
    }

    /**
     * Retrieves a value from the database.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    protected function retrieve(string $key)
    {
        return Arr::get($this->cachedConfigs, $key);
    }

    /**
     * Persist the given configuration to the database.
     *
     * @param string     $key
     * @param mixed|null $value
     *
     * @return void
     */
    public function persist(string $key, $value = null): void
    {
        // If there is no databse, we'll not persist anything which will make
        // the configuration act as if this package was not installed.
        if (isset($this->database)) {
            $query = $this->database->table($this->databaseTable)->where('item', '=', $key);

            // Firstly, we'll see if the configuration exists
            $existing = $query->first();

            if ($existing) {
                if (isset($value)) {
                    // We'll update an existing record
                    $query->update(['value' => $this->prepareValue($value)]);
                } else {
                    $query->delete();

                    $this->set($key, null);
                }
            }

            if (! $existing && isset($value)) {
                $query->insert([
                    'item'  => $key,
                    'value' => $this->prepareValue($value),
                ]);
            }

            $this->fetchAndCache();
        }
    }

    /**
     * Returns the database connection.
     *
     * @return Illuminate\Database\Connection
     */
    public function getDatabase(): DatabaseConnection
    {
        return $this->database;
    }

    /**
     * Sets the database connection.
     *
     * @param Illuminate\Database\Connection $database
     *
     * @return $this
     */
    public function setDatabase(DatabaseConnection $database): self
    {
        $this->database = $database;

        return $this;
    }

    /**
     * Returns the database table.
     *
     * @return string
     */
    public function getDatabaseTable(): string
    {
        return $this->databaseTable;
    }

    /**
     * Sets the database table.
     *
     * @param string $databaseTable
     *
     * @return $this
     */
    public function setDatabaseTable(string $databaseTable): self
    {
        $this->databaseTable = $databaseTable;

        return $this;
    }

    /**
     * Cache all configurations.
     *
     * @return void
     */
    public function fetchAndCache(): void
    {
        $this->removeCache();

        $configs = $this->cache->rememberForever('cartalyst.config', function () {
            return $this->database->table($this->databaseTable)->get();
        });

        foreach ($configs as $key => $config) {
            $value = $this->parseValue($config->value);

            Arr::set($this->cachedConfigs, $config->item, $value);

            parent::set($config->item, $value);
        }
    }

    /**
     * Parses a value from the database and attempts to return it's
     * JSON decoded value.
     *
     * @param mixed $value
     *
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
     * @param mixed $value
     *
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
    protected function removeCache(): void
    {
        $this->cache->forget('cartalyst.config');
    }
}
