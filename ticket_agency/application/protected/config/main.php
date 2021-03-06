<?php

Yii::setPathOfAlias('common', realpath(dirname(__FILE__) . '/../../../common'));
include(dirname(__FILE__) . '/../../../../setting/ticket_agency.php');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$config = array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => '智慧旅游票务平台',
    // preloading 'log' component
    'preload' => array('log'),
    'defaultController' => 'site',
    'layout' => 'main',
    'language' => 'zh_cn',
    // autoloading model and component classes
    'import' => array(
        'application.controllers.system.*',
        'application.models.*',
        'application.api.*',
        'application.components.*',
        'application.helpers.*',
        'application.extensions.*',
        'common.components.UActiveRecord',
        'common.extensions.CDbConnectionExt',
        'common.helpers.*'
    ),
    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '1',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            //'ipFilters'=>array('127.0.0.1','::1'),
            'ipFilters' => FALSE,
        ),
        'igii' => array(
            'class' => 'common.igii.IgiiModule',
        ),
        'urbac' => array(
            'userClass' => 'Member',
            'userId' => 'username',
            'username' => 'display_name',
            'resourceClass' => 'Site',
            'resourceId' => 'id',
            'resourceName' => 'name',
            'resourceKey' => 'site_id',
            'resourceLabel' => '网站'
        ),
        'api',
    ),
    'components' => array(
        'user' => array(
            'class' => 'system.web.auth.CWebUser',
            'allowAutoLogin' => true,
        ),
        'session' => array (
            'class' => 'system.web.CCacheHttpSession',
        ),
        'upyun' => array(
            'class' => 'common.components.UYouPai',
            'formApiSecret' => 'tIBpPPoqLDZ07XvWjMYXzyI8Ulw=',
            'bucket' => 'piaowu',
            'uploadDir' => 'jiankong',
            'returnUrl' => '/site/upyunAgent/',
            'host' => 'http://piaowu.b0.upaiyun.com',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
            'showScriptName' => false,
            'caseSensitive' => true,
        ),
        'cache' => array(
            'class' => 'common.caching.URedisCache',
            'host' => '127.0.0.1',
            'port' => '6379',
        ),
        'redis' => array(
            'class' => 'common.components.URedis',
            'host' => '127.0.0.1',
            'port' => '6379',
        ),
        'db' => array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=statsys',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        //'tablePrefix'=>'kf_'
        ),
        'clientScript' => array(
            'scriptMap' => array(
                'jquery.js' => '/js/jquery-1.7.2.min.js'
            )
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
    ),
    'params' => array(
	    'supplyUrl' => 'http://supply.pw/',
        // this is used in contact page
        'ticket-api-info' => array(
            'url' => 'http://ticket-api-info.demo.org.cn/v1/',
            'sign' => 'debug'
        ),
        'ticket-api-organization' => array(
            'url' => 'http://ticket-api-organization.demo.org.cn/v1/',
            'sign' => 'debug'
        ),
        'ticket-api-scenic' => array(
            'url' => 'http://ticket-api-scenic.demo.org.cn/v1/',
            'sign' => 'debug'
        ),
        'ticket-api-order' => array('url' => 'http://ticket-api-order.demo.org.cn/v1/', 'sign' => 'huilian123'),
    ),
);
defined('PW_CACHE') || define('PW_CACHE', 'a:0:{}');
defined('PW_REDIS') || define('PW_REDIS', 'a:0:{}');
defined('PW_DB') || define('PW_DB', 'a:0:{}');
defined('EXT') || define('EXT', 'a:0:{}');
defined('PARAMS') || define('PARAMS', 'a:0:{}');
return array_replace_recursive($config, unserialize(PW_CACHE), unserialize(PW_REDIS), unserialize(PW_DB), unserialize(EXT), unserialize(PARAMS));

