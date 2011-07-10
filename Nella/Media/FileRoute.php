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
	 * @param int
	 * @return int
	 */
	protected function loadFile($id)
	{
		$file = $this->container->getService('Nella\Media\FileEntity')->repository->find($id);
		return $file ? $id : NULL;
	}

	/**
	 * @param string  URL mask, e.g. '<presenter>/<action>/<id \d{1,3}>'
	 * @param rray|string   default values or metadata
	 * @param int     flags
	 * @param \Nella\Doctrine\Container
	 */
	public function __construct($mask, $metadata = array(), $flags = 0, \Nella\Doctrine\Container $container)
	{
		$this->container = $container;
		
		// String to array conversion
		if (is_string($metadata)) {
			$a = strrpos($metadata, ':');
			if (!$a) {
				throw new Nette\InvalidArgumentException("Second argument must be array or string in format Presenter:action, '$metadata' given.");
			}
			$metadata = array(
				static::PRESENTER_KEY => substr($metadata, 0, $a),
				'action' => $a === strlen($metadata) - 1 ? \Nette\Application\UI\Presenter::DEFAULT_ACTION : substr($metadata, $a + 1),
			);
		}
		
		$metadata[static::FILE_KEY] = array(
			static::VALUE => isset($metadata[static::FILE_KEY]) && is_string($metadata[static::FILE_KEY])
				? $metadata[static::FILE_KEY] : NULL, 
			static::FILTER_IN => 'rawurldecode', 
			static::FILTER_OUT => 'rawurlencode', 
			static::PATTERN => '[0-9]+', 
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
		$params[static::FILE_KEY] = $this->loadFile($params[static::FILE_KEY]);
		if (!$params[static::FILE_KEY]) {
			return NULL;
		}

		$presenterRequest->params = $params;
		return $presenterRequest;
	}
}