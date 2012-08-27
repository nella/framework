<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Application\UI;

/**
 * Template form
 *
 * @author	Patrik Votoček
 *
 * @property-read \Nette\Templating\ITemplate $template
 */
abstract class Form extends \Nella\NetteAddons\Forms\Form
{
	const USE_DEFAULT_RENDERER = 1,
		THROW_EXCEPTION = 2;

	/** @var int */
	public $invalidTemplateMode = self::USE_DEFAULT_RENDERER;
	/** @var \Nella\Templating\ITemplateFilesFormatter */
	private $templateFilesFormatter;
	/** @var \Nette\Templating\ITemplate */
	private $template;

	/**
	 * @return \Nette\Templating\ITemplate
	 */
	public function getTempate()
	{
		if (!$this->template) {
			$this->template = $this->createTemplate();
		}

		return $this->template;
	}

	/**
	 * @param  string|NULL
	 * @return \Nette\Templating\ITemplate
	 */
	protected function createTemplate($class = NULL)
	{
		$template = $class ? new $class : new \Nette\Templating\FileTemplate;
		$presenter = $this->getPresenter(FALSE);
		$template->onPrepareFilters[] = callback($presenter, 'templatePrepareFilters');
		$template->registerHelperLoader('Nette\Templating\Helpers::loader');

		// default parameters
		$template->name = $template->_name = $this->getName();
		$template->control = $template->_control = $this->getParent();
		$template->presenter = $template->_presenter = $presenter;
		if ($presenter instanceof \Nette\Application\UI\Presenter) {
			$context = $presenter->getContext();
			$template->setCacheStorage($context->nette->templateCacheStorage);
			$template->user = $presenter->getUser();
			$template->netteHttpResponse = $context->httpResponse;
			$template->netteCacheStorage = $context->getByType('Nette\Caching\IStorage');
			$template->baseUri = $template->baseUrl = rtrim($context->httpRequest->getUrl()->getBaseUrl(), '/');
			$template->basePath = preg_replace('#https?://[^/]+#A', '', $template->baseUrl);

			// flash message
			if ($presenter->hasFlashSession()) {
				$id = $template->control->getParameterId('flash');
				$template->flashes = $presenter->getFlashSession()->$id;
			}
		}
		if (!isset($template->flashes) || !is_array($template->flashes)) {
			$template->flashes = array();
		}

		return $template;
	}

	/**
	 * @param Presenter
	 */
	protected function attached($presenter)
	{
		if ($presenter instanceof \Nette\Application\UI\Presenter) {
			if (!$presenter instanceof Presenter) {
				throw new \Nette\InvalidStateException(
					'Nella\Application\UI\Form supports only Nella\Application\UI\Presenter'
					 . " '".get_class($presenter)."' given"
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
			throw new \Nette\InvalidStateException('Control does not attached to presenter');
		}

		$view = $this->methodToView($method);
		return $this->templateFilesFormatter->formatTemplateFiles(get_called_class(), $view);
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

		throw new \Nette\InvalidStateException('No template files found');
	}

	/**
	 * @param string
	 */
	protected function beforeRender($method)
	{
		$this->getTempate()->setFile($this->formatTemplateFile($method));
	}

	final public function render()
	{
		if (func_num_args() < 1) {
			try {
				$this->beforeRender(__METHOD__);
				$this->getTempate()->render();
				return;
			} catch (\Nette\InvalidStateException $e) {
				if ($this->invalidTemplateMode == static::THROW_EXCEPTION) {
					throw $e;
				}
			}
		}

		$args = func_get_args();
		array_unshift($args, $this);
		echo call_user_func_array(array($this->getRenderer(), 'render'), $args);
	}
}

