<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine\Mapping\Driver;

/**
 * @author	Pavel Kučera
 */
class AnnotationDriver extends \Doctrine\ORM\Mapping\Driver\AnnotationDriver
{
	/**
	 * @param string
	 * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata
	 */
	public function loadMetadataForClass($className, \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata)
	{
		parent::loadMetadataForClass($className, $metadata);

		if ($metadata instanceof \Nella\Doctrine\Mapping\ClassMetadata) {
			$class = \Nette\Reflection\ClassType::from($className);
			if ($class->hasAnnotation('service')) {
				$service = $class->getAnnotation('service');
				if (!isset($service['class'])) {
					throw new \Doctrine\ORM\Mapping\MappingException("Missing service class.");
				}

				$metadata->setServiceClass($service['class']);
			}
		}
	}
}