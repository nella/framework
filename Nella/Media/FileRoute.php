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
 *
 * @property-write \Nella\Doctrine\Container
 */
class FileRoute extends \Nette\Application\Routers\Route
{
	const FILE_KEY = 'file';

	/** @var \Nella\Doctrine\Container */
	private $container;

	/**
	 * @param \Nella\Doctrine\Container
	 */
	public function setContainer(\Nella\Doctrine\Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param int
	 * @return FileEntity
	 */
	protected function getFile($id)
	{
		return $this->container->getService('Nella\Media\FileEntity')->repository->find($id);
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
			static::FILTER_IN => 'rawurldecode',
			static::FILTER_OUT => 'rawurlencode',
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
		if (!isset($params[static::FILE_KEY])) {
			throw new \Nette\InvalidStateException('Missing file in route definition.');
		}

		// Find file
		$file = $this->getFile($params[static::FILE_KEY]);
		if (!$file) {
			return NULL;
		}
		$params[static::FILE_KEY] = $file;

		$presenterRequest->params = $params;
		return $presenterRequest;
	}
}