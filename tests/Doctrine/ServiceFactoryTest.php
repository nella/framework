<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Doctrine;

require_once __DIR__ . "/../bootstrap.php";

use Nella\Doctrine\ServiceFactory;

class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testConfiguration()
	{
		$this->assertInstanceOf('Doctrine\ORM\Configuration', ServiceFactory::configuration(), "::configuration instance of Doctrine configuration");
	}

	public function testEntityManager()
	{
		$conf = array(
			'driver' => "pdo_sqlite", 
			'path' => __DIR__ . "/db.sqlite",
		);
		
		$this->assertInstanceOf('Doctrine\ORM\EntityManager', ServiceFactory::entityManager($conf), "::entityManager instance of Doctrine entity manager");
	}
}
