<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\NetteAddons\Forms;

use Nella\NetteAddons\Forms\Container,
	Nella\NetteAddons\Forms\Form,
	Nette\Forms\Rule;

class ContainerTest extends \Nella\Testing\TestCase
{
	/** @var Nella\NetteAddons\Form\Container */
	private $container;

	public function setup()
	{
		$form = new Form;
		$form['test'] = $this->container = new Container;
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nette\Forms\Container', $this->container);
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

	public function testContainer()
	{
		$cont = $this->container->addContainer('foo');

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Container', $cont, 'is Nella\NetteAddons\Forms\Container');
		$this->assertSame($this->container['foo'], $cont, "is registered container same as created");
	}

	public function testEmail()
	{
		$item = $this->container->addEmail('email', "E-mail");

		$this->assertInstanceOf('Nette\Forms\Controls\TextInput', $item, 'is Nette\Forms\Controls\TextInput');
		$this->assertEquals('email', $item->control->type, "email type");
		$this->assertTrue($this->isRuleExist($item, Form::EMAIL), "Form::EMAIL after Form::FILLED");
	}

	public function testUrl()
	{
		$item = $this->container->addUrl('url', "URL");

		$this->assertInstanceOf('Nette\Forms\Controls\TextInput', $item, 'is Nette\Forms\Controls\TextInput');
		$this->assertEquals("url", $item->control->type, "url type");
		$this->assertTrue($this->isRuleExist($item, Form::URL), "Form::URL after Form::FILLED");
	}

	public function testNumber()
	{
		$item = $this->container->addNumber('number', "Number", 2, 0, 20);

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
		$item = $this->container->addRange('range', "Range", 2, 0, 20);

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
		$item = $this->container->addDate('date', "Date");

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Controls\Date', $item, 'is Nella\NetteAddons\Forms\Controls\Date');
		$this->assertEquals("date", $item->control->type, "date type");
	}

	public function testDateTime()
	{
		$item = $this->container->addDateTime('datetime', "Datetime");

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Controls\DateTime', $item, 'is Nella\NetteAddons\Forms\Controls\DateTime');
		$this->assertEquals("datetime", $item->control->type, "datetime type");
	}

	public function testTime()
	{
		$item = $this->container->addTime('time', "Time");

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Controls\Time', $item, 'is Nella\NetteAddons\Forms\Controls\Time');
		$this->assertEquals("time", $item->control->type, "time type");
	}

	public function testSearch()
	{
		$item = $this->container->addSearch('search', "Search");

		$this->assertInstanceOf('Nette\Forms\Controls\TextInput', $item, 'is Nette\Forms\Controls\TextInput');
		$this->assertEquals("search", $item->control->type, "search type");
	}

	public function testEditor()
	{
		$item = $this->container->addEditor('editor', "Editor");

		$this->assertInstanceOf('Nette\Forms\Controls\TextArea', $item, 'is Nette\Forms\Controls\TextArea');
		$this->assertTrue((bool) $item->control->{'data-nella-editor'}, "editor data attribute");
	}

	public function testDynamic()
	{
		$item = $this->container->addDynamic('dyn', function($container) {});

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Multipler', $item, 'is Nella\NetteAddons\Forms\Multipler');
	}

	public function testTags()
	{
		$item = $this->container->addTags('tags', "Tags");

		$this->assertInstanceOf('Nella\NetteAddons\Forms\Controls\TagsInput', $item, 'is Nella\NetteAddons\Forms\Controls\TagsInput');
	}
}
