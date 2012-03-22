<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Templating;

use Nette\Application\UI\Control;

/**
 * ITemplateFactoryFilesFormatter
 *
 * @author	Patrik Votoček
 */
interface ITemplateFilesFormatter
{
	/**
	 * Formats layout template file names
	 *
	 * @param string	presenter name
	 * @param string	layout name
	 * @return array
	 */
	public function formatLayoutTemplateFiles($name, $layout = 'layout');

	/**
	 * Formats view template file names
	 *
	 * @param string	presenter name
	 * @param string	view name
	 * @return array
	 */
	public function formatTemplateFiles($name, $view);

	/**
	 * Formats layout template file names
	 *
	 * @param string	control name
	 * @param string	view name
	 * @return array
	 */
	public function formatComponentTemplateFiles($class, $view);
}