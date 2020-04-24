<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link https://rmc.ink/
 * @copyright Copyright (c) 2018
 */

// 系统常量定义
defined('VERSION') or define('VERSION', '1.2');
defined('AY') or define('AY', dirname(str_replace('\\', '/', __FILE__)) . '/');
defined('ROOT') or define('ROOT', dirname(AY) . '/');
defined('COMMON') or define('COMMON', AY . 'common/');
defined('CONFIG') or define('CONFIG', AY . 'config/');
defined('LIB') or define('LIB', AY . 'lib/');
defined('DRIVE') or define('DRIVE', AY . 'drive/');
defined('TMP') or define('TMP', AY . 'tmp/');
defined('VENDOR') or define('VENDOR', ROOT . 'vendor/');
defined('EXTEND') or define('EXTEND', ROOT . 'extend/');
defined('PUB') or define('PUB', ROOT . 'public/');
defined('TEMP') or define('TEMP', ROOT . 'temp/');
defined('CACHE') or define('CACHE', TEMP . 'cache/');
defined('COMPILE') or define('COMPILE', TEMP . 'compile/');
defined('LOG') or define('LOG', TEMP . 'log/');
define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false);
define('IS_GET', ($_SERVER['REQUEST_METHOD'] == 'GET') ? true : false);


$url = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' . $_SERVER['SERVER_NAME'] : 'http://' . $_SERVER['SERVER_NAME'];
defined('URL') or define('URL', $url);

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpREquest') {
    $is_ajax = true;
} else {
    $is_ajax = false;
}

defined('IS_AJAX') or define('IS_AJAX', $is_ajax);

// 用户目录常量
defined('APP_PATH') or define('APP_PATH', ROOT . APP_NAME . '/');
defined('APP_VIEW') or define('APP_VIEW', APP_PATH . 'view/');
defined('APP_CONTROLLER') or define('APP_CONTROLLER', APP_PATH . 'controller/');
defined('APP_CONFIG') or define('APP_CONFIG', ROOT . 'config/');
defined('APP_COMMON') or define('APP_COMMON', ROOT . 'common/');
defined('APP_MODEL') or define('APP_MODEL', APP_PATH . 'model/');

// 自动加载
require VENDOR . 'autoload.php';
require DRIVE . 'Autoload.php';
\ay\drive\Autoloader::instance()->init();

// 安全
require DRIVE . 'Safe.php';
// \ay\drive\Safe::instance()->init();

//  加载扩展函数
require COMMON . 'function.php';

// 加载默认配置
$configArr = [
    'system.php',
    'database.php'
];
foreach ($configArr as $v) {
    if (file_exists(APP_PATH . $v)) {
        $path = APP_PATH . $v;
    } else {
        $path = CONFIG . $v;
    }
    C(include $path);
}

// 错误类
error_reporting(E_ALL);
ini_set('display_errors', 'On');

if (C('DEBUG')) {
    \Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT);
    \Tracy\Debugger::$strictMode = true;
} else {
    \Tracy\Debugger::enable(\Tracy\Debugger::PRODUCTION);
}

// 加载用户自定义配置
$userCommonFile = scandir(APP_CONFIG);
foreach ($userCommonFile as $v) {
    if (!strstr($v, '.php') or $v == '.' or $v == '..') continue;
    C(include APP_CONFIG . $v);
}

// 加载用户自定义函数
if (file_exists(APP_PATH . 'function.php')) {
    require_once APP_PATH . 'function.php';
}

runtime('KJ');

// 框架启动
\ay\Core::run();
