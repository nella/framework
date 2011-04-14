<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Latte;

/**
 * Nella macros
 *
 * @author	Patrik Votoček
 */
class Macros extends \Nette\Latte\DefaultMacros
{
	/**
	 * {image ...}
	 * 
	 * @param string
	 * @param mixed
	 * @return string
	 */
	public function macroImage($content, $modifiers)
	{
		$out = ':Media:Media:image image => ?1, format => ?2, type => ?3';
		
		$data = explode(', ', $content);
		list($image, $format) = $data;
		
		$out = str_replace("?1", $image . ' instanceof \Nella\Media\ImageEntity ? ' . $image . '->id : ' . $image, $out);
		$out = str_replace("?2", $format . ' instanceof \Nella\Media\FormatEntity ? ' . $format . '->id : ' . $format, $out);
		
		if (count($data) < 3) {
			$out = str_replace("?3", $image . ' instanceof \Nella\Media\ImageEntity ? ' . $image . '->type : "jpg"', $out);
		} else {
			$out = str_replace("?3", $data[2], $out);
		}
		
		return $this->macroPlink($out, $modifiers);
	}
	
	/**
	 * {file ...}
	 * 
	 * @param string
	 * @param mixed
	 * @return string
	 */
	public function macroFile($content, $modifiers)
	{
		$out = ':Media:Media:file file => ';
		$out .= $content . ' instanceof \Nella\Media\FileEntity ? ' . $content . '->id : ' . $content;
		
		return $this->macroPlink($out, $modifiers);
	}
}

Macros::$defaultMacros['@phref'] = ' href="<?php echo %:escape%(%:macroPlink%) ?>"';

Macros::$defaultMacros['image'] = '<?php echo %:escape%(%:macroImage%) ?>';
Macros::$defaultMacros['@src'] = ' src="<?php echo %:escape%(%:macroImage%) ?>"';
Macros::$defaultMacros['@ihref'] = ' href="<?php echo %:escape%(%:macroImage%) ?>"';

Macros::$defaultMacros['file'] = '<?php echo %:escape%(%:macroFile%) ?>';
Macros::$defaultMacros['@fhref'] = ' href="<?php echo %:escape%(%:macroFile%) ?>"';
