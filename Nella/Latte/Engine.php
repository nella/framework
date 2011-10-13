<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Latte;

use Nette\Latte\Parser;

/**
 * Nella latte engine
 *
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 */
class Engine extends \Nette\Latte\Engine
{
	/**
	 * @param \Nette\Latte\Parser
	 */
	public function __construct(Parser $parser = NULL)
	{
		// Init parser
		$this->parser = $parser ?: new Parser;

		// Register Nella macros
		Macros\UIMacros::install($this->parser);
		
		// Register Nette macros
		\Nette\Latte\Macros\CoreMacros::install($this->parser);
		$this->parser->addMacro('cache', new \Nette\Latte\Macros\CacheMacro($this->parser));
		\Nette\Latte\Macros\UIMacros::install($this->parser);
		\Nette\Latte\Macros\FormMacros::install($this->parser);
	}
}
