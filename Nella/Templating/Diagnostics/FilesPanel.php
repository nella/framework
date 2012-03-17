<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Templating\Diagnostics;

/**
 * @author	Patrik Votočke
 */
class FilesPanel extends \Nette\Object implements \Nette\Diagnostics\IBarPanel, \Nella\Templating\IFilesFormatterLogger
{
	/** @var array */
	private $files = array();

	/**
	 * @param string
	 * @param string
	 * @param array
	 */
	public function logFiles($name, $view, array $files)
	{
		$this->files[$name.':'.$view] = $files;
	}

	/**
	 * Renders HTML code for custom tab
	 *
	 * @return string
	 */
	function getTab()
	{
		return '<span title="Templates">'
				. '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsSAAALEgHS3X78AAAAB3RJTUUH1ggZDQos1T0RfAAAAB10RVh0Q29tbWVudABDcmVhdGVkIHdpdGggVGhlIEdJTVDvZCVuAAABoklEQVQ4y4WSz2saURDHP89u3NRG8JCcgqbaJNCfJDmF9tJDQv+fHkqhZ0GE/DMhoRU95NBriVbyS9vE9NJYdQUXRGV504PdZTfr2oGB9+a9+c5n5j3F1N4Uivmv/N/2P374VAlFC8W8dLtd6XQ6AbdtWxzHkfF4LI7jyOcvx9L80Tjw58b8m36/j2VZWD2LXq9HrV7lttXi7LzOwB6wv/eOZrPxvlDM74UERMRzjaBFyK7lWF5ZZjgcYsZNBvaA3d3XAGU3z/ATiBYEjQggwvXNT16+eIVpLnJa/cZkMmE0GgXaDwho+ZeMoBRkszm01qTTGSzLwjAesGg+5Hu9NnsGXhtac9f+DaK4bFwQX4iTTC5hGAskEoloAlcABetPNhHRpFcziAiPEksApFIpIl9Bpvy023copbhpXQNw1biM/BgzW9hY3wQg+zgHQCa95onPFQD402kTi03DLoGIRFKEBNzqfoJ5FLGo6n6CeRTebaVUoPp9ApfivnnPePurxfbWTuDw/OKMZ0+fh2KzBE7KldLbcqUUqnB0fDhrdifu4i+3M+KsEqll2AAAAABJRU5ErkJggg%3D%3D" />'
				. ' templates</span>';
	}

	/**
	 * Renders HTML code for custom panel
	 *
	 * @return string
	 */
	function getPanel()
	{
		$files = $this->files;
		ob_start();
		require __DIR__ . '/templates/FilesPanel.panel.phtml';
		return ob_get_clean();
	}

}
