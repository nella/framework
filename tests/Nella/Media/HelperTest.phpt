<?php
/**
 * Test: Nella\Media\Helper
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Media\HelperTest
 */

namespace Nella\Tests\Media;

use Assert,
	Nella\Media\Helper;

require_once __DIR__ . '/../../bootstrap.php';

class HelperTest extends \TestCase
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
		Assert::equal($mime, Helper::extToMimeType($ext), "::extToMimeType('$ext')");
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
		Assert::equal($ext, Helper::mimeTypeToExt($mime), "::mimeTypeToExt('$mime')");
	}
}
