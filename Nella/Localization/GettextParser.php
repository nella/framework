<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Localization;

use Nette\Utils\Strings;

/**
 * Gettext localization file parser
 *
 * @author	Patrik Votoček
 */
class GettextParser extends \Nette\Object implements IParser
{
	/**
	 * @param mixed
	 * @param string save file path
	 */
	public function encode($var, $filename)
	{
		throw new \Nette\NotImplementedException;
	}

	/**
	 * @param string file path
	 * @return array
	 * @throws \Nette\InvalidArgumentException
	 */
	public function decode($filename)
	{
		if (!file_exists($filename)) {
			throw new \Nette\InvalidArgumentException("File '$filename' does not exist");
		}
		if (@filesize($filename) < 10) {
			throw new \Nette\InvalidArgumentException("File '$filename' is not a gettext file");
		}

		$handle = @fopen($filename, "rb");

		$endian = FALSE;
		$read = function($bytes) use ($handle, $endian) {
			$data = fread($handle, 4 * $bytes);
			return $endian === FALSE ? unpack('V'.$bytes, $data) : unpack('N'.$bytes, $data);
		};

		$input = $read(1);
		if (Strings::lower(substr(dechex($input[1]), -8)) == "950412de") {
			$endian = FALSE;
		} elseif (Strings::lower(substr(dechex($input[1]), -8)) == "de120495") {
			$endian = TRUE;
		} else {
			throw new \Nette\InvalidArgumentException("File '$filename' is not a gettext file");
		}

		$input = $read(1);

		$input = $read(1);
		$total = $input[1];

		$input = $read(1);
		$originalOffset = $input[1];

		$input = $read(1);
		$translationOffset = $input[1];

		fseek($handle, $originalOffset);
		$orignalTmp = $read(2 * $total);
		fseek($handle, $translationOffset);
		$translationTmp = $read(2 * $total);

		$output = array('metadata' => array(), 'dictionary' => array());

		for ($i = 0; $i < $total; ++$i) {
			if ($orignalTmp[$i * 2 + 1] != 0) {
				fseek($handle, $orignalTmp[$i * 2 + 2]);
				$original = @fread($handle, $orignalTmp[$i * 2 + 1]);
			} else {
				$original = "";
			}

			if ($translationTmp[$i * 2 + 1] != 0) {
				fseek($handle, $translationTmp[$i * 2 + 2]);
				$translation = fread($handle, $translationTmp[$i * 2 + 1]);
				if ($original === "") {
					$output['metadata'] += $this->decodeMetadata($translation);
					continue;
				}

				$original = explode(Strings::chr(0x00), $original);
				$translation = explode(Strings::chr(0x00), $translation);
				$output['dictionary'][is_array($original) ? $original[0] : $original]['original'] = $original;
				$output['dictionary'][is_array($original) ? $original[0] : $original]['translation'] = $translation;
			}
		}

		return $output;
	}

	/**
	 * Header metadata parser
	 *
	 * @param string
	 * @return array
	 */
	private function decodeMetadata($input)
	{
		$input = trim($input);
		$output = array();

		$input = preg_split('/[\n,]+/', $input);
		$pattern = ': ';
		foreach ($input as $metadata) {
			$tmp = preg_split("($pattern)", $metadata);
			$output[trim($tmp[0])] = count($tmp) > 2 ? ltrim(strstr($metadata, $pattern), $pattern) : (isset($tmp[1]) ? $tmp[1] : NULL);
		}

		return $output;
	}
}