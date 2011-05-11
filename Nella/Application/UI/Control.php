<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Application\UI;

/**
 * Basic control implementation
 *
 * @author	Patrik Votoček
 */
abstract class Control extends \Nette\Application\UI\Control
{
	/**
	 * Descendant can override this method to customize template compile-time filters
	 * 
	 * @param \Nette\Templating\Template
	 */
	public function templatePrepareFilters($template)
	{
		$template->registerFilter(new \Nella\Latte\Engine($this->getContext()));
	}

	/**
	 * Formats component template files
	 *
	 * @param string
	 * @return array
	 */
	protected function formatTemplateFiles($method)
	{
		if (strpos($method, "::") !== FALSE) {
			list($class, $method) = explode("::", $method);
		}
		if (!isset($class)) {
			$class = get_called_class();
		}
		foreach ($this->getPresenter()->context->getParam('prefixies') as $prefix) {
			if (\Nette\Utils\Strings::startsWith($class, $prefix)) {
				$class = substr($class, strlen($prefix));
				break;
			}
		}
		$view = lcfirst(str_replace("render", NULL, $method));

		$generator = function ($dir) use ($class, $view) {
			if ($view) {
				return array(
					$dir . "/" . str_replace('\\', "/", $class) . ".$view.latte",
					$dir . "/templates/" . str_replace('\\', "/", $class) . ".$view.latte",
				);
			} else {
				return array(
					$dir . "/" . str_replace('\\', "/", $class) . ".latte",
					$dir . "/templates/" . str_replace('\\', "/", $class) . ".latte",
				);
			}
		};

		$files = array();
		foreach ($this->getPresenter()->context->getParam('templates') as $dir) {
			$files = array_merge($files, $generator($dir));
		}

		return $files;
	}

	/**
	 * Format component template file
	 *
	 * @param string
	 * @return string
	 * @throws \Nette\InvalidStateException
	 */
	protected function formatTemplateFile($method)
	{
		$files = $this->formatTemplateFiles($method);
		foreach ($files as $file) {
			if (file_exists($file)) {
				return $file;
			}
		}

		throw new \Nette\InvalidStateException("No template files found for method '$method'");
	}

	/**
	 * Render component template file
	 *
	 * @param string
	 * @return void
	 */
	protected function _render($method)
	{
		$this->template->setFile($this->formatTemplateFile($method));
		$this->template->render();
	}

	/**
	 * Component factory. Delegates the creation of components to a createComponent<Name> method.
	 * @param  string
	 * @return \Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		$container = $this->getContext()->getService('components');
		if ($container->hasComponent($name)) {
			return $container->getComponent($name, $this);
		}

		return parent::createComponent($name);
	}
	
	/**
	 * @return \Nette\DI\IContainer
	 */
	public function getContext()
	{
		return $this->getPresenter()->context;
	}

	/**
	 * @return \Nella\Doctrine\Container
	 */
	public function getDoctrineContainer()
	{
		return $this->getContext()->getService('doctrineContainer');
	}
}