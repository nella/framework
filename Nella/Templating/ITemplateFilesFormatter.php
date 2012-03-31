<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
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