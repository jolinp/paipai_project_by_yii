<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

$signFile = __DIR__ . DIRECTORY_SEPARATOR . 'deploy';

if (file_exists($signFile)) {
    define('YII_DEBUG', false);
    $config = require 'production/main.php';
} else {
    define('YII_DEBUG', true);
    define('YII_TRACE_LEVEL', 3);
    $config = require 'development/main.php';
}

$common = require 'common.php';

return array_merge_recursive($common, $config);
