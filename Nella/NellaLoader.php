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
	/** @var NellaLoader */
	private static $instance;

	/** @var array */
	public $list = array(
		'nella\framework' => '/Framework.php',
		'nella\freezablearray' => '/FreezableArray.php',
		'nella\image' => '/Image.php',
		'nella\consoleservicefactory' => '/ConsoleServiceFactory.php',
		'nella\application\application' => '/Application/Application.php',
		'nella\application\ui\backendpresenter' => '/Application/UI/BackendPresenter.php',
		'nella\application\ui\control' => '/Application/UI/Control.php',
		'nella\application\ui\presenter' => '/Application/UI/Presenter.php',
		'nella\application\presenterfactory' => '/Application/PresenterFactory.php',
		'nella\di\icontext' => '/DI/IContext.php',
		'nella\di\context' => '/DI/Context.php',
		'nella\di\contextbuilder' => '/DI/ContextBuilder.php',
		'nella\di\contexthelper' => '/DI/ContextHelper.php',
		'nella\di\iservicefactory' => '/DI/IServiceFactory.php',
		'nella\di\servicefactory' => '/DI/ServiceFactory.php',
		'nella\doctrine\cache' => '/Doctrine/Cache.php',
		'nella\doctrine\entitymanagerhelper' => '/Doctrine/EntityManagerHelper.php',
		'nella\doctrine\panel' => '/Doctrine/Panel.php',
		'nella\doctrine\servicefactory' => '/Doctrine/ServiceFactory.php',
		'nella\forms\controls\basedatetime' => '/Forms/Controls/BaseDateTime.php',
		'nella\forms\controls\date' => '/Forms/Controls/Date.php',
		'nella\forms\controls\datetime' => '/Forms/Controls/DateTime.php',
		'nella\forms\controls\time' => '/Forms/Controls/Time.php',
		'nella\forms\controls\multiplefileupload' => '/Forms/Controls/MultipleFileUpload.php',
		'nella\forms\form' => '/Forms/Form.php',
		'nella\latte\macros' => '/Latte/Macros.php',
		'nella\localization\dictionary' => '/Localization/Dictionary.php',
		'nella\localization\parsers\gettext' => '/Localization/Parsers/Gettext.php',
		'nella\localization\translator' => '/Localization/Translator.php',
		'nella\localization\iparser' => '/Localization/IParser.php',
		'nella\media\basefileentity' => '/Media/BaseFileEntity.php',
		'nella\media\fileentity' => '/Media/FileEntity.php',
		'nella\media\fileroute' => '/Media/FileRoute.php',
		'nella\media\formatentity' => '/Media/FormatEntity.php',
		'nella\media\imageentity' => '/Media/ImageEntity.php',
		'nella\media\imageroute' => '/Media/ImageRoute.php',
		'nella\media\ifile' => '/Media/IFile.php',
		'nella\media\iformat' => '/Media/IFormat.php',
		'nella\media\iimage' => '/Media/IImage.php',
		'nella\media\mediapresenter' => '/Media/MediaPresenter.php',
		'nella\models\entity' => '/Models/Entity.php',
		'nella\models\notvalidentityException' => '/Models/NotValidEntityException.php',
		'nella\models\repository' => '/Models/Repository.php',
		'nella\models\service' => '/Models/Service.php',
		'nella\models\listeners\timestampable' => '/Models/Listeners/Timestampable.php',
		'nella\models\listeners\userable' => '/Models/Listeners/Userable.php',
		'nella\models\listeners\validator' => '/Models/Listeners/Validator.php',
		'nella\models\listeners\version' => '/Models/Listeners/Version.php',
		'nella\models\listeners\versionentity' => '/Models/Listeners/VersionEntity.php',
		'nella\models\iversionableentity' => '/Models/IVersionableEntity.php',
		'nella\panels\callback' => '/Panels/Callback.php',
		'nella\panels\version' => '/Panels/Version.php',
		'nella\security\authenticator' => '/Security/Authenticator.php',
		'nella\security\authorizator' => '/Security/Authorizator.php',
		'nella\security\identity' => '/Security/Identity.php',
		'nella\security\identityentity' => '/Security/IdentityEntity.php',
		'nella\security\permissionentity' => '/Security/PermissionEntity.php',
		'nella\security\roleentity' => '/Security/RoleEntity.php',
		'nella\security\rolerepository' => '/Security/RoleRepository.php',
		'nella\utils\curl\badrequestexception' => '/Utils/Curl/BadRequestException.php',
		'nella\utils\curl\request' => '/Utils/Curl/Request.php',
		'nella\utils\curl\response' => '/Utils/Curl/Response.php',
		'nella\utils\loggerstorages\actionentity' => '/Utils/LoggerStorages/ActionEntity.php',
		'nella\utils\loggerstorages\databasestorage' => '/Utils/LoggerStorages/DatabaseStorage.php',
		'nella\utils\loggerstorages\filestorage' => '/Utils/LoggerStorages/FileStorage.php',
		'nella\utils\iactionlogger' => '/Utils/IActionLogger.php',
		'nella\validator\ivalidator' => '/Validator/IValidator.php',
		'nella\validator\validator' => '/Validator/Validator.php',
		'nella\validator\iclassmetadatafactory' => '/Validator/IClassMetadataFactory.php',
		'nella\validator\classmetadatafactory' => '/Validator/ClassMetadataFactory.php',
		'nella\validator\classmetadata' => '/Validator/ClassMetadata.php',
		'nella\validator\imetadataparser' => '/Validator/IMetadataParser.php',
		'nella\validator\metadataparsers\annotation' => '/Validator/MetadataParsers/Annotation.php',
		//'nella\validator\doctrineannotationparser' => '/Validator/DoctrineAnnotationParser.php',
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
			\Nette\Utils\LimitedScope::load(NELLA_FRAMEWORK_DIR . $this->list[$type]);
			self::$count++;
		}
	}
}
