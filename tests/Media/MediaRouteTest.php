<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests;

class MediaRouteTest extends \Nella\Testing\TestCase
{
	/**
	 * @param string
	 * @return \Nella\Media\ImageRoute
	 */
	protected function generateRoute($mask)
	{
		return new \Nella\Media\ImageRoute($mask, array(
			'module' => "Media", 
			'presenter' => "Media", 
			'action' => "image", 
		), 0, new \Nella\Doctrine\Container(new \Nette\DI\Container));
	}
	
	/**
	 * @param string|int
	 * @param string|int
	 * @return \Nette\Application\Request
	 */
	protected function generateRequest($image, $format)
	{
		return new \Nette\Application\Request('Media:Media', 'GET', array(
			'action' => "image", 
			'image' => $image, 'format' => $format, 'type' => "jpg"
		));
	}
	
	public function dataConstructUrl()
	{
		return array(
			array("<image>/<format>.<type>", array(1, 2), "http://1/2.jpg"), 
			array("<image>/<format>.<type>", array("foo", 2), "http://foo/2.jpg"), 
			array("<image>/<format>.<type>", array(1, "foo"), "http://1/foo.jpg"), 
			array("<image>/<format>.<type>", array("foo", "bar"), "http://foo/bar.jpg"), 
			array("<format>/<image>.<type>", array(1, 2), "http://2/1.jpg"), 
			array("<format>/<image>.<type>", array("foo", 2), "http://2/foo.jpg"), 
			array("<format>/<image>.<type>", array(1, "foo"), "http://foo/1.jpg"), 
			array("<format>/<image>.<type>", array("foo", "bar"), "http://bar/foo.jpg"), 
		);
	}
	
	public function testRoute()
	{
		$mask = "<format>/<image>.<type>";
		$route = $this->generateRoute($mask);
		
		$this->assertEquals($mask, $route->getMask(), "->getMask()");
		$this->assertEquals(array(
			'action' => "image", 
			'format' => NULL, 
			'image' => NULL, 
			'module' => "Media", 
			'presenter' => "Media", 
			'type' => NULL, 
		), $route->getDefaults(), "->getDefaults()");
	}
	
	/**
	 * @dataProvider dataConstructUrl
	 * @param string
	 * @param array
	 * @param string
	 */
	public function testConstructUrl($mask, $params, $url)
	{
		$route = $this->generateRoute($mask);
		$request = $this->generateRequest($params[0], $params[1]);
		
		$this->assertEquals($url, (string) $route->constructUrl($request, new \Nette\Http\Url), 
			"->constructUrl() - {$params[0]}:{$params[1]}"
		);
	}
	
	public function dataMatch()
	{
		return array(
			array("<format>/<image>.<type>", "http://foo.bar/1/2.jpg"), 
		);
	}
	
	/**
	 * @dataProvider dataMatch
	 * @param string
	 * @param string
	 */
	public function testMatch($mask, $url)
	{
		$route = $this->generateRoute($mask);
		$httpRequest = new \Nette\Http\Request(new \Nette\Http\UrlScript($url));
		
		$this->markTestSkipped('needed doctrine');
	}
}