<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Diagnostics;

/**
 * Callback panel for nette debug bar
 *
 * @author	Patrik Votoček
 */
final class CallbackPanel extends \Nette\Object implements \Nette\Diagnostics\IBarPanel
{
	const VERSION = "1.9",
		XHR_HEADER = "X-Nella-Callback-Panel";

	/** @var \Nette\DI\Container */
	private $container;
	/** @var array[]|array */
	private $callbacks = array();
	/** @var bool */
	private static $registered = FALSE;

	/**
	 * @param \Nette\DI\Container
	 * @param array[]|array
	 */
	public function __construct(\Nette\DI\Container $container, array $callbacks = array())
	{
		if (static::$registered) {
			 throw new \Nette\InvalidStateException("Callback panel is already registered");
		}

		$this->container = $container;

		$this->initDefaultsCallbacks();
		$this->callbacks = array_merge($this->callbacks, $callbacks);

		$this->run();

		static::$registered = TRUE;
	}

	protected function initDefaultsCallbacks()
	{
		if ($this->container->hasService('cacheStorage')) {
			$cacheStorage = $this->container->cacheStorage;
			$this->callbacks['cache'] = array(
				'name' => 'Clean cache',
				'callback' => function () use ($cacheStorage) {
					$cacheStorage->clean(array(\Nette\Caching\Cache::ALL => TRUE));
				}
			);
		}

		if ($this->container->hasService('session')) {
			$session = $this->container->session;
			$this->callbacks['session'] = array(
				'name' => 'Clean session',
				'callback' => function () use ($session) {
					if (!$session->isStarted()) {
			            $session->clean();
			        }
				}
			);
		}
	}

	/**
	 * @param string
	 */
	protected function invoke($id)
	{
		if (isset($this->callbacks[$id]) && isset($this->callbacks[$id]['callback'])) {
			callback($this->callbacks[$id]['callback'])->invoke();
			die(json_encode(array('status' => "OK")));
		}
	}

	protected function run()
	{
		$httpRequest = $this->container->httpRequest;
		if ($httpRequest->getHeader(static::XHR_HEADER)) {
			$data = (array) json_decode(file_get_contents('php://input'), TRUE);
			foreach ($data as $key => $value) {
				if (isset($this->callbacks[$key]) && isset($this->callbacks[$key]['callback']) && $value === TRUE) {
					$this->invoke($key);
				}
			}
		}
	}

	/**
	 * Renders HTML code for custom tab
	 *
	 * @return string
	 */
	public function getTab()
	{
		return '<span title="Callbacks"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAK8AAACvABQqw0mAAAABh0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzT7MfTgAAAY9JREFUOI2lkj1rVUEQhp93d49XjYiCUUFtgiBpFLyWFhKxEAsbGy0ErQQrG/EHCII/QMTGSrQ3hY1FijS5lQp2guBHCiFRSaLnnN0di3Pu9Rpy0IsDCwsz8+w776zMjP+J0JV48nrufMwrc2AUbt/CleMv5ycClHH1UZWWD4MRva4CByYDpHqjSgKEETcmHiHmItW5STuF/FfAg8HZvghHDDMpkKzYXScPgFcx9XBw4WImApITn26cejEAkJlxf7F/MOYfy8K3OJGtJlscKsCpAJqNGRknd+jO6TefA8B6WU1lMrBZ6fiE1R8Zs7hzVJHSjvJnNMb/hMSmht93IYIP5Qhw99zSx1vP+5eSxZmhzpzttmHTbcOKk+413Sav4v3J6ZsfRh5sFdefnnhr2Gz75rvHl18d3aquc43f1/BjaN9V1wn4tq6eta4LtnUCQuPWHmAv0AOKDNXstZln2/f3zgCUX8oFJx1zDagGSmA1mn2VmREk36pxw5NgzVqDhOTFLhjtOgMxmqVOE/81fgFilqPyaom5BAAAAABJRU5ErkJggg=="></span>';
	}

	/**
	 * Renders HTML code for custom panel
	 *
	 * @return string
	 */
	public function getPanel()
	{
		$callbacks = $this->callbacks;
		$absoluteUrl = $this->container->httpRequest->url->absoluteUrl;
		ob_start();
		require_once __DIR__ . "/templates/CallbackPanel.panel.phtml";
		return ob_get_clean();
	}

	/**
	 * @param \Nette\DI\Container
	 * @param array[]|array
	 */
	public static function register(\Nette\DI\Container $container, array $callbacks = array())
	{
		if (\Nette\Diagnostics\Debugger::$bar) {
			\Nette\Diagnostics\Debugger::$bar->addPanel(new static($container, $callbacks));
		}
	}
}
