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

use Illuminate\Support\ServiceProvider;

class CompositeConfigServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// We will grab the new loader and syncronize all of the namespaces.
		$compositeLoader = $this->app['config.loader.composite'];
		foreach ($this->app['config.loader']->getNamespaces() as $namespace => $hint)
		{
			$compositeLoader->addNamespace($namespace, $hint);
		}

		// Now we will set the config loader instance.
		unset($this->app['config.loader.composite']);
		$this->app->instance('config.loader', $compositeLoader);
		$this->app['config']->setLoader($this->app['config.loader']);

		// Set the database property on the composite loader so it will now
		// merge database configuration with file configuration.
		if (method_exists($this->app['config.loader'], 'setDatabase'))
		{
			$this->app['config.loader']->setDatabase($this->app['db']->connection());
			$this->app['config.loader']->setDatabaseTable('config');
		}

		// We'll also set the repository
		if (method_exists($this->app['config.loader'], 'setRepository'))
		{
			$this->app['config.loader']->setRepository($this->app['config']);
		}
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
