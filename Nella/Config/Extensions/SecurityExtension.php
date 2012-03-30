<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Config\Extensions;

/**
 * Security extension
 *
 * @author    Patrik Votoček
 */
class SecurityExtension extends \Nette\Config\CompilerExtension
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

		$builder->addDefinition($this->prefix('entityManager'))
			->setClass('Doctrine\ORM\EntityManager')
			->setFactory($config['entityManager']);

		if ($builder->hasDefinition('nette.userStorage')) {
			$builder->removeDefinition('nette.userStorage');
		}
		$builder->addDefinition($this->prefix('userStorage'))
			->setClass('Nella\Security\UserStorage', array('@session', $this->prefix('entityManager')));

		$credentialsRepository = $builder->addDefinition($this->prefix('credentialsRepository'))
			->setClass('Nella\Doctrine\Repository')
			->setFactory($this->prefix('entityManager').'->getRepository(?)', array(
				'Nella\Security\Model\CredentialsEntity'
			));

		$credentialsDao = $builder->addDefinition($this->prefix('credentialsDao'))
			->setClass('Nella\Security\Model\CredentialsDao', array(
				$this->prefix('entityManager'), $credentialsRepository
			));


		if ($builder->hasDefinition('nette.authenticator')) {
			$builder->getDefinition('nette.authenticator')->setAutowired(FALSE);
		}
		$builder->addDefinition($this->prefix('authenticator'))
			->setClass('Nella\Security\Authenticator', array($credentialsDao));
	}
}