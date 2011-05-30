<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Forms\Controls;

require_once __DIR__ . "/../../bootstrap.php";

class DateTimeTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Forms\Controls\DateTime */
	private $item;

	public function setup()
	{
		$form = new \Nella\Forms\Form;
		$form['foo'] = $this->item = new \Nella\Forms\Controls\DateTime("foo");
	}

	public function testType()
	{
		$this->assertEquals("datetime", $this->item->control->type, "datetime type");
	}

	public function testValues()
	{
		$dt = new \DateTime();
		$this->assertNull($this->item->getValue(), "is default NULL");
		$this->item->setValue($dt);
		$this->assertInstanceOf('DateTime', $this->item->getValue(), "test value getter returns DateTime object");
		$this->assertEquals($dt->format("Y-m-d H:i"), $this->item->getValue()->format("Y-m-d H:i"), "test value getter (previous set with setter)");
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
