<?php
/**
 * Test: Nella\Config\Extensions\NellaExtension
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Config\Extensions\NellaExtensionTest
 */

namespace Nella\Tests\Config\Extensions;

use Assert,
	Nella\Mocks\Config\Configurator,
	Nella\Config\Extensions\NellaExtension;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Config/Configurator.php';

class NellaExtensionTest extends \TestCase
{
	public function testRegister()
	{
		$configurator = new Configurator;
		NellaExtension::register($configurator);
		$compiler = $configurator->createCompilerMock();
		$configurator->onCompile($configurator, $compiler);

		$extension = $compiler->extensions[NellaExtension::DEFAULT_EXTENSION_NAME];
		Assert::true($extension instanceof NellaExtension);
	}
}
