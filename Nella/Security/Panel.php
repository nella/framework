<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Security;

/**
 * Security panel for Nette debug bar
 *
 * @author	Patrik Votoček
 */
class Panel extends \Nette\Object implements \Nette\Diagnostics\IBarPanel
{
	/** @var IdentityEntity */
	private $identity;
	/** @var \Nella\Doctrine\Container */
	private $container;

	/**
	 * @param IdentityEntity
	 * @param \Nella\Doctrine\Container
	 */
	public function __construct(IdentityEntity $identity = NULL, \Nella\Doctrine\Container $container)
	{
		$this->identity = $identity;
		$this->container = $container;
		\Nette\Diagnostics\Debugger::$bar->addPanel($this);
	}

	/**
	 * @return string
	 */
	private function getUsername()
	{
		if ($this->identity) {
			return $this->identity->displayName;
		}
		return 'not authenticated';
	}

	/**
	 * @return string
	 */
	public function getTab()
	{
		$s = '<span title="User"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAnpJREFUeNqEU19IU1EY/927e52bWbaMQLbJwmgP0zIpffDFUClsyF56WJBQkv1RyJeo2IMPEghRQeAIoscegpBqTy6y3CDwrdzDwjCVkdqmzT+7u//O1jm3knkV/MF3z3e+8zu/7zv3O4crFotgaHC7jfHrwgKuBYPtVqt1BBx3SlNV5HK5KSmXu/N6fPxTKY+BMwvUNzY22cvFz6TIi0TXoWkaFEWBrkra+rrUtJLJTJcKCDCBZrqvyBaRCTMBnRCwKhRZFlVFuUspl0r5OwRUKXu+opxgsP8qfE4Bmk7wZV7Bg5FRqIR0m/m8OfA7K9n6bt1GvbeWlq2CKxCcPnEM1wf6sZknFXsKDF+c+dHgVKBmf4JoqmHMb/Va8OTK4vSeAhThpW9vwdsPociJ1ATD/zU7bqyZyVtdKMWHIXH0SJ3/RrWn05hn5t5jeeZN+OyQdtPMFbA77i1/f9dE7cy/+RS10G7EbRX4fL42OvQGAoFgT6uM2uPnjHhq9iNeTABjY2Mv6fR5IpGY2Cbg9XqPUr/PZrMNOJ1Oq65pfCQSwcPwK1TtE9F7OYCurgsQRbGQSqWUfD7/lPKfJZPJWc7j8ZzkeX7S5XLZHA6HIEkSqBCam5uxYqnDwf02WDeTiMVikGUZdrsdq6urOhWSCSGdFhoIud3ulrKyMiGbzRrXVqX9j8fj8Pu7UXO4EiPDIZYdNDN7F6DvhKf7+HQ6bRGoaju970bm/2CZmCXn0nAcyBn+xsbG1joTooJsbxv71LDNhUJh299lpPnFNaxt/hVjlZWCPTIar+YEQXhEzzxobk9HRyeWrC2oqhRRnplENBrd0UKa5PEfAQYAH6s95RSa3ooAAAAASUVORK5CYII=" alt="icon"> ';
		return $s . $this->getUsername() . '</span>';
	}

	/**
	 * @return string
	 */
	public function getPanel()
	{
		if (!$this->identity) {
			return NULL;
		}

		ob_start();
		$role = $this->identity->role;
		$username = $this->getUsername();
		require_once __DIR__ . "/Panel.phtml";
		return ob_get_clean();
	}
}