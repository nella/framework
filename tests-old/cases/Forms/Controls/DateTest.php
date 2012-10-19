<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Forms\Controls;

class DateTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Forms\Controls\Date */
	private $item;

	public function setup()
	{
		$form = new \Nella\Forms\Form;
		$form['foo'] = $this->item = new \Nella\Forms\Controls\Date("foo");
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

	public function testValidate()
	{
		$this->assertFalse($this->item->isFilled(), "validate empty value");
		$this->item->value = new \DateTime;
		$this->assertTrue($this->item->isFilled(), "validate value");
		$this->item->value = "test";
		$this->assertFalse($this->item->isFilled(), "validate invalid value");
	}
}
