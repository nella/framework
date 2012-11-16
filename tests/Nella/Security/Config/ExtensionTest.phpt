<?php
/**
 * Test: Nella\Security\Config\Extension
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Security\Config;

use Tester\Assert,
	Nella\Mocks\Config\Configurator,
	Nella\Security\Config\Extension;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Config/Configurator.php';

class ExtensionTest extends \Tester\TestCase
{
	public function testRegister()
	{
		$configurator = new Configurator;
		Extension::register($configurator);
		$compiler = $configurator->createCompilerMock();
		$configurator->onCompile($configurator, $compiler);

		$extension = $compiler->extensions[Extension::DEFAULT_EXTENSION_NAME];
		Assert::true($extension instanceof \Nette\Config\CompilerExtension);
		Assert::true($extension instanceof Extension);
	}
}

id(new ExtensionTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
