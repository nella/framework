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
 * Application base presenter
 *
 * @author	Patrik Votoček
 *
 * @property-read \Nette\DI\IContext $context
 */
abstract class Presenter extends \Nette\Application\UI\Presenter
{
	protected function beforeRender()
	{
		$this->template->productionMode = $this->getContext()->params['productionMode'];
	}

	/**
	 * Saves the message to template, that can be displayed after redirect
	 *
	 * @param  string
	 * @param  string
	 * @return stdClass
	 */
	public function flashMessage($message, $type = 'info')
	{
		$types = $this->getContext()->params['flashes'];
		if (isset($types[$type])) {
			$type = $types[$type];
		}

		return parent::flashMessage($message, $type);
	}

	/**
	 * @param string	module name
	 * @param string
	 * @param string
	 * @param user \Nette\Security\IIdentity
	 */
	public function logAction($module, $action = self::OTHER, $message = "", \Nette\Security\IIdentity $user = NULL)
	{
		return $this->getContext()->actionLogger->logAction($module, $action, $message, $user);
	}

	/**
	 * Determines whether it links to the current page
	 *
	 * @param  string   destination in format "[[module:]presenter:]action" or "signal!" or "this"
	 * @param  array|mixed
	 * @return bool
	 * @throws \Nette\Application\InvalidLinkException
	 */
	public function isLinkCurrent($destination = NULL, $args = array())
	{
		if (is_array($destination)) {
			foreach ($destination as $link) {
				if (parent::isLinkCurrent($link[0], isset($link[1]) ? $link[1] : array())) {
					return TRUE;
				}
			}
			return FALSE;
		} elseif ($destination !== NULL) {
			if (!is_array($args)) {
				$args = func_get_args();
				array_shift($args);
			}
			return parent::isLinkCurrent($destination, $args);
		}
		return FALSE;
	}

	/**
	 * Descendant can override this method to customize template compile-time filters
	 *
	 * @param \Nette\Templating\Template
	 */
	public function templatePrepareFilters($template)
	{
		$template->registerFilter($this->getContext()->latteEngine);
	}

	/**
	 * Formats layout template file names.
	 *
	 * @return array
	 */
	public function formatLayoutTemplateFiles()
	{
		$presenter = $this->getName();
		$layout = $this->layout ? $this->layout : 'layout';
		$path = str_replace(":", "/", substr($presenter, 0, strrpos($presenter, ":")));
		$subPath = substr($presenter, strrpos($presenter, ":") !== FALSE ? strrpos($presenter, ":") + 1 : 0);
		if ($path) {
			$path .= "/";
		}

		$generator = function ($dir) use ($presenter, $path, $subPath, $layout) {
			$files = array();
			// classic modules templates
			if (strpos($presenter, ':') !== FALSE) {
				$files[] = $dir . "/" .$path . "templates/$subPath/@$layout.latte";
				$files[] = $dir . "/" .$path . "templates/$subPath.@$layout.latte";
				$files[] = $dir . "/" .$path . "templates/@$layout.latte";
			}
			// classic templates
			$files[] = $dir . "/templates/" .$path . "$subPath/@$layout.latte";
			$files[] = $dir . "/templates/" .$path . "$subPath.@$layout.latte";
			$files[] = $dir . "/templates/" .$path . "@$layout.latte";

			$file = $dir . "/templates/@$layout.latte";
			if (!in_array($file, $files)) {
				$files[] = $file;
			}

			return $files;
		};

		$files = array();
		foreach ($this->getContext()->params['templates'] as $dir) {
			$files = array_merge($files, $generator($dir));
		}

		if ($this->getContext()->hasService('debugPanel')) {
			$this->getContext()->debugPanel->addTemplates(get_called_class(), $files);
		}

		return $files;
	}

	/**
	 * Formats view template file names.
	 *
	 * @return array
	 */
	public function formatTemplateFiles()
	{
		$presenter = $this->getName();
		$view = $this->view;
		$path = str_replace(":", "/", substr($presenter, 0, strrpos($presenter, ":")));
		$subPath = substr($presenter, strrpos($presenter, ":") !== FALSE ? strrpos($presenter, ":") + 1 : 0);
		if ($path) {
			$path .= "/";
		}

		$generator = function ($dir) use ($presenter, $path, $subPath, $view) {
			$files = array();
			// classic modules templates
			if (strpos($presenter, ':') !== FALSE) {
				$files[] = $dir . "/" .$path . "templates/$subPath/$view.latte";
				$files[] = $dir . "/" .$path . "templates/$subPath.$view.latte";
				$files[] = $dir . "/" .$path . "templates/$subPath/@global.latte";
				$files[] = $dir . "/" .$path . "templates/@global.latte";

			}
			// classic templates
			$files[] = $dir . "/templates/" .$path . "$subPath/$view.latte";
			$files[] = $dir . "/templates/" .$path . "$subPath.$view.latte";
			$files[] = $dir . "/templates/" .$path . "$subPath/@global.latte";
			$files[] = $dir . "/templates/" .$path . "@global.latte";

			$file = $dir . "/templates/@global.latte";
			if (!in_array($file, $files)) {
				$files[] = $file;
			}

			return $files;
		};

		$files = array();
		foreach ($this->getContext()->params['templates'] as $dir) {
			$files = array_merge($files, $generator($dir));
		}

		if ($this->getContext()->hasService('debugPanel')) {
			$this->getContext()->debugPanel->addTemplates(get_called_class(), $files);
		}

		return $files;
	}

	/**
	 * Component factory. Delegates the creation of components to a createComponent<Name> method.
	 * @param  string
	 * @return \Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		$container = $this->getContext()->components;
		if ($container->hasComponent($name)) {
			return $container->getComponent($name, $this);
		}

		return parent::createComponent($name);
	}

	/**
	 * @return \Nella\Doctrine\Container
	 */
	public function getDoctrineContainer()
	{
		return $this->getContext()->doctrineContainer;
	}
}
