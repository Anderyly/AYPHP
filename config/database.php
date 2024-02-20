<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

use ay\lib\Env;

return [
    'DB_TYPE' => Env::get("db.type", "mysql"),
    'DB_HOST' => Env::get("db.host", "127.0.0.1"),
    'DB_PORT' => Env::get("db.port", 3306),
    'DB_USER' => Env::get("db.user", "root"),
    'DB_PASS' => Env::get("db.pass", "root"),
    'DB_NAME' => Env::get("db.database", "ay"),
    'DB_PRE' => Env::get("db.pre", "ay_")
];
