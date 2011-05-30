<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Localization\Filters;

use Nella\Localization\Dictionary;

require_once __DIR__ . "/../../bootstrap.php";

class LatteTest extends \Nella\Testing\TestCase
{

	/** @var \Nella\Localization\Filters\Latte */
	private $filter;

	protected function setup()
	{
		$this->filter = new \Nella\Localization\Filters\Latte;
	}

	/**
	 * @return \Nella\Localization\Dictionary
	 */
	protected function createDictionary()
	{
		return new Dictionary(__DIR__, new \NellaTests\Localization\Storages\Mock);
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Localization\IFilter', $this->filter, "is instance of 'Nella\\Localization\\IFilter'");
	}

	public function testProcess()
	{
		$dictionary = $this->createDictionary();
		$this->filter->process($dictionary);

		$this->assertEquals(array(
			"Translation",
			"Quoted Translation",
			"PluralTranslation1",
			"%d PluralTranslation",
			"%s VariablesTranslation %d",
		), array_keys($dictionary->iterator->getArrayCopy()), "->process(\$dictionary)");
	}
}