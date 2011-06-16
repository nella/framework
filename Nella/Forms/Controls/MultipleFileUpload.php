<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Forms\Controls;

use Nette\Application\UI\Form,
	Nette\Http\FileUpload,
	Nette\Utils\Finder,
	Nette\Utils\Strings;

/**
 * Text box and browse button that allow users to select a multiple files to upload to the server.
 *
 * @author	Patrik Votoček
 */
class MultipleFileUpload extends \Nette\Forms\Controls\BaseControl
{
	const MULTIPLE_FILE_UPLOAD_KEY = '_mfutoken';
	/** @var \Nette\Http\IRequest */
	protected static $httpRequest;
	/** @var string */
	protected static $storageDir;
	/** @var int */
	protected static $expire;

	public static function register(\Nette\Http\IRequest $httpRequest, $storageDir, $expire = 3600)
	{
		if (static::$httpRequest) {
			throw new \Nette\InvalidStateException("Multiple file uploader allready registered");
		} elseif (!file_exists($storageDir) || !is_writable($storageDir)) {
			throw new \Nette\InvalidStateException("Storage dir must be writable");
		}

		static::$httpRequest = $httpRequest;
		static::$storageDir = $storageDir;
		static::$expire = $expire;

		// process uploaded file
		if ($httpRequest->getHeader('X-Nella-MFU-Token') && $httpRequest->getHeader('X-Uploader')) {
			$files = $httpRequest->files;
			if (isset($files['file']) && $files['file']->ok) {
				$token = $httpRequest->getHeader('X-Nella-MFU-Token');
				$path = static::$storageDir . "/";
				$path .= $token . "_" . time() . "_" . Strings::random(16);
				$path .= "." . pathinfo($files['file']->name, PATHINFO_EXTENSION);
				$path .= ".tmp";

				$files['file']->move($path);
			}

			echo "{success:true}";
			\Nette\Diagnostics\Debugger::$bar = NULL;
			exit;
		}

		// clean expired files
		$files = Finder::findFiles("*.tmp")->from(static::$storageDir);
		$expire = time() + static::$expire;
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
	protected function attached($form)
	{
		if ($form instanceof Form) {
			if ($form->getMethod() !== Form::POST) {
				throw new \Nette\InvalidStateException("File upload requires method POST.");
			}
			$form->getElementPrototype()->enctype = 'multipart/form-data';
		}
		parent::attached($form);
	}

	/**
	 * Generates control's HTML element.
	 *
	 * @return \Nette\Utils\Html
	 */
	public function getControl()
	{
		$control = parent::getControl();
		$control->name .= "[]";

		$token = Strings::random(16);

		if (static::$httpRequest) {
			$control->data('nella-mfu-token', $token);
		}

		$control .= \Nette\Utils\Html::el('input')
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
			$token = static::$httpRequest->getPost($this->name . static::MULTIPLE_FILE_UPLOAD_KEY);
			if ($token) {
				$this->value = array();
				$files = Finder::findFiles($token . "_*.tmp")->from(static::$storageDir);
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
			$control->getValue(), function($file) {
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
			$control->getValue(), function($file) {
				return $file instanceof FileUpload;
			}
		));

		$size = array_sum(array_map(function($file) {
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
			$control->getValue(), function($file) {
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
			$control->getValue(), function($file) {
				return $file instanceof FileUpload && $file->isImage();
			}
		));
	}
}
