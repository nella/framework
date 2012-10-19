<?php
/**
 * Test: Nella\Doctrine\Cache
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Doctrine\CacheTest
 */

namespace Nella\Tests\Doctrine;

use Assert,
	Nella\Doctrine\Cache;

require_once __DIR__ . '/../../bootstrap.php';

class CacheTest extends \TestCase
{
	/** @var \Nella\Doctrine\Cache */
	private $cache;

	public function setUp()
	{
		parent::setUp();
		$this->cache = new Cache(new \Nette\Caching\Storages\MemoryStorage);
	}

	public function testDefault()
	{
		Assert::false($this->cache->contains('foo'), "default is emtpy");

		$this->cache->save('foo', "test");
		Assert::true($this->cache->contains('foo'), "->contains('foo')");
		Assert::equal("test", $this->cache->fetch('foo'), "->load('foo')");

		$this->cache->delete('foo');
		Assert::false($this->cache->contains('foo'), "->contains('foo') - removed");
	}
}
