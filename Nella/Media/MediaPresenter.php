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
class MediaPresenter extends \Nella\Application\Presenter
{
	/**
	 * @param IFile
	 */
	public function actionFile(IFile $file)
	{
		$this->sendResponse(new \Nette\Application\DownloadResponse(
			$file->getContent(), 
			$file->getFilename(), 
			$file->getMimeType()
		));
		$this->terminate();
	}
	
	/**
	 * @param IImage
	 * @param IFormat
	 */
	public function actionImage(IImage $image, IFormat $format = NULL)
	{
		if ($format) {
			$image = $format->process($image);
		} else {
			$image = $image->toImage();
		}
		
		// @todo cache
		$image->send();
		$this->terminate();
	}
}
