<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Diagnostics;

use Nette\Diagnostics\Debugger;

/**
 * Shows exceptions thrown in CLI mode in browser.
 *
 * @author	Ondřej Mirtes
 * @author	Patrik Votoček
 */
class ConsoleDebug extends \Nette\Object
{
	/** @var string */
	private static $browser = NULL;

	final public function __construct()
	{
		throw new \Nette\StaticClassException;
	}

	/**
	 * @param string Browser CLI command with %s placeholder for log file path
	 */
	public static function enable($browser)
	{
		static::$browser = $browser;

		Debugger::$onFatalError[] = array(get_called_class(), '_exceptionHandler');
	}

	/**
	 * @param \Exception
	 */
	public static function _exceptionHandler(\Exception $exception)
	{
		if (PHP_SAPI == 'cli' && static::$browser) {
			try {
				Debugger::log($exception);

				$hash = md5($exception);
				$path = Debugger::$logDirectory;
				foreach (new \DirectoryIterator($path) as $entry) {
					if (strpos($entry, $hash)) {
						$path .= "/" . $entry;
						break;
					}
				}

				if ($path != Debugger::$logDirectory) {
					exec(sprintf(static::$browser, escapeshellarg('file://' . $path)));
					static::$browser = NULL;
				}
			} catch (\Exception $e) {
				echo $e->getMessage();
			}
		}
	}

}