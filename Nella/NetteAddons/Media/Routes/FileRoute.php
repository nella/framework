<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\NetteAddons\Media\Routes;

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
	/** @var \Nette\Application\Routes\Route */
	private $route;

	/**
	 * @param string example '/some/<file>.<ext>'
	 * @param \Nella\NetteAddons\Media\Model\IFileDao
	 * @param \Nella\Addons\Media\IFilePresenterCallback
	 * @param string
	 */
	public function __construct($mask, \Nella\NetteAddons\Media\Model\IFileDao $model, \Nella\NetteAddons\Media\IFilePresenterCallback $callback, $fullSlugMask = "<file>_<ext>")
	{
		$this->route = new \Nette\Application\Routers\Route($mask, function($file, $ext) use($model, $callback, $fullSlugMask) {
			$fullSlug = str_replace(array('<file>', '<ext>'), array($file, $ext), $fullSlugMask);
			$fileEntity = $model->findOneByFullSlug($fullSlug);

			if (!$fileEntity) {
				throw new \Nette\Application\BadRequestException("Invalid file '$file.$ext' does not found");
			}

			return callback($callback)->invoke($fileEntity);
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
	public function constructUrl(\Nette\Application\Request $appRequest, \Nette\Http\Url $refUrl)
	{
		return $this->route->constructUrl($appRequest, $refUrl);
	}
}