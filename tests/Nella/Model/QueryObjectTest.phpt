<?php
/**
 * Test: Nella\Model\QueryObject
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Model\QueryObjectTest
 */

namespace Nella\Tests\Model;

use Assert,
	Nella\Model\QueryObject;

require_once __DIR__ . '/../../bootstrap.php';

class QueryObjectTest extends \TestCase
{
	/** @var \Nella\Model\IQueryObject */
	private $query;

	public function setUp()
	{
		parent::setUp();
		$this->query = new QueryObject;
	}

	public function testInstance()
	{
		Assert::true($this->query instanceof \Nella\Model\IQueryObject, 'is instance "IQueryObject"');
	}

	public function testGetPaginator()
	{
		Assert::null($this->query->getPaginator(), '->getPaginator()');
		Assert::null($this->query->paginator, '->paginator');

		$paginator = new \Nette\Utils\Paginator;
		$query = new QueryObject($paginator);
		Assert::same($paginator, $query->getPaginator(), '->getPaginator() same');
	}
}