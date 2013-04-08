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

use Illuminate\Config\LoaderInterface;

class Repository extends \Illuminate\Config\Repository {

	/**
	 * Create a new configuration repository.
	 *
	 * @param  Illuminate\Config\LoaderInterface  $loader
	 * @param  string  $environment
	 * @param  array   $items
	 * @return void
	 */
	public function __construct(LoaderInterface $loader, $environment, array $items = array())
	{
		// We've kept the same arguments to keep compatible with our parent,
		// so we'll validate the loader type here.
		if ( ! $loader instanceof CompositeLoader)
		{
			throw new \InvalidArgumentException('Config loader must be instance of [Cartalyst\CompositeConfig\CompositeLoader], ['.get_class($loader).'] given.');
		}

		parent::__construct($loader, $environment);
		$this->items = $items;
	}

	/**
	 * Set a given configuration value.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function set($key, $value)
	{
		list($namespace, $group, $item) = $this->parseKey($key);

		$collection = $this->getCollection($group, $namespace);

		// Persist the configuration in the database
		$this->persist($group, $item, $value, $namespace, $collection);

		if (is_null($item))
		{
			$this->items[$collection] = $value;
		}
		else
		{
			array_set($this->items[$collection], $item, $value);
		}
	}

	/**
	 * Persist the configuration group for the key.
	 *
	 * @param  string  $key
	 * @param  string  $item
	 * @param  mixed   $value
	 * @param  string  $namespace
	 * @param  string  $collection
	 * @return void
	 */
	protected function persist($group, $item, $value, $namespace, $collection)
	{
		// We'll need to go ahead and lazy load each configuration groups even when
		// we're just setting a configuration item so that the set item does not
		// get overwritten if a different item in the group is requested later.
		$this->load($group, $namespace, $collection);

		$env = $this->environment;

		$this->loader->persist($env, $group, $item, $value, $namespace);
	}

}
