<?php
/**
 * Test: Nella\Forms\Multipler
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
	Nella\Forms\Form,
	Nella\Forms\Multipler;

require_once __DIR__ . '/../../bootstrap.php';
require_once MOCKS_DIR . '/Application/UI/ControlPresenter.php';

class MultiplerTest extends \Tester\TestCase
{
	/** @var \Nella\Forms\Multipler */
	protected $multipler;

	public function setUp()
	{
		parent::setUp();
		$form = new \Nella\Forms\Form;

		$form['test'] = $this->multipler = new Multipler(function($container) {
			$container->addText('foo', "Foo");
			$container->addRemoveContainerButton("Remove container");
		});
	}

	/**
	 * @param \Nette\Application\UI\Form
	 * @param array
	 */
	protected function runForm(\Nette\Application\UI\Form $form, array $data)
	{
		$request = new \Nette\Application\Request('Default', 'POST', array('do' => 'test-submit'), $data);

		$context = new \Nette\DI\Container;
		$context->parameters['productionMode'] = FALSE;

		$context->addService('nette', new \Nette\DI\NestedAccessor($context, 'nette'));
		$context->addService('nette.templateCacheStorage', new \Nette\Caching\Storages\DevNullStorage);
		$context->addService('nette.httpRequest', new \Nette\Http\Request(new \Nette\Http\UrlScript));
		$context->classes['nette\security\user'] = 'nette';
		$context->classes['nette\http\iresponse'] = 'nette';
		$context->classes['nette\caching\istorage'] = 'nette.templateCacheStorage';
		$context->classes['nette\http\irequest'] = 'nette.httpRequest';

		$presenter = new \Nella\Mocks\Application\UI\ControlPresenter($context, $form);

		$presenter->run($request);
	}

	public function testInstance()
	{
		Assert::true($this->multipler instanceof \Nella\Forms\Container);
	}

	public function testOneContainer()
	{
		$cont = $this->multipler[0];
		Assert::true($cont instanceof \Nella\Forms\MultiplerContainer, 'is Nella\Forms\MultiplerContainer');
		Assert::true($cont['foo'] instanceof \Nette\Forms\IControl, 'has Nette\Forms\IControl');
	}

	public function testCreateOne()
	{
		$components = $this->multipler->getComponents(FALSE, 'Nette\Forms\Container');
		Assert::equal(0, count($components), "test defaults");

		$this->multipler->createOne();

		$components = $this->multipler->getComponents(FALSE, 'Nette\Forms\Container');
		Assert::equal(1, count($components), "test defaults");
	}

	public function testAttachInvalidForm()
	{
		Assert::throws(function() {
			$form = new \Nette\Forms\Form;
			$form['dyn'] = new \Nella\Forms\Multipler(function($container) {
				$container->addText('foo', "Foo");
			});
		}, 'Nette\InvalidStateException');
	}

	public function dataValues()
	{
		return array(
			array(
				array(
					'1' => array('foo' => "Lorem")
				),
			),
			array(
				array(
					'id1' => array('foo' => "ipsum"),
					'id3' => array('foo' => "dolor")
				),
			),
		);
	}

	/**
	 * @dataProvider dataValues
	 */
	public function testSetValues($data)
	{
		$this->multipler->setValues($data);

		$ids = array_keys($data);
		foreach ($ids as $id) {
			Assert::true(isset($this->multipler[$id]), "is container '$id' exists");

			Assert::equal(
				$data[$id]['foo'], $this->multipler[$id]['foo']->value,
				"is foo value '{$data[$id]['foo']}' in container '$id'"
			);
		}
	}

	/**
	 * @dataProvider dataValues
	 */
	public function testGetValues($data)
	{
		$this->multipler->setValues($data);

		Assert::equal($data, $this->multipler->getValues(TRUE));
	}

	public function testCreateExistingContainer()
	{
		$multipler = $this->multipler;
		Assert::throws(function() use($multipler) {
			$multipler->createOne(1);
		}, 'Nette\InvalidArgumentException');
	}

	public function testRemoveContainer()
	{
		$container = $this->multipler[1];

		$containers = $this->multipler->getComponents(FALSE, 'Nella\Forms\MultiplerContainer');
		Assert::equal(1, count($containers), "is container exist");

		$this->multipler->remove($container);

		$containers = $this->multipler->getComponents(FALSE, 'Nella\Forms\MultiplerContainer');
		Assert::equal(0, count($containers), "is container removed");
	}

	/**
	 * @expectedException
	 */
	public function testRemoveNonExistContainer()
	{
		$multipler = $this->multipler;
		Assert::throws(function() use($multipler) {
			$container = new \Nella\Forms\MultiplerContainer(NULL, 'test');
			$multipler->remove($container);
		}, 'Nette\InvalidStateException');
	}

	public function testAddContainerButton()
	{
		$this->multipler->addAddContainerButton("Add container");
		Assert::true(isset($this->multipler[Multipler::ADD_CONTAINER_BUTTON_ID]), "is add button exist");
		Assert::true(
			$this->multipler[Multipler::ADD_CONTAINER_BUTTON_ID] instanceof \Nette\Forms\Controls\SubmitButton,
			"is add button valid type"
		);
	}

	public function testAddContainerHttp()
	{
		$this->multipler->addAddContainerButton("Add container");

		$this->runForm($this->multipler->form, array(
			'test' => array(
				Multipler::ADD_CONTAINER_BUTTON_ID => "Add container",
				Multipler::CONTAINERS_KEYS_ID => "1",
			)
		));

		$containers = $this->multipler->getComponents(FALSE, 'Nella\Forms\MultiplerContainer');
		Assert::equal(2, count($containers), "now is 2 containers");
	}

	public function testRemoveContainerHttp()
	{
		$this->multipler->addAddContainerButton("Add container");

		$this->runForm($this->multipler->form, array(
			'test' => array(
				Multipler::CONTAINERS_KEYS_ID => "1",
				1 => array(
					\Nella\Forms\MultiplerContainer::REMOVE_CONTAINER_BUTTON_ID => "Remove container"
				)
			)
		));

		$containers = $this->multipler->getComponents(FALSE, 'Nella\Forms\MultiplerContainer');
		Assert::equal(0, count($containers), "now is 0 containers");
	}
}

id(new MultiplerTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
