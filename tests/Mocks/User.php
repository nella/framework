<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Mocks;

class User extends \Nette\Object implements \Nette\Http\IUser
{
	function login()
	{
		return NULL;
	}

	function logout($clearIdentity = FALSE)
	{
		return NULL;
	}

	/**
	 * Is this user authenticated?
	 * @return bool
	 */
	function isLoggedIn()
	{
		return TRUE;
	}

	function getIdentity()
	{
		return new \Nette\Security\Identity('foo', array('admin'));
	}

	function setAuthenticationHandler(\Nette\Security\IAuthenticator $handler)
	{
		return $this;
	}

	function getAuthenticationHandler()
	{
		return NULL;
	}

	function setNamespace($namespace)
	{
		return $this;
	}

	function getNamespace()
	{
		return 'user';
	}

	function getRoles()
	{
		return array('admin');
	}

	function isInRole($role)
	{
		return FALSE;
	}

	function isAllowed()
	{
		return FALSE;
	}

	function setAuthorizationHandler(\Nette\Security\IAuthorizator $handler)
	{
		return $this;
	}

	function getAuthorizationHandler()
	{
		return NULL;
	}
}