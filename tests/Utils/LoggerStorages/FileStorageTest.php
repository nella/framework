<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Utils\LoggerStorages;

require_once __DIR__ . "/../../bootstrap.php";

use Nella\Utils\LoggerStorages\FileStorage;

class FileStorageTest extends \Nella\Testing\TestCase
{
	/** @var Nella\Utils\LoggerStorages\FileStorage */
	private $logger;
	/** @var string */
	private $file;

	public function setup()
	{
		$file = "_file_action_logger_test_" . time() . ".log";
		$this->file = (\Nette\Diagnostics\Debugger::$logDirectory = __DIR__ . "/..") . "/" . $file;
		$this->logger = new FileStorage(new \Nette\Security\Identity(1), $file);
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Utils\IActionLogger', $this->logger, "is instance of Nella\\Utils\\IActionLogger");
	}

	public function testLogAction()
	{
		$fakeIdentity = new \Nette\Security\Identity(13);

		$data = "[" . date("Y-m-d H:i:s P") . "] Module:create: Test 1 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileStorage::CREATE, "Test 1", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() create");

		$data .= "[" . date("Y-m-d H:i:s P") . "] Module:read: Test 2 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileStorage::READ, "Test 2", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() read");

		$data .= "[" . date("Y-m-d H:i:s P") . "] Module:update: Test 3 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileStorage::UPDATE, "Test 3", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() update");

		$data .= "[" . date("Y-m-d H:i:s P") . "] Module:delete: Test 4 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileStorage::DELETE, "Test 4", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() delete");

		$data .= "[" . date("Y-m-d H:i:s P") . "] Module:other: Test 5 #13" . PHP_EOL;
		$this->logger->logAction("Module", FileStorage::OTHER, "Test 5", $fakeIdentity);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() other");
	}

	public function tearDown()
	{
		@unlink($this->file);
	}
}