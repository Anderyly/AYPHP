<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

use ay\lib\Env;

return [
    'DEBUG' => Env::get("app.debug", false),
    'DEFAULT_TIME_ZONE' => 'PRC',
    'REWRITE' => '',
    'SAVE_ERROR_LOG' => true,
    'SAVE_VISIT_LOG' => true,
    'HOME' => 'index.php',
    'MODE' => 'index',
    'CONTROLLER' => 'index',
    'ACTION' => 'index',
    'CACHE' => true,
    'CACHE_TIME' => 1,
    'CHARSET' => 'UTF-8',
    'KEY' => 'anderyly',
    'DOMAIN' => Env::get("app.domain", "127.0.0.1"),
];