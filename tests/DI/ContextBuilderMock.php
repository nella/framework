<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\DI;

use Nette\Config\Config;

require_once __DIR__ . "/../bootstrap.php";

class ContextBuilderMock extends \Nella\DI\ContextBuilder
{
	public function loadEnvironmentNameMock($name)
	{
		return $this->loadEnvironmentName($name);
	}

	public function loadIniMock(Config $config)
	{
		return $this->loadIni($config);
	}

	public function loadParametersMock(Config $config)
	{
		return $this->loadParameters($config);
	}
}
