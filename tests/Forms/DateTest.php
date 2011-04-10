<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Forms;

require_once __DIR__ . "/../bootstrap.php";

class DateTest extends \PHPUnit_Framework_TestCase
{
	/** @var Nella\Forms\Date */
	private $item;
	
	public function setUp()
	{
		$form = new \Nella\Forms\Form;
		$form['foo'] = $this->item = new \Nella\Forms\Date("foo");
	}
	
	public function testType()
	{
		$this->assertEquals("date", $this->item->control->type, "date type");
	}
	
	public function testValues()
	{
		$dt = new \DateTime();
		$this->assertNull($this->item->getValue(), "is default NULL");
		$this->item->setValue($dt);
		$this->assertInstanceOf('DateTime', $this->item->getValue(), "test value getter returns DateTime object");
		$this->assertEquals($dt->format("Y-m-d"), $this->item->getValue()->format("Y-m-d"), "test value getter (previous set with setter)");
		$this->item->value = NULL;
		$this->assertNull($this->item->value, "test value property getter (previous set with property setter)");
	}
}
