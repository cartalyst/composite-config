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
 * @version    5.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\CompositeConfig\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class RepositoryTest extends FunctionalTestCase
{
    /** @test */
    public function it_can_set_and_get_the_database_connection()
    {
        $connection = DB::connection();

        Config::setDatabase($connection);

        $this->assertSame($connection, Config::getDatabase());
    }

    /** @test */
    public function it_can_set_and_get_the_database_table()
    {
        Config::setDatabaseTable('config');

        $this->assertSame('config', Config::getDatabaseTable());
    }

    // /** @test */
    // public function it_loads_configs_from_the_database()
    // {
    //     $this->shouldFetch();

    //     $expected = [
    //         'baz' => [
    //             'bat' => [
    //                 'qux' => 'corge',
    //             ],
    //         ],
    //         'foo'  => 'bar',
    //         'fred' => [
    //             'waldo' => true,
    //             'fred'  => 'thud',
    //         ],
    //     ];

    //     $actual = $this->repository->all();

    //     $this->assertSame($expected, $actual);
    // }

    /** @test */
    public function it_fallsback_to_the_filesystem_if_not_found_on_database()
    {
        $this->assertSame('foo', Config::get('qux', 'foo'));
    }

    /** @test */
    public function it_can_persist_configs_to_the_database()
    {
        Config::persist('foo', 'bar');

        $this->assertSame('bar', Config::get('foo'));
    }

    /** @test */
    public function it_will_update_existing_records_on_persist()
    {
        Config::persist('foo', 'Value 1');

        $this->assertSame('Value 1', Config::get('foo'));

        Config::persist('foo', 'Value 2');

        $this->assertSame('Value 2', Config::get('foo'));
    }

    /** @test */
    public function it_will_delete_existing_records_on_persist_if_value_is_unset()
    {
        Config::persist('something', 'Value');

        $this->assertSame('Value', Config::get('something'));

        Config::persist('something', null);

        $this->assertNull(Config::get('something'));
    }

    /** @test */
    public function retrieve_level_one_config_value_set_at_runtime()
    {
        $configValuePreRuntimeSet = Config::get('foo');

        Config::set('foo', 'not bar');

        $configValuePostRuntimeSet = Config::get('foo');

        $this->assertNotSame($configValuePreRuntimeSet, $configValuePostRuntimeSet);
    }

    /** @test */
    public function retrieve_level_two_config_value_set_at_runtime()
    {
        $configValuePreRuntimeSet = Config::get('fred.fred');

        Config::set('fred.fred', 'not thud');

        $configValuePostRuntimeSet = Config::get('fred.fred');

        $this->assertNotSame($configValuePreRuntimeSet, $configValuePostRuntimeSet);
    }

    /** @test */
    public function retrieve_level_three_config_value_set_at_runtime()
    {
        $configValuePreRuntimeSetLevelThree = Config::get('baz.bat.qux');
        $configValuePreRuntimeSetLevelTwo   = Config::get('baz.bat');
        $configValuePreRuntimeSetLevelOne   = Config::get('baz');

        Config::set('baz.bat.qux', 'not corge');

        $configValuePostRuntimeSetLevelThree = Config::get('baz.bat.qux');
        $configValuePostRuntimeSetLevelTwo   = Config::get('baz.bat');
        $configValuePostRuntimeSetLevelOne   = Config::get('baz');

        $this->assertNotSame($configValuePreRuntimeSetLevelThree, $configValuePostRuntimeSetLevelThree);
        $this->assertNotSame($configValuePreRuntimeSetLevelTwo, $configValuePostRuntimeSetLevelTwo);
        $this->assertNotSame($configValuePreRuntimeSetLevelOne, $configValuePostRuntimeSetLevelOne);
    }

    /** @test */
    public function retrieve_level_one_config_value_from_array_set_at_runtime()
    {
        $configValuePreRuntimeSet = Config::get('foo');
        $configPreRuntimeSet      = Config::all();

        Config::set(['foo' => 'not bar']);

        $configValuePostRuntimeSet = Config::get('foo');
        $configPostRuntimeSet      = Config::all();

        $this->assertNotSame($configValuePreRuntimeSet, $configValuePostRuntimeSet);
        $this->assertNotSame($configPreRuntimeSet, $configPostRuntimeSet);
    }

    /** @test */
    public function retrieve_level_two_config_value_from_array_set_at_runtime()
    {
        $configValuePreRuntimeSet = Config::get('fred.fred');

        Config::set(['fred.fred' => 'not thud']);

        $configValuePostRuntimeSet = Config::get('fred.fred');

        $this->assertNotSame($configValuePreRuntimeSet, $configValuePostRuntimeSet);
    }

    /** @test */
    public function retrieve_level_three_config_value_from_array_set_at_runtime()
    {
        $configValuePreRuntimeSetLevelThree = Config::get('baz.bat.qux');
        $configValuePreRuntimeSetLevelTwo   = Config::get('baz.bat');
        $configValuePreRuntimeSetLevelOne   = Config::get('baz');

        Config::set(['baz.bat.qux' => 'not corge']);

        $configValuePostRuntimeSetLevelThree = Config::get('baz.bat.qux');
        $configValuePostRuntimeSetLevelTwo   = Config::get('baz.bat');
        $configValuePostRuntimeSetLevelOne   = Config::get('baz');

        $this->assertNotSame($configValuePreRuntimeSetLevelThree, $configValuePostRuntimeSetLevelThree);
        $this->assertNotSame($configValuePreRuntimeSetLevelTwo, $configValuePostRuntimeSetLevelTwo);
        $this->assertNotSame($configValuePreRuntimeSetLevelOne, $configValuePostRuntimeSetLevelOne);
    }
}
