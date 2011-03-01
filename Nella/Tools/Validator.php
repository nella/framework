<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Tools;

use Nette\String;

/**
 * Validator
 *
 * @author	Patrik Votoček
 */
class Validator extends \Nette\Object
{
	/**
	 * URL validator matching urls including port number and also relative urls
	 * 
	 * @param string
	 * @return bool
	 */
	public static function url($url)
	{
		$chars = "a-z0-9\x80-\xFF";
		$isAbsoluteUrl = (bool) String::match($url, "#^(?:https?://|)(?:[$chars](?:[-$chars]{0,61}[$chars])?\\.)+[-$chars]{2,19}(?::([0-9]+))?(/\S*)?$#i");
		$isRelativeUrl = (bool) String::match($url, "#^/\S+$#i");
		$isValidIpUrl = (bool) String::match($url, "#^(?:https?://|)(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}(/\S*)?$#");

		return $isAbsoluteUrl || $isRelativeUrl || $isValidIpUrl;
	}

	/**
	 * Email validator
	 * 
	 * @author David Grudl
	 * @param string
	 * @return bool
	 */
	public static function email($email)
	{
		$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
		$localPart = "(?:\"(?:[ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(?:\\.$atom+)*)"; // quoted or unquoted
		$chars = "a-z0-9\x80-\xFF"; // superset of IDN
		$domain = "[$chars](?:[-$chars]{0,61}[$chars])"; // RFC 1034 one domain component
		return (bool) String::match($email, "(^$localPart@(?:$domain?\\.)+[-$chars]{2,19}\\z)i");
	}
}
