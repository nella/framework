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
 * Translation extractor
 *
 * @author	Patrik Votoček
 */
class Extractor extends \Nella\FreezableObject
{
	/** @var Translator */
	protected $translator;
	/** @var array */
	protected $filters = array();

	/**
	 * @param Translator
	 */
	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
		$this->addFilter(new Filters\Latte);
		
		$this->onFreeze[] = function(Extractor $extractor) { // Setup default filters
			$extractor->addFilter(new Filters\Latte);
		};
	}

	/**
	 * @param IFilter
	 * @return Extractor
	 * @throws \Nette\InvalidStateException
	 */
	public function addFilter(IFilter $filter)
	{
		$this->updating();
		$this->filters[] = $filter;
		return $this;
	}
	
	

	/**
	 * @internal
	 */
	public function run()
	{
		$this->updating();
		$this->freeze();
		
		foreach ($this->translator->dictionaries as $dictionary) {
			foreach ($this->filters as $filter) {
				$filter->process($dictionary);
			}
		}
	}
}
