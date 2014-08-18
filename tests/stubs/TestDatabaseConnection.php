<?php
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

/**
 * @todo Remove when https://github.com/laravel/framework/pull/1426 gets merged.
 */

class TestDatabaseConnection extends \Illuminate\Database\Connection {

    /**
     * Get the cache manager instance.
     *
     * @return \Illuminate\Cache\CacheManager
     */
    public function getCacheManager()
    {
        if ($this->cache instanceof Closure)
        {
            $this->cache = call_user_func($this->cache);
        }

        return $this->cache;
    }

    /**
     * Set the cache manager instance on the connection.
     *
     * @param  \Illuminate\Cache\CacheManager|\Closure  $cache
     * @return void
     */
    public function setCacheManager($cache)
    {
        $this->cache = $cache;
    }
}
