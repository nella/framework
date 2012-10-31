<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Application\UI;

/**
 * UI Form
 *
 * @author    Patrik Votoček
 */
class Form extends \Nella\Forms\Form
{
	/** @var array */
	public $onComplete = array();

	public function __construct()
	{
		parent::__construct();
		$this->setUp();
		$this->onSuccess[] = callback($this, 'process');
	}

	protected function setUp()
	{
		// ...
	}

	/**
	 * @param Form
	 */
	protected function doOnComplete(self $form)
	{
		if ($form->valid) {
			callback($this, 'onComplete')->invokeArgs(func_get_args());
		}
	}

	/**
	 * @internal
	 * @param Form
	 */
	public function process(self $form)
	{
		// ...
	}
}
