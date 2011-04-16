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
	Nette\Http\FileUpload;

/**
 * Text box and browse button that allow users to select a multiple files to upload to the server.
 *
 * @author	Patrik Votoček
 */
class MultipleFileUpload extends \Nette\Forms\Controls\BaseControl
{
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
		$token = substr(md5(uniqid()), 0, 8);

		$control = parent::getControl();
		$control->name .= "[]";
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
			throw new \Nette\NotImplementedException;
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
