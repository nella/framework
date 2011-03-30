<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Panels;

use Nette\Reflection\ClassReflection;

/**
 * Version panel for nette debug bar
 *
 * @author		Patrik Votoček
 */
class Version implements \Nette\IDebugPanel
{
	/** @var array */
	private $updates = array();
	/** @var array */
	private $libs = array();
	/** @var bool */
	private static $registered = FALSE;

	const VERSION = "3.0";

	/**
	 * Get panel id
	 *
	 * @return string
	 */
	public function getId()
	{
		return 'version-panel';
	}

	/**
	 * Get rendered panel
	 *
	 * @return string
	 */
	public function getPanel()
	{
		if (count($this->updates) < 1) {
			$this->loadUpdates();
		}
		
		$updates = $this->updates;
		$libs = $this->libs;

		ob_start();
		require_once __DIR__ . "/Version.phtml";
		return ob_get_clean();
	}

	/**
	 * Get rendered tab
	 *
	 * @return string
	 */
	public function getTab()
	{
		if (count($this->updates) < 1) {
			$this->loadUpdates();
		}
		
		$data = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAjpJREFUeNpi/P//PwMlgAWb4M/f/xhWX3qltPrKW5P/7CxCinxsT0JU+I6byfC+ZediQ1HLiO6CX//+M0WsulW1/tKbIgZmBkGGf0B5ZiYGhu+/7/urC+Qvi9PZzMXGjN2Aj19/MSStu5Wz7sqbyQwMQPHffz8BldwFqtJk4mDj+Pfr36cF4Rrm8SYSN2B6mJBtP//sE+e6Yw9ymH58BVr96/HCKC2ng9lGJjH6oqn/Pnz8z/D9C9/CU4+9cYYB079/zAyfPvz+B3S2gZH0xDgTqbMg8aP3Pz1i+HYd6BhGhv/fvqJYisIxlOX/sjDDItJBTSCu3Flp6ndgYC48fEe/ffnheez/fzEyfPj4KdFMZjNKoIHCABdOm3HAisGz/wV72LT/DN793wI7tgZ8+/EbRQ0Trvi9fP815/Id5yZzsfwT//Xx07swC4XAFUXuGzjZUWMepwGv331W+vzyrcHfL58ZTGX5G5aU++5kY2XGUIfTAAlB7tfSfKzH/nz8dL0y2mYjKxbNeA1QlhF+pSbKvYDhy+d1f3/8fIMzLeMKwMLGZSZsinH/OVXi//sl9zfgUgcPkbdv3zKsWL6cfdWqVewszMyMQjL6rLxc7L8YmdjYLpzc/9PJcSMfKOXKysr+zczM/G5hafkXxQVnz5xhMDExEQK5Hog1gVidgZHfl4FdJg7I1oZiDSCWz0hP54Tpw8hM165eZXj77h0DI8jwf39AOYKBiQkUgIxgtrCQEIOWtjZcPUCAAQD2kictFO3NpAAAAABJRU5ErkJggg==">versions';
		if (count($this->updates) > 0) {
			$data .= '<span style="background: #47d; border: 1px solid #126; border-bottom-left-radius: 3px 3px; border-bottom-right-radius: 3px 3px; border-top-left-radius: 3px 3px; border-top-right-radius: 3px 3px; bottom: 0px; color: white; display: block; font-size: 75%; font-weight: bold; line-height: 100%; position: absolute;">' . count($this->updates) . '</span>';
		}
		return $data;
	}

