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
 * Media presenter
 *
 * @author	Patrik Votoček
 */
class MediaPresenter extends \Nella\Application\UI\SimplePresenter
{
	/**
	 * @param \Nette\Application\Request
	 * @return \Nette\Application\IResponse
	 */
	public function run(\Nette\Application\Request $request)
	{
		$params = $request->params;
		if (isset($params['file'])) {
			callback($this, 'actionFile')->invokeArgs(array('file' => $params['file']));
		} elseif (isset($params['image']) && isset($params['format'])) {
			callback($this, 'actionImage')->invokeArgs(array(
				'image' => $params['image'],
				'format' => $params['format'],
				'path' => $params['path'],
				'type' => isset($params['type']) ? $params['type'] : NULL
			));
		} else {
			return parent::run($request);
		}
	}

	/**
	 * @param IFile
	 */
	public function actionFile(IFile $file)
	{
		$this->sendResponse(new \Nette\Application\Responses\FileResponse(
			$file->getFileinfo()->getRealPath(), //$file->getContent(),
			$file->getFilename(),
			$file->getMimeType()
		));
		$this->terminate();
	}

	/**
	 * @param IImage
	 * @param IFormat
	 * @param string
	 * @param int
	 */
	public function actionImage(IImage $image, IFormat $format, $path, $type = NULL)
	{
		if ($format) {
			$image = $format->process($image);
		} else {
			$image = $image->toImage();
		}

		$path = $this->getContext()->params['wwwDir'] . $path;
		$dir = pathinfo($path, PATHINFO_DIRNAME);
		if (!file_exists($dir)) {
			mkdir($dir, 0777, TRUE);
		}

		$image->save($path);
		if (!$type) {
			$image->send();
		} else {
			$image->send($type);
		}

		$this->terminate();
	}
}
