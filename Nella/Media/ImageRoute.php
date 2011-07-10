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
 * Image route
 *
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 *
 * @property-write \Nella\Doctrine\Container
 */
class ImageRoute extends \Nette\Application\Routers\Route
{
	const FORMAT_KEY = 'format';
	const IMAGE_KEY = 'image';
	const TYPE_KEY = 'type';
	const PATH_PARAMETER = 'path';

	/** @var \Nella\Doctrine\Container */
	private $container;

	/**
	 * @param string
	 * @return int
	 */
	protected function loadFormat($slug)
	{
		$service = $this->container->getService('Nella\Media\FormatEntity');
		return $service->repository->fetchIdBySlug($slug);
	}

	/**
	 * @param string
	 * @return int
	 */
	protected function loadImage($slug)
	{
		$service = $this->container->getService('Nella\Media\ImageEntity');
		return $service->repository->fetchIdBySlug($slug);
	}

	/**
	 * @param string  URL mask, e.g. '<presenter>/<action>/<id \d{1,3}>'
	 * @param array|string   default values or metadata
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
		
		$metadata[static::IMAGE_KEY] = array(
			static::VALUE => isset($metadata[static::IMAGE_KEY]) && is_string($metadata[static::IMAGE_KEY])
				? $metadata[static::IMAGE_KEY] : NULL, 
			static::FILTER_IN => 'rawurldecode', 
			static::FILTER_OUT => 'rawurlencode', 
			static::PATTERN => '[0-9a-z-]+', 
		);
		
		$metadata[static::FORMAT_KEY] = array(
			static::VALUE => isset($metadata[static::FORMAT_KEY]) && is_string($metadata[static::FORMAT_KEY])
				? $metadata[static::FORMAT_KEY] : NULL, 
			static::FILTER_IN => 'rawurldecode', 
			static::FILTER_OUT => 'rawurlencode', 
			static::PATTERN => '[0-9a-z-]+', 
		);
		
		$metadata[static::TYPE_KEY] = array(
			static::VALUE => isset($metadata[static::TYPE_KEY]) && is_string($metadata[static::TYPE_KEY])
				? $metadata[static::TYPE_KEY] : NULL, 
			static::FILTER_IN => 'rawurldecode', 
			static::FILTER_OUT => 'rawurlencode', 
			static::PATTERN => '(jpg|png|gif)', 
		);

		parent::__construct($mask, $metadata, $flags);
	}

	/**
	 * Maps HTTP request to a PresenterRequest object.
	 * @param \Nette\Http\IRequest
	 * @return \Nette\Application\Request|NULL
	 * @throws \Nette\InvalidStateException
	 */
	public function match(\Nette\Http\IRequest $httpRequest)
	{
		if (($presenterRequest = parent::match($httpRequest)) === NULL) {
			return NULL;
		}

		$params = $presenterRequest->params;
		if (!isset($params[static::FORMAT_KEY])) {
			throw new \Nette\InvalidStateException('Missing format in route definition.');
		}
		if (!isset($params[static::IMAGE_KEY])) {
			throw new \Nette\InvalidStateException('Missing image in route definition.');
		}
		if (!isset($params[static::TYPE_KEY])) {
			throw new \Nette\InvalidStateException('Missing type in route definition.');
		}

		// Find image
		$params[static::IMAGE_KEY] = $this->loadImage($params[static::IMAGE_KEY]);
		// Find format
		$params[static::FORMAT_KEY] = $this->loadFormat($params[static::FORMAT_KEY]);
		
		// Invalid format / image
		if (!$params[static::FORMAT_KEY] || !$params[static::IMAGE_KEY]) {
			return NULL;
		}

		// Set path parameter
		$params[static::PATH_PARAMETER] = $httpRequest->url->path;

		$presenterRequest->params = $params;
		return $presenterRequest;
	}
}
