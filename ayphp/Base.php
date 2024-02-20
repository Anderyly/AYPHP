<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

namespace ay;

use ay\drive\Autoloader;
use ay\drive\Error;
use ay\drive\Safe;
use ay\lib\Env;

class Base
{

    public function __construct()
    {
        // 记录开始运行时间
        $GLOBALS['_startTime'] = microtime(true);

        // 系统常量定义
        defined('VERSION') or define('VERSION', '2.0');
        defined('AY') or define('AY', dirname(str_replace('\\', '/', __FILE__)) . '/');
        defined('ROOT') or define('ROOT', dirname(AY) . '/');
        defined('COMMON') or define('COMMON', AY . 'common/');
        defined('CONFIG') or define('CONFIG', ROOT . 'config/');
        defined('LIB') or define('LIB', AY . 'lib/');
        defined('DRIVE') or define('DRIVE', AY . 'drive/');
        defined('TEMPLATE') or define('TEMPLATE', AY . 'template/');
        defined('TMP') or define('TMP', AY . 'tmp/');
        defined('VENDOR') or define('VENDOR', ROOT . 'vendor/');
        defined('EXTEND') or define('EXTEND', ROOT . 'extend/');
        defined('PUB') or define('PUB', ROOT . 'public/');
        defined('TEMP') or define('TEMP', ROOT . 'temp/');
        defined('CACHE') or define('CACHE', TEMP . 'cache/');
        defined('COMPILE') or define('COMPILE', TEMP . 'compile/');
        defined('LOG') or define('LOG', TEMP . 'log/');

        // 用户目录常量
        defined('APP_PATH') or define('APP_PATH', ROOT . APP_NAME . '/');
        defined('APP_VIEW') or define('APP_VIEW', APP_PATH . 'view/');
        defined('APP_CONTROLLER') or define('APP_CONTROLLER', APP_PATH . 'controller/');
        defined('APP_CONFIG') or define('APP_CONFIG', ROOT . 'config/');
        defined('APP_COMMON') or define('APP_COMMON', ROOT . 'common/');
        defined('APP_MODEL') or define('APP_MODEL', APP_PATH . 'model/');

        // 自动加载
        if (file_exists(VENDOR . 'autoload.php')) require VENDOR . 'autoload.php';
        require DRIVE . 'Autoload.php';
        Autoloader::instance()->init();

        Safe::instance()->init();

        //  加载扩展函数
        require AY . 'unity.php';

        $domain = $_SERVER['HTTP_HOST'] ?? Env::get("DOMAIN") ?? C("APP.DOMAIN");
        if (is_ssl()) {
            $url = 'https://' . $domain;
        } else {
            $url = 'http://' . $domain;
        }
        defined('URL') or define('URL', $url);

        // 加载默认配置
        $configFileArr = scandir(CONFIG);
        foreach ($configFileArr as $v) {
            if (!str_contains($v, '.php') or $v == '.' or $v == '..') {
                continue;
            }
            C([str_replace('.php', '', $v) => include CONFIG . $v]);
        }


        // 错误类
        error_reporting(0);
        if (C('APP.DEBUG')) {
            ini_set('display_errors', 'On');
        } else {
            ini_set('display_errors', 'off');
        }

        Error::instance()->init();

        // 加载环境变量配置文件
        if (is_file(ROOT . '.env')) {
            $env = parse_ini_file(ROOT . '.env', true);

            foreach ($env as $key => $val) {
                $name = 'PHP_' . strtoupper($key);

                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $item = $name . '_' . strtoupper($k);
                        putenv("$item=$v");
                    }
                } else {
                    putenv("$name=$val");
                }
            }
        }


        // 加载配置
        $userCommonFile = scandir(APP_CONFIG);
        foreach ($userCommonFile as $v) {
            if (!str_contains($v, '.php') or $v == '.' or $v == '..') {
                continue;
            }

            C([str_replace('.php', '', $v) => include APP_CONFIG . $v]);
        }

        // 加载用户自定义函数
        if (file_exists(APP_PATH . 'function.php')) {
            require_once APP_PATH . 'function.php';
        }
    }

    public static function http()
    {
        Core::run();
    }

    public static function console()
    {
        defined('RUN_METHOD') or define('RUN_METHOD', 'cli');
        Core::run();
    }
}
