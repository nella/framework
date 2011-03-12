<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella;

/**
 * Nella auto loader is responsible for loading Nella classes and interfaces.
 *
 * @author	Patrik Votoček
 */
class NellaLoader extends \Nette\Loaders\AutoLoader
{
	/** @var NetteLoader */
	private static $instance;

	/** @var array */
	public $list = array(
		'nella\configurator' => '/Configurator.php', 
		'nella\framework' => '/Framework.php', 
		'nella\freezablearray' => '/FreezableArray.php', 
		'nella\image' => '/Image.php', 
		'nella\application\backendpresenter' => '/Application/BackendPresenter.php', 
		'nella\application\control' => '/Application/Control.php', 
		'nella\application\presenter' => '/Application/Presenter.php', 
		'nella\application\presenterfactory' => '/Application/PresenterFactory.php', 
		'nella\dependencyinjection\context' => '/DependencyInjection/Context.php', 
		'nella\doctrine\cache' => '/Doctrine/Cache.php', 
		'nella\doctrine\entitymanagerhelper' => '/Doctrine/EntityManagerHelper.php', 
		'nella\doctrine\panel' => '/Doctrine/Panel.php', 
		'nella\doctrine\servicefactory' => '/Doctrine/ServiceFactory.php', 
		'nella\forms\date' => '/Forms/Date.php', 
		'nella\forms\datetime' => '/Forms/DateTime.php', 
		'nella\forms\form' => '/Forms/Form.php', 
		'nella\forms\time' => '/Forms/Time.php', 
		'nella\localization\dictionary' => '/Localization/Dictionary.php', 
		'nella\localization\gettextparser' => '/Localization/GettextParser.php', 
		'nella\localization\translator' => '/Localization/Translator.php', 
		'nella\localization\iparser' => '/Localization/IParser.php', 
		'nella\media\basefileentity' => '/Media/BaseFileEntity.php', 
		'nella\media\fileentity' => '/Media/FileEntity.php', 
		'nella\media\formatentity' => '/Media/FormatEntity.php', 
		'nella\media\imageentity' => '/Media/ImageEntity.php', 
		'nella\media\ifile' => '/Media/IFile.php', 
		'nella\media\iformat' => '/Media/IFormat.php', 
		'nella\media\iimage' => '/Media/IImage.php', 
		'nella\models\baseentity' => '/Models/Entities/BaseEntity.php', 
		'nella\models\duplicateentryexception' => '/Models/Exceptions/DuplicateEntryException.php', 
		'nella\models\emptyvalueexception' => '/Models/Exceptions/EmptyValueException.php', 
		'nella\models\entity' => '/Models/Entities/Entity.php', 
		'nella\models\exception' => '/Models/Exceptions/Exception.php', 
		'nella\models\invalidformatexception' => '/Models/Exceptions/InvalidFormatException.php', 
		'nella\models\repository' => '/Models/Repository.php', 
		'nella\models\service' => '/Models/Service.php', 
		'nella\models\timestampablelistener' => '/Models/Timestampable/TimestampableListener.php', 
		'nella\models\versionentity' => '/Models/Versionable/VersionEntity.php', 
		'nella\models\versionlistener' => '/Models/Versionable/VersionListener.php', 
		'nella\models\iexception' => '/Models/Exceptions/IException.php', 
		'nella\models\itimestampable' => '/Models/Timestampable/ITimestampable.php', 
		'nella\models\iversionable' => '/Models/Versionable/IVersionable.php', 
		'nella\panels\callback' => '/Panels/Callback.php', 
		'nella\panels\version' => '/Panels/Version.php', 
		'nella\security\authenticator' => '/Security/Authenticator.php', 
		'nella\security\authorizator' => '/Security/Authorizator.php', 
		'nella\security\identity' => '/Security/Identity.php', 
		'nella\security\identityentity' => '/Security/IdentityEntity.php', 
		'nella\security\permissionentity' => '/Security/PermissionEntity.php', 
		'nella\security\roleentity' => '/Security/RoleEntity.php', 
		'nella\tools\curlbadrequestexception' => '/Tools/cUrlRequest.php', 
		'nella\tools\curlrequest' => '/Tools/cUrlRequest.php', 
		'nella\tools\curlresponse' => '/Tools/cUrlResponse.php', 
		'nella\tools\dbactionloggerentity' => '/Tools/DBActionLoggerEntity.php', 
		'nella\tools\dbactionloggerservice' => '/Tools/DBActionLoggerService.php', 
		'nella\tools\dbloggerentity' => '/Tools/DBLoggerEntity.php', 
		'nella\tools\dbloggerservice' => '/Tools/DBLoggerService.php', 
		'nella\tools\fileactionlogger' => '/Tools/FileActionLogger.php', 
		'nella\tools\filelogger' => '/Tools/FileLogger.php', 
		'nella\tools\validator' => '/Tools/Validator.php', 
		'nella\tools\iactionlogger' => '/Tools/IActionLogger.php', 
		'nella\tools\ilogger' => '/Tools/ILogger.php', 
	);

	/**
	 * Returns singleton instance with lazy instantiation.
	 * @return NellaLoader
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Handles autoloading of classes or interfaces.
	 * @param  string
	 * @return void
	 */
	public function tryLoad($type)
	{
		$type = ltrim(strtolower($type), '\\');
		if (isset($this->list[$type])) {
			\Nette\Loaders\LimitedScope::load(NELLA_FRAMEWORK_DIR . $this->list[$type]);
			self::$count++;
		}
	}
}