	/**
	 * Load all updates
	 */
	private function loadUpdates()
	{
		$libs = array();
		// Nette Framework
		$libs['nette:nette'] = array(
			'name' => \Nette\Framework::NAME, 
			'version' => \Nette\Framework::VERSION, 
			'revision' => \Nette\Framework::REVISION, 
			'url' => "http://files.nette.org/NetteFramework-%tag2%-PHP5.3.zip", 
			'url-dev' => "http://files.nette.org/NetteFramework-%version%dev-PHP5.3.zip", 
			'file' => ClassReflection::from('Nette\Framework')->getFileName(), 
		);
		
		// Nella Framework
		$libs['nella:framework'] = array(
			'name' => \Nella\Framework::NAME, 
			'version' => \Nella\Framework::VERSION, 
			'revision' => \Nella\Framework::REVISION, 
			'url' => "http://s3.nella-project.org/package/NellaFramework-%tag%.zip",  
			'url-dev' => "http://s3.nella-project.org/package/NellaFramework-%version%dev.zip", 
			'file' => ClassReflection::from('Nella\Framework')->getFileName(), 
		);

		// dibi
		if (class_exists('dibi')) {
			$libs['nette:dibi'] = array(
				'name' => "dibi", 
				'version' => \dibi::VERSION, 
				'revision' => \dibi::REVISION, 
				'url' => "http://files.dibiphp.com/dibi-%tag2%.zip", 
				'url-dev' => "http://files.dibiphp.com/latest.zip", 
				'file' => ClassReflection::from('dibi')->getFileName(), 
			);
		}
		
		// Texy
		if (class_exists('Texy')) {
			$libs['dg:texy'] = array(
				'name' => "Texy!", 
				'version' => \Texy::VERSION, 
				'revision' => \Texy::REVISION, 
				'url' => "http://files.texy.info/latest.zip", 
				'url-dev' => "http://files.texy.info/texy-%version%-dev.zip", 
				'file' => ClassReflection::from('Texy')->getFileName(), 
			);
		}
		
		// Doctrine\Common
		if (class_exists('Doctrine\Common\Version')) {
			$libs['doctrine:common'] = array(
				'name' => "Doctrine Common", 
				'version' => \Doctrine\Common\Version::VERSION,
				'revision' => NULL, 
				'url' => "http://github.com/%user%/%repo%/zipball/%tag%", 
				'url-dev' => NULL, 
				'file' => ClassReflection::from('Doctrine\Common\Version')->getFileName(), 
			);
		}
		
		// Doctrine\DBAL
		if (class_exists('Doctrine\DBAL\Version')) {
			$libs['doctrine:dbal'] = array(
				'name' => "Doctrine DBAL", 
				'version' => \Doctrine\DBAL\Version::VERSION,
				'revision' => NULL, 
				'url' => "http://github.com/%user%/%repo%/zipball/%tag%", 
				'url-dev' => NULL, 
				'file' => ClassReflection::from('Doctrine\DBAL\Version')->getFileName(),
			);
		}
		
		// Doctrine\ORM
		if (class_exists('Doctrine\ORM\Version')) {
			$libs['doctrine:doctrine2'] = array(
				'name' => "Doctrine ORM", 
				'version' => \Doctrine\ORM\Version::VERSION,
				'revision' => NULL, 
				'url' => "http://github.com/%user%/%repo%/zipball/%tag%", 
				'url-dev' => NULL, 
				'file' => ClassReflection::from('Doctrine\ORM\Version')->getFileName(),
			);
		}
		
		// Doctrine\MongoDB
		if (class_exists('Doctrine\MongoDB\Version')) {
			$libs['doctrine:mongodb'] = array(
				'name' => "Doctrine MongoDB", 
				'version' => \Doctrine\MongoDB\Version::VERSION,
				'revision' => NULL, 
				'url' => "http://github.com/%user%/%repo%/zipball/%tag%", 
				'url-dev' => NULL, 
				'file' => ClassReflection::from('Doctrine\MongoDB\Version')->getFileName(),
			);
		}
		
		// Doctrine\ODM\MongoDB
		if (class_exists('Doctrine\ODM\MongoDB\Version')) {
			$libs['doctrine:mongodb-odm'] = array(
				'name' => "Doctrine MongoDB ODM", 
				'version' => \Doctrine\ODM\MongoDB\Version::VERSION,
				'revision' => NULL, 
				'url' => "http://github.com/%user%/%repo%/zipball/%tag%", 
				'url-dev' => NULL, 
				'file' => ClassReflection::from('Doctrine\ODM\MongoDB\Version')->getFileName(),
			);
		}
		
		// Doctrine ORM 1
		if (class_exists('Doctrine_Core')) {
			$libs['doctrine:doctrine'] = array(
				'name' => "Doctrine ORM", 
				'version' => \Doctrine_Core::VERSION,
				'revision' => NULL, 
				'url' => "http://github.com/%user%/%repo%/zipball/%tag%", 
				'url-dev' => NULL, 
				'file' => ClassReflection::from('Doctrine_Core')->getFileName(), 
			);
		}
		
		// Symfony
		if (class_exists('Symfony\Component\HttpKernel\Kernel')) {
			$libs['symfony:symfony'] = array(
				'name' => "Symfony 2", 
				'version' => \Symfony\Component\HttpKernel\Kernel::VERSION,
				'revision' => NULL, 
				'url' => "http://github.com/%user%/%repo%/zipball/%tag%", 
				'url-dev' => NULL, 
				'file' => ClassReflection::from('Symfony\Component\HttpKernel\Kernel')->getFileName(), 
			);
		}
		
		if (is_array($this->libs)) {
			$this->libs = array_merge($this->libs, $libs);
		} else {
			$this->libs = $libs;
		}
		
		$cache = \Nette\Environment::getCache('Nella.Panels.Version');
		$this->updates = array();
		if (class_exists('Nella\Tools\cUrlRequest') && !isset($cache['updates'])) {
			$files = array();
			
			foreach ($this->libs as $repo => $lib) {
				$files[] = $lib['file'];
				$data = $this->getLatestByGithub($repo, $lib['version'], $lib['revision']);
				if ($data) {
					$this->updates[$lib['name']] = $data;
				}
			}

			$files[] = __FILE__;
			$files[] = __DIR__ . "/Version.phtml";
			$cache->save('updates', $this->updates, array('expire' => time() + 60 * 60 * 2, 'files' => $files));
		} elseif (isset($cache['updates'])) {
			$this->updates = $cache['updates'];
		}
	}

