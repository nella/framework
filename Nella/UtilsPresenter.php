<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella;

use Nette\Debug;

/**
 * Utils presenter
 *
 * @author	Patrik Votoček
 */
class UtilsPresenter extends \Nella\Application\Presenter
{
	protected function startup()
  	{
  		parent::startup();
  		
  		if (\Nette\Environment::isConsole()) {
  			$title = \Nella\Framework::NAME . " CMS Console " . \Nella\Framework::VERSION;
  			echo $title . PHP_EOL;
  			echo str_repeat('=', strlen($title)) . PHP_EOL . PHP_EOL;
		}
  	}
  	
  	public function actionHelp()
  	{
  		throw new \Nette\Application\BadRequestException;
  	}
	
	/**
	 * Temporary solution for create schema
	 * 
	 * @param mixed
	 */
	public function actionCreateSchema($dump = NULL)
	{
		$mapper = function ($row) { return $row . ";\n"; };
 		echo "Creating database schema..." . PHP_EOL;
 		try {
 			$em = $this->getEntityManager();
 			$metadatas = $em->getMetadataFactory()->getAllMetadata();
 			$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
 			
 			if ($dump === TRUE) {
 				Debug::dump($schemaTool->getCreateSchemaSql($metadatas));
 			} elseif ($dump) {
 				file_put_contents($dump, array_map($mapper, $schemaTool->getCreateSchemaSql($metadatas)));
 			} else {
 				$schemaTool->createSchema($metadatas);
 			}
			
 			echo "DONE" . PHP_EOL;
 		} catch (\Exception $e) {
 			Debug::log($e);
 			echo "Error #" . get_class($e) . ": " . $e->getMessage() . "\n\r";
 		}
	}
	
	/**
	 * Temporary solution for update schema
	 * 
	 * @param mixed
	 */
	public function actionUpdateSchema($dump = NULL)
 	{
 		$mapper = function ($row) { return $row . ";\n"; };
 		echo "Updating database schema..." . PHP_EOL;
 		
 		try {
 			$em = $this->getEntityManager();
 			$metadatas = $em->getMetadataFactory()->getAllMetadata();
 			$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
 			
 			if ($dump === TRUE) {
 				Debug::dump($schemaTool->getUpdateSchemaSql($metadatas));
 			} elseif ($dump) {
 				file_put_contents($dump, array_map($mapper, $schemaTool->getUpdateSchemaSql($metadatas)));
 			} else {
 				$schemaTool->updateSchema($metadatas);
 			}
 			
 			echo "DONE" . PHP_EOL;
 		} catch (\Exception $e) {
 			Debug::log($e);
 			echo "Error #" . get_class($e) . ": " . $e->getMessage() . "\n\r";
 		}
 	}
}