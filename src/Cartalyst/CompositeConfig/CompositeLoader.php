<?php namespace Cartalyst\CompositeConfig;
/**
 * Part of the Composite Config package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Composite Config
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Config\FileLoader;
use Illuminate\Database\Connection as DatabaseConnection;
use Illuminate\Filesystem\Filesystem;

class CompositeLoader extends FileLoader {

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
	 * Returns the database connection.
	 *
	 * @return Illuminate\Database\Connection
	 */
	public function getDatabase()
	{
		return $this->database;
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
	 * Load the given configuration group.
	 *
	 * @param  string  $environment
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	public function load($environment, $group, $namespace = null)
	{
		// If the database has not been set on the config
		// loader, we simply default to our parent - file
		// loading.
		if ( ! isset($this->database))
		{
			return parent::load($environment, $group, $namespace);
		}

		$items = array();

		$query = $this->getGroupQuery($environment, $group, $namespace);

		$result = $query->get();

		if ( ! empty($result))
		{
			foreach ($result as $result)
			{
				array_set($items, $result->item, ($this->getJson($result->value) ?: $result->value));
			}
		}

		$parentItems = parent::load($environment, $group, $namespace);

		return array_replace_recursive($parentItems, $items);
	}

	/**
	 * Persist the given configuration to the database.
	 *
	 * @param  string  $environment
	 * @param  string  $group
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  string  $namespace
	 * @return void
	 */
	public function persist($environment, $group, $item, $value, $namespace = null)
	{
		// If there is no databse, we'll not persist anything which will make
		// the configuration act as if this package was not installed.
		if ( ! isset($this->database)) return;

		$query = $this
			->getGroupQuery($environment, $group, $namespace)
			->where('item', '=', $item);

		// Firstly, we'll see if the configuration exists
		$existing = $query->first();

		if ($existing)
		{
			// We'll update an existing record
			$query->update(array('value' => $this->prepareValue($value)));
		}
		else
		{
			// Prepare our data
			$data = compact('environment', 'group', 'item');
			$data['value'] = $this->prepareValue($value);
			if (isset($namespace)) $data['namespace'] = $namespace;

			$this
				->database->table($this->databaseTable)
				->insert($data);
		}
	}

	/**
	 * Returns a query builder object for the given environment, group
	 * and namespace.
	 *
	 * @param  string  $environment
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return Illuminate\Database\Query  $query
	 */
	protected function getGroupQuery($environment, $group, $namespace)
	{
		$query = $this->database->table($this->databaseTable);
		$query
		    ->where('environment', '=', $environment)
		    ->where('group', '=', $group);

		if (isset($namespace))
		{
			$query->where('namespace', '=', $namespace);
		}
		else
		{
			$query->whereNull('namespace');
		}

		return $query;
	}

	/**
	 * Returns the JSON value of the string.
	 *
	 * @param  string  $json
	 * @return mixed
	 */
	protected function getJson($string)
	{
		$decoded = json_decode($string, true);

		if (json_last_error() !== JSON_ERROR_NONE) return false;

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
		// We'll JSON encode arrays
		if (is_array($value))
		{
			return json_encode($value);
		}

		// Strings, "null", "true", integers etc...
		if ( ! is_object($value))
		{
			return $value;
		}

		throw new \InvalidArgumentException('Cannot persist value of type ['.gettype($value).'] to database.');
	}

}
