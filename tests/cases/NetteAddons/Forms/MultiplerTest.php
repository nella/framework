<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\NetteAddons\Forms;

use Nella\NetteAddons\Forms\Multipler;

class MultiplerTest extends \Nella\Testing\FormTestCase
{
	/** @var \Nella\NetteAddons\Forms\Multipler */
	protected $multipler;

	public function setup()
	{
		$form = new \Nella\NetteAddons\Forms\Form;

		$form['test'] = $this->multipler = new Multipler(function($container) {
			$container->addText('foo', "Foo");
			$container->addRemoveContainerButton("Remove container");
		});
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Forms\Container', $this->multipler);
	}

	public function testOneContainer()
	{
		$cont = $this->multipler[0];
		$this->assertInstanceOf('Nella\NetteAddons\Forms\MultiplerContainer', $cont, 'is Nella\NetteAddons\Forms\MultiplerContainer');
		$this->assertInstanceOf('Nette\Forms\IControl', $cont['foo'], 'has Nette\Forms\IControl');
	}

	public function testCreateOne()
	{
		$components = $this->multipler->getComponents(FALSE, 'Nette\Forms\Container');
		$this->assertEquals(0, count($components), "test defaults");

		$this->multipler->createOne();

		$components = $this->multipler->getComponents(FALSE, 'Nette\Forms\Container');
		$this->assertEquals(1, count($components), "test defaults");
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testAttachInvalidForm()
	{
		$form = new \Nette\Forms\Form;
		$form['dyn'] = new \Nella\NetteAddons\Forms\Multipler(function($container) {
			$container->addText('foo', "Foo");
		});
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
			$this->assertTrue(isset($this->multipler[$id]), "is container '$id' exists");

			$this->assertEquals(
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

		$this->assertEquals(\Nette\ArrayHash::from($data), $this->multipler->values);
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testCreateExistingContainer()
	{
		$this->multipler->createOne(1);
	}

	public function testRemoveContainer()
	{
		$container = $this->multipler[1];

		$containers = $this->multipler->getComponents(FALSE, 'Nella\NetteAddons\Forms\MultiplerContainer');
		$this->assertEquals(1, count($containers), "is container exist");

		$this->multipler->remove($container);

		$containers = $this->multipler->getComponents(FALSE, 'Nella\NetteAddons\Forms\MultiplerContainer');
		$this->assertEquals(0, count($containers), "is container removed");
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testRemoveNonExistContainer()
	{
		$container = new \Nella\NetteAddons\Forms\MultiplerContainer(NULL, 'test');
		$this->multipler->remove($container);
	}

	public function testAddContainerButton()
	{
		$this->multipler->addAddContainerButton("Add container");
		$this->assertTrue(isset($this->multipler[Multipler::ADD_CONTAINER_BUTTON_ID]), "is add button exist");
		$this->assertInstanceOf(
			'Nette\Forms\Controls\SubmitButton', $this->multipler[Multipler::ADD_CONTAINER_BUTTON_ID],
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

		$containers = $this->multipler->getComponents(FALSE, 'Nella\NetteAddons\Forms\MultiplerContainer');
		$this->assertEquals(2, count($containers), "now is 2 containers");
	}

	public function testRemoveContainerHttp()
	{
		$this->multipler->addAddContainerButton("Add container");

		$this->runForm($this->multipler->form, array(
			'test' => array(
				Multipler::CONTAINERS_KEYS_ID => "1",
				1 => array(
					\Nella\NetteAddons\Forms\MultiplerContainer::REMOVE_CONTAINER_BUTTON_ID => "Remove container"
				)
			)
		));

		$containers = $this->multipler->getComponents(FALSE, 'Nella\NetteAddons\Forms\MultiplerContainer');
		$this->assertEquals(0, count($containers), "now is 0 containers");
	}
}