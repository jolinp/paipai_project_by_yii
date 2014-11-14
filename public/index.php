<?php
// change the following paths if necessary
$config = require dirname(__FILE__) . '/../protected/config/main.php';

if (YII_DEBUG) {
    $yii = dirname(__FILE__) . '/../framework/yii.php';
} else {
    $yii = dirname(__FILE__) . '/../framework/yiilite.php';
}

require_once($yii);

$app = Yii::createWebApplication($config);
require_once dirname(dirname(__FILE__)) .'/protected/vendor/toplinker/lib/TopLinker/Autoloader.php';
Yii::registerAutoloader(array('TopLinker_Autoloader', 'autoload'));

$app->run();
