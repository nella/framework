<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\DependencyInjection;

require_once __DIR__ . "/../bootstrap.php";

class ServiceFactoryMock extends \Nella\DependencyInjection\ServiceFactory
{
	public function getContext()
	{
		return $this->context;
	}
	
	public function getClass()
	{
		return $this->class;
	}
	
	public function getFactory()
	{
		return $this->factory;
	}
	
	public function getArguments()
	{
		return $this->arguments;
	}
	
	public function getMethods()
	{
		return $this->methods;
	}
	
	public function createInstanceMock()
	{
		return $this->createInstance();
	}
}
