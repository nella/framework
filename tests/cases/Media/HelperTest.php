<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Media;

use Nella\Media\Helper;

class HelperTest extends \Nella\Testing\TestCase
{
	public function dataExtToMime()
	{
		return array(
			array('jpg', 'image/jpeg'),
			array('jpeg', 'image/jpeg'),
			array('gif', 'image/gif'),
			array('png', 'image/png'),
		);
	}

	/**
	 * @dataProvider dataExtToMime
	 * @param string
	 * @param string
	 */
	public function testExtToMime($ext, $mime)
	{
		$this->assertEquals($mime, Helper::extToMimeType($ext), "::extToMimeType('$ext')");
	}

	public function dataMimeToExt()
	{
		return array(
			array('image/jpeg', 'jpg'),
			array('image/gif', 'gif'),
			array('image/png', 'png'),
			array('image/x-png', 'png'),
		);
	}

	/**
	 * @dataProvider dataMimeToExt
	 * @param string
	 * @param string
	 */
	public function testMimeToExt($mime, $ext)
	{
		$this->assertEquals($ext, Helper::mimeTypeToExt($mime), "::mimeTypeToExt('$mime')");
	}
}