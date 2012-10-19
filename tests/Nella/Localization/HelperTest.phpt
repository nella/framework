<?php
/**
 * Test: Nella\Localization\Helper
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Localization\HelperTest
 */

namespace Nella\Tests\Localization;

use Assert;

require_once __DIR__ . '/../../bootstrap.php';

class HelperTest extends \TestCase
{
	public function dataForms()
	{
		return array(
			array(0, 2),
			array(1, 0),
			array(2, 1),
			array(3, 1),
			array(4, 1),
			array(5, 2),
		);
	}

	/**
	 * @dataProvider dataForms
	 * @param int
	 * @param int
	 */
	public function testToForm($count, $expected)
	{
		$forms = 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4 ? 1 : 2);';
		$form = \Nella\Localization\Helper::toForm($forms, $count);

		Assert::equal($expected, $form, "from $count to $expected");
	}
}
