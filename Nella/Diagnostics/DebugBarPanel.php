<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Diagnostics;

/**
 * Tools panel for Nette debug bar
 *
 * @author	Patrik Votoček
 */
class DebugBarPanel extends \Nette\Object implements \Nette\Diagnostics\IBarPanel
{
	/** @var \Nette\DI\Container */
	private $container;
	/** @var array */
	private $templates;
	/** @var bool */
	public $serviceDump = FALSE;

	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Nette\DI\Container $container)
	{
		$this->container = $container;
		$this->templates = array();
	}

	/**
	 * @param string
	 * @param array
	 */
	public function addTemplates($control, array $templates = NULL)
	{
		$this->templates[$control] = $templates;
	}

	/**
	 * @return string
	 */
	public function getTab()
	{
		return '<span title="Nella Tools"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAGSSURBVCjPVVFNSwJhEF78Ad79Cf6PvXQRsotUlzKICosuRYmR2RJR0KE6lBFFZVEbpFBSqKu2rum6llFS9HHI4iUhT153n6ZtIWMOM+/MM88z7wwH7s9Ub16SJcnbmrNcxVm2q7Z8/QPvEOtntpj92NkCqITLepEpjix7xQtiLOoQ2b6+E7YAN/5nfOEJ2WbKqOIOJ4bYVMEQx4LfBBQDsvFMhUcCVU1/CxVXmDBGA5ZETrhDCQVcYAPbyEJBhvrnBVPiSpNr6cYDNCQwo4zzU/ySckkgDYuNuVpI42T9k4gLKGMPs/xPzzovQiY2hQYe0jlJfyNNhTqiWDYBq/wBMcSRpnyPzu1oS7WtxjVBSthU1vgVksiQ3Dn6Gp5ah2YOKQo5GiuHPA6xT1EKpxQNCNYejgIR457KKio0S56YckjSa9jo//3mrj+BV0QQagqGTOo+Y7gZIf1puP3WHoLhEb2PjTlCTCWGXtbp8DCX3hZuOdaIc9A+aQvWk4ihq95p67a7nP+u+Ws+r0dql9z/zv0NCYhdCPKZ7oYAAAAASUVORK5CYII=" alt="icon"> debug</span>';
	}

	private function getServices()
	{
		$ref = new \Nette\Reflection\Property('Nette\DI\Container', 'registry');
		$ref->setAccessible(TRUE);
		$registry = $ref->getValue($this->container);
		$services = array();
		foreach ($registry as $name => $service) {
			$type = gettype($service);
			if ($service == NULL || $type == "NULL") {
				continue;
			} elseif ($type == "object") {
				$type = get_class($service);
			}
			$services[] = array(
				'name' => $name,
				'type' => $type,
				'instance' => $this->serviceDump ? $service : NULL,
			);
		}
		return $services;
	}

	/**
	 * @return string
	 */
	public function getPanel()
	{
		ob_start();
		$templates = $this->templates;
		$services = $this->getServices();
		require_once __DIR__ . "/templates/bar.debug.panel.phtml";
		return ob_get_clean();
	}
	
	/**
	 * @param \Nette\Diagnostics\Bar
	 * @param \Nette\DI\Container
	 * @return DebugBarPanel
	 */
	public static function register(\Nette\Diagnostics\Bar $bar, \Nette\DI\Container $container)
	{
		$panel = new static($container);
		$bar->addPanel($panel);
		return $panel;
	}
}