<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Mocks\Application\UI;

class Control extends \Nella\Application\UI\Control
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