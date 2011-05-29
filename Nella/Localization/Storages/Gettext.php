<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Localization\Storages;

use Nette\Utils\Strings, 
	Nella\Localization\Dictionary;

/**
 * Gettext localization storage
 *
 * This solution is mostly based on Zend_Acl (c) Zend Technologies USA Inc. (http://www.zend.com), new BSD license
 *
 * @copyright  Copyright (c) 2005, 2009 Zend Technologies USA Inc.
 * @author	Patrik Votoček
 */
class Gettext extends \Nette\Object implements \Nella\Localization\IStorage
{
	/** @var string */
	private $fileMask;
	
	/**
	 * @param string
	 */
	public function __construct($fileMask = "%dir%/lang/%lang%.mo")
	{
		$this->fileMask = $fileMask;
	}
	
	/**
	 * Process gettext metadata array
	 *
	 * @return array
	 */
	private function processMetadata($metadata)
	{
		$result = array();
		if (isset($metadata['Project-Id-Version']))
			$result[] = "Project-Id-Version: ".$metadata['Project-Id-Version'];
		else
			$result[] = "Project-Id-Version: ";
		if (isset($metadata['Report-Msgid-Bugs-To']))
			$result[] = "Report-Msgid-Bugs-To: ".$metadata['Report-Msgid-Bugs-To'];
		if (isset($metadata['POT-Creation-Date']))
			$result[] = "POT-Creation-Date: ".$metadata['POT-Creation-Date'];
		else
			$result[] = "POT-Creation-Date: ";
		$result[] = "PO-Revision-Date: ".date("Y-m-d H:iO");
		if (isset($metadata['Last-Translator']))
			$result[] = "Last-Translator: ".$metadata['Last-Translator'];
		if (isset($metadata['Language-Team']))
			$result[] = "Language-Team: ".$metadata['Language-Team'];
		if (isset($metadata['MIME-Version']))
			$result[] = "MIME-Version: ".$metadata['MIME-Version'];
		else
			$result[] = "MIME-Version: 1.0";
		if (isset($metadata['Content-Type']))
			$result[] = "Content-Type: ".$metadata['Content-Type'];
		else
			$result[] = "Content-Type: text/plain; charset=UTF-8";
		if (isset($metadata['Content-Transfer-Encoding']))
			$result[] = "Content-Transfer-Encoding: ".$metadata['Content-Transfer-Encoding'];
		else
			$result[] = "Content-Transfer-Encoding: 8bit";
		if (isset($metadata['Plural-Forms']))
			$result[] = "Plural-Forms: ".$metadata['Plural-Forms'];
		else
			$result[] = "Plural-Forms: ";
		if (isset($metadata['X-Poedit-Language']))
			$result[] = "X-Poedit-Language: ".$metadata['X-Poedit-Language'];
		if (isset($metadata['X-Poedit-Country']))
			$result[] = "X-Poedit-Country: ".$metadata['X-Poedit-Country'];
		if (isset($metadata['X-Poedit-SourceCharset']))
			$result[] = "X-Poedit-SourceCharset: ".$metadata['X-Poedit-SourceCharset'];
		if (isset($metadata['X-Poedit-KeywordsList']))
			$result[] = "X-Poedit-KeywordsList: ".$metadata['X-Poedit-KeywordsList'];

		return $result;
	}
	
	/**
	 * @param \Nella\Localization\Dictionary
	 * @param string
	 */
	public function save(Dictionary $dictionary, $lang)
	{
		$metadata = $dictionary->metadata;
		$metadata['Plural-Forms'] = $dictionary->pluralForm;
		$metadata = implode("\n", $this->processMetadata($metadata));
		
		$translations = array();
		foreach ($dictionary as $message => $item) {
			if ($item['status'] == Dictionary::STATUS_SAVED || $item['status'] == Dictionary::STATUS_TRANSLATED) {
				$translations[$message] = $item['translation'];
			}
		}
		
		ksort($translations);
		$items = count($translations) + 1;
		$ids = Strings::chr(0x00);
		$strings = $metadata.Strings::chr(0x00);
		$idsOffsets = array(0, 28 + $items * 16);
		$stringsOffsets = array(array(0, strlen($metadata)));
		
		foreach ($translations as $key => $value) {
			$id = $key;
			$string = implode(Strings::chr(0x00), $value);
			$idsOffsets[] = strlen($id);
			$idsOffsets[] = strlen($ids) + 28 + $items * 16;
			$stringsOffsets[] = array(strlen($strings), strlen($string));
			$ids .= $id.Strings::chr(0x00);
			$strings .= $string.Strings::chr(0x00);
		}
		
		$valuesOffsets = array();
		foreach ($stringsOffsets as $offset) {
			list ($all, $one) = $offset;
			$valuesOffsets[] = $one;
			$valuesOffsets[] = $all + strlen($ids) + 28 + $items * 16;
		}
		$offsets= array_merge($idsOffsets, $valuesOffsets);
		
		$mo = pack('Iiiiiii', 0x950412de, 0, $items, 28, 28 + $items * 8, 0, 28 + $items * 16);
		foreach ($offsets as $offset)
			$mo .= pack('i', $offset);

		$path = str_replace(array('%dir%', '%lang%'), array($dictionary->dir, $lang), $this->fileMask);
		file_put_contents($path, $mo.$ids.$strings);
	}

	/**
	 * @param string
	 * @return \Nella\Localization\Dictionary
	 * @throws \Nette\InvalidArgumentException
	 */
	public function load($lang, Dictionary $dictionary)
	{
		$path = str_replace(array('%dir%', '%lang%'), array($dictionary->dir, $lang), $this->fileMask);
		if (!file_exists($path)) {
			throw new \Nette\InvalidArgumentException("File '$path' does not exist");
		}
		if (@filesize($path) < 10) {
			throw new \Nette\InvalidArgumentException("File '$path' is not a gettext file");
		}

		$handle = @fopen($path, "rb");

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
			throw new \Nette\InvalidArgumentException("File '$path' is not a gettext file");
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

		$metadata = array();

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
					$metadata += $this->decodeMetadata($translation);
					continue;
				}

				$original = explode(Strings::chr(0x00), $original);
				$translation = explode(Strings::chr(0x00), $translation);
				// needed $original data (if array) ?
				$dictionary->addTranslation(is_array($original) ? $original[0] : $original, $translation);
			}
		}

		$dictionary->metadata = $metadata;
		if (isset($metadata['Plural-Forms'])) {
			$dictionary->pluralForm = $metadata['Plural-Forms'];
		}
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