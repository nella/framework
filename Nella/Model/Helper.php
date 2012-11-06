<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Model;

/**
 * Doctrine helper
 *
 * @author	Patrik Votoček
 */
class Helper extends \Nette\Object
{
	public function __construct()
	{
		throw new \Nette\StaticClassException;
	}

	/**
	 * @param \PDOException|\Doctrine\DBAL\DBALException
	 * @throws \Nella\Model\DuplicateEntryException
	 * @throws \Nella\Model\EmptyValueException
	 * @throws \Nella\Model\Exception
	 */
	public static function convertException(\Exception $e)
	{
		if ($e instanceof \Doctrine\DBAL\DBALException) {
			$pe = $e->getPrevious();
			if ($pe instanceof \PDOException) {
				$info = $pe->errorInfo;
			} else {
				throw new \Nette\InvalidArgumentException('Not supported DBAL exteption type', 0, $e);
			}
		} elseif ($e instanceof \PDOException) {
			$info = $e->errorInfo;
		} else {
			throw new \Nette\InvalidArgumentException('Only PDO and DBAL exteption accepted', 0, $e);
		}

		if ($info[0] == 23000 && $info[1] == 1062) { // unique fail
			// @todo how to detect column name ?
			throw new \Nella\Model\DuplicateEntryException($e->getMessage(), NULL, $e);
		} elseif ($info[0] == 23000 && $info[1] == 1048) { // notnull fail
			// @todo convert table column name to entity column name
			$name = substr($info[2], strpos($info[2], "'") + 1);
			$name = substr($name, 0, strpos($name, "'"));
			throw new \Nella\Model\EmptyValueException($e->getMessage(), $name, $e);
		} else { // other fail
			throw new \Nella\Model\Exception($e->getMessage(), 0, $e);
		}
	}
}

