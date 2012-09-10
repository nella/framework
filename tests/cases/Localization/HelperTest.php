<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Localization;

class HelperTest extends \Nella\Testing\TestCase
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

		$this->assertEquals($expected, $form, "from $count to $expected");
	}
}
