<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Application;

class ControlMock extends \Nella\Application\Control
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
}
