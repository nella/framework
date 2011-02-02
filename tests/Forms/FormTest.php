<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Form;

use Nella\Forms\Form, 
	Nette\Forms\Rule;

class FormTest extends \PHPUnit_Framework_TestCase
{
	/** @var Nella\Forms\Form */
	private $form;
	
	public function setUp()
	{
		$this->form = new Form;
	}
	
	/**
	 * @param Nette\Forms\IFormControl
	 * @param int
	 * @param bool
	 */
	private function isRuleExist($item, $cond, $filled = TRUE)
	{
		if ($filled) {
			$rules = (array) $item->rules->iterator;
			$rules = array_filter($rules, function ($rule) { if ($rule->type == Rule::CONDITION) return $rule; });
		} else {
			$rules = (array) $item->rules->iterator;
		}
		
		return (bool) count(array_filter($rules, function ($rule) use ($cond) {
			if ((bool) array_filter((array) $rule->subRules->iterator, 
				function ($rule) use ($cond) {
					if ($rule->operation == $cond) {
						return $rule;
					}
				}
			)) {
				return $rule;
			}
		}));
	}
	
	/**
	 * @covers Nella\Forms\Form::addEmail
	 */
	public function testEmail()
	{
		$item = $this->form->addEmail('email', "E-mail");
		
		$this->assertInstanceOf('Nette\Forms\TextInput', $item, 'is Nette\Forms\TextInput');
		$this->assertEquals('email', $item->control->type, "email type");
		$this->assertTrue($this->isRuleExist($item, Form::EMAIL), "Form::EMAIL after Form::FILLED");
	}
	
	/**
	 * @covers Nella\Forms\Form::addUrl
	 */
	public function testUrl()
	{
		$item = $this->form->addUrl('url', "URL");
		
		$this->assertInstanceOf('Nette\Forms\TextInput', $item, 'is Nette\Forms\TextInput');
		$this->assertEquals("url", $item->control->type, "url type");
		$this->assertTrue($this->isRuleExist($item, Form::URL), "Form::URL after Form::FILLED");
	}
	
	/**
	 * @covers Nella\Forms\Form::addNumber
	 */
	public function testNumber()
	{
		$item = $this->form->addNumber('number', "Number", 2, 0, 20);
		
		$this->assertInstanceOf('Nette\Forms\TextInput', $item, 'is Nette\Forms\TextInput');
		$this->assertEquals("number", $item->control->type, "number type");
		$this->assertEquals(0, $item->control->min, "min");
		$this->assertEquals(20, $item->control->max, "max");
		$this->assertEquals(2, $item->control->step, "step");
		$this->assertTrue($this->isRuleExist($item, Form::NUMERIC), "Form::NUMERIC after Form::FILLED");
		$this->assertTrue($this->isRuleExist($item, Form::RANGE), "Form::RANGE after Form::FILLED");
	}
	
	/**
	 * @covers Nella\Forms\Form::addRange
	 */
	public function testRange()
	{
		$item = $this->form->addRange('range', "Range", 2, 0, 20);
		
		$this->assertInstanceOf('Nette\Forms\TextInput', $item, 'is Nette\Forms\TextInput');
		$this->assertEquals("range", $item->control->type, "range type");
		$this->assertEquals(0, $item->control->min, "min");
		$this->assertEquals(20, $item->control->max, "max");
		$this->assertEquals(2, $item->control->step, "step");
		$this->assertTrue($this->isRuleExist($item, Form::NUMERIC), "Form::NUMERIC after Form::FILLED");
		$this->assertTrue($this->isRuleExist($item, Form::RANGE), "Form::RANGE after Form::FILLED");
	}
	
	/**
	 * @covers Nella\Forms\Form::addDate
	 */
	public function testDate()
	{
		$item = $this->form->addDate('date', "Date");
		
		$this->assertInstanceOf('Nella\Forms\Date', $item, 'is Nella\Forms\Date');
		$this->assertEquals("date", $item->control->type, "date type");
		//$this->assertTrue($this->isRuleExist($item, Form::DATE), "Form::DATE after Form::FILLED");
	}
	
	/**
	 * @covers Nella\Forms\Form::addDateTime
	 */
	public function testDateTime()
	{
		$item = $this->form->addDateTime('datetime', "Datetime");
		
		$this->assertInstanceOf('Nella\Forms\DateTime', $item, 'is Nette\Forms\DateTime');
		$this->assertEquals("datetime", $item->control->type, "datetime type");
		//$this->assertTrue($this->isRuleExist($item, Form::DATETIME), "Form::DATETIME after Form::FILLED");
	}
	
	/**
	 * @covers Nella\Forms\Form::addTime
	 */
	public function testTime()
	{
		$item = $this->form->addTime('time', "Time");
		
		$this->assertInstanceOf('Nella\Forms\Time', $item, 'is Nella\Forms\Time');
		$this->assertEquals("time", $item->control->type, "time type");
		//$this->assertTrue($this->isRuleExist($item, Form::TIME), "Form::TIME after Form::FILLED");
	}
	
	/**
	 * @covers Nella\Forms\Form::addSearch
	 */
	public function testSearch()
	{
		$item = $this->form->addSearch('search', "Search");
		
		$this->assertInstanceOf('Nette\Forms\TextInput', $item, 'is Nette\Forms\TextInput');
		$this->assertEquals("search", $item->control->type, "search type");
	}
	
	/**
	 * @covers Nella\Forms\Form::addEditor
	 */
	public function testEditor()
	{
		$item = $this->form->addEditor('editor', "Editor");
		
		$this->assertInstanceOf('Nette\Forms\TextArea', $item, 'is Nette\Forms\TextArea');
		$this->assertTrue((bool) $item->control->{'data-nella-editor'}, "editor data attribute");
	}
}
