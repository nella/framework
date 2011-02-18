<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
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
	/** @var array */
	public static $templateDirs = array(
		APP_DIR, 
		NELLA_FRAMEWORK_DIR, 
	);

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
				$files[] = $dir . "/" .$path . "Templates/$subPath/@$layout.latte";
				$files[] = $dir . "/" .$path . "Templates/$subPath.@$layout.latte";
				$files[] = $dir . "/" .$path . "Templates/@$layout.latte";
			}
			// clasic templates
			$files[] = $dir . "/Templates/" .$path . "$subPath/@$layout.latte";
			$files[] = $dir . "/Templates/" .$path . "$subPath.@$layout.latte";
			$files[] = $dir . "/Templates/" .$path . "@$layout.latte";
			
			$file = $dir . "/Templates/@$layout.latte";
			if (!in_array($file, $files)) {
				$files[] = $file;
			}
			
			return $files;
		};
		
		$files = array();
		foreach (static::$templateDirs as $dir) {
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
				$files[] = $dir . "/" .$path . "Templates/$subPath/$view.latte";
				$files[] = $dir . "/" .$path . "Templates/$subPath.$view.latte";
				$files[] = $dir . "/" .$path . "Templates/$subPath/@global.latte";
				$files[] = $dir . "/" .$path . "Templates/@global.latte";

			}
			// clasic templates
			$files[] = $dir . "/Templates/" .$path . "$subPath/$view.latte";
			$files[] = $dir . "/Templates/" .$path . "$subPath.$view.latte";
			$files[] = $dir . "/Templates/" .$path . "$subPath/@global.latte";
			$files[] = $dir . "/Templates/" .$path . "@global.latte";
			
			$file = $dir . "/Templates/@global.latte";
			if (!in_array($file, $files)) {
				$files[] = $file;
			}
			
			return $files;
		};
		
		$files = array();
		foreach (static::$templateDirs as $dir) {
			$files = array_merge($files, $generator($dir));
		}

		return $files;
	}
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->getContext()->getService('Doctrine\ORM\EntityManager');
	}
}
