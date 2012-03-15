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
 */
abstract class control extends \Nette\Application\UI\Control
{
	/** @var \Nella\Templating\ITemplateFilesFormatter */
	private $templateFilesFormatter;

	/**
	 * @param Presenter
	 */
	protected function attached($presenter)
	{
		if ($presenter instanceof \Nette\Application\UI\Presenter) {
			if (!$presenter instanceof Presenter) {
				throw new \Nette\InvalidStateException(
					"Nella\\Application\\UI\\Control supports only Nella\\Application\\UI\\Presenter '".get_class($presenter)."' given"
				);
			}
			$this->templateFilesFormatter = $presenter->getTemplateFilesFormatter();
		}
		parent::attached($presenter);
	}

	/**
	 * @param string
	 * @return array
	 */
	private function methodToNameAndView($method)
	{
		if (strpos($method, "::") === FALSE) {
			$method = get_called_class() . "::" . $method;
		}
		list($class, $method) = explode("::render", $method);

		$class = substr($class, strpos($class, '\\') + 1);
		if (\Nette\Utils\Strings::endsWith($class, 'Control')) {
			$class = substr($class, 0, -7);
		}
		list($name, $view) = str_split($class, strrpos($class, '\\') + 1);

		$name = substr(preg_replace('~(\w+)(?:Module)?\\\\~U', '\1:', $name), 0, -1);
		if ($method) {
			$view .= "." . lcfirst($method);
		}

		return array($name, $view);
	}

	/**
	 * Formats component template files
	 *
	 * @param string
	 * @return array
	 */
	public function formatTemplateFiles($method)
	{
		if (!$this->templateFilesFormatter) {
			throw new \Nette\InvalidStateException("Control does not attached to presenter");
		}

		list($name, $view) = $this->methodToNameAndView($method);
		return $this->templateFilesFormatter->formatTemplateFiles($name, $view);
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
}
