<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Validator\MetadataParsers;

/**
 * Annotation validation rules parser
 *
 * @author	Patrik Votoček
 */
class Annotation extends \Nette\Object implements \Nella\Validator\IMetadataParser
{
	/**
	 * @param \Nella\Validator\ClassMetadata
	 */
	public function parse(\Nella\Validator\ClassMetadata $metadata)
	{
		$reflection = $metadata->getClassReflection();

		foreach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation('validate')) {
				$rules = (array) $property->getAnnotation('validate');
				foreach ($rules as $key => $value) {
					if (is_int($key)) {
						$key = $value;
						$value = NULL;
					}

					$metadata->addRule($property->getName(), $key, $value);
				}
			}
		}
	}
}