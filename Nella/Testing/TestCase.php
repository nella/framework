<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Testing;

/**
 * Base Nella Framework test case for PHPUnit
 *
 * @author	Patrik Votoček
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
	/** @var \Nette\DI\Container */
	protected $context;

	/**
     * Enable or disable the backup and restoration of the $GLOBALS array.
     * Overwrite this attribute in a child class of TestCase.
     * Setting this attribute in setUp() has no effect!
     *
     * @var bool
     */
    protected $backupGlobals = FALSE;

	/**
     * Enable or disable the backup and restoration of static attributes.
     * Overwrite this attribute in a child class of TestCase.
     * Setting this attribute in setUp() has no effect!
     *
     * @var bool
     */
    protected $backupStaticAttributes = FALSE;

	public function runBare()
	{
		try {
			return parent::runBare();
		} catch (\Exception $e) {
			if (!$e instanceof \PHPUnit_Framework_AssertionFailedError) {
				\Nella\Diagnostics\ConsoleDebug::_exceptionHandler($e);
			}
			throw $e;
		}
	}

	protected function setup()
	{
		$this->context = clone \Nette\Environment::getContext();
	}
}