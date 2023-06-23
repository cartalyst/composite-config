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
 * @version    7.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2023, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\CompositeConfig\Laravel;

use PDOException;
use Illuminate\Support\ServiceProvider;
use Cartalyst\CompositeConfig\Repository;

class CompositeConfigServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
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
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../config/config.php'), 'cartalyst.composite-config'
        );

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                realpath(__DIR__.'/../config/config.php') => config_path('cartalyst.composite-config.php'),
            ], 'config');

            // Publish migrations
            $this->publishes([
                realpath(__DIR__.'/../migrations') => database_path('migrations'),
            ], 'migrations');

            // Load migrations
            $this->loadMigrationsFrom(
                realpath(__DIR__.'/../migrations')
            );
        }
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
