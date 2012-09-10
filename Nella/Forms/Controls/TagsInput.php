<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 * Copyright (c) 2010, 2011 Mikulas Dite
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Forms\Controls;

use Nette\Forms\Form,
	Nette\Forms\Rules,
	Nette\Forms\IControl,
	Nette\Forms\Controls\TextBase,
	Nette\Utils\Strings,
	Nette\Utils\Html;

/**
 * Form date field item
 *
 * @author	Patrik Votoček
 * @author	Mikulas Dite
 *
 * @property \DateTime $value
 */
class TagsInput extends \Nette\Forms\Controls\TextInput
{
	const UNIQUE = ':unique',
		ORIGINAL = ':original';

	/** @var string */
	private $renderName;
	/** @var int */
	protected $payloadLimit = 5;
	/** @var string regex */
	protected $delimiter = '[\s,]+';
	/** @var string */
	protected $joiner = ', ';
	/** @var callback returning array */
	protected $suggestCallback;

	/**
	 * @param string regex
	 * @return TagInput
	 */
	public function setDelimiter($delimiter)
	{
		$this->delimiter = $delimiter;
		return $this;
	}

	/**
	 * @param string
	 * @return TagInput
	 */
	public function setJoiner($joiner)
	{
		$this->joiner = $joiner;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getValue()
	{
		// temporarily disable filters
		$filters = $this->filters;
		$this->filters = array();

		$res = Strings::split(parent::getValue(), "\x01" . $this->delimiter . "\x01");
		$this->filters = $filters;

		foreach ($res as &$tag) {
			foreach ($this->filters as $filter) {
				$tag = $filter($tag);
			}
			if (!$tag) {
				unset($tag);
			}
		}

		return $res;
	}

	/**
	 * Generates control's HTML element.
	 *
	 * @return \Nette\Utils\Html
	 */
	public function getControl()
	{
		$control = parent::getControl();

		if ($this->delimiter !== NULL && Strings::trim($this->delimiter) !== '') {
			$control->attrs['data-nella-tag-delimiter'] = $this->delimiter;
		}

		if ($this->joiner !== NULL && Strings::trim($this->joiner) !== '') {
			$control->attrs['data-nella-tag-joiner'] = $this->joiner;
		}

		if ($this->suggestCallback !== NULL) {
			$form = $this->getForm();
			if (!$form || !$form instanceof Form) {
				throw new \Nette\InvalidStateException('TagInput supports only Nette\Application\UI\Form.');
			}
			$control->attrs['data-nella-tag-suggest'] = $form->getPresenter()->link($this->renderName, array('word_filter' => '%filter%'));
		}

		return $control;
	}

	/**
	 * Sets control's value.
	 *
	 * @param  string|array
	 * @return TagInput
	 */
	public function setValue($value)
	{
		if (!is_array($value)) {
			$value = explode(trim($this->joiner), $value);
		}
		parent::setValue(implode($this->joiner, $value));
		return $this;
	}

	/**
	 * @param array
	 * @return TagInput
	 */
	public function setDefaultValue($value)
	{
		if (!is_array($value)) {
			$value = explode(trim($this->joiner), $value);
		}
		parent::setDefaultValue(implode($this->joiner, $value));
		return $this;
	}

	/**
	 * @param int
	 * @return TagInput
	 */
	public function setPayloadLimit($limit)
	{
		if ($limit < 0) {
			throw new \Nette\InvalidArgumentException('Invalid limit, expected positive integer.');
		}

		$this->payloadLimit = $limit;
		return $this;
	}

	/**
	 * Adds a validation rule.
	 *
	 * @param  mixed	  rule type
	 * @param  string	 message to display for invalid data
	 * @param  mixed	  optional rule arguments
	 * @return TagInput
	 */
	public function addRule($operation, $message = NULL, $arg = NULL)
	{
		switch ($operation) {
			case Form::EQUAL:
				if (!is_array($arg)) {
					throw new \Nette\InvalidArgumentException(__METHOD__ . '(' . $operation . ') must be compared to array.');
				}
		}

		return parent::addRule($operation, $message, $arg);
	}

	/**
	 * @param array
	 * @return TagInput
	 */
	public function setSuggestCallback($suggest)
	{
		$this->suggestCallback = callback($suggest);
		Rules::$defaultMessages[self::UNIQUE] = 'Please insert each tag only once.';
		Rules::$defaultMessages[self::ORIGINAL] = 'Please do use only suggested tags.';
		return $this;
	}

	/**
	 * @param \Nette\Application\UI\Presenter presenter
	 * @param string presenter
	 */
	public function renderResponse($presenter, $filter)
	{
		$data = array();
		if (!($this->suggestCallback instanceof \Nette\Callback)) {
			throw new \Nette\InvalidStateException('Callback not set.');
		}

		foreach ($this->suggestCallback->invoke($filter, $this->payloadLimit) as $tag) {
			if (count($data) >= $this->payloadLimit) {
				break;
			}
			$data[] = (string)$tag;
		}

		$presenter->sendResponse(new \Nette\Application\Responses\JsonResponse($data));
	}

	/**
	 * Equal validator: are control's value and second parameter equal?
	 *
	 * @param  \Nette\Forms\IControl
	 * @param  mixed
	 * @return bool
	 */
	public static function validateEqual(IControl $control, $arg)
	{
		$value = $control->getValue();
		sort($value);
		sort($arg);
		return $value === $arg;
	}

	/**
	 * Filled validator: is control filled?
	 *
	 * @param  \Nette\Forms\IControl
	 * @return bool
	 */
	public static function validateFilled(IControl $control)
	{
		return count($control->getValue()) !== 0;
	}

	/**
	 * Min-length validator: has control's value minimal length?
	 *
	 * @param  \Nette\Forms\Controls\TextBase
	 * @param  int  length
	 * @return bool
	 */
	public static function validateMinLength(TextBase $control, $length)
	{
		return count($control->getValue()) >= $length;
	}

	/**
	 * Max-length validator: is control's value length in limit?
	 *
	 * @param  \Nette\Forms\Controls\TextBase
	 * @param  int  length
	 * @return bool
	 */
	public static function validateMaxLength(TextBase $control, $length)
	{
		return count($control->getValue()) <= $length;
	}

	/**
	 * Length validator: is control's value length in range?
	 *
	 * @param  \Nette\Forms\Controls\TextBase
	 * @param  array  min and max length pair
	 * @return bool
	 */
	public static function validateLength(TextBase $control, $range)
	{
		if (!is_array($range)) {
			$range = array($range, $range);
		}
		$len = count($control->getValue());
		return ($range[0] === NULL || $len >= $range[0]) && ($range[1] === NULL || $len <= $range[1]);
	}


	/**
	 * Email validator: is control's value valid email address?
	 *
	 * @param  \Nette\Forms\Controls\TextBase
	 * @return bool
	 */
	public static function validateEmail(TextBase $control)
	{
		throw new \LogicException(':EMAIL validator is not applicable to TagInput.');
	}

	/**
	 * URL validator: is control's value valid URL?
	 *
	 * @param  \Nette\Forms\Controls\TextBase
	 * @return bool
	 */
	public static function validateUrl(TextBase $control)
	{
		throw new \LogicException(':URL validator is not applicable to TagInput.');
	}

	/**
	 * Regular expression validator: matches control's value regular expression?
	 *
	 * @param  \Nette\Forms\Controls\TextBase
	 * @param  string
	 * @return bool
	 */
	public static function validatePattern(TextBase $control, $pattern)
	{
		throw new \LogicException(':PATTERN validator is not applicable to TagInput.');
	}

	/**
	 * Integer validator: is each value of tag of control decimal number?
	 *
	 * @param  \Nette\Forms\Controls\TextBase
	 * @return bool
	 */
	public static function validateInteger(TextBase $control)
	{
		foreach ($control->getValue() as $tag) {
			if (!Strings::match($tag, '/^-?[0-9]+$/')) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Float validator: is each value of tag of control value float number?
	 *
	 * @param  \Nette\Forms\Controls\TextBase
	 * @return bool
	 */
	public static function validateFloat(TextBase $control)
	{
		foreach ($control->getValue() as $tag) {
			if (!Strings::match($tag, '/^-?[0-9]*[.,]?[0-9]+$/')) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Range validator: is a control's value number in specified range?
	 *
	 * @param  \Nette\Forms\Controls\TextBase
	 * @param  array  min and max value pair
	 * @return bool
	 */
	public static function validateRange(TextBase $control, $range)
	{
		throw new \LogicException(':RANGE validator is not applicable to TagInput.');
	}

	/**
	 * Uniqueness validator: is each value of tag of control unique?
	 *
	 * @param  TagInput
	 * @return bool
	 */
	public static function validateUnique(TagInput $control)
	{
		return count(array_unique($control->getValue())) === count($control->getValue());
	}

	/**
	 * Are all tags from suggest?
	 *
	 * @param  TagInput
	 * @return bool
	 */
	public static function validateOriginal(TagInput $control)
	{
		foreach ($control->getValue() as $tag) {
			$found = FALSE;
			foreach ($control->suggestCallback->invoke($tag, 1) as $suggest) {
				if ($tag === $suggest) {
					return TRUE;
				}
			}
			if (!$found) {
				return FALSE;
			}
		}
		return TRUE;
	}
}

