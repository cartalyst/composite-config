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

use Illuminate\Support\ServiceProvider;

class CompositeConfigServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Add the config as a package so migrations can be run
		$this->package('cartalyst/composite-config', __DIR__.'/../..', 'cartalyst/composite-config');

		// Set the database property on the composite loader so it will now
		// merge database configuration with file configuration.
		if (method_exists($configLoader = $this->app['config.loader'], 'setDatabase') and isset($this->app['db']))
		{
			$configLoader->setDatabase($this->app['db']->connection());
			$configLoader->setDatabaseTable('config');
		}
	}

}