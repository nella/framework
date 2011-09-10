<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests;

use Nella\Image;

class FreezableObjectTest extends \Nella\Testing\TestCase
{
	/** @var FreezableObjectMock */
	private $object;

	protected function setup()
	{
		parent::setup();
		$this->object = new FreezableObjectMock;
		$this->object->onFreeze[] = function($object) {
			$object->test .= "TRUE";
		};
	}

	public function testOnFreeze()
	{
		$this->object->freeze();
		$this->assertEquals("FALSETRUE", $this->object->test, "first freeze");
		$this->object->freeze();
		$this->assertEquals("FALSETRUE", $this->object->test, "second freeze");
	}
}

class FreezableObjectMock extends \Nella\FreezableObject
{
	/** @var string */
	public $test = "FALSE";
}