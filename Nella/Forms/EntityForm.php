<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Forms;

/**
 * Nella entity forms
 *
 * @author	Patrik Votoček
 */
class EntityForm extends Form
{
	/**
	 * @param \Nella\Models\IEntity
	 * @param bool
	 * @return EntityForm
	 */
	public function setDefaults($entity, $erase = FALSE)
	{
		if ($entity instanceof \Nella\Models\IEntity) {
			$supportedComponents = array(
				'Nette\Forms\Controls\TextBase',
				'Nette\Forms\Controls\RadioList',
				'Nette\Forms\Controls\Checkbox',
				'Nette\Forms\Controls\Selectbox',
			);
			$arr = array();
			foreach ($this->getComponents() as $component) {
				foreach ($supportedComponents as $class) {
					if (!$component instanceof $class) {
						continue;
					}
				}
				$name = $component->getName();
				$method = 'get' . ucfirst($name);
				if (method_exists($entity, $method)) {
					if ($component instanceof \Nette\Forms\Controls\Selectbox) {
						$value = $entity->$method();
						$arr[$name] = $value ? (is_string($value) ? $value : $value->getId()) : NULL;
					} else {
						$arr[$name] = $entity->$method();
					}
				}
			}
		} else {
			$arr = $entity;
		}
		return parent::setDefaults($arr, $erase);
	}
}
