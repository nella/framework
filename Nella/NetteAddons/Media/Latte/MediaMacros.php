<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\NetteAddons\Media\Latte;

use Nette\Latte\MacroNode,
	Nette\Latte\PhpWriter;

/**
 * Media macros
 *
 * /--code latte
 * {* file *}
 * <a href="{file $file}">Link</a>
 * <a n:fhref="$file">Link</a>
 *
 * {* image *}
 * <a href="{image $image, $format}">Link</a> or <img src="{image $image, $format}">
 * <a href="{img $image, $format}">Link</a> or <img src="{img $image, $format}">
 * <a n:ihref="$image, $format">Link</a>
 * <img n:src="$image, $format">
 * \--
 *
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 */
class MediaMacros extends \Nette\Latte\Macros\MacroSet
{
	/**
	 * @param \Nette\Latte\Engine
	 * @return \Nette\Latte\Macros\MacroSet
	 */
	public static function factory(\Nette\Latte\Engine $engine)
	{
		return static::install($engine->getCompiler());
	}

	/**
	 * @param \Nette\Latte\Compiler
	 */
	public static function install(\Nette\Latte\Compiler $compiler)
	{
		$me = parent::install($compiler);

		// file
		$me->addMacro('file', array($me, 'macroFile'));

		// n:fhref
		$me->addMacro('fhref', NULL, NULL, function(MacroNode $node, PhpWriter $writer) use ($me) {
			return ' ?> href="<?php ' . $me->macroFile($node, $writer) . ' ?>"<?php ';
		});

		// image
		$me->addMacro('image', array($me, 'macroImage'));
		// img
		$me->addMacro('img', array($me, 'macroImage'));

		// n:src
		$me->addMacro('src', NULL, NULL, function(MacroNode $node, PhpWriter $writer) use ($me) {
			return ' ?> src="<?php ' . $me->macroImage($node, $writer) . ' ?>"<?php ';
		});

		// n:ihref
		$me->addMacro('ihref', NULL, NULL, function(MacroNode $node, PhpWriter $writer) use ($me) {
			return ' ?> href="<?php ' . $me->macroImage($node, $writer) . ' ?>"<?php ';
		});
	}

	/**
	 * {file ...}
	 * n:fhref
	 *
	 * @param string
	 * @param mixed
	 * @return string
	 */
	public function macroFile(MacroNode $node, $writer)
	{
		return $writer->write("echo %escape(\$_presenter->link(':Nette:Micro:', array('file'=>%node.word)))");
	}

	/**
	 * {image ...}
	 * {img ...}
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
		if (!isset($data[2])) {
			$data['2'] = "'jpg'";
			if (\Nette\Utils\Strings::startsWith($image, '$')) {
				$data['2'] = $writer->formatWord($image) . " instanceof \\Nella\\NetteAddons\\Media\\IImage ? \$image->getImageType() : 'jpg'";
			}
		}

		return $writer->write("echo %escape(\$_presenter->link(':Nette:Micro:', array('image'=>"
			 . $writer->formatWord($image) . ",'format'=>" . $writer->formatWord($format) . ",'type'=>"
			 . $writer->formatWord($data[2]) . ")))");
	}
}