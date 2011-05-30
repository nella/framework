<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine\Listeners;

/**
 * Validator listener
 *
 * @author	Patrik Votoček
 */
class Validator extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/** @var \Nella\Validator\IValidator */
	private $validator;

	/**
	 * @param \Nella\Validator\IValidator
	 */
	public function __construct(\Nella\Validator\IValidator $validator)
	{
		$this->validator = $validator;
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents()
    {
        return array(
        	\Doctrine\ORM\Events::onFlush,
        );
    }

    /**
     * @param BaseEntity
     * @throws NotValidEntityException
     */
    protected function validate($entity)
    {
    	$class = get_class($entity);
    	$errors = $this->validator->validate($entity);

    	if (count($errors)) {
			throw new \Nella\Models\NotValidEntityException("Entity $class is not valid", $errors);
    	}
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args
     * @throws NotValidEntityException
     */
    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $args)
    {
		$em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            $this->validate($entity);
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            $this->validate($entity);
        }
    }
}
