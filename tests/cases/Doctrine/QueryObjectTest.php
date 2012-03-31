<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Doctrine;

use Nella\Doctrine\QueryObject;

class QueryObjectTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Doctrine\IQueryObject */
	private $query;

	public function setup()
	{
		parent::setup();
		$this->query = new QueryObject;
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Model\IQueryObject', $this->query, 'is instance "QueryObject"');
	}

	public function testGetPaginator()
	{
		$this->assertNull($this->query->getPaginator(), '->getPaginator()');
		$this->assertNull($this->query->paginator, '->paginator');

		$paginator = new \Nette\Utils\Paginator;
		$query = new QueryObject($paginator);
		$this->assertSame($paginator, $query->getPaginator(), '->getPaginator() same');
	}
}