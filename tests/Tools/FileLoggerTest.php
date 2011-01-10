<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Tools;

use Nella\Tools\FileLogger;

class FileLoggerTest extends \PHPUnit_Framework_TestCase
{
	/** @var Nella\Tools\FileLogger */
	private $logger;
	/** @var string */
	private $file;
	
	public function setUp()
	{
		$file = "_file_logger_test_" . time() . ".log";
		$this->file = (\Nette\Debug::$logDirectory = __DIR__ . "/..") . "/" . $file;
		$this->logger = new FileLogger($file);
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Tools\ILogger', $this->logger, "is instance of Nella\\Tools\\ILogger");
	}
	
	public function testLogMessage()
	{
		$data = "[" . date("Y-m-d H:i:s P") . "] error: Test 1" . PHP_EOL;
		$this->logger->logMessage("Test 1");
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() error");
		
		$data .= "[" . date("Y-m-d H:i:s P") . "] info: Test 2" . PHP_EOL;
		$this->logger->logMessage("Test 2", FileLogger::INFO);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() info");
		
		$data .= "[" . date("Y-m-d H:i:s P") . "] warning: Test 3" . PHP_EOL;
		$this->logger->logMessage("Test 3", FileLogger::WARNING);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() warning");
		
		$data .= "[" . date("Y-m-d H:i:s P") . "] fatal: Test 4" . PHP_EOL;
		$this->logger->logMessage("Test 4", FileLogger::FATAL);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() fatal");
		
		$data .= "[" . date("Y-m-d H:i:s P") . "] debug: Test 5" . PHP_EOL;
		$this->logger->logMessage("Test 5", FileLogger::DEBUG);
		$this->assertEquals($data, file_get_contents($this->file), "->logMessage() debug");
	}
	
	public function tearDown()
	{
		@unlink($this->file);
	}
}