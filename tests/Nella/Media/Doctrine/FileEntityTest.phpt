<?php
/**
 * Test: Nella\Media\Doctrine\FileEntity
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Media\Doctrine;

use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class FileEntityTest extends \Tester\TestCase
{
	/** @var \Nella\Media\Doctrine\FileEntity */
	private $file;

	public function setUp()
	{
		parent::setUp();
		$this->file = new \Nella\Media\Doctrine\FileEntity('foo.bar', 'application/octet-stream');
	}

	public function testInstance()
	{
		Assert::true($this->file instanceof \Nella\Media\IFile);
	}

	public function testDefaultValuesSettersAndGetters()
	{
		Assert::null($this->file->getId(), "->getId() default value");
		Assert::equal('foo.bar', $this->file->getPath(), "->getPath() default value");
		Assert::null($this->file->getSlug(FALSE), "->getSlug(FALSE) default value");
		Assert::equal('application/octet-stream', $this->file->getContentType(), "->getContentType() default value");
	}

	public function dataSettersAndGetters()
	{
		return array(
			array('slug', 'logo'),
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersMethods($method, $value)
	{
		$setter = "set" . ucfirst($method);
		$getter = "get" . ucfirst($method);
		$this->file->$setter($value);
		Assert::equal($value, $this->file->$getter(),
			"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->file->$property = $value;
		Assert::equal($value, $this->file->$property,
			"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}
}

id(new FileEntityTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
