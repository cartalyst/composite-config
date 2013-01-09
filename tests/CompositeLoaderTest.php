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

	protected $databaseTable;

	protected $loader;

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->filesystem    = m::mock('Illuminate\Filesystem');
		$this->defaultPath   = __DIR__;

		$this->loader        = new CompositeLoader($this->filesystem, $this->defaultPath);

		$this->database = m::mock('Illuminate\Database\Connection');
		$this->loader->setDatabase($this->database);

		$this->databaseTable = 'config';
		$this->loader->setDatabaseTable($this->databaseTable);
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

	public function testLoadingFromDatabase()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($this->database);
		$this->database->shouldReceive('where')->with('environment', '=', 'local')->once()->andReturn($this->database);
		$this->database->shouldReceive('where')->with('group', '=', 'foo')->once()->andReturn($this->database);
		$this->database->shouldReceive('where')->with('namespace', '=', 'bar')->once()->andReturn($this->database);

		$record1 = new StdClass;
		$record1->environment = 'local';
		$record1->group       = 'foo';
		$record1->namespace   = 'bar';
		$record1->item        = 'baz.bat.qux';
		$record1->value       = 'corge';

		$record2 = new StdClass;
		$record2->environment = 'local';
		$record2->group       = 'foo';
		$record2->namespace   = 'bar';
		$record2->item        = 'foo';
		$record2->value       = 'bar';

		$records = array($record1, $record2);

		$this->database->shouldReceive('get')->once()->andReturn($records);

		$expected = array(
			'baz' => array(
				'bat' => array(
					'qux' => 'corge',
				),
			),
			'foo' => 'bar',
		);

		$actual = $this->loader->load('local', 'foo', 'bar');
		$this->assertEquals($expected, $actual);
	}

	public function testMergingWithFileConfig()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($this->database);
		$this->database->shouldReceive('where')->with('environment', '=', 'local')->once()->andReturn($this->database);
		$this->database->shouldReceive('where')->with('group', '=', 'foo')->once()->andReturn($this->database);
		$this->database->shouldReceive('where')->with('namespace', '=', 'bar')->once()->andReturn($this->database);

		$record1 = new StdClass;
		$record1->environment = 'local';
		$record1->group       = 'foo';
		$record1->namespace   = 'bar';
		$record1->item        = 'baz';
		$record1->value       = 'bat';

		$records = array($record1);

		$this->database->shouldReceive('get')->once()->andReturn($records);

		// Configure file based loading
		$this->loader->addNamespace('bar', 'path/to/bar');
		$this->filesystem->shouldReceive('exists')->with('path/to/bar/foo.php')->once()->andReturn(true);
		$this->filesystem->shouldReceive('getRequire')->with('path/to/bar/foo.php')->once()->andReturn(array(
			'qux' => 'corge',
		));
		$this->filesystem->shouldReceive('exists')->with('path/to/bar/local/foo.php')->once()->andReturn(false);

		$expected = array(
			'baz' => 'bat',
			'qux' => 'corge',
		);

		$actual = $this->loader->load('local', 'foo', 'bar');
		$this->assertEquals($expected, $actual);
	}

	public function testDatabaseOverridesFilesystem()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($this->database);
		$this->database->shouldReceive('where')->with('environment', '=', 'local')->once()->andReturn($this->database);
		$this->database->shouldReceive('where')->with('group', '=', 'foo')->once()->andReturn($this->database);
		$this->database->shouldReceive('where')->with('namespace', '=', 'bar')->once()->andReturn($this->database);

		$record1 = new StdClass;
		$record1->environment = 'local';
		$record1->group       = 'foo';
		$record1->namespace   = 'bar';
		$record1->item        = 'baz';
		$record1->value       = 'bat';

		$record2 = new StdClass;
		$record2->environment = 'local';
		$record2->group       = 'foo';
		$record2->namespace   = 'bar';
		$record2->item        = 'foo';
		$record2->value       = 'bar_nar_nar_nar';

		$records = array($record1, $record2);

		$this->database->shouldReceive('get')->once()->andReturn($records);

		// Configure file based loading
		$this->loader->addNamespace('bar', 'path/to/bar');
		$this->filesystem->shouldReceive('exists')->with('path/to/bar/foo.php')->once()->andReturn(true);
		$this->filesystem->shouldReceive('getRequire')->with('path/to/bar/foo.php')->once()->andReturn(array(
			'foo' => 'bar',
			'qux' => 'corge',
		));
		$this->filesystem->shouldReceive('exists')->with('path/to/bar/local/foo.php')->once()->andReturn(false);

		$expected = array(
			'baz' => 'bat',
			'qux' => 'corge',

			// Existent in both file and database,
			// database should win
			'foo' => 'bar_nar_nar_nar',
		);

		$actual = $this->loader->load('local', 'foo', 'bar');
		$this->assertEquals($expected, $actual);
	}

}