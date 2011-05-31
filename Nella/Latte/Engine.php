<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Latte;

/**
 * Nella latte engine
 *
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 */
class Engine extends \Nette\Latte\Engine
{
	public function __construct()
	{
		// Register Nette macros
		parent::__construct();

		// Register Nella macros
		Macros\UIMacros::install($this->parser);
	}
}
