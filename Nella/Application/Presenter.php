<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Application;

/**
 * Application base presenter
 *
 * @author	Patrik Votoček
 *
 * @property-read Nette\IContext $context
 */
abstract class Presenter extends \Nette\Application\Presenter
{
	/**
	 * Formats layout template file names.
	 *
	 * @param string
	 * @param string
	 * @return array
	 */
	public function formatLayoutTemplateFiles($presenter, $layout)
	{
		$path = str_replace(":", "/", substr($presenter, 0, strrpos($presenter, ":")));
		$subPath = substr($presenter, strrpos($presenter, ":") !== FALSE ? strrpos($presenter, ":") + 1 : 0);
		if ($path) {
			$path .= "/";
		}

		$generator = function ($dir) use ($presenter, $path, $subPath, $layout) {
			$files = array();
			// clasic modules templates
			if (strpos($presenter, ':') !== FALSE) {
				$files[] = $dir . "/" .$path . "templates/$subPath/@$layout.latte";
				$files[] = $dir . "/" .$path . "templates/$subPath.@$layout.latte";
				$files[] = $dir . "/" .$path . "templates/@$layout.latte";
			}
			// clasic templates
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
		foreach ($this->getContext()->getService('Nella\Registry\TemplateDirs') as $dir) {
			$files = array_merge($files, $generator($dir));
		}

		return $files;
	}

	/**
	 * Formats view template file names.
	 *
	 * @param string
	 * @param string
	 * @return array
	 */
	public function formatTemplateFiles($presenter, $view)
	{
		$path = str_replace(":", "/", substr($presenter, 0, strrpos($presenter, ":")));
		$subPath = substr($presenter, strrpos($presenter, ":") !== FALSE ? strrpos($presenter, ":") + 1 : 0);
		if ($path) {
			$path .= "/";
		}

		$generator = function ($dir) use ($presenter, $path, $subPath, $view) {
			$files = array();
			// clasic modules templates
			if (strpos($presenter, ':') !== FALSE) {
				$files[] = $dir . "/" .$path . "templates/$subPath/$view.latte";
				$files[] = $dir . "/" .$path . "templates/$subPath.$view.latte";
				$files[] = $dir . "/" .$path . "templates/$subPath/@global.latte";
				$files[] = $dir . "/" .$path . "templates/@global.latte";

			}
			// clasic templates
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
		foreach ($this->getContext()->getService('Nella\Registry\TemplateDirs') as $dir) {
			$files = array_merge($files, $generator($dir));
		}

		return $files;
	}
	
	/**
	 * Component factory. Delegates the creation of components to a createComponent<Name> method.
	 * @param  string
	 * @return \Nette\IComponent
	 */
	protected function createComponent($name)
	{
		$globalComponentRegistry = $this->getContext()->getService('Nella\Registry\GlobalComponentFactories');
		if (isset($globalComponentRegistry[$name])) {
			return callback($globalComponentRegistry[$name])->invoke($this, $name);
		}
		
		return parent::createComponent($name);
	}
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->getContext()->getService('Doctrine\ORM\EntityManager');
	}
}
