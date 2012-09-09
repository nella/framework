<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Doctrine\Diagnostics;

use Nette\Diagnostics\Debugger,
	Nette\Utils\Strings;

/**
 * Debug panel for Doctrine
 *
 * @author	David Grudl
 * @author	Patrik Votoček
 * @author	Michael Moravec
 */
class ConnectionPanel extends \Nette\Object implements \Nette\Diagnostics\IBarPanel, \Doctrine\DBAL\Logging\SQLLogger
{
	/** @var bool whether to do explain queries for selects or not */
	public $doExplains = TRUE;

	/** @var bool */
	private $explainRunning = FALSE;

	/** @var \Doctrine\DBAL\Connection|NULL */
	private $connection;

	/** @var int logged time */
	public $totalTime = 0;

	const SQL = 0;
	const PARAMS = 1;
	const TYPES = 2;
	const TIME = 3;
	const EXPLAIN = 4;

	/** @var array */
	public $queries = array();

	/**
	 * @param \Doctrine\DBAL\Connection $connection
	 */
	public function setConnection(\Doctrine\DBAL\Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * @param string
	 * @param array
	 * @param array
	 */
	public function startQuery($sql, array $params = NULL, array $types = NULL)
	{
		if ($this->explainRunning) {
			return;
		}

		Debugger::timer('doctrine');

		$this->queries[] = array(
			self::SQL => $sql,
			self::PARAMS => $params,
			self::TYPES => $types,
			self::TIME => 0,
			self::EXPLAIN => NULL,
		);
	}

	public function stopQuery()
	{
		if ($this->explainRunning) {
			return;
		}

		$keys = array_keys($this->queries);
		$key = end($keys);
		$this->queries[$key][self::TIME] = Debugger::timer('doctrine');
		$this->totalTime += $this->queries[$key][self::TIME];

		// get EXPLAIN for SELECT queries
		if ($this->doExplains) {
			if ($this->connection === NULL) {
				throw new \Nette\InvalidStateException('You must set a Doctrine\DBAL\Connection to get EXPLAIN.');
			}

			$query = $this->queries[$key][self::SQL];

			if (!Strings::startsWith($query, 'SELECT')) { // only SELECTs are supported
				return;
			}

			// prevent logging explains & infinite recursion
			$this->explainRunning = TRUE;

			$params = $this->queries[$key][self::PARAMS];
			$types = $this->queries[$key][self::TYPES];

			$stmt = $this->connection->executeQuery('EXPLAIN ' . $query, $params, $types);

			$this->queries[$key][self::EXPLAIN] = $stmt->fetchAll();

			$this->explainRunning = FALSE;
		}
	}

	public function getTab()
	{
		return '<span title="Doctrine 2">'
			. '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEYSURBVBgZBcHPio5hGAfg6/2+R980k6wmJgsJ5U/ZOAqbSc2GnXOwUg7BESgLUeIQ1GSjLFnMwsKGGg1qxJRmPM97/1zXFAAAAEADdlfZzr26miup2svnelq7d2aYgt3rebl585wN6+K3I1/9fJe7O/uIePP2SypJkiRJ0vMhr55FLCA3zgIAOK9uQ4MS361ZOSX+OrTvkgINSjS/HIvhjxNNFGgQsbSmabohKDNoUGLohsls6BaiQIMSs2FYmnXdUsygQYmumy3Nhi6igwalDEOJEjPKP7CA2aFNK8Bkyy3fdNCg7r9/fW3jgpVJbDmy5+PB2IYp4MXFelQ7izPrhkPHB+P5/PjhD5gCgCenx+VR/dODEwD+A3T7nqbxwf1HAAAAAElFTkSuQmCC" />'
			. count($this->queries) . ' queries'
			. ($this->totalTime ? ' / ' . sprintf('%0.1f', $this->totalTime * 1000) . 'ms' : '')
			. '</span>';
	}

	/**
	 * @param array
	 * @return string
	 */
	protected function processQuery(array $query)
	{
		$s = '<tr>';
		$s .= '<td>' . sprintf('%0.3f', $query[self::TIME] * 1000) . '</td>';
		$s .= '<td class="nette-Doctrine2Panel-sql" style="min-width: 400px">' . \Nette\Database\Helpers::dumpSql($query[self::SQL]) . '</td>';
		$s .= '<td>' . \Nette\Diagnostics\Helpers::clickableDump($query[self::PARAMS], TRUE) . '</td>';

		if ($this->doExplains) {
			$s .= '<td>';

			if ($query[self::EXPLAIN]) {
				$s .= '<table>';
				$s .= '<tr><th>' . implode('</th><th>', array_keys($query[self::EXPLAIN][0])) . '</th></tr>';
				foreach ($query[self::EXPLAIN] as $row) {
					$s .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
				}
				$s .='</table>';
			}

			$s .= '</td>';
		}

		$s .= '</tr>';

		return $s;
	}

	protected function renderStyles()
	{
		return '<style> #nette-debug td.nette-Doctrine2Panel-sql { background: white !important }
			#nette-debug .nette-Doctrine2Panel-source { color: #BBB !important }
			#nette-debug nette-Doctrine2Panel tr table { margin: 8px 0; max-height: 150px; overflow:auto } </style>';
	}

	/**
	 * @param \PDOException
	 * @return array
	 */
	public function renderException($e)
	{
		if ($e instanceof \PDOException && count($this->queries)) {
			$s = '<table><tr><th>Time&nbsp;ms</th><th>SQL</th><th>Params</th>' . ($this->doExplains ? '<th>Explain</th>' : '') . '</tr>';
			$s .= $this->processQuery(end($this->queries));
			$s .= '</table>';
			return array(
				'tab' => 'SQL',
				'panel' => $this->renderStyles() . '<div class="nette-inner nette-Doctrine2Panel">' . $s . '</div>',
			);
		} else {
			\Nette\Database\Diagnostics\ConnectionPanel::renderException($e);
		}
	}

	public function getPanel()
	{
		$s = '';
		foreach ($this->queries as $query) {
			$s .= $this->processQuery($query);
		}

		return empty($this->queries) ? '' :
			$this->renderStyles() .
				'<h1>Queries: ' . count($this->queries) . ($this->totalTime ? ', time: ' . sprintf('%0.3f', $this->totalTime * 1000) . ' ms' : '') . '</h1>
			<div class="nette-inner nette-Doctrine2Panel">
			<table>
			<tr><th>Time&nbsp;ms</th><th>SQL</th><th>Params</th>' . ($this->doExplains ? '<th>Explain</th>' : '') . '</tr>' . $s . '
			</table>
			</div>';
	}
}

