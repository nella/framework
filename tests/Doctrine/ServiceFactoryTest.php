<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Doctrine;

require_once __DIR__ . "/../bootstrap.php";

use Nella\Doctrine\ServiceFactory;

class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testAnnotationDriver()
	{
		$this->assertInstanceOf('Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver', ServiceFactory::annotationDriver(), "::annotationDriver instance od doctrine annotation driver");
	}

	/**
	 * @expectedException \MongoConnectionException
	 */
	public function testConnection()
	{
		$this->assertInstanceOf(
			'Doctrine\MongoDB\Connection', 
			ServiceFactory::connection(array(
				'host' => "localhost",
				'username' => "test", 
				'password' => "test", 
				'dbname' => "test", 
			)), 
			"::connection instance of Doctrine MongoDB connection"
		);
	}

	public function testConfiguration()
	{
		$this->assertInstanceOf(
			'Doctrine\ODM\MongoDB\Configuration', 
			ServiceFactory::configuration(
				ServiceFactory::annotationDriver(), 
				new \Nella\Doctrine\Cache(new \Nette\Caching\Cache(new \Nette\Caching\MemoryStorage))), 
			"::config instance of Doctrine configuration"
		);
	}

	public function testDocumentManager()
	{
		$connection = new \Doctrine\MongoDB\Connection();
		$config = ServiceFactory::configuration(
			ServiceFactory::annotationDriver(), 
			new \Nella\Doctrine\Cache(new \Nette\Caching\Cache(new \Nette\Caching\MemoryStorage))
		);
		

		$this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', ServiceFactory::documentManager($connection, $config), "::documentManager instance of Doctrine document manager");
	}
}
