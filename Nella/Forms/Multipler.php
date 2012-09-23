<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Forms;

use Nette\Forms\Form as NForm,
	Nette\Application\UI\Form as UIForm,
	Nette\Application\UI\Presenter,
	Nette\Forms\Controls\SubmitButton,
	Nette\Utils\Arrays,
	Nette\Reflection\ClassType;

/**
 * Form multipler container
 *
 * @author	Patrik Votoček
 */
class Multipler extends Container
{
	const CONTAINERS_KEYS_SEPARATOR = ';',
		CONTAINERS_KEYS_ID = '__subcontainers',
		ADD_CONTAINER_BUTTON_ID = '__addcontainer';

	/** @var callable */
	private $factory;
	/** @var int */
	private $i = 1;

	/**
	 * @param callable
	 * @param int
	 */
	public function __construct($factory, $createDefault = 1)
	{
		parent::__construct();

		$this->factory = callback($factory);

		$this->addHidden(self::CONTAINERS_KEYS_ID);

		$this->setIds($createDefault > 0 ? range(1, $createDefault) : NULL);

		$this->monitor('Nette\Application\UI\Presenter');
		$this->monitor('Nette\Forms\Form');
	}

	/**
	 * @param \Nette\ComponentModel\IContainer
	 */
	protected function attached($obj)
	{
		parent::attached($obj);

		if ($obj instanceof NForm) {
			if (!$obj instanceof UIForm) {
				throw new \Nette\InvalidStateException('Form multipler support only Nette\Application\UI\Form');
			}
		}

		if (!$obj instanceof Presenter) {
			return;
		}

		$this->init();
	}

	protected function init()
	{
		$data = $this->receiveHttpData();
		if (!empty($data)) {
			if (isset($data[self::CONTAINERS_KEYS_ID])) {
				$this->setIds($data[self::CONTAINERS_KEYS_ID]);
			}
		}

		foreach ($this->getIds() as $name) {
			$this->createOne($name, TRUE);
		}
	}

	/**
	 * @param string
	 * @return MultiplerContainer
	 */
	protected function createComponent($name)
	{
		$container = $this->addContainer($name);

		$this->factory->invoke($container);

		return $container;
	}

	/**
	 * Fill-in with values
	 *
	 * @param  array|Traversable  values used to fill the form
	 * @param  bool     erase other controls?
	 * @return Multipler
	 */
	public function setValues($values, $erase = FALSE)
	{
		foreach ($values as $key => $value) {
			if (is_array($value)) { // TODO: better subcontainer detection
				$this->getComponent($key);
			}
		}

		return parent::setValues($values, $erase);
	}

	/**
	 * @param string|int
	 * @return MultiplerContainer
	 */
	public function createOne($name = NULL, $registered = FALSE)
	{
		if (empty($name)) {
			$name = $this->i;
		}

		if (!$registered && in_array($name, $this->getIds())) {
			throw new \Nette\InvalidArgumentException("Container with name '$name' already exists.");
		}

		if (!$registered) {
			$this->addId($name);
		}

		return $this[$name];
	}

	/**
	 * @param MultiplerContainer
	 * @param bool
	 * @throws \Nette\InvalidStateException
	 */
	public function remove(MultiplerContainer $container, $cleanUpGroups = FALSE)
	{
		$name = $container->getName();
		if (!in_array($name, $this->getIds())) {
			throw new \Nette\InvalidStateException("Sub-container with name '$name' does not exist");
		}

		if ($cleanUpGroups) {
			$this->removeGroups($container);
		}
		$this->removeComponent($container);
		$this->removeId($name);
	}

	/**
	 * Adds naming container to the form
	 *
	 * @param string    name
	 * @return MultiplerContainer
	 */
	public function addContainer($name)
	{
		$container = new MultiplerContainer;
		$container->currentGroup = $this->currentGroup;
		$this->addComponent($container, $name, self::CONTAINERS_KEYS_ID);
		return $container;
	}

	/**
	 * @param string
	 * @return \Nette\Forms\Controls\SubmitButton
	 */
	public function addAddContainerButton($caption = NULL)
	{
		$button = $this->addSubmit(self::ADD_CONTAINER_BUTTON_ID, $caption)->setValidationScope(FALSE);
		$button->onClick[] = function (SubmitButton $button) {
			$button->getParent()->createOne();
		};

		return $button;
	}

