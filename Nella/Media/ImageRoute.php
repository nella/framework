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
	 * @param \Nella\Doctrine\Container
	 */
	public function setContainer(\Nella\Doctrine\Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @return \Nella\Models\Service
	 */
	protected function getFormatService()
	{
		return $this->container->getService('Nella\Media\FormatEntity');
	}

	/**
	 * @return \Nella\Models\Service
	 */
	protected function getImageService()
	{
		return $this->container->getService('Nella\Media\ImageEntity');
	}

	/**
	 * @param int
	 * @return \Nella\Models\FormatEntity
	 */
	protected function getFormat($id)
	{
		return $this->getFormatService()->repository->find($id);
	}

	/**
	 * @param int
	 * @return \Nella\Models\ImageEntity
	 */
	protected function getImage($id)
	{
		return $this->getImageService()->repository->find($id);
	}

	/**
	 * @param string  URL mask, e.g. '<presenter>/<action>/<id \d{1,3}>'
	 * @param array|string   default values or metadata
	 * @param int     flags
	 */
	public function __construct($mask, $metadata = array(), $flags = 0)
	{
		parent::$styles[static::FORMAT_KEY] = parent::$styles[static::IMAGE_KEY] = array(
			'pattern'	=> '[0-9]+',
			static::FILTER_IN => 'rawurldecode',
			static::FILTER_OUT => 'rawurlencode',
		);
		parent::$styles[static::TYPE_KEY] = array(
			'pattern'	=> '(jpg|png|gif)',
			static::FILTER_IN => 'rawurldecode',
			static::FILTER_OUT => 'rawurlencode',
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
			throw new \Nette\InvalidStateException('Missing id in route definition.');
		}

		// Find image
		$image = $this->getImage($params[static::IMAGE_KEY]);
		if (!$image) {
			return NULL;
		}
		$params[static::IMAGE_KEY] = $image;

		// Find format
		$format = $this->getFormat($params[static::FORMAT_KEY]);
		if (!$format) {
			return NULL;
		}
		$params[static::FORMAT_KEY] = $format;

		// Set path parameter
		$params[static::PATH_PARAMETER] = $httpRequest->url->path;

		$presenterRequest->params = $params;
		return $presenterRequest;
	}
}
