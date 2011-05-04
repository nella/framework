<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Localization;

/**
 * Translator panel
 *
 * @author	Patrik Votoček
 */
class Panel extends \Nella\FreezableObject implements \Nette\Diagnostics\IBarPanel
{
	const VERSION = "2.0", 
		XHR_HEADER = "X-Translation-Client";
	
	/** @var ITranslator */
	private $translator;
	/** @var Extractor */
	private $extractor;
	
	/**
	 * @param ITranslator
	 */
	public function __construct(ITranslator $translator)
	{
		$this->translator = $translator;
		$this->extractor = new Extractor($translator);
		$this->processRequests();
	}
	
	/**
	 * @return Extractor
	 */
	public function getExtractor()
	{
		return $this->extractor;
	}
	
	/**
	 * @param bool|array
	 * @return array
	 */
	protected function getDictionaries($data = FALSE)
	{
		$dictionaries = array();
		foreach ($this->translator->dictionaries as $name => $dictionary) {
			if ($data && isset($data[$dictionary->dir])) {
				$dictionary->pluralForm = $data[$dictionary->dir]['pluralForm'];
				foreach ($data[$dictionary->dir]['translations'] as $message => $translation) {
					$dictionary->addTranslation(
						$message, 
						$translation['translation'], 
						isset($translation['status']) ? $translation['status'] : NULL
					);
				}
				$dictionary->save();
			}

			$current = $dictionaries[$dictionary->dir] = array(
				'name' => $name, 
				'pluralForm' => $dictionary->pluralForm, 
				'translations' => $dictionary->iterator->getArrayCopy(), 
			);
		}
		return $dictionaries;
	}
	
	protected function processRequests()
	{
		$this->updating();
		
		$context = \Nette\Environment::getApplication()->context;
		$httpRequest = $context->getService('Nette\Web\IHttpRequest');
		$httpResponse = $context->getService('Nette\Web\IHttpResponse');
		
		if ($httpRequest->getHeader(self::XHR_HEADER, FALSE)) {
			$data = FALSE;
			switch($httpRequest->getPost('action')) {
				case 'get':
					break;
				case 'extract':
					$this->extractor->run();
					break;
				case 'save':
					$data = $httpRequest->getPost('data', "");
					break;
			}

			$dictionaries = $this->getDictionaries($data);
			
			$response = new \Nette\Application\Responses\JsonResponse(array(
				'status' => "OK", 
				'data' => $dictionaries, 
			));
			
			$response->send($httpRequest, $httpResponse);
			exit(255);
		}
		
		$this->freeze();
	}

	/**
	 * @return string
	 */
	public function getTab()
	{
		return '<span title="Translation panel">
		<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAnhJREFUeNqUkktoU0EUhv+5M7kxQVOk1U27UbBEqPQFATcuFDcudCWCG0EQXdlVxV1R12qzkyIUJNZVfFQr4kKkIIKPUEGMJWJLbamt5t08bm7ujGfuoyqi4CHn3tw5c77zn5nD0pf2PzBl/eh8aOD29ejVWe7Uz0oph6QC6AeDXHBmmYKNc8YugsEzCjL6z9JjCTU02Iv+TxM4uEsh0cMx3B1GfAcQEUDTAV4uSYzeW0ZnlLOKJQcJ3kuAM4LjkFBKQck2pNPCzWMdeLQAzFeB12VQAvC9AZzebaBpK9RaUsW7BKIhA6MHtuP41FcIT47WKzG3Dkxkge5twE5yxYFVAiwQ0HYkZk71IBJiaFNjjDLbJMUFML/XggUQ3HVBi9z3cssrkrE4tQPk8sCFvp95rukDKdBGIzikAEyPkuWBPlYAk95hyipbXmwToD/y/kY3YHjOyUs2vf1qQVyr3VTA3EWGIi0KaioUop7p0NaKLfe6qo4GMXcfgnZ9tSIQa1C5x1kLolFGrtJw8cqfhejWqJ4FvxDBSE5pE6B7VDGEmY2NhcVpO/f8yWpq5FXQWnx86U272YRpCpj65E1SZzmYzlWhqqt3hSJBYe5gspjA4SufRygnH0+uVBmkC6jMzZyP9R+5RlrEjfQS9NzIjW9P6+8eptbvX54lAINDQmoshmdjexZPdr2HrSoQyoaGrEyeu7UCvPAvJbAC+dre5Je68MbagM1MbBgcU/l9ONH5ARHUEFYNxJPLZQaVwV/MvXam74sgbWhIB1L5AdzJ96HFtkD9VvhPEzo5m8m4EMX0mIagCCukDUfPq/o3QEeHf/l+i/+0HwIMAH5w+NCLkY8+AAAAAElFTkSuQmCC">
		Translations
		</span>';
	}

	/**
	 * @return string
	 */
	public function getPanel()
	{
		$lang = $this->translator->lang;
		$dictionaries = $this->getDictionaries();
		ob_start();
		require_once __DIR__ . "/Panel.phtml";
		return ob_get_clean();
	}

	/**
	 * @param ITranslator
	 */
	public static function register(ITranslator $translator)
	{
		\Nette\Diagnostics\Debugger::$bar->addPanel(new static($translator));
	}
}


