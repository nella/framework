<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Media\Model;

class FileEntityTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Media\Model\FileEntity */
	private $file;

	public function setup()
	{
		parent::setup();
		$this->file = new \Nella\Media\Model\FileEntity('foo.bar', 'application/octet-stream');
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\IFile', $this->file);
	}

	public function testDefaultValuesSettersAndGetters()
	{
		$this->assertNull($this->file->getId(), "->getId() default value");
		$this->assertEquals('foo.bar', $this->file->getPath(), "->getPath() default value");
		$this->assertNull($this->file->getSlug(FALSE), "->getSlug(FALSE) default value");
		$this->assertEquals('application/octet-stream', $this->file->getContentType(), "->getContentType() default value");
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
		$this->assertEquals($value, $this->file->$getter(),
			"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->file->$property = $value;
		$this->assertEquals($value, $this->file->$property,
			"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}
}