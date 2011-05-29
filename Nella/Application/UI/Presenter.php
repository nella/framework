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
		foreach ($this->getContext()->getParam('templates') as $dir) {
			$files = array_merge($files, $generator($dir));
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
		foreach ($this->getContext()->getParam('templates') as $dir) {
			$files = array_merge($files, $generator($dir));
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
		$container = $this->getContext()->getService('components');
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
		return $this->getContext()->getService('doctrineContainer');
	}
}
