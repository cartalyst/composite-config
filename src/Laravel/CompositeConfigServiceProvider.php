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
 * @version    2.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\CompositeConfig\Laravel;

use PDOException;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider;
use Cartalyst\CompositeConfig\Repository;;

class CompositeConfigServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->prepareResources();

        $this->overrideConfigInstance();

        $this->setUpConfig();
    }

    /**
     * Overrides the config instance.
     *
     * @return void
     */
    protected function overrideConfigInstance()
    {
        $this->app->register('Illuminate\Cache\CacheServiceProvider');

        $repository = new Repository([], $this->app['cache']);

        $oldItems = $this->app['config']->all();

        foreach ($oldItems as $key => $value) {
            $repository->set($key, $value);
        }

        $this->app->instance('config', $repository);
    }

    /**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        // Publish config
        $config = realpath(__DIR__.'/../config/config.php');

        $this->mergeConfigFrom($config, 'cartalyst.composite-config');

        $this->publishes([
            $config => config_path('cartalyst.composite-config.php'),
        ], 'config');

        // Publish migrations
        $migrations = realpath(__DIR__.'/../migrations');

        $this->publishes([
            $migrations => $this->app->databasePath().'/migrations',
        ], 'migrations');
    }

    /**
     * Sets up, fetches and caches configurations.
     *
     * @return void
     */
    protected function setUpConfig()
    {
        $config = $this->app['config'];

        $table = $this->app['config']['cartalyst.composite-config.table'];

        try {
            $config->setDatabase($this->app['db']->connection());
            $config->setDatabaseTable($table);
            $config->fetchAndCache();
        } catch (PDOException $e) {
        }
    }
}
