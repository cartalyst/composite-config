<?php
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

/**
 * Place the following in the start.php file of your
 * base application path.
 */

/*
|--------------------------------------------------------------------------
| Register The Configuration Loader
|--------------------------------------------------------------------------
|
| The configuration loader is responsible for loading the configuration
| options for the application.
|
*/

if (class_exists('Cartalyst\CompositeConfig\CompositeLoader'))
{
	$app->bind('config.loader', function($app) use ($appPath)
	{
		return new Cartalyst\CompositeConfig\CompositeLoader(new Illuminate\Filesystem\Filesystem, $appPath.'/config');
	});
}