<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

/**
 * Translates the given string.
 *
 * @param string
 * @return string
 */
function __($message)
{
	return Nette\Environment::getService('Nette\Localization\ITranslator')
		->translate($message);
}

/**
 * Translates the given string with plural.
 *
 * @param string
 * @param string 
 * @param int plural form (positive number)
 * @return string
 */
function _n($single, $plural, $number)
{
	return Nette\Environment::getService('Nette\Localization\ITranslator')
		->translate(array($single, $plural), $number);
}

/**
 * Translates the given string with vsprintf.
 *
 * @param string
 * @param array for vsprintf 
 * @return string
 */
function _x($message, array $args)
{
	return Nette\Environment::getService('Nette\Localization\ITranslator')
		->translate($message, $args);
}

/**
 * Translates the given string with plural and vsprintf.
 *
 * @param string
 * @param string
 * @param int plural form (positive number)
 * @param array for vsprintf
 * @return string
 */
function _nx($single, $plural, $number, array $args)
{
	return Nette\Environment::getService('Nette\Localization\ITranslator')
		->translate(array($single, $plural), array_merge(array($number), $args));
}
