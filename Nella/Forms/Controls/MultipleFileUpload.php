<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Forms\Controls;

use Nette\Application\UI\Form,
	Nette\Http\FileUpload,
	Nette\Utils\Finder,
	Nette\Utils\Strings,
	Nette\Http\IRequest,
	Nette\Diagnostics\Debugger,
	Nette\Utils\Html;

/**
 * Text box and browse button that allow users to select a multiple files to upload to the server.
 *
 * @author	Patrik Votoček
 */
class MultipleFileUpload extends \Nette\Forms\Controls\BaseControl
{
	const MULTIPLE_FILE_UPLOAD_KEY = '__mfutoken';
	/** @var \Nette\Callback */
	protected static $detector;
	/** @var string */
	protected static $storageDir;

	/**
	 * @param \Nette\Http\IRequest
	 * @param string	temp storage dir
	 * @param callable   token detector callback ($httpRequest)
	 * @param int
	 * @throws \Nette\InvalidStateException
	 */
	public static function register(IRequest $httpRequest, $storageDir, $detector = NULL, $expire = 3600)
	{
		if (static::$detector) {
			throw new \Nette\InvalidStateException('Multiple file uploader allready registered');
		} elseif ((!file_exists($storageDir) || !is_writable($storageDir)) && !@mkdir($storageDir, 0777, TRUE)) {
			throw new \Nette\InvalidStateException("Storage dir '$storageDir' must be writable");
		}

		static::$storageDir = $storageDir;
		static::$detector = callback($detector ?: function (IRequest $httpRequest) {
			if ($httpRequest->getHeader('X-Nella-MFU-Token') && $httpRequest->getHeader('X-Uploader')) {
				return $httpRequest->getHeader('X-Nella-MFU-Token');
			}

			return FALSE;
		});

		// process uploaded file
		if ($token = static::$detector->invoke($httpRequest)) {
			$files = $httpRequest->files;
			if (isset($files['file']) && $files['file']->ok) {
				$path = static::$storageDir . '/';
				$path .= $token . '_' . time() . '_' . Strings::random(16);
				$path .= '.' . pathinfo($files['file']->name, PATHINFO_EXTENSION);
				$path .= '.tmp';

				$files['file']->move($path);
			}

			echo '{success:true}';
			Debugger::$bar = NULL;
			exit;
		}

		// clean expired files
		$files = Finder::findFiles('*.tmp')->from(static::$storageDir);
		$expire = time() + $expire;
		foreach ($files as $file) {
			if ($file->getMTime() > $expire) {
				@unlink($file->getRealPath()); // prevents error
			}
		}
	}

	/**
	 * @param string
	 */
	public function __construct($label = NULL)
	{
		parent::__construct($label);
		$this->control->type = 'file';
		$this->control->multiple = TRUE;
	}

	/**
	 * This method will be called when the component (or component's parent) becomes attached to a monitored object.
	 * Do not call this method yourself.
	 *
	 * @param \Nette\ComponentModel\IComponent
	 */
	protected function attached($parent)
	{
		parent::attached($parent);
		if ($parent instanceof Form) {
			if ($parent->getMethod() !== Form::POST) {
				throw new \Nette\InvalidStateException('File upload requires method POST.');
			}
			$parent->getElementPrototype()->enctype = 'multipart/form-data';
		}
	}

	/**
	 * Generates control's HTML element.
	 *
	 * @return \Nette\Utils\Html
	 */
	public function getControl()
	{
		$control = parent::getControl();
		$control->name .= '[]';

		$token = Strings::random(16);

		if (static::$detector) {
			$control->data('nella-mfu-token', $token);
		}

		$control .= Html::el('input')
			->type('hidden')
			->name($this->name . static::MULTIPLE_FILE_UPLOAD_KEY)
			->value($token);

		return $control;
	}

	/**
	 * Sets control's value.
	 *
	 * @param  array|\Nette\Http\FileUpload
	 * @return MultipleFileUpload  provides a fluent interface
	 */
	public function setValue($value)
	{
		if (is_array($value) && is_array(reset($value))) {
			$this->value = array();
			foreach ($value as $key => $file) {
				$this->value[$key] = new FileUpload($file);
			}
		} elseif (is_array($value) && reset($value) instanceof FileUpload) {
			$this->value = $value;
		} else {
			$presenter = $this->getForm()->getPresenter(TRUE);
			if (!$presenter) {
				throw new \Nette\InvalidStateException('Form must be attached to presenter');
			}
			$post = $presenter->getRequest()->getPost();
			$key = $this->name . static::MULTIPLE_FILE_UPLOAD_KEY;
			$token = isset($post[$key]) ? $post[$key] : NULL;
			if ($token) {
				$this->value = array();
				$files = Finder::findFiles($token . '_*.tmp')->from(static::$storageDir);
				foreach ($files as $file) {
					$this->value[] = new FileUpload(array(
						'error' => UPLOAD_ERR_OK,
						'name' => substr($file->getBaseName(), 0, -4),
						'tmp_name' => $file->getRealPath(),
						'size' => $file->getSize(),
						'type' => $file->getType(),
					));
				}
			}
		}

		if (count($this->value) == 1 && !reset($this->value)->temporaryFile) {
			$this->value = array();
		}

		return $this;
	}

	/**
	 * Filled validator: has been any filed?
	 *
	 * @param  \Nette\Forms\IControl
	 * @return bool
	 */
	public static function validateFilled(\Nette\Forms\IControl $control)
	{
		return (bool) count(array_filter(
			$control->getValue(), function ($file) {
				return $file instanceof FileUpload && $file->isOK();
			}
		));
	}

	/**
	 * FileSize validator: is file size in limit?
	 *
	 * @param MultipleFileUpload
	 * @param int file size limit
	 * @return bool
	 */
	public static function validateFileSize(MultipleFileUpload $control, $limit)
	{
		$isFiles = (bool) count(array_filter(
			$control->getValue(), function ($file) {
				return $file instanceof FileUpload;
			}
		));

		$size = array_sum(array_map(function ($file) {
			return $file->getSize();
		}, $control->getValue()));

		return $isFiles && $size <= $limit;
	}

	/**
	 * MimeType validator: has file specified mime type?
	 *
	 * @param MultipleFileUpload
	 * @param array|string mime type
	 * @return bool
	 */
	public static function validateMimeType(MultipleFileUpload $control, $mimeType)
	{
		return (bool) count(array_filter(
			$control->getValue(), function ($file) use ($mimeType) {
				if ($file instanceof FileUpload) {
					$type = strtolower($file->getContentType());
					$mimeTypes = is_array($mimeType) ? $mimeType : explode(',', $mimeType);
					if (in_array($type, $mimeTypes, TRUE)) {
						return TRUE;
					}
					if (in_array(preg_replace('#/.*#', '/*', $type), $mimeTypes, TRUE)) {
						return TRUE;
					}
				}
				return FALSE;
			}
		));
	}

	/**
	 * Image validator: is file image?
	 *
	 * @param MultipleFileUpload
	 * @return bool
	 */
	public static function validateImage(MultipleFileUpload $control)
	{
		return (bool) count(array_filter(
			$control->getValue(), function ($file) {
				return $file instanceof FileUpload && $file->isImage();
			}
		));
	}
}

