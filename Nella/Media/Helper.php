<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Media;

/**
 * Media helper
 *
 * @author	Patrik Votoček
 */
class Helper extends \Nette\Object
{
	/**
	 * @throws \Nette\StaticClassException
	 */
	public function __construct()
	{
		throw new \Nette\StaticClassException;
	}

	/**
	 * @param string
	 * @return string
	 * @throws \Nette\InvalidArgumentException
	 */
	public static function extToMimeType($ext)
	{
		$map = array(
			'gif' => 'image/gif',
			'png' => 'image/png',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
		);
		if (!array_key_exists($ext, $map)) {
			throw new \Nette\InvalidArgumentException("Ext '$ext' does not supported");
		}

		return $map[$ext];
	}

	/**
	 * @param string
	 * @return string
	 * @throws \Nette\InvalidArgumentException
	 */
	public static function mimeTypeToExt($mime)
	{
		$map = array(
			'image/gif' => 'gif',
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/x-png' => 'png',
		);
		if (!array_key_exists($mime, $map)) {
			throw new \Nette\InvalidArgumentException("Mime type '$mime' does not supported");
		}

		return $map[$mime];
	}
}
