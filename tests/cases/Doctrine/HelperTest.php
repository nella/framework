<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Doctrine;

use Nella\Doctrine\Helper,
	Nella\Model;

class HelperTest extends \Nella\Testing\TestCase
{
	public function dataConvertException()
	{
		$dupl1 = new \PDOException('foo', 23000);
		$dupl1->errorInfo = array(23000, 1062);

		$empty1 = new \PDOException('bar', 23000);
		$empty1->errorInfo = array(23000, 1048, "error in 'test'");

		$other = new \PDOException('baz', 23000);
		$other->errorInfo = array(23000, 999999);

		return array(
			array($dupl1, new Model\DuplicateEntryException($dupl1->getMessage(), NULL, $dupl1)),
			array($empty1, new Model\EmptyValueException($empty1->getMessage(), 'test', $empty1)),
			array($other, new Model\Exception($other->getMessage(), NULL, $other)),
		);
	}

	/**
	 * @dataProvider dataConvertException
	 */
	public function testConvertException($exception, $expected)
	{
		try {
			Helper::convertException($exception);
		} catch (\Nella\Model\Exception $e) {
			$this->assertEquals($expected, $e); // @todo message
		}
	}
}