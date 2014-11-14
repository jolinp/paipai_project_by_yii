<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);

$yiic = dirname(__FILE__) . '/../framework/yiic.php';
$config = require dirname(__FILE__) . '/config/main.php';
unset($config['controllerNamespace']);

require_once(dirname(__FILE__).'/../framework/yii.php');

if(isset($config))
{
    $app=Yii::createConsoleApplication($config);
    $app->commandRunner->addCommands(YII_PATH.'/cli/commands');
    $env=@getenv('YII_CONSOLE_COMMANDS');
    if(!empty($env))
        $app->commandRunner->addCommands($env);
}
else
    $app=Yii::createConsoleApplication(array('basePath'=>YII_PATH.'/cli'));

require_once dirname(__FILE__) . '/vendor/toplinker/lib/TopLinker/Autoloader.php';
Yii::registerAutoloader(array('TopLinker_Autoloader', 'autoload'));

$app->run();