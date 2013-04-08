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
		$originalConfig = $this->app['config'];
		$originalLoader = $config->getLoader();

		// Now, we'll create a new composite config loader
		$newLoader = new CompositeLoader($this->app['files'], $this->app['path'].'/config');
		$newLoader->setdatabase($this->app['db']->connection());
		$newLoader->setDatabaseTable('config');
		$this->app->instance('config.loader', $newLoader);

		// Note, we are intentionally not carrying across the cached items. This
		// is because there may be a conflicting value in the database. If the items
		// are cached, the database value will never be returned.
		$newConfig = new Repository($app['config.loader'], $originalConfig->getEnvironment());
		$this->app->instance('config', $newConfig);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {}

}
