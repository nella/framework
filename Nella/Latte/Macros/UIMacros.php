<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Latte\Macros;

use Nette\Latte\MacroNode;

/**
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 */
class UIMacros extends \Nette\Latte\Macros\MacroSet
{
	/**
	 * @param \Nette\Latte\Parser
	 */
	public static function install(\Nette\Latte\Parser $parser)
	{
		$me = parent::install($parser);

		// n:phref
		$me->addMacro('@phref', function(MacroNode $node, $writer) use ($me) {
			return ' ?> href="<?php ' . $me->macroPlink($node, $writer) . ' ?>"<?php ';
		});

		// image
		$me->addMacro('image', array($me, 'macroImage'));

		// n:src
		$me->addMacro('@src', function(MacroNode $node, $writer) use ($me) {
			return ' ?> src="<?php ' . $me->macroImage($node, $writer) . ' ?>"<?php ';
		});

		// n:ihref
		$me->addMacro('@ihref', function(MacroNode $node, $writer) use ($me) {
			return ' ?> href="<?php ' . $me->macroImage($node, $writer) . ' ?>"<?php ';
		});

		// file
		$me->addMacro('file', array($me, 'macroFile'));

		// n:fhref
		$me->addMacro('@fhref', function(MacroNode $node, $writer) use ($me) {
			return ' ?> href="<?php ' . $me->macroFile($node, $writer) . ' ?>"<?php ';
		});
	}

	/**
	 * {plink destination [,] [params]}
	 * n:phref="destination [,] [params]"
	 */
	public function macroPlink(MacroNode $node, $writer)
	{
		return $writer->write('echo %escape($presenter->link(%node.word, %node.array?))');
	}

	/**
	 * {image ...}
	 * n:src
	 * n:ihref
	 *
	 * @param string
	 * @param mixed
	 * @return string
	 * @throws \Nette\Latte\ParseException
	 */
	public function macroImage(MacroNode $node, $writer)
	{
		$data = explode(',', $node->args);
		
		if (count($data) < 2) {
			throw new \Nette\Latte\ParseException("Invalid arguments count for image macro");
		}
		
		foreach ($data as &$value) {
			$value = trim($value);
		}
		
		list($image, $format) = $data;
		isset($data[2]) ?: $data['2'] = "'jpg'";

		return $writer->write("echo %escape(\$presenter->link(':Media:Media:image', array('image'=>"
			 . $writer->formatWord($image) . ",'format'=>" . $writer->formatWord($format) . ",'type'=>"
			 . $writer->formatWord($data[2]) . ")))");
	}

	/**
	 * {file ...}
	 *
	 * @param string
	 * @param mixed
	 * @return string
	 */
	public function macroFile(MacroNode $node, $writer)
	{
		return $writer->write("echo %escape(\$presenter->link(':Media:Media:file', array('file'=>%node.word)))");
	}
}