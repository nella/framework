<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */
 
namespace NellaTests\Tools;

use Nella\Framework;

require_once __DIR__ . "/../bootstrap.php";

/**
 * Test: Nella cUrl wrapper
 *
 * @author	Patrik Votoček
 */
class cUrlTest extends \PHPUnit_Framework_TestCase
{
	/** @var Nella\Tools\cUrlRequest */
	private $wrapper;
	
	public function setUp()
	{
		$this->wrapper = new \Nella\Tools\cUrlRequest;
	}
	
	/**
	 * @covers Nella\Tools\cUrlRequest::__construct
	 * @covers Nella\Tools\cUrlRequest::getUrl
	 */
	public function testInstace()
	{
		$this->assertInstanceOf('Nella\Tools\cUrlRequest', $this->wrapper, "is Nella\\Tools\\cUrl instance");
		$wrapper = new \Nella\Tools\cUrlRequest("http://example.com");
		$this->assertTrue(is_string($wrapper->getUrl()), "->getUrl()");
		$this->assertTrue(is_string($wrapper->url), "->url");
	}
	
	/**
	 * @covers Nella\Tools\cUrlRequest::getOption
	 * @covers Nella\Tools\cUrlRequest::setOption
	 * @covers Nella\Tools\cUrlRequest::getOptions
	 */
	public function testOptions()
	{
		// Option
		$this->assertTrue(is_string($this->wrapper->getOption('useragent')), "->getOption()");
		$this->wrapper->setOption('useragent', "Test");
		$this->assertEquals("Test", $this->wrapper->getOption('useragent'), "->setOption()");
		
		// Options
		$this->assertTrue(is_array($this->wrapper->getOptions()), "->getOptions()");
		$this->assertTrue(is_array($this->wrapper->options), "->options");
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testOptionException1()
	{
		$this->wrapper->getOption('foo');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testOptionException2()
	{
		$this->wrapper->setOption('foo', NULL);
	}
	
	/**
	 * @covers Nella\Tools\cUrlRequest::getHeader
	 * @covers Nella\Tools\cUrlRequest::setHeader
	 * @covers Nella\Tools\cUrlRequest::getHeaders
	 */
	public function testHeaders()
	{
		// Header
		$this->wrapper->setHeader('foo', "Test");
		$this->assertEquals("Test", $this->wrapper->getHeader('foo'), "->set/getHeader()");
		
		// Headers
		$this->assertTrue(is_array($this->wrapper->getHeaders()), "->getHeaders()");
		$this->assertTrue(is_array($this->wrapper->headers), "->headers");
	}
	
	/**
	 * @covers Nella\Tools\cUrlRequest::getProxies
	 * @covers Nella\Tools\cUrlRequest::addProxy
	 */
	public function testProxies()
	{
		$ip = "127.0.0.1";
		
		$this->wrapper->addProxy($ip);
		$this->assertTrue(is_array($this->wrapper->getProxies()), "->getProxies()");
		$this->assertTrue(is_array($this->wrapper->proxies), "->proxies");
		$proxy = $this->wrapper->proxies[0];
		$this->assertEquals($ip, $proxy->ip, "->proxies[0] + ->addProxy");
	}
	
	/**
	 * @covers Nella\Tools\cUrlRequest::getUserAgent
	 * @covers Nella\Tools\cUrlRequest::setUserAgent
	 */
	public function testUserAgent()
	{
		$this->assertEquals('Nella\Tools\cUrl ' . Framework::VERSION . " (http://nellacms.com)", $this->wrapper->userAgent, "->getUserAgent()");
		$this->assertEquals('Nella\Tools\cUrl ' . Framework::VERSION . " (http://nellacms.com)", $this->wrapper->userAgent, "->userAgent");
		$this->assertEquals("Test 1", $this->wrapper->setUserAgent("Test 1")->userAgent, "->setUserAgent()");
		$this->wrapper->userAgent = "Test 2";
		$this->assertEquals("Test 2", $this->wrapper->userAgent, "->userAgent setter");
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testUserAgentException()
	{
		$this->wrapper->userAgent = NULL;
	}
	
	/**
	 * @covers Nella\Tools\cUrlRequest::getInfo
	 */
	public function testInfo()
	{
		$this->assertTrue(is_array($this->wrapper->getInfo()), "->getInfo()");
		$this->assertTrue(is_array($this->wrapper->info), "->info");
	}
	
	/**
	 * @covers Nella\Tools\cUrlRequest::getResponse
	 * 
	 * @covers Nella\Tools\cUrlRequest::run
	 * @covers Nella\Tools\cUrlRequest::open
	 * @covers Nella\Tools\cUrlRequest::close
	 * @covers Nella\Tools\cUrlRequest::execute
	 * @covers Nella\Tools\cUrlRequest::tryProxy
	 * @covers Nella\Tools\cUrlRequest::setupOptions
	 * @covers Nella\Tools\cUrlRequest::setupHeaders
	 * @covers Nella\Tools\cUrlRequest::setupMethod
	 */
	public function testGetResponse()
	{
		$this->assertInstanceOf('Nella\Tools\cUrlResponse', $this->wrapper->getResponse(\Nella\Tools\cUrlRequest::HEAD, "http://www.google.com"));
	}
}
