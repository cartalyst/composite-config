<?php namespace Cartalyst\CompositeConfig;
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Config\FileLoader;
use Illuminate\Database\Connection;
use Illuminate\Filesystem;

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
	 * Create a new composite configuration loader.
	 *
	 * @param  Illuminate\Filesystem  $files
	 * @param  string  $defaultPath
	 * @param  Illuminate\Database\Connection  $database
	 * @return void
	 */
	public function __construct(Filesystem $files, $defaultPath, $database, $databaseTable)
	{
		parent::__construct($files, $defaultPath);
		$this->database      = $database;
		$this->databaseTable = $databaseTable;
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
	 * Load the given configuration group.
	 *
	 * @param  string  $environment
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	public function load($environment, $group, $namespace = null)
	{
		$items = array();

		$query = $this->database->from($this->databaseTable);

		$query
		    ->where('environment', '=', $environment)
		    ->where('group', '=', $group);

		if (isset($namespace))
		{
			$query->where('namespace', '=', $namespace);
		}
		else
		{
			$query->whereNull($namespace);
		}

		$result = $query->get();

		if ( ! empty($result))
		{
			foreach ($result as $result)
			{
				array_set($items, $result->item, $result->value);
			}
		}

		$parentItems = parent::load($environment, $group, $namespace);
		var_dump($parentItems);

		return array_replace_recursive($parentItems, $items);
	}

}