	/**
	 * Returns the values submitted by the form.
	 *
	 * @param  bool  return values as an array?
	 * @return \Nette\ArrayHash|array
	 */
	public function getValues($asArray = FALSE)
	{
		$values = parent::getValues($asArray);
		unset($values[self::CONTAINERS_KEYS_ID]);
		return $values;
	}

	/**
	 * @param bool
	 * @return array|string
	 */
	protected function getFullName($asChain = FALSE)
	{
		$chain = array();
		$parent = $this;

		while (!$parent instanceof Form) {
			$chain[] = $parent->getName();
			$parent = $parent->getParent();
		}
		$chain[] = $parent->getName();

		$chain = array_reverse($chain);

		if ($asChain) {
			return $chain;
		}

		return implode('-', $chain);
	}

	/**
	 * Internal: receives submitted HTTP data.
	 *
	 * @return array
	 */
	protected function receiveHttpData()
	{
		$form = $this->getForm();
		$presenter = $form->getPresenter();

		if (!$form->isSubmitted()) {
			return;
		}

		$isPost = $form->getMethod() === Form::POST;
		$request = $presenter->getRequest();
		if ($request->isMethod('forward') || $request->isMethod('post') !== $isPost) {
			return;
		}

		if ($isPost) {
			$data =  Arrays::mergeTree($request->getPost(), $request->getFiles());
		} else {
			$data = $request->getParameters();
		}

		// get data only for this container
		$chain = array_slice($this->getFullName(TRUE), 1);
		while ($chain ) {
			$data = &$data[array_pop($chain)];
		}

		return $data;
	}

	/**
	 * @param array
	 * @return string
	 */
	private function idsToString(array $ids)
	{
		if (empty($ids)) {
			return '';
		}

		return implode(self::CONTAINERS_KEYS_SEPARATOR, $ids);
	}

	/**
	 * @param string
	 * @return array
	 */
	private function idsToArray($ids)
	{
		if (empty($ids) || $ids === self::CONTAINERS_KEYS_SEPARATOR) {
			return array();
		}

		return explode(self::CONTAINERS_KEYS_SEPARATOR, $ids);
	}

	/**
	 * @return array
	 */
	private function getIds()
	{
		return $this->idsToArray($this[self::CONTAINERS_KEYS_ID]->getValue());
	}

	/**
	 * @param string|array|NULL
	 */
	private function setIds($ids)
	{
		if (empty($ids)) {
			$ids = NULL;
		} elseif (!is_string($ids)) {
			$ids = $this->idsToString($ids);
		}

		$this[self::CONTAINERS_KEYS_ID]->setValue($ids);

		if (!empty($ids)) {
			$this->i = max($this->idsToArray($ids)) + 1;
		} else {
			$this->i = 1;
		}
	}

	/**
	 * @param string
	 */
	private function addId($id)
	{
		$ids = $this->getIds();
		$ids[] = $id;
		$this->setIds($ids);
	}

	/**
	 * @param string
	 */
	private function removeId($id)
	{
		$ids = $this->getIds();
		$key = array_search($id, $ids);
		unset($ids[$key]);
		$this->setIds($ids);
	}

	/** ------------------------------------------------ 3rd -------------------------------------------------- */

	/**
	 * @author	Filip Procházka <filip.prochazka@kdyby.org>
	 * @author	Jan Tvrdík
	 *
	 * @param MultiplerContainer
	 * @throws \Nette\InvalidArgumentException
	 */
	public function removeGroups(MultiplerContainer $container)
	{
		// get components
		$components = $container->getComponents(TRUE);

		// reflection is required to hack form groups
		$groupRefl = ClassType::from('Nette\Forms\ControlGroup');
		$controlsProperty = $groupRefl->getProperty('controls');
		$controlsProperty->setAccessible(TRUE);

		// walk groups and clean then from removed components
		$affected = array();
		foreach ($this->getForm()->getGroups() as $group) {
			$groupControls = $controlsProperty->getValue($group);

			foreach ($components as $control) {
				if ($groupControls->contains($control)) {
					$groupControls->detach($control);

					if (!in_array($group, $affected, TRUE)) {
						$affected[] = $group;
					}
				}
			}
		}

		// remove affected & empty groups
		if ($affected) {
			foreach ($this->getForm()->getComponents(FALSE, 'Nette\Forms\Container') as $container) {
				if ($index = array_search($container->currentGroup, $affected, TRUE)) {
					unset($affected[$index]);
				}
			}

			foreach ($affected as $group) {
				if (!$group->getControls() && in_array($group, $this->getForm()->getGroups(), TRUE)) {
					$this->getForm()->removeGroup($group);
				}
			}
		}
	}
}

