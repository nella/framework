<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Security\Config;

use Nette\Config\Compiler,
	Nette\Config\Configurator;

/**
 * Security extension
 *
 * @author    Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	const DEFAULT_EXTENSION_NAME = 'security';

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
			->setClass('Nella\Security\UserStorage', array('@session', $this->prefix('@entityManager')));

		$credentialsRepository = $builder->addDefinition($this->prefix('credentialsRepository'))
			->setClass('Nella\Doctrine\Repository')
			->setFactory(
				$this->prefix('@entityManager::getRepository'), array('Nella\Security\Model\CredentialsEntity')
			);

		$credentialsFacade = $builder->addDefinition($this->prefix('credentialsFacade'))
			->setClass(
				'Nella\Security\Model\CredentialsFacade', array($this->prefix('@entityManager'), $credentialsRepository)
			);


		if ($builder->hasDefinition('nette.authenticator')) {
			$builder->getDefinition('nette.authenticator')->setAutowired(FALSE);
		}
		$builder->addDefinition($this->prefix('authenticator'))
			->setClass('Nella\Security\Authenticator', array($credentialsFacade));
	}

	/**
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator
	 * @param string
	 */
	public static function register(Configurator $configurator, $name = self::DEFAULT_EXTENSION_NAME)
	{
		$class = get_called_class();
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) use ($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}