	/**
	 * Get cURL response
	 *
	 * @param string
	 * @return string
	 */
	private function getCurlResponse($url)
	{
		$res = new \Nella\Tools\cUrlRequest($url);
		$res->setUserAgent("Mozilla/5.0 (compatible; Nella\\Panels\\Version/".self::VERSION."; http://addons.nette.org/cs/versionpanel)");
		$res->setOption('returntransfer', TRUE)->setOption('header', TRUE)->setOption('followlocation', TRUE)->setOption('ssl_verifypeer', FALSE);
		$res->setHeader('HTTP_ACCEPT', "text/javascript,text/json,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8*/*;q=0.5");
		$res->setHeader('HTTP_ACCEPT_CHARSET', "windows-1250,utf-8;q=0.7,*;q=0.7")->setHeader('HTTP_KEEP_ALIVE', "300");
		$res->setHeader('HTTP_CONNECTION', "keep-alive");
		
		return $res->response->body;
	}

	/**
	 * Get response by array
	 *
	 * @param string $url
	 * @return array
	 */
	private function getArrayResponse($url)
	{
		return json_decode($this->getCurlResponse($url), TRUE);
	}

	/**
	 * Get latest version by GitHub API
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @return array|NULL
	 */
	private function getLatestByGithub($repo, $version, $revision = NULL)
	{
		list($userId, $repo) = explode(':', $repo);
		
		$dev = FALSE;
		if (strpos($version, 'dev') !== FALSE) {
			$dev = TRUE;
			$version = substr($version, 0, strpos($version, "-dev"));
		}
		$tags = $this->getArrayResponse("http://github.com/api/v2/json/repos/show/$userId/$repo/tags");
		if (empty($tags) || !array_key_exists('tags', $tags)) {
			return;
		}

		$keys = array_keys($tags['tags']);
		sort($keys);
		$mapper = function ($input) use ($version, $repo) {
			return version_compare(strpos($input, 'v') !== FALSE ? substr($input, 1) : $input, $version, '>');
		};
		$latest = (bool)count($keys) ? !(bool)count(array_filter($keys, $mapper)) : FALSE;
		if (!$latest && !$dev) {
			$tag = $keys[count($keys)-1];
			$commit = $tags['tags'][$tag];

			$commitData = $this->getArrayResponse("http://github.com/api/v2/json/commits/show/$userId/$repo/".$commit);
			$date = $commitData['commit']['authored_date'];
			$timeZone = ini_get("date.timezone");
			if (!empty($timeZone)) {
				$date = date_create($date)->setTimezone(new \DateTimeZone($timeZone))->format("c");
			}

			$data = array(
				'version' => strpos($tag, 'v') !== FALSE ? substr($tag, 1) : $tag,
				'revision' => substr($commit, 0, 7)." released on ".substr($date, 0, 10),
			);
			
			$replace = array(
				'%tag%' => $tag, 
				'%tag2%' => substr($tag, 1), 
				'%user%' => $userId, 
				'%repo%' => $repo, 
			);
			
			if (isset($this->libs[$userId.":".$repo]) && isset($this->libs[$userId.":".$repo]['url'])) {
				$data['url'] = str_replace(array_keys($replace), array_values($replace), $this->libs[$userId.":".$repo]['url']);
			}


			return $data;
		}
		
		if (!$dev) {
			return;
		}

		$commits = $this->getArrayResponse("http://github.com/api/v2/json/commits/list/$userId/$repo/master");
		if (empty($commits) || !array_key_exists('commits', $commits) || !array_key_exists(0, $commits['commits']) || empty($revision)) {
			return;
		}

		if (substr($commits['commits'][0]['id'], 0, 7) != substr($revision, 0, 7)) {
			$date = $commits['commits'][0]['authored_date'];
			$timeZone = ini_get("date.timezone");
			if (!empty($timeZone)) {
				$date = date_create($date)->setTimezone(new \DateTimeZone($timeZone))->format("c");
			}

			$data = array(
				'version' => $version."-dev",
				'revision' => substr($commits['commits'][0]['id'], 0, 7)." released on ".substr($date, 0, 10),
			);

			$replace = array(
				'%user%' => $userId, 
				'%repo%' => $repo, 
			);
			
			if (isset($this->libs[$userId.":".$repo]) && isset($this->libs[$userId.":".$repo]['url-dev'])) {
				$data['url'] = str_replace(array_keys($replace), array_values($replace), $this->libs[$userId.":".$repo]['url-dev']);
			}

			return $data;
		}

		return;
	}
	
	/**
	 * @param array
	 */
	protected function __construct(array $libs = NULL)
	{
		$this->libs = $libs;
	}

	/**
	 * Register this panel
	 * 
	 * @param array
	 */
	public static function register(array $libs = NULL)
	{
		if (static::$registered) {
			throw new \InvalidStateException("Version panel is already registered");
		}
		
		\Nette\Debug::addPanel(new static($libs));
		static::$registered = TRUE;
	}
}
