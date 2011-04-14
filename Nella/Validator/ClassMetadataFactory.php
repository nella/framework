<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Validator;

use Nette\Caching\Cache;

/**
 * Class metadata factory
 *
 * @author	Patrik Votoček
 */
class ClassMetadataFactory extends \Nette\Object implements IClassMetadataFactory
{
	/** @var \Nette\Caching\Cache */
	private $cache;
	/** @var array */
	private $metas = array();
	/** @var array */
	private $parsers = array();

	/**
	 * @param \Nette\Caching\ICacheStorage
	 */
	public function __construct(\Nette\Caching\IStorage $cacheStorage = NULL)
	{
		$this->cache = $cacheStorage ? new Cache($cacheStorage, "Nella.Validator.Metadata") : array();
		$this->loadDefaultParsers();
	}

	protected function loadDefaultParsers()
	{
		$this->addParser(new AnnotationParser);
		//$this->addParser(new DoctrineAnnotationParser);
	}

	/**
	 * @param IMetadataParser
	 * @return ClassMetadataFactory
	 */
	public function addParser(IMetadataParser $parser)
	{
		$this->parsers[] = $parser;
		return $this;
	}

	/**
	 * @param string
	 * @return ClassMetadata
	 * @throws \Nette\InvalidStateException
	 */
	public function getClassMetadata($class)
	{
		$lower = strtolower($class);

		if (isset($this->metas[$lower])) {
			return $this->metas[$lower];
		}

		if ($this->cache && $this->cache[$lower]) {
			return $this->metas[$lower] = $this->cache[$lower];
		}

		if (!class_exists($lower)) {
			throw new \Nette\InvalidArgumentException("Class '$class' not exist");
		}

		$metadata = new ClassMetadata($class);
		foreach ($this->parsers as $parser) {
			$parser->parse($metadata);
		}

		if ($this->cache) {
			$this->cache->save($lower, $metadata, array(
				Cache::FILES => array($metadata->getReflection()->getFileName())
			));
		}

		return $this->metas[$lower] = $metadata;
	}
}