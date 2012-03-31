<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\NetteAddons\Media\Routes;

use Nette\Utils\Strings,
	Nette\Http\Url;

/**
 * Image route
 *
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 */
class ImageRoute extends \Nette\Object implements \Nette\Application\IRouter
{
	/** @var string */
	private $route;

	/**
	 * @param string example '/images/<format>/<image>.<type>'
	 * @param \Nella\Addons\Media\IImagePresenterCallback
	 * @param \Nette\DI\Container
	 * @param string
	 */
	public function __construct($mask, \Nella\NetteAddons\Media\Model\IImageDao $imageModel, \Nella\NetteAddons\Media\Model\IImageFormatDao $formatModel, \Nella\NetteAddons\Media\IImagePresenterCallback $callback, $imageMask = '<image>_<type>')
	{
		$this->route = new \Nette\Application\Routers\Route($mask, function($image, $format, $type) use($imageModel, $formatModel, $callback, $imageMask) {
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
	public function match(\Nette\Http\IRequest $httpRequest)
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
	public function constructUrl(\Nette\Application\Request $appRequest, Url $refUrl)
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
