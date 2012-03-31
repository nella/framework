<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\NetteAddons\Forms;

use Nella\NetteAddons\Forms\Form,
	Nette\Forms\Rule;

class FormTest extends \Nella\Testing\TestCase
{
	/** @var Nella\NetteAddons\Forms\Form */
	private $form;

	public function setup()
	{
		$this->form = new Form;
	}

	/**
	 * @param \Nette\Forms\IFormControl
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

	public function testContainer()
	{
		$cont = $this->form->addContainer('foo');

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Container', $cont, 'is Nella\NetteAddons\Forms\Container');
		$this->assertSame($this->form['foo'], $cont, "is registered container same as created");
	}

	public function testEmail()
	{
		$item = $this->form->addEmail('email', "E-mail");

		$this->assertInstanceOf('Nette\Forms\Controls\TextInput', $item, 'is Nette\Forms\Controls\TextInput');
		$this->assertEquals('email', $item->control->type, "email type");
		$this->assertTrue($this->isRuleExist($item, Form::EMAIL), "Form::EMAIL after Form::FILLED");
	}

	public function testUrl()
	{
		$item = $this->form->addUrl('url', "URL");

		$this->assertInstanceOf('Nette\Forms\Controls\TextInput', $item, 'is Nette\Forms\Controls\TextInput');
		$this->assertEquals("url", $item->control->type, "url type");
		$this->assertTrue($this->isRuleExist($item, Form::URL), "Form::URL after Form::FILLED");
	}

	public function testNumber()
	{
		$item = $this->form->addNumber('number', "Number", 2, 0, 20);

		$this->assertInstanceOf('Nette\Forms\Controls\TextInput', $item, 'is Nette\Forms\Controls\TextInput');
		$this->assertEquals("number", $item->control->type, "number type");
		$this->assertEquals(0, $item->control->min, "min");
		$this->assertEquals(20, $item->control->max, "max");
		$this->assertEquals(2, $item->control->step, "step");
		$this->assertTrue($this->isRuleExist($item, Form::NUMERIC), "Form::NUMERIC after Form::FILLED");
		$this->assertTrue($this->isRuleExist($item, Form::RANGE), "Form::RANGE after Form::FILLED");
	}

	public function testRange()
	{
		$item = $this->form->addRange('range', "Range", 2, 0, 20);

		$this->assertInstanceOf('Nette\Forms\Controls\TextInput', $item, 'is Nette\Forms\Controls\TextInput');
		$this->assertEquals("range", $item->control->type, "range type");
		$this->assertEquals(0, $item->control->min, "min");
		$this->assertEquals(20, $item->control->max, "max");
		$this->assertEquals(2, $item->control->step, "step");
		$this->assertTrue($this->isRuleExist($item, Form::NUMERIC), "Form::NUMERIC after Form::FILLED");
		$this->assertTrue($this->isRuleExist($item, Form::RANGE), "Form::RANGE after Form::FILLED");
	}

	public function testDate()
	{
		$item = $this->form->addDate('date', "Date");

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Controls\Date', $item, 'is Nella\NetteAddons\Forms\Controls\Date');
		$this->assertEquals("date", $item->control->type, "date type");
	}

	public function testDateTime()
	{
		$item = $this->form->addDateTime('datetime', "Datetime");

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Controls\DateTime', $item, 'is Nella\NetteAddons\Forms\Controls\DateTime');
		$this->assertEquals("datetime", $item->control->type, "datetime type");
	}

	public function testTime()
	{
		$item = $this->form->addTime('time', "Time");

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Controls\Time', $item, 'is Nella\NetteAddons\Forms\Controls\Time');
		$this->assertEquals("time", $item->control->type, "time type");
	}

	public function testSearch()
	{
		$item = $this->form->addSearch('search', "Search");

		$this->assertInstanceOf('Nette\Forms\Controls\TextInput', $item, 'is Nette\Forms\Controls\TextInput');
		$this->assertEquals("search", $item->control->type, "search type");
	}

	public function testEditor()
	{
		$item = $this->form->addEditor('editor', "Editor");

		$this->assertInstanceOf('Nette\Forms\Controls\TextArea', $item, 'is Nette\Forms\Controls\TextArea');
		$this->assertTrue((bool) $item->control->{'data-nella-editor'}, "editor data attribute");
	}

	public function testDynamic()
	{
		$item = $this->form->addDynamic('dyn', function($container) {});

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Multipler', $item, 'is Nella\NetteAddons\Forms\Multipler');
	}

	public function testTags()
	{
		$item = $this->form->addTags('tags', "Tags");

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Controls\TagsInput', $item, 'is Nella\NetteAddons\Forms\Controls\TagsInput');
	}
}
