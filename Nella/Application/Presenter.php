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
	/** #@+ Base presenter flash messages class */
	const FLASH_SUCCESS = "success";
	const FLASH_ERROR = "error";
	const FLASH_INFO = "info";
	const FLASH_WARNING = "warning";
	/** #@- */

	/**
	 * Formats layout template file names.
	 *
	 * @param string
	 * @param string
	 * @return array
	 */
	public function formatLayoutTemplateFiles($presenter, $layout)
	{
		$files = array();

		$path = str_replace(":", "/", substr($presenter, 0, strrpos($presenter, ":")));
		$subPath = substr($presenter, strrpos($presenter, ":") !== FALSE ? strrpos($presenter, ":") + 1 : 0);
		if ($path) {
			$path .= "/";
		}

		// App modules
		if (strpos($presenter, ':') !== FALSE) {
			$files[] = APP_DIR . "/" .$path . "Templates/$subPath/@$layout.latte";
			$files[] = APP_DIR . "/" .$path . "Templates/$subPath.@$layout.latte";
			$files[] = APP_DIR . "/" .$path . "Templates/@$layout.latte";
		}
		// App templates
		$files[] = APP_DIR . "/templates/" .$path . "$subPath/@$layout.latte";
		$files[] = APP_DIR . "/templates/" .$path . "$subPath.@$layout.latte";
		$files[] = APP_DIR . "/templates/" .$path . "@$layout.latte";
		if (strpos($presenter, ':') !== FALSE) {
			$files[] = APP_DIR . "/templates/@$layout.latte";
		}
		// Nella modules
		if (strpos($presenter, ':') !== FALSE) {
			$files[] = NELLA_DIR . $path . "Templates/$subPath/@$layout.latte";
			$files[] = NELLA_DIR . $path . "Templates/$subPath.@$layout.latte";
			$files[] = NELLA_DIR . $path . "Templates/@$layout.latte";
		}
		// Nella core
		$files[] = NELLA_DIR . "Templates/$path$subPath/@$layout.latte";
		$files[] = NELLA_DIR . "Templates/$path$subPath.@$layout.latte";
		$files[] = NELLA_DIR . "Templates/$path@$layout.latte";
		// Nella templates
		$files[] = NELLA_DIR . "/Templates/@$layout.latte";

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
		$files = array();

		$path = str_replace(":", "/", substr($presenter, 0, strrpos($presenter, ":")));
		$subPath = substr($presenter, strrpos($presenter, ":") !== FALSE ? strrpos($presenter, ":") + 1 : 0);
		if ($path) {
			$path .= "/";
		}

		// App modules
		if (strpos($presenter, ':') !== FALSE) {
			$files[] = APP_DIR . "/" .$path . "Templates/$subPath/$view.latte";
			$files[] = APP_DIR . "/" .$path . "Templates/$subPath.$view.latte";
			$files[] = APP_DIR . "/" .$path . "Templates/$subPath/@global.latte";
			$files[] = APP_DIR . "/" .$path . "Templates/@global.latte";

		}
		// App templates
		$files[] = APP_DIR . "/templates/" .$path . "$subPath/$view.latte";
		$files[] = APP_DIR . "/templates/" .$path . "$subPath.$view.latte";
		$files[] = APP_DIR . "/templates/" .$path . "$subPath/@global.latte";
		$files[] = APP_DIR . "/templates/" .$path . "@global.latte";
		if (strpos($presenter, ':') !== FALSE) {
			$files[] = APP_DIR . "/templates/@global.latte";
		}
		// Nella modules
		if (strpos($presenter, ':') !== FALSE) {
			$files[] = NELLA_DIR . $path . "Templates/$subPath/$view.latte";
			$files[] = NELLA_DIR . $path . "Templates/$subPath.$view.latte";
			$files[] = NELLA_DIR . $path . "Templates/$subPath/@global.latte";
			$files[] = NELLA_DIR . $path . "Templates/@global.latte";
		}
		// Nella templates
		$files[] = NELLA_DIR . "/Templates/$path$subPath/$view.latte";
		$files[] = NELLA_DIR . "/Templates/$path$subPath.$view.latte";
		$files[] = NELLA_DIR . "/Templates/$path$subPath/@global.latte";
		if (strpos($presenter, ':') !== FALSE) {
			$files[] = NELLA_DIR . "/Templates/$path@global.latte";
		}
		// Nella templates
		$files[] = NELLA_DIR . "/Templates/@global.latte";

		return $files;
	}

	/**
	 * @return Nella\IContext
	 */
	public function getContext()
	{
		return $this->getApplication()->getContext();
	}
}
