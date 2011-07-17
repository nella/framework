<?php

/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Diagnostics;


/**
 * Callback panel for nette debug bar
 *
 * @author	Patrik Votoček
 */
final class CallbackBarPanel extends \Nette\Object implements \Nette\Diagnostics\IBarPanel
{
	const VERSION = "1.7",
		XHR_HEADER = "X-Nella-Callback-Panel";

	/** @var \Nette\DI\IContainer */
	private $container;

	/** @var array */
	private $callbacks = array();

	/** @var bool */
	private static $registered = FALSE;



	/**
	 * @param \Nette\DI\IContainer
	 */
	public function __construct(\Nette\DI\IContainer $container)
	{
		if (static::$registered) {
			 throw new \Nette\InvalidStateException("Callback panel is already registered");
		}

		$this->container = $container;

		$this->initDefaultsCallbacks();

		static::$registered = TRUE;
	}



	protected function initDefaultsCallbacks()
	{
		$cacheStorage = $this->container->cacheStorage;
		$this->addCallback('--cache', "Invalidate cache", function() use($cacheStorage) {
			$cacheStorage->clean(array(\Nette\Caching\Cache::ALL => TRUE));
		});

		$robotLoader = $this->container->robotLoader;
		$this->addCallback('--robotloader', "Rebuild robotloader cache", function() use($robotLoader) {
			$robotLoader->rebuild();
		});
	}



	protected function processRequest()
	{
		$httpRequest = $this->container->httpRequest;
		if ($httpRequest->getHeader(static::XHR_HEADER)) {
			$data = (array) json_decode(file_get_contents('php://input'), TRUE);
			foreach ($data as $key => $value) {
				if (isset($this->callbacks[$key]) && isset($this->callbacks[$key]['callback']) && $value === TRUE) {
					callback($this->callbacks[$key]['callback'])->invoke();
				}
			}

			die(json_encode(array('status' => "OK")));
		}
	}



	/**
	 * @param string
	 * @return CallbackPanel
	 */
	public function removeCallback($id)
	{
		unset($this->callbacks[$id]);
		return $this;
	}



	/**
	 * @param string
	 * @param string
	 * @param array|\Nette\Callback|\Closure
	 * @return CallbackPanel
	 */
	public function addCallback($id, $name, $callback)
	{
		$this->callbacks[$id] = array(
			'name' => $name,
			'callback' => $callback,
		);
		return $this;
	}



	/**
	 * Renders HTML code for custom tab
	 *
	 * @return string
	 * @see Nette\IDebugPanel::getTab()
	 */
	public function getTab()
	{
		$this->processRequest();

		return '<span title="Callbacks">
			<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAK8AAACvABQqw0mAAAABh0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzT7MfTgAAAY9JREFUOI2lkj1rVUEQhp93d49XjYiCUUFtgiBpFLyWFhKxEAsbGy0ErQQrG/EHCII/QMTGSrQ3hY1FijS5lQp2guBHCiFRSaLnnN0di3Pu9Rpy0IsDCwsz8+w776zMjP+J0JV48nrufMwrc2AUbt/CleMv5ycClHH1UZWWD4MRva4CByYDpHqjSgKEETcmHiHmItW5STuF/FfAg8HZvghHDDMpkKzYXScPgFcx9XBw4WImApITn26cejEAkJlxf7F/MOYfy8K3OJGtJlscKsCpAJqNGRknd+jO6TefA8B6WU1lMrBZ6fiE1R8Zs7hzVJHSjvJnNMb/hMSmht93IYIP5Qhw99zSx1vP+5eSxZmhzpzttmHTbcOKk+413Sav4v3J6ZsfRh5sFdefnnhr2Gz75rvHl18d3aquc43f1/BjaN9V1wn4tq6eta4LtnUCQuPWHmAv0AOKDNXstZln2/f3zgCUX8oFJx1zDagGSmA1mn2VmREk36pxw5NgzVqDhOTFLhjtOgMxmqVOE/81fgFilqPyaom5BAAAAABJRU5ErkJggg==">
			callback
			</span>';
	}



	/**
	 * Renders HTML code for custom panel
	 *
	 * @return string
	 * @see Nette\IDebugPanel::getPanel()
	 */
	public function getPanel()
	{
		$callbacks = $this->callbacks;
		$absoluteUrl = $this->container->httpRequest->url->absoluteUrl;
		ob_start();
		require_once __DIR__ . "/templates/bar.callback.panel.phtml";
		return ob_get_clean();
	}



	/**
	 * @param \Nette\Diagnostics\Bar
	 * @param \Nette\DI\IContainer
	 */
	public static function register(\Nette\Diagnostics\Bar $bar, \Nette\DI\IContainer $container, array $callbacks = NULL)
	{
		$instance = new static($container);

		if ($callbacks) {
			foreach ($callbacks as $id => $cb) {
				$instance->addCallback($id, $cb['name'], $cb['callback']);
			}
		}

		$bar->addPanel($instance);
	}
}