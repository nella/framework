<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Validator\MetadataParsers;

use Nella\Validator\Validator,
	Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Doctrine entity metadata parser
 *
 * @author	Patrik Votoček
 */
class DoctrineEntity extends \Nette\Object implements \Nella\Validator\IMetadataParser
{
	/** @var \Nella\Doctrine\Container */
	private $container;

	public function __construct(\Nella\Doctrine\Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param \Nella\Validator\ClassMetadata
	 */
	public function parse(\Nella\Validator\ClassMetadata $metadata)
	{
		$ref = $metadata->getClassReflection();
		if ($ref->implementsInterface('Nella\Models\IEntity')
			 && ($ref->hasAnnotation('entity') || $ref->hasAnnotation('mappedSuperClass'))) {
			$emeta = $this->container->getEntityManager()->getClassMetadata($ref->getName());
			foreach ($emeta->fieldMappings as $field) {
				if ($field['declared'] != $ref->getName()) {
					continue;
				}

				switch ($field['type']) {
					case 'integer':
					case 'smallint':
					case 'bigint':
						$metadata->addRule($field['fieldName'], Validator::TYPE, 'int');
						break;
					case 'decimal':
					case 'float':
						$metadata->addRule($field['fieldName'], Validator::TYPE, 'double');
						break;
					case 'boolean':
					case 'object':
					case 'array':
						$metadata->addRule($field['fieldName'], Validator::TYPE, $field['type']);
						break;
					case 'string':
						$metadata->addRule($field['fieldName'], Validator::TYPE, $field['type']);
						$metadata->addRule($field['fieldName'], Validator::MAX_LENGTH, $field['length']);
						break;
					case 'date':
					case 'time':
					case 'datetime':
					case 'datetimetz':
						$metadata->addRule($field['fieldName'], Validator::INSTANCE, 'DateTime');
						break;
					case 'text':
						$metadata->addRule($field['fieldName'], Validator::TYPE, 'string');
						break;
				}

				if (!isset($field['id']) || !$field['id']) {
					$metadata->addRule($field['fieldName'], $field['nullable'] ? Validator::NULLABLE : Validator::NOTNULL);
				} else {
					$metadata->addRule($field['fieldName'], Validator::NULLABLE);
				}
			}

			foreach ($emeta->associationMappings as $field) {
				if ($field['type'] == ClassMetadata::ONE_TO_ONE || $field['type'] == ClassMetadata::MANY_TO_ONE) {
					$metadata->addRule($field['fieldName'], Validator::INSTANCE, $field['targetEntity']);
					$metadata->addRule($field['fieldName'],
						$field['joinColumns'][0]['nullable'] ? Validator::NULLABLE : Validator::NOTNULL
					);
				}
			}
		}
	}
}
