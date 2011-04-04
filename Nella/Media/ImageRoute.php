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
 */
class ImageRoute extends \Nette\Application\Route
{
	const FORMAT_KEY = 'format';
	const IMAGE_KEY = 'image';
	const TYPE_KEY = 'type';
	const PATH_PARAMETER = 'path';

	/** @var \Doctrine\ORM\EntityManager */
	private $em;

	/** @var \Nella\Models\Service */
	private $formatService;

	/** @var \Nella\Models\Service */
	private $imageService;
	
	/**
	 * @param \Doctrine\ORM\EntityManager
	 * @return ImageRoute	 
	 */
	public function setEntityManager(\Doctrine\ORM\EntityManager $em)
	{
		$this->em = $em;
		return $this;
	}

	/**
	 * @return \Nella\Models\Service
	 */
	protected function getFormatService()
	{
		if (!$this->formatService) {
			$this->formatService = new \Nella\Models\Service($this->em, 'Nella\Media\FormatEntity');
		}

		return $this->formatService;
	}

	/**
	 * @return \Nella\Models\Service
	 */
	protected function getImageService()
	{
		if (!$this->imageService) {
			$this->imageService = new \Nella\Models\Service($this->em, 'Nella\Media\ImageEntity');
		}

		return $this->imageService;
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
	 * @param  string  URL mask, e.g. '<presenter>/<action>/<id \d{1,3}>'
	 * @param  array|string   default values or metadata
	 * @param  int     flags
	 */
	public function __construct($mask, $metadata = array(), $flags = 0)
	{
		parent::$styles[self::FORMAT_KEY] = parent::$styles[self::IMAGE_KEY] = array(
			'pattern'	=> '[0-9]+',
			self::FILTER_IN => 'rawurldecode',
			self::FILTER_OUT => 'rawurlencode',
		);
		parent::$styles[self::TYPE_KEY] = array(
			'pattern'	=> '(jpg|png|gif)',
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
		if (!isset($params[self::FORMAT_KEY])) {
			throw new \InvalidStateException('Missing format in route definition.');
		}
		if (!isset($params[self::IMAGE_KEY])) {
			throw new \InvalidStateException('Missing id in route definition.');
		}

		// Find image
		$image = $this->getImage($params[self::IMAGE_KEY]);
		if (!$image) {
			return NULL;
		}
		$params[self::IMAGE_KEY] = $image;

		// Find format
		$format = $this->getFormat($params[self::FORMAT_KEY]);
		if (!$format) {
			return NULL;
		}
		$params[self::FORMAT_KEY] = $format;

		// Set path parameter
		$params[self::PATH_PARAMETER] = $httpRequest->uri->path;

		$presenterRequest->params = $params;
		return $presenterRequest;
	}
}
