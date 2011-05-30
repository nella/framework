<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine\Mapping;

/**
 * @author	Pavel Kučera
 */
class ClassMetadata extends \Doctrine\ORM\Mapping\ClassMetadata
{
	/** @var string */
	public $customRepositoryClassName = 'Nella\Doctrine\Repository';

	/** @var string */
	public $serviceClassName = 'Nella\Doctrine\Service';

	/**
     * Registers a service class for the entity class.
     *
     * @param string
     */
    public function setServiceClass($serviceClassName)
    {
        $this->serviceClassName = $serviceClassName;
    }

	/**
     * Determines which fields get serialized.
     *
     * It is only serialized what is necessary for best unserialization performance.
     * That means any metadata properties that are not set or empty or simply have
     * their default value are NOT serialized.
     *
     * Parts that are also NOT serialized because they can not be properly unserialized:
     *      - reflClass (ReflectionClass)
     *      - reflFields (ReflectionProperty array)
     *
     * @return array The names of all the fields that should be serialized.
     */
	public function __sleep()
	{
		$serialized = parent::__sleep();
		$serialized[] = 'serviceClassName';

		return $serialized;
	}
}