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
	private function methodToView($method)
	{
		$pos = strpos($method, '::');
		if ($pos !== FALSE) {
			$method = substr($method, strpos($method, '::')+2);
		}
		return lcfirst(substr($method, 6));
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

		$view = $this->methodToView($method);
		return $this->templateFilesFormatter->formatComponentTemplateFiles(get_called_class(), $view);
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
