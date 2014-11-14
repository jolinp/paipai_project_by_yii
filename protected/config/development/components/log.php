<?php

return array(
    'class' => 'CLogRouter',
    'routes' => array(
        array(
            'class' => 'CFileLogRoute',
            'levels'=>'error, warning',
            'logFile'=>"application_".date("Ymd").".log",
        ),
        array(
            'class'=>'CFileLogRoute',
            'levels'=>'error',
            'logFile'=>"application_".date("Ymd").".log",
            'categories'=>array('api'),
        ),
    ),
);
