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
class FileRoute extends \Nette\Application\Routers\Route
{
	const FILE_KEY = 'file';

	/** @var \Nette\DI\IContainer */
	private $container;
	
	/**
	 * @param \Nette\DI\IContainer
	 */
	public function setContainer(\Nette\DI\IContainer $container)
	{
		$this->container = $container;
	}
	
	/**
	 * @return \Nella\Doctrine\Container
	 */
	protected function getEntityContainer()
	{
		return $this->container->getService('doctrineContainer');
	}
	
	/**
	 * @return \Nella\Models\Service
	 */
	protected function getService()
	{
		return $this->getEntityContainer()->getEntityService('Nella\Media\FileEntity');
	}

	/**
	 * @param int
	 * @return FileEntity
	 */
	protected function getFile($id)
	{
		return $this->getService()->repository->find($id);
	}

	/**
	 * @param string  URL mask, e.g. '<presenter>/<action>/<id \d{1,3}>'
	 * @param rray|string   default values or metadata
	 * @param int     flags
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
	 * @param  Nette\Http\IRequest
	 * @return \Nette\Application\Request|NULL
	 * @throws \Nette\InvalidStateException
	 */
	public function match(\Nette\Http\IRequest $httpRequest)
	{
		if (($presenterRequest = parent::match($httpRequest)) === NULL) {
			return NULL;
		}

		$params = $presenterRequest->params;
		if (!isset($params[self::FILE_KEY])) {
			throw new \Nette\InvalidStateException('Missing file in route definition.');
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