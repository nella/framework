<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Media;

/**
 * File route
 *
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 */
class FileRoute extends \Nette\Application\Route
{
	const FILE_KEY = 'file';

	/** @var \Doctrine\ORM\EntityManager */
	private $em;

	/** @var \Nella\Models\Service */
	private $service;

	/**
	 * @param \Doctrine\ORM\EntityManager
	 * @return FileRoute	 
	 */
	public function setEntityManager(\Doctrine\ORM\EntityManager $em)
	{
		$this->em = $em;
		return $this;
	}

	/**
	 * @return \Nella\Models\Service
	 */
	protected function getService()
	{
		if (!$this->service) {
			$this->service = new \Nella\Models\Service($this->em, 'Nella\Media\FileEntity');
		}

		return $this->service;
	}

	/**
	 * @param int
	 * @return \Nella\Media\FileEntity
	 */
	protected function getFile($id)
	{
		return $this->getService()->repository->find($id);
	}

	/**
	 * @param  string  URL mask, e.g. '<presenter>/<action>/<id \d{1,3}>'
	 * @param  array|string   default values or metadata
	 * @param  int     flags
	 */
	public function __construct($mask, $metadata = array(), $flags = 0)
	{
		parent::$styles[self::FILE_KEY] = array(
			'pattern'	=> '[0-9]+',
			self::FILTER_IN => 'rawurldecode',
			self::FILTER_OUT => 'rawurlencode',
		);

		parent::__construct($mask, $metadata, $flags);
	}

	/**
	 * Maps HTTP request to a PresenterRequest object.
	 * @param  Nette\Web\IHttpRequest
	 * @return PresenterRequest|NULL
	 */
	public function match(\Nette\Web\IHttpRequest $httpRequest)
	{
		if (($presenterRequest = parent::match($httpRequest)) === NULL) {
			return NULL;
		}

		$params = $presenterRequest->params;
		if (!isset($params[self::FILE_KEY])) {
			throw new \InvalidStateException('Missing file in route definition.');
		}

		// Find file
		$file = $this->getFile($params[self::FILE_KEY]);
		if (!$file) {
			return NULL;
		}
		$params[self::FILE_KEY] = $file;

		$presenterRequest->params = $params;
		return $presenterRequest;
	}
}