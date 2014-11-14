<?php

return array(
    'class' => 'CLogRouter',
    'routes' => array(
        array(
            'class' => 'CFileLogRoute',
            'levels' => 'error, warning',
        ),
        // uncomment the following to show log messages on web pages
        /*
        array(
            'class'=>'CWebLogRoute',
        ),
        */
    ),
);
