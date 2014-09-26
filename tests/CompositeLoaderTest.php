<?php namespace Cartalyst\CompositeConfig\Tests;
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
 * @version    1.1.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use stdClass;
use Mockery as m;
use PHPUnit_Framework_TestCase;
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
		$this->filesystem    = m::mock('Illuminate\Filesystem\Filesystem');
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

	public function testGetDatabase()
	{
		$this->assertInstanceOf('Illuminate\Database\Connection', $this->loader->getDatabase());
	}

	public function testLoadingFromDatabase()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($query = m::mock('Illuminate\Database\Query'));

		$record1 = new stdClass;
		$record1->environment = 'local';
		$record1->group       = 'foo';
		$record1->namespace   = 'bar';
		$record1->item        = 'baz.bat.qux';
		$record1->value       = 'corge';

		$record2 = new stdClass;
		$record2->environment = 'local';
		$record2->group       = 'foo';
		$record2->namespace   = 'bar';
		$record2->item        = 'foo';
		$record2->value       = 'bar';

		$record3 = new stdClass;
		$record3->environment = 'local';
		$record3->group       = 'foo';
		$record3->namespace   = 'bar';
		$record3->item        = 'fred';
		$record3->value       = '{"waldo":true,"fred":"thud"}';

		$records = array($record1, $record2, $record3);

		$query->shouldReceive('get')->once()->andReturn($records);
		$query->shouldReceive('rememberForever')->once()->andReturn($query);
		$this->loader->cacheConfigs();

		$expected = array(
			'baz'  => array(
				'bat' => array(
					'qux' => 'corge',
				),
			),
			'foo'  => 'bar',
			'fred' => array(
				'waldo' => true,
				'fred'  => 'thud',
			),
		);

		$actual = $this->loader->load('local', 'foo', 'bar');
		$this->assertEquals($expected, $actual);
	}

	public function testMergingWithFileConfig()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($query = m::mock('Illuminate\Database\Query'));

		$record1 = new stdClass;
		$record1->environment = 'local';
		$record1->group       = 'foo';
		$record1->namespace   = 'bar';
		$record1->item        = 'baz';
		$record1->value       = 'bat';

		$records = array($record1);

		$query->shouldReceive('get')->once()->andReturn($records);
		$query->shouldReceive('rememberForever')->once()->andReturn($query);
		$this->loader->cacheConfigs();

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
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($query = m::mock('Illuminate\Database\Query'));

		$record1 = new stdClass;
		$record1->environment = 'local';
		$record1->group       = 'foo';
		$record1->namespace   = 'bar';
		$record1->item        = 'baz';
		$record1->value       = 'bat';

		$record2 = new stdClass;
		$record2->environment = 'local';
		$record2->group       = 'foo';
		$record2->namespace   = 'bar';
		$record2->item        = 'foo';
		$record2->value       = 'bar_nar_nar_nar';

		$records = array($record1, $record2);

		$query->shouldReceive('get')->once()->andReturn($records);
		$query->shouldReceive('rememberForever')->once()->andReturn($query);
		$this->loader->cacheConfigs();

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

	public function testPersistingExisting()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($query = m::mock('Illuminate\Database\Query'));
		$this->database->shouldReceive('getCacheManager')->once()->andReturn($cacheManager = m::mock('Illuminate\Cache\cacheManager'));
		$cacheManager->shouldReceive('forget')->once();

		$query->shouldReceive('where')->with('environment', '=', 'local')->once()->andReturn($query);
		$query->shouldReceive('where')->with('group', '=', 'foo')->once()->andReturn($query);
		$query->shouldReceive('where')->with('item', '=', 'bar.baz')->once()->andReturn($query);
		$query->shouldReceive('where')->with('namespace', '=', 'corge')->once()->andReturn($query);

		$query->shouldReceive('first')->once()->andReturn(new stdClass);
		$query->shouldReceive('update')->with(array('value' => '{"qux":"fred","thud":true}'))->once();

		$this->loader->setRepository($repository = m::mock('Illuminate\Config\Repository'));
		$repository->shouldReceive('set')->with('corge::foo.bar.baz', null)->once();

		$this->loader->persist('local', 'foo', 'bar.baz', array('qux' => 'fred', 'thud' => true), 'corge');
	}

	public function testPersistingNew()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->twice()->andReturn($query = m::mock('Illuminate\Database\Query'));
		$this->database->shouldReceive('getCacheManager')->once()->andReturn($cacheManager = m::mock('Illuminate\Cache\cacheManager'));
		$cacheManager->shouldReceive('forget')->once();

		$query->shouldReceive('where')->with('environment', '=', 'local')->once()->andReturn($query);
		$query->shouldReceive('where')->with('group', '=', 'foo')->once()->andReturn($query);
		$query->shouldReceive('where')->with('item', '=', 'bar.baz')->once()->andReturn($query);
		$query->shouldReceive('where')->with('namespace', '=', 'corge')->once()->andReturn($query);

		$query->shouldReceive('first')->once()->andReturn(null);
		$query->shouldReceive('insert')->with(array(
			'environment' => 'local',
			'group'       => 'foo',
			'item'        => 'bar.baz',
			'value'       => '{"qux":"fred","thud":true}',
			'namespace'   => 'corge',
		))->once();

		$this->loader->setRepository($repository = m::mock('Illuminate\Config\Repository'));
		$repository->shouldReceive('set')->with('corge::foo.bar.baz', null)->once();

		$this->loader->persist('local', 'foo', 'bar.baz', array('qux' => 'fred', 'thud' => true), 'corge');
	}

	public function testPersistingStoresAllValuesAsJson()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->twice()->andReturn($query = m::mock('Illuminate\Database\Query'));
		$this->database->shouldReceive('getCacheManager')->once()->andReturn($cacheManager = m::mock('Illuminate\Cache\cacheManager'));
		$cacheManager->shouldReceive('forget')->once();

		$query->shouldReceive('where')->with('environment', '=', 'local')->once()->andReturn($query);
		$query->shouldReceive('where')->with('group', '=', 'foo')->once()->andReturn($query);
		$query->shouldReceive('where')->with('item', '=', 'bar.baz')->once()->andReturn($query);
		$query->shouldReceive('where')->with('namespace', '=', 'corge')->once()->andReturn($query);

		$query->shouldReceive('first')->once()->andReturn(null);
		$query->shouldReceive('insert')->with(array(
			'environment' => 'local',
			'group'       => 'foo',
			'item'        => 'bar.baz',
			'value'       => 'false',
			'namespace'   => 'corge',
		))->once();

		$this->loader->setRepository($repository = m::mock('Illuminate\Config\Repository'));
		$repository->shouldReceive('set')->with('corge::foo.bar.baz', null)->once();

		$this->loader->persist('local', 'foo', 'bar.baz', false, 'corge');
	}

	public function testPersistingNullRemovesEntry()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($query = m::mock('Illuminate\Database\Query'));
		$this->database->shouldReceive('getCacheManager')->once()->andReturn($cacheManager = m::mock('Illuminate\Cache\cacheManager'));
		$cacheManager->shouldReceive('forget')->once();

		$query->shouldReceive('where')->with('environment', '=', 'local')->once()->andReturn($query);
		$query->shouldReceive('where')->with('group', '=', 'foo')->once()->andReturn($query);
		$query->shouldReceive('where')->with('item', '=', 'bar.baz')->once()->andReturn($query);
		$query->shouldReceive('where')->with('namespace', '=', 'corge')->once()->andReturn($query);

		$query->shouldReceive('first')->once()->andReturn(new stdClass);
		$query->shouldReceive('delete')->once();

		$this->loader->setRepository($repository = m::mock('Illuminate\Config\Repository'));
		$repository->shouldReceive('set')->with('corge::foo.bar.baz', null)->once();

		$this->loader->persist('local', 'foo', 'bar.baz', null, 'corge');
	}

	public function testPersistingWitoutNamespace()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($query = m::mock('Illuminate\Database\Query'));
		$this->database->shouldReceive('getCacheManager')->once()->andReturn($cacheManager = m::mock('Illuminate\Cache\cacheManager'));
		$cacheManager->shouldReceive('forget')->once();

		$query->shouldReceive('where')->with('environment', '=', 'local')->once()->andReturn($query);
		$query->shouldReceive('where')->with('group', '=', 'foo')->once()->andReturn($query);
		$query->shouldReceive('where')->with('item', '=', 'bar.baz')->once()->andReturn($query);
		$query->shouldReceive('whereNull')->with('namespace')->once()->andReturn($query);

		$query->shouldReceive('first')->once()->andReturn(new stdClass);
		$query->shouldReceive('delete')->once();

		$this->loader->setRepository($repository = m::mock('Illuminate\Config\Repository'));
		$repository->shouldReceive('set')->with('foo.bar.baz', '')->once();

		$this->loader->persist('local', 'foo', 'bar.baz');
	}

	public function testPersistingWithFallback()
	{
		$this->database->shouldReceive('table')->with($this->databaseTable)->once()->andReturn($query = m::mock('Illuminate\Database\Query'));
		$this->database->shouldReceive('getCacheManager')->once()->andReturn($cacheManager = m::mock('Illuminate\Cache\cacheManager'));
		$cacheManager->shouldReceive('forget')->once();

		$query->shouldReceive('where')->with('environment', '=', 'local')->once()->andReturn($query);
		$query->shouldReceive('where')->with('group', '=', 'foo')->once()->andReturn($query);
		$query->shouldReceive('where')->with('item', '=', 'bar.baz')->once()->andReturn($query);
		$query->shouldReceive('where')->with('namespace', '=', 'corge')->once()->andReturn($query);

		$query->shouldReceive('first')->once()->andReturn(new stdClass);
		$query->shouldReceive('delete')->once();

		$this->loader->setRepository($repository = m::mock('Illuminate\Config\Repository'));
		$repository->shouldReceive('set')->with('corge::foo.bar.baz', null)->once();

		$this->loader->persist('local', 'foo', 'bar.baz', null, 'corge');
	}

	public function testPersistingWithoutDatabase()
	{
		$this->loader = new CompositeLoader($this->filesystem, $this->defaultPath);

		$this->loader->setRepository($repository = m::mock('Illuminate\Config\Repository'));

		$repository->shouldReceive('parseKey')->once();

		$this->loader->set('local', 'foo', 'bar', 'foobar');

		$actual = $this->loader->load('local', 'foo', 'bar');

		$this->assertEmpty($actual);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testPersistingWithoutRepository()
	{
		$this->loader = new CompositeLoader($this->filesystem, $this->defaultPath);

		$record1 = new stdClass;
		$record1->environment = 'local';
		$record1->group       = 'foo';
		$record1->namespace   = 'bar';
		$record1->item        = 'baz.bat.qux';
		$record1->value       = 'corge';

		$record2 = new stdClass;
		$record2->environment = 'local';
		$record2->group       = 'foo';
		$record2->namespace   = 'bar';
		$record2->item        = 'foo';
		$record2->value       = 'bar';

		$record3 = new stdClass;
		$record3->environment = 'local';
		$record3->group       = 'foo';
		$record3->namespace   = 'bar';
		$record3->item        = 'fred';
		$record3->value       = '{"waldo":true,"fred":"thud"}';

		$records = array($record1, $record2, $record3);

		$this->loader->set('local', 'foo', 'bar', 'foobar	');
	}

	public function testCascadePackage()
	{
		$this->filesystem->shouldReceive('exists')->twice()->andReturn(true);

		$actual = array(
			'host'     => null,
			'username' => null,
		);

		$this->filesystem->shouldReceive('getRequire')->twice()->andReturn($replacement = array(
			'host'     => 'smtp.example.com',
			'username' => 'foobar',
		));

		$cascaded = $this->loader->cascadePackage('*', 'config', 'mail', $actual);

		$this->assertSame($cascaded, $replacement);
	}

}
