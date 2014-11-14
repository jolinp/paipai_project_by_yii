<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => '啪啪直通车',
    'sourceLanguage' => 'zh_cn',

    //set default layout
    'layout' => '//layout/no_style',

    // preloading 'log' component
    'preload' => array('log'),

    // autoloading model and component classes
    'import' => array(
        'application.components.*',
        'application.commands.*',
        'application.extensions.extended.common.db.*',
        'application.models.*',
        'application.vendor.*',
        'application.vendor.pop.*',
        'application.vendor.pop.request.*',
        'application.events.*',
        'application.extensions.extended.common.web.*',
        'application.extensions.extended.common.web.actions.*',
        'application.vendor.grammar.*',
    ),

    'controllerNamespace' => 'application\\controllers',

    'modules' => array( // uncomment the following to enable the Gii tool

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),

        'admin' => array(),
        'discount' => array('controllerNamespace' => 'application\modules\discount\controllers'),
    ),

    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'class' => 'User',
            'allowAutoLogin' => true,
        ),
        // uncomment the following to enable URLs in path-format
        /*
        'urlManager'=>array(
            'urlFormat'=>'path',
            'rules'=>array(
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),
        */
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'clientScript' => array(
            'class' => 'application.extensions.EClientScript.EClientScript',
            'combineScriptFiles' => !YII_DEBUG, // By default this is set to true, set this to true if you'd like to combine the script files
            'combineCssFiles' => !YII_DEBUG, // By default this is set to true, set this to true if you'd like to combine the css files
            'optimizeScriptFiles' => false, // @since: 1.1
            'optimizeCssFiles' => false, // @since: 1.1
            'optimizeInlineScript' => false, // @since: 1.6, This may case response slower
            'optimizeInlineCss' => false, // @since: 1.6, This may case response slower
        )/*,
        'request' => array(
            'enableCsrfValidation' => true, //csrf验证，在ajax的时候要加上这个参数
        ),*/
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(),
);
