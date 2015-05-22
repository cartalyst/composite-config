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

namespace Cartalyst\CompositeConfig\Tests;

use stdClass;
use Mockery as m;
use PHPUnit_Framework_TestCase;
use Cartalyst\CompositeConfig\Repository;

class RepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Setup resources and dependencies.
     *
     * @return void
     */
    public function setUp()
    {
        $this->cache    = m::mock('Illuminate\Cache\CacheManager');
        $this->database = m::mock('Illuminate\Database\Connection');

        $this->repository = new Repository([], $this->cache);
        $this->repository->setDatabase($this->database);
        $this->repository->setDatabaseTable('config');
    }

    /** @test */
    public function it_can_set_and_retrieve_the_database_connection()
    {
        $connection = m::mock('Illuminate\Database\Connection');

        $this->repository->setDatabase($connection);

        $this->assertSame($connection, $this->repository->getDatabase());
    }

    /** @test */
    public function it_can_set_and_retrieve_the_database_table()
    {
        $this->repository->setDatabaseTable('config');

        $this->assertSame('config', $this->repository->getDatabaseTable());
    }

    /** @test */
    public function it_loads_configs_from_the_database()
    {
        $this->shouldFetch();

        $expected = [
            'baz'  => [
                'bat' => [
                    'qux' => 'corge',
                ],
            ],
            'foo'  => 'bar',
            'fred' => [
                'waldo' => true,
                'fred'  => 'thud',
            ],
        ];

        $actual = $this->repository->all();

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_loads_configs_out_of_the_cached_array()
    {
        $this->shouldFetch();

        $this->assertEquals('bar', $this->repository->get('foo'));
    }

    /** @test */
    public function it_fallsback_to_the_filesystem_if_not_found_on_database()
    {
        $this->repository = new Repository(['qux' => 'foo']);

        $this->assertEquals('foo', $this->repository->get('qux'));
    }

    /** @test */
    public function it_can_persist_configs_to_the_database()
    {
        $this->database->shouldReceive('table')
            ->with('config')
            ->once()
            ->andReturn($query = m::mock('Illuminate\Database\Query\Builder'));

        $query->shouldReceive('where')
            ->with('item', '=', 'foo')
            ->once()
            ->andReturn($query);

        $query->shouldReceive('first')
            ->once();

        $query->shouldReceive('insert')
            ->with(['item' => 'foo', 'value' => '"bar"'])
            ->once();

        $this->shouldFetch(false);

        $this->repository->persist('foo', 'bar');
    }

    /** @test */
    public function it_will_update_existing_records_on_persist()
    {
        $this->database->shouldReceive('table')
            ->with('config')
            ->once()
            ->andReturn($query = m::mock('Illuminate\Database\Query\Builder'));

        $model = m::mock('Illuminate\Support\Collection');

        $query->shouldReceive('where')
            ->with('item', '=', 'foo')
            ->once()
            ->andReturn($query);

        $query->shouldReceive('first')
            ->once()
            ->andReturn($model);

        $query->shouldReceive('update')
            ->with(['value' => '"bar"'])
            ->once();

        $this->shouldFetch(false);

        $this->repository->persist('foo', 'bar');
    }

    /** @test */
    public function it_will_delete_existing_records_on_persist_if_value_is_unset()
    {
        $this->database->shouldReceive('table')
            ->with('config')
            ->once()
            ->andReturn($query = m::mock('Illuminate\Database\Query\Builder'));

        $model = m::mock('Illuminate\Support\Collection');

        $query->shouldReceive('where')
            ->with('item', '=', 'foo')
            ->once()
            ->andReturn($query);

        $query->shouldReceive('first')
            ->once()
            ->andReturn($model);

        $query->shouldReceive('delete')
            ->once();

        $this->shouldFetch(false);

        $this->repository->persist('foo', null);
    }

    /** @test */
    public function it_will_return_null_if_database_connection_is_unset()
    {
        $this->repository = new Repository();

        $this->assertNull($this->repository->persist('foo', 'bar'));
    }

    /**
     * Instantiates a config repository.
     *
     * @param  array  $items
     * @return void
     */
    protected function shouldFetch($trigger = true)
    {
        $record1        = new stdClass;
        $record1->item  = 'baz.bat.qux';
        $record1->value = 'corge';

        $record2        = new stdClass;
        $record2->item  = 'foo';
        $record2->value = 'bar';

        $record3        = new stdClass;
        $record3->item  = 'fred';
        $record3->value = '{"waldo":true,"fred":"thud"}';

        $records = [$record1, $record2, $record3];

        $this->cache->shouldReceive('forget')
            ->with('cartalyst.config')
            ->once();

        $this->cache->shouldReceive('rememberForever')
            ->with('cartalyst.config', m::on(function ($callback) {
                $callback();

                return true;
            }))
            ->once()
            ->andReturn($records);

        $this->database->shouldReceive('table')
            ->with('config')
            ->once()
            ->andReturn($this->database);

        $this->database->shouldReceive('get');

        if ($trigger)
        {
            $this->repository->fetchAndCache();
        }
    }
}
