<?php

return array(
    'connectionString' => 'mysql:host=localhost;dbname=ppztc',
    'emulatePrepare' => true,
    'username' => 'smartbus',
    'password' => 'smartbus',
    'charset' => 'utf8',
    'schemaCachingDuration' => 3600, // 缓存表结构 1 小时，如果表结构有更新需要清空缓存
);
