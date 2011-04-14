<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application\UI;

class ControlMock extends \Nella\Application\UI\Control
{
	public function formatTemplateFilesMock($method)
	{
		return $this->formatTemplateFiles($method);
	}
	
	public function formatTemplateFileMock($method)
	{
		return $this->formatTemplateFile($method);
	}
	
	public function render()
	{
		$this->_render(__METHOD__);
	}
	
	public function createComponentMock($name)
	{
		return $this->createComponent($name);
	}
}
