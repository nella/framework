<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Validator;

/**
 * Annotation validation rules parser
 *
 * @author	Patrik Votoček
 */
class AnnotationParser extends \Nette\Object implements IMetadataParser
{
	/**
	 * @param ClassMetadata
	 */
	public function parse(ClassMetadata $metadata)
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