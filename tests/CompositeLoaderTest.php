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

use Mockery as m;
use Cartalyst\CompositeConfig\CompositeLoader;

class CompositeLoaderTest extends PHPUnit_Framework_TestCase {

	protected $filesystem;

	protected $defaultPath;

	protected $database;

	protected $loader;

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->filesystem  = m::mock('Illuminate\Filesystem');
		$this->defaultPath = __DIR__;
		$this->database    = m::mock('Illuminate\Database\Connection');
		$this->loader      = new CompositeLoader($this->filesystem, $this->defaultPath, $this->database);
	}

	/**
	 * Close mockery.
	 * 
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	

}