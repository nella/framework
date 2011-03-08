<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella;

use Nette\Environment;

/**
 * Front Controller
 * 
 * @author	Patrik Votoček
 */
class Application extends \Nette\Application\Application
{
	/**
	 * @param string
	 */
	public function run($mode = Environment::PRODUCTION)
	{
		if ($mode = Environment::CONSOLE) {
			$context = $this->getContext();
			$helperSet = new \Symfony\Component\Console\Helper\HelperSet();
			$helperSet->set(new \Nella\Doctrine\EntityManagerHelper(function() use ($context) {
				return $context->getService('Doctrine\ORM\EntityManager');
			}));
			\Doctrine\ORM\Tools\Console\ConsoleRunner::run($helperSet);
		} else {
			parent::run();
		}
	}
}
