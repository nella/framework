<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Localization;

require_once __DIR__ . "/../bootstrap.php";

class ExtractorTest extends \Nella\Testing\TestCase
{

	/** @var \Nella\Localization\Extractor */
	private $extractor;
	/** @var \Nella\Localization\Translator */
	private $translator;

	protected function setup()
	{
		$this->translator = new \Nella\Localization\Translator;
		$this->translator->addDictionary('test', __DIR__ . "/Filters", new Storages\Mock(array(
			'Translation' => array("Překlad"),
		)));
		$this->translator->setLang('test')->init();

		$this->extractor = new \Nella\Localization\Extractor($this->translator);
	}

	public function testRun()
	{
		$this->extractor->run();
		$dictionaries = $this->translator->dictionaries;
		$dictionary = reset($dictionaries)->iterator->getArrayCopy();
		$this->assertEquals(array(
			"Translation",
			"Quoted Translation",
			"PluralTranslation1",
			"%d PluralTranslation",
			"%s VariablesTranslation %d",
		), array_keys($dictionary), "dictionary after ->run()");
	}

}
