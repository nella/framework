<?php
/**
 * Test: Nella\Doctrine\Config\MigrationsExtension
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Doctrine\Config;

use Tester\Assert,
	Nella\Mocks\Config\Configurator,
	Nella\Doctrine\Config\MigrationsExtension;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Config/Configurator.php';

class MigrationsExtensionTest extends \Tester\TestCase
{
	public function testRegister()
	{
		$configurator = new Configurator;
		MigrationsExtension::register($configurator);
		$compiler = $configurator->createCompilerMock();
		$configurator->onCompile($configurator, $compiler);

		$extension = $compiler->extensions[MigrationsExtension::DEFAULT_EXTENSION_NAME];
		Assert::true($extension instanceof MigrationsExtension);
	}
}

id(new MigrationsExtensionTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
