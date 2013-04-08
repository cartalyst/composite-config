<?php
/**
 * Part of the Composite Config package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Composite Config
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\CompositeConfig\Repository;

class RepositoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	public function testSettingConfigPersists()
	{
		$config = m::mock('Cartalyst\CompositeConfig\Repository[load]');
		$config->__construct($loader = m::mock('Cartalyst\CompositeConfig\CompositeLoader'), 'local');

		$loader->shouldReceive('load')->with('local', 'bar', 'foo')->once();
		$loader->shouldReceive('persist')->with('local', 'bar', 'baz.qux', 'baz', 'foo')->once();

		$config->set('foo::bar.baz.qux', 'baz');
		$this->assertEquals('baz', $config->get('foo::bar.baz.qux'));
	}

}
