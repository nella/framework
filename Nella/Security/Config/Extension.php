<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Security\Config;

/**
 * Security extension
 *
 * @author    Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	public function loadConfiguration()
	{
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		if (!$config) {
			return;
		} elseif (!isset($config['entityManager'])) {
			throw new \Nette\InvalidStateException('Model extension entity manager not set');
		}

		if ($builder->hasDefinition('nette.userStorage')) {
			$builder->removeDefinition('nette.userStorage');
		}

		$builder->addDefinition($this->prefix('userStorage'))
			->setClass('Nella\Security\UserStorage', array('@session', $config['entityManager']));
	}
}