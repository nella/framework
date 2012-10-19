<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Mocks\Config;

class Compiler extends \Nette\Config\Compiler
{
	/** @var \Nette\DI\ContainerBuilder */
	public $builder;

	public function getContainerBuilder()
	{
		if (!$this->builder) {
			$this->builder = new \Nette\DI\ContainerBuilder;
		}

		return $this->builder;
	}
}