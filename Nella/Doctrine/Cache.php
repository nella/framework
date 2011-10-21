<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine;

/**
 * Nette cache driver for doctrine
 *
 * @author	Patrik Votoček
 */
class Cache extends \Doctrine\Common\Cache\AbstractCache
{
	/** @var \Nette\Caching\Cache */
	private $data = array();

	/**
	 * @param \Nette\Caching\IStorage
	 */
	public function  __construct(\Nette\Caching\IStorage $cacheStorage)
	{
		$this->data = new \Nette\Caching\Cache($cacheStorage, "Nella.Doctrine");
	}
	
	/**
	 * @param string
	 */
	protected function addId($id)
	{
		$ids = $this->data->load('__ids') ?: array();
		$ids[] = $id;
		$this->data->save('__ids', $ids, array('expire' => "+ 10 years"));
	}
	
	/**
	 * @param string
	 */
	protected function removeId($id)
	{
		$ids = $this->data->load('__ids') ?: array();
		$key = array_search($id, $ids);
		if ($key) {
			unset($ids[$key]);
			$this->data->save('__ids', $ids, array('expire' => "+ 10 years"));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIds()
	{
		return $this->data->load('__ids') ?: array();
	}

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        $data = $this->data->load($id);
		return $data ?: FALSE;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        return $this->data->load($id) !== NULL;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
		$files = array();
		if ($data instanceof \Doctrine\ORM\Mapping\ClassMetadata) {
			$ref = \Nette\Reflection\ClassType::from($data->name);
			$files[] = $ref->getFileName();
			foreach ($data->parentClasses as $class) {
				$ref = \Nette\Reflection\ClassType::from($class);
				$files[] = $ref->getFileName();
			}
		}

		if ($lifeTime != 0) {
			$this->data->save($id, $data, array('expire' => time() + $lifeTime, 'tags' => array("doctrine"), 'files' => $files));
		} else {
			$this->data->save($id, $data, array('tags' => array("doctrine"), 'files' => $files));
		}
		
		$this->addId($id);

		return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        $this->data->save($id, NULL);
		$this->removeId($id);
        return TRUE;
    }
}
