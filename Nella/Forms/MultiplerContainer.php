<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Forms;

use Nette\Forms\Controls\SubmitButton;

/**
 * Multipler item container
 *
 * @author    Patrik Votoček
 */
class MultiplerContainer extends Container
{
	const REMOVE_CONTAINER_BUTTON_ID = '__removecontainer';

	/**
	 * @param string
	 * @param bool
	 * @return \Nette\Forms\Controls\SubmitButton
	 */
	public function addRemoveContainerButton($caption, $cleanUpGroups = FALSE)
	{
		$button = $this->addSubmit(self::REMOVE_CONTAINER_BUTTON_ID, $caption)->setValidationScope(FALSE);
		$button->onClick[] = function (SubmitButton $button) use ($cleanUpGroups) {
			$container = $button->getParent();
			$container->getParent()->remove($container, $cleanUpGroups);
		};
		return $button;
	}
}

