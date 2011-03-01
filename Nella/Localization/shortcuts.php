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
 * @param string $message
 * @return string
 */
function __($message)
{
	return Nette\Environment::getService('Nette\ITranslator')->translate($message);
}

/**
 * Translates the given string with plural.
 *
 * @param string $single
 * @param string $plural 
 * @param int $muber plural form (positive number)
 * @return string
 */
function _n($single, $plural, $number)
{
	return Nette\Environment::getService('Nette\ITranslator')->translate(array($single, $plural), $number);
}

/**
 * Translates the given string with vsprintf.
 *
 * @param string $message
 * @paran array $args for vsprintf 
 * @return string
 */
function _x($message, array $args)
{
	return Nette\Environment::getService('Nette\ITranslator')->translate($message, $args);
}

/**
 * Translates the given string with plural and vsprintf.
 *
 * @param string $single
 * @param string $plural 
 * @param int $muber plural form (positive number)
 * @return string
 */
function _nx($single, $plural, $number, array $args)
{
	return Nette\Environment::getService('Nette\ITranslator')->translate(array($single, $plural), array_merge(array($number), $args));
}
