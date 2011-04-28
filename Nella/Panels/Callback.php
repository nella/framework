<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Panels;

use Nette\Environment;

/**
 * Callback panel for nette debug bar
 *
 * @author	Patrik Votoček
 */
class Callback extends \Nette\Object implements \Nette\Diagnostics\IBarPanel
{
	const VERSION = "1.6";
	/** @var array */
	private $items = array();
	/** @var bool */
	private static $registered = FALSE;

	/**
	 * @param \Nella\DI\IContext
	 * @param array
	 */
	public function __construct(\Nella\DI\IContext $context, array $items = NULL)
	{
		$cache = $context->getService('Nette\Caching\IStorage');
		$loader = $context->getService('Nette\Loaders\RobotLoader');

		$this->items = array(
			'--cache' => array(
				'callback' => callback($cache, 'clean'),
				'name' => "Invalidate cache",
				'args' => array(array(\Nette\Caching\Cache::ALL => TRUE))
			),
			'--robotloader' => array(
				'callback' => callback($loader, 'rebuild'),
				'name' => "Rebuild",
				'args' => array(),
			),
		);

		if ($items) {
			$this->items = array_merge($this->items, $items);
		}

		$this->processRequest();
	}

	/**
	 * Renders HTML code for custom tab.
	 * @return string
	 * @see Nette\IDebugPanel::getTab()
	 */
	public function getTab()
	{
		return '<span title="Callbacks">
			<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAK8AAACvABQqw0mAAAABh0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzT7MfTgAAAY9JREFUOI2lkj1rVUEQhp93d49XjYiCUUFtgiBpFLyWFhKxEAsbGy0ErQQrG/EHCII/QMTGSrQ3hY1FijS5lQp2guBHCiFRSaLnnN0di3Pu9Rpy0IsDCwsz8+w776zMjP+J0JV48nrufMwrc2AUbt/CleMv5ycClHH1UZWWD4MRva4CByYDpHqjSgKEETcmHiHmItW5STuF/FfAg8HZvghHDDMpkKzYXScPgFcx9XBw4WImApITn26cejEAkJlxf7F/MOYfy8K3OJGtJlscKsCpAJqNGRknd+jO6TefA8B6WU1lMrBZ6fiE1R8Zs7hzVJHSjvJnNMb/hMSmht93IYIP5Qhw99zSx1vP+5eSxZmhzpzttmHTbcOKk+413Sav4v3J6ZsfRh5sFdefnnhr2Gz75rvHl18d3aquc43f1/BjaN9V1wn4tq6eta4LtnUCQuPWHmAv0AOKDNXstZln2/f3zgCUX8oFJx1zDagGSmA1mn2VmREk36pxw5NgzVqDhOTFLhjtOgMxmqVOE/81fgFilqPyaom5BAAAAABJRU5ErkJggg==">
			callback
			</span>';
	}

	/**
	 * Renders HTML code for custom panel.
	 * @return string
	 * @see Nette\IDebugPanel::getPanel()
	 */
	public function getPanel()
	{
		$items = $this->items;
		ob_start();
		require_once __DIR__ . "/Callback.phtml";
		return ob_get_clean();
	}

	/**
	 * Handles an incomuing request and saves the data if necessary.
	 */
	private function processRequest()
	{
		$request = Environment::getApplication()->getService('Nette\Http\IRequest');
		if ($request->isPost() && $request->isAjax() && $request->getHeader('X-Callback-Panel')) {
			$data = json_decode(file_get_contents('php://input'), TRUE);
			if (count($data) > 0) {
				foreach ($data as $key => $value) {
					if (isset($this->items[$key]) && isset($this->items[$key]['callback']) && $value === TRUE) {
						$this->items[$key]['callback']->invokeArgs($this->items[$key]['args']);
					}
				}
			}

			die(json_encode(array('status' => "OK")));
		}
	}

	/**
	 * Register this panel
	 *
	 * @param array	items for add to pannel
	 */
	public static function register(array $items = NULL)
	{
		if (self::$registered) {
			throw new \Nette\InvalidStateException("Callback panel is already registered");
		}

		\Nette\Diagnostics\Debugger::$bar->addPanel(new static($items));
		self::$registered = TRUE;
	}
}
