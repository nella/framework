<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Tools;

use Nella\Tools\FileActionLogger;

class FileActionLoggerTest extends \PHPUnit_Framework_TestCase
{
	/** @var Nella\Tools\FileActionLogger */
	private $logger;
	/** @var string */
	private $file;
	
	public function setUp()
	{
		$file = "_file_action_logger_test_" . time() . ".log";
		$this->file = (\Nette\Debug::$logDirectory = __DIR__ . "/..") . "/" . $file;
		$this->logger = new FileActionLogger($file);
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Tools\IActionLogger', $this->logger, "is instance of Nella\\Tools\\IActionLogger");
	}
	
	public function testLogAction()
	{
		$fakeIdentity = new \Nette\Security\Identity(13);
		
		$data = "[" . date("Y-m-d H:i:s P") . "] Module:create: Test 1 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileActionLogger::CREATE, "Test 1", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() create");
		
		$data .= "[" . date("Y-m-d H:i:s P") . "] Module:read: Test 2 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileActionLogger::READ, "Test 2", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() read");
		
		$data .= "[" . date("Y-m-d H:i:s P") . "] Module:update: Test 3 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileActionLogger::UPDATE, "Test 3", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() update");
		
		$data .= "[" . date("Y-m-d H:i:s P") . "] Module:delete: Test 4 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileActionLogger::DELETE, "Test 4", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() delete");
		
		$data .= "[" . date("Y-m-d H:i:s P") . "] Module:other: Test 5 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileActionLogger::OTHER, "Test 5", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() other");
	}
	
	public function tearDown()
	{
		@unlink($this->file);
	}
}