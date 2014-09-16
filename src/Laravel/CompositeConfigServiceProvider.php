<?php namespace Cartalyst\CompositeConfig\Laravel;
/**
 * Part of the Composite Config package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Composite Config
 * @version    1.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\CompositeConfig\CompositeLoader;
use Illuminate\Support\ServiceProvider;
use PDOException;

class CompositeConfigServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('cartalyst/composite-config', 'cartalyst/composite-config', __DIR__.'/..');

		$originalLoader = $this->app['config']->getLoader();

		// We will grab the new loader and syncronize all of the namespaces.
		$compositeLoader = $this->app['config.loader.composite'];
		foreach ($originalLoader->getNamespaces() as $namespace => $hint)
		{
			$compositeLoader->addNamespace($namespace, $hint);
		}

		$table = $this->app['config']['cartalyst/composite-config::table'];

		// Now we will set the config loader instance.
		unset($this->app['config.loader.composite']);
		$this->app['config']->setLoader($compositeLoader);

		// Set the database property on the composite loader so it will now
		// merge database configuration with file configuration.
		try
		{
			$compositeLoader->setDatabase($this->app['db']->connection());
			$compositeLoader->setDatabaseTable($table);
			$compositeLoader->cacheConfigs();
			$compositeLoader->setRepository($this->app['config']);
		}
		catch (PDOException $e) {}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$compositeLoader = new CompositeLoader($this->app['files'], $this->app['path'].'/config');

		$this->app->instance('config.loader.composite', $compositeLoader);
	}

}
