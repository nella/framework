<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Application\UI;

/**
 * Application base presenter
 *
 * @author	Patrik Votoček
 */
abstract class Presenter extends \Nette\Application\UI\Presenter
{
	/**
	 * @return \Nella\Templating\ITemplateFilesFormatter
	 */
	public function getTemplateFilesFormatter()
	{
		return $this->getContext()->nella->templateFilesFormatter;
	}

	/**
	 * @return array
	 */
	public function formatLayoutTemplateFiles()
	{
		return $this->getTemplateFilesFormatter()->formatLayoutTemplateFiles(
			$this->getName(), $this->getLayout() ? $this->getLayout() : 'layout'
		);
	}

	/**
	 * @return array
	 */
	public function formatTemplateFiles()
	{
		return $this->getTemplateFilesFormatter()->formatTemplateFiles($this->getName(), $this->getView());
	}

}
