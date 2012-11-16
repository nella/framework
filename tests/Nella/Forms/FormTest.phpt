<?php
/**
 * Test: Nella\Forms\Form
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Forms;

use Tester\Assert,
	Nella\Forms\Form;

require_once __DIR__ . '/../../bootstrap.php';

class FormTest extends \Tester\TestCase
{
	/** @var \Nella\Forms\Form */
	private $form;

	public function setUp()
	{
		parent::setUp();
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
			$rules = array_filter($rules, function ($rule) {
				if ($rule->type == \Nette\Forms\Rule::CONDITION) {
					return $rule;
				}
			});
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

		Assert::true($cont instanceof \Nella\Forms\Container, 'is Nella\Forms\Container');
		Assert::same($this->form['foo'], $cont, "is registered container same as created");
	}

	public function testEmail()
	{
		$item = $this->form->addEmail('email', "E-mail");

		Assert::true($item instanceof \Nette\Forms\Controls\TextInput, 'is Nette\Forms\Controls\TextInput');
		Assert::equal('email', $item->control->type, "email type");
		Assert::true($this->isRuleExist($item, Form::EMAIL), "Form::EMAIL after Form::FILLED");
	}

	public function testUrl()
	{
		$item = $this->form->addUrl('url', "URL");

		Assert::true($item instanceof \Nette\Forms\Controls\TextInput, 'is Nette\Forms\Controls\TextInput');
		Assert::equal("url", $item->control->type, "url type");
		Assert::true($this->isRuleExist($item, Form::URL), "Form::URL after Form::FILLED");
	}

	public function testNumber()
	{
		$item = $this->form->addNumber('number', "Number", 2, 0, 20);

		Assert::true($item instanceof \Nette\Forms\Controls\TextInput, 'is Nette\Forms\Controls\TextInput');
		Assert::equal("number", $item->control->type, "number type");
		Assert::equal(0, $item->control->min, "min");
		Assert::equal(20, $item->control->max, "max");
		Assert::equal(2, $item->control->step, "step");
		Assert::true($this->isRuleExist($item, Form::NUMERIC), "Form::NUMERIC after Form::FILLED");
		Assert::true($this->isRuleExist($item, Form::RANGE), "Form::RANGE after Form::FILLED");
	}

	public function testRange()
	{
		$item = $this->form->addRange('range', "Range", 2, 0, 20);

		Assert::true($item instanceof \Nette\Forms\Controls\TextInput, 'is Nette\Forms\Controls\TextInput');
		Assert::equal("range", $item->control->type, "range type");
		Assert::equal(0, $item->control->min, "min");
		Assert::equal(20, $item->control->max, "max");
		Assert::equal(2, $item->control->step, "step");
		Assert::true($this->isRuleExist($item, Form::NUMERIC), "Form::NUMERIC after Form::FILLED");
		Assert::true($this->isRuleExist($item, Form::RANGE), "Form::RANGE after Form::FILLED");
	}

	public function testDate()
	{
		$item = $this->form->addDate('date', "Date");

		Assert::true($item instanceof \Nella\Forms\Controls\Date, 'is Nella\Forms\Controls\Date');
		Assert::equal("date", $item->control->type, "date type");
	}

	public function testDateTime()
	{
		$item = $this->form->addDateTime('datetime', "Datetime");

		Assert::true($item instanceof \Nella\Forms\Controls\DateTime, 'is Nella\Forms\Controls\DateTime');
		Assert::equal("datetime", $item->control->type, "datetime type");
	}

	public function testTime()
	{
		$item = $this->form->addTime('time', "Time");

		Assert::true($item instanceof \Nella\Forms\Controls\Time, 'is Nella\Forms\Controls\Time');
		Assert::equal("time", $item->control->type, "time type");
	}

	public function testSearch()
	{
		$item = $this->form->addSearch('search', "Search");

		Assert::true($item instanceof \Nette\Forms\Controls\TextInput, 'is Nette\Forms\Controls\TextInput');
		Assert::equal("search", $item->control->type, "search type");
	}

	public function testEditor()
	{
		$item = $this->form->addEditor('editor', "Editor");

		Assert::true($item instanceof \Nette\Forms\Controls\TextArea, 'is Nette\Forms\Controls\TextArea');
		Assert::true((bool) $item->control->{'data-nella-editor'}, "editor data attribute");
	}

	public function testDynamic()
	{
		$item = $this->form->addDynamic('dyn', function($container) {});

		Assert::true($item instanceof \Nella\Forms\Multipler, 'is Nella\Forms\Multipler');
	}

	public function testTags()
	{
		$item = $this->form->addTags('tags', "Tags");

		Assert::true($item instanceof \Nella\Forms\Controls\TagsInput, 'is Nella\Forms\Controls\TagsInput');
	}
}

id(new FormTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
