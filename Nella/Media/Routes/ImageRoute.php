<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Media\Routes;

use Nette\Utils\Strings,
	Nette\Http\Url,
	Nella\Media\Model\IImageDao,
	Nella\Media\Model\IImageFormatDao,
	Nella\Media\IImagePresenterCallback,
	Nette\Application\Routers\Route,
	Nette\Http\IRequest,
	Nette\Application\Request;

/**
 * Image route
 *
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 */
class ImageRoute extends \Nette\Object implements \Nette\Application\IRouter
{
	/** @var \Nette\Application\Routers\Route */
	private $route;

	/**
	 * @param string example '/images/<format>/<image>.<type>'
	 * @param \Nette\Media\Model\IImageDao
	 * @param \Nette\Media\Model\IImageFormatDao
	 * @param \Nella\Media\IImagePresenterCallback
	 * @param string example '<image>_<type>'
	 */
	public function __construct($mask, IImageDao $imageModel, IImageFormatDao $formatModel, IImagePresenterCallback $callback, $imageMask = '<image>_<type>')
	{
		$this->route = new Route($mask, function ($image, $format, $type) use ($imageModel, $formatModel, $callback, $imageMask) {
			$formatEntity = $formatModel->findOneByFullSlug($format);

			if (!$formatEntity) {
				throw new \Nette\Application\BadRequestException("Invalid format '$format' does not found");
			}

			$fullSlug = str_replace(array('<image>', '<type>'), array($image, $type), $imageMask);
			$imageEntity = $imageModel->findOneByFullSlug($fullSlug);

			if (!$imageEntity) {
				throw new \Nette\Application\BadRequestException("Invalid format '$image.$type' does not found");
			}

			return callback($callback)->invoke($imageEntity, $formatEntity, $type);
		});
	}

	/**
	 * Maps HTTP request to a PresenterRequest object.
	 *
	 * @param \Nette\Http\IRequest
	 * @return \Nette\Application\Request|NULL
	 * @throws \Nette\InvalidStateException
	 */
	public function match(IRequest $httpRequest)
	{
		return $this->route->match($httpRequest);
	}

	/**
	 * Constructs absolute URL from Request object.
	 *
	 * @param  \Nette\Application\Request
	 * @param  \Nette\Http\Url referential URI
	 * @return string|NULL
	 */
	public function constructUrl(Request $appRequest, Url $refUrl)
	{
		$url = $this->route->constructUrl($appRequest, $refUrl);
		if ($url != NULL) {
			if (is_string($url)) {
				$url = new Url($url);
			}
			$url->setQuery('')->canonicalize();
		}
		return $url;
	}
}

