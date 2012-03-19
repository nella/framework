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
 * Template form
 *
 * @author	Patrik Votoček
 *
 * @property-read \Nette\Templating\ITemplate $template
 */
class Form extends \Nella\NetteAddons\Forms\Form
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
					"Nella\\Application\\UI\\Control supports only Nella\\Application\\UI\\Presenter '".get_class($presenter)."' given"
				);
			}
			$this->templateFilesFormatter = $presenter->getTemplateFilesFormatter();
		}
		parent::attached($presenter);
	}

	/**
	 * @return array
	 */
	private function getNameAndView()
	{
		$class = get_called_class();
		$class = substr($class, strpos($class, '\\') + 1);
		if (\Nette\Utils\Strings::endsWith($class, 'Control')) {
			$class = substr($class, 0, -7);
		}
		list($name, $view) = str_split($class, strrpos($class, '\\') + 1);

		$name = substr(preg_replace('~(\w+)(?:Module)?\\\\~U', '\1:', $name), 0, -1);

		return array($name, $view);
	}

	/**
	 * Formats component template files
	 *
	 * @return array
	 */
	public function formatTemplateFiles()
	{
		if (!$this->templateFilesFormatter) {
			throw new \Nette\InvalidStateException("Control does not attached to presenter");
		}

		list($name, $view) = $this->getNameAndView();
		return $this->templateFilesFormatter->formatTemplateFiles($name, $view);
	}

	/**
	 * Format component template file
	 *
	 * @return string
	 * @throws \Nette\InvalidStateException
	 */
	protected function formatTemplateFile()
	{
		$files = $this->formatTemplateFiles();
		foreach ($files as $file) {
			if (file_exists($file)) {
				return $file;
			}
		}

		throw new \Nette\InvalidStateException("No template files found");
	}

	protected function beforeRender()
	{
		$this->getTempate()->setFile($this->formatTemplateFile($this->getNameAndView()));
	}

	final public function render()
	{
		if (func_num_args() < 1) {
			try {
				$this->beforeRender();
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
