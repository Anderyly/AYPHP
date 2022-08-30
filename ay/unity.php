<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

use ay\lib\View;

/**
 * 跳转函数
 * @param string $str 地址
 * @return null $data 参数
 */
function url(string $str, $data = null)
{
    return \ay\drive\Url::instance()->get($str, $data);
}

/**
 * 打印函数
 * @param object|bool|array|string|null $arr
 */
function dump(object|bool|array|string|null $arr): void
{
    if (is_bool($arr)) {
        var_dump($arr);
    } else if (is_null($arr)) {
        var_dump(null);
    } else {
        echo "<pre style='padding:10px;border_radius:5px;background:#f5f5f5;border:1px solid #ccc;'>";
        print_r($arr);
        echo "</pre>";
    }
}

/**
 * 打印函数
 * @param string $msg
 * @param null $alink 跳转链接
 */
function fail(string $msg = '页面错误！请稍后再试～', $alink = null): void
{
    assign('msg', $msg);
    if (!is_null($alink)) assign('link', $alink);
    view(TEMPLATE . '/fail.html');
    exit;
}

/**
 * 打印函数
 * @param string $msg 输出内容
 * @param string|null $alink 跳转链接
 */
function success(string $msg = '操作成功～', string $alink = null): void
{
    assign('msg', $msg);
    if (!is_null($alink)) assign('link', $alink);
    view(TEMPLATE . '/success.html');
    exit;
}


/**
 * @param null $str
 * @param null $type
 * @return array|bool|float|int|string|string[]|null
 */
function R($str = NULL, $type = null): array|float|bool|int|string|null
{
    if (!strpos($str, '.')) {
        $qm = $str;
        $hm = '';
    } else {
        $hm = substr($str, strripos($str, '.') + 1);
        $qm = substr($str, 0, strrpos($str, '.'));
    }

    return match ($qm) {
        'get' => \ay\lib\Request::instance()->get($hm, $type),
        'post' => \ay\lib\Request::instance()->post($hm, $type),
        'url' => \ay\lib\Request::instance()->url(),
        'file' => \ay\lib\Request::instance()->file($hm),
        'param' => \ay\lib\Request::instance()->param(),
        '?get' => \ay\lib\Request::instance()->has($hm, 'get'),
        '?post' => \ay\lib\Request::instance()->has($hm, 'post'),
        default => false,
    };
}

function controller($name, $vae = ''): void
{
    try {
        \ay\lib\Controller::instance()->controller($name, $vae);
    } catch (Exception $e) {
        halt("加载控制器失败！");
    }
}

/**
 * @param string $filename
 * @param array|null $data
 * @throws Exception
 */
function view(string $filename = '', ?array $data = []): void
{
    View::view($filename, (array)$data);
}

function assign($name, $value): void
{
    View::assign($name, $value);
}

/**
 * 导入extend下文件
 * @param $filePath
 * @throws Exception
 */
function extend($filePath): void
{
    $filePath = EXTEND . $filePath;
    if (!is_file($filePath)) halt($filePath . ' 不存在');
    include_once $filePath;
}

/**
 * 导入vendor目录下文件
 * @param $filePath
 * @throws Exception
 */
function vendor($filePath): void
{
    $filePath = VENDOR . $filePath;
    if (!is_file($filePath)) halt($filePath . ' 不存在');
    include_once $filePath;
}

/**
 * 全局导入
 * @param string $file 文件名
 * @param string $path 路径
 * @throws Exception
 */
function import(string $file, string $path): void
{
    $filePath = $path . $file;
    if (!is_file($filePath)) halt($filePath . ' 不存在');
    include_once $filePath;
}

/**
 * 载入或设置配置顶
 * @param array|string $name 配置名
 * @param string $value 配置值
 * @return array|string|null
 */
function C(array|string $name = '', string $value = ''): array|string|null
{
    static $config = [];
    if (empty($name)) {
        return $config;
    } else if (is_string($name)) {
        $name = strtoupper($name);
        $data = array_change_key_case($config, CASE_UPPER);
        if (!str_contains($name, '.')) {
            //获得配置
            if (empty($value)) {
                return $data[$name] ?? null;
            } else {
                return $config[$name] = isset($data[$name]) && is_array($data[$name]) && is_array($value) ? array_merge($config[$name], (array)($value)) : $value;
            }
        } else {
            //二维数组
            $name = array_change_key_case(explode(".", $name));
            if (empty($value)) {
                return $data[$name[0]][$name[1]] ?? null;
            } else {
                return $config[$name[0]][$name[1]] = $value;
            }
        }
    } else if (is_array($name)) {
        return $config = array_merge($config, array_change_key_case($name, CASE_UPPER));
    }
}


/**
 * 跳转网址
 * @param string $url 跳转
 * @param int $time 跳转时间
 * @param string $msg
 */
function go(string $url, int $time = 0, string $msg = ''): void
{
    if (!headers_sent()) {
        $time == 0 ? header("Location:" . $url) : header("refresh:{$time};url={$url}");
        exit($msg);
    } else {
        echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time) {
            exit($msg);
        }
    }
}

/**
 * 计算脚本运行时间
 * 传递$end参数时为得到执行时间
 * @param string $start 开始标识
 * @param string $end 结束标识
 * @param int $decimals 小数位
 * @return string
 */
function runtime(string $start, string $end = '', int $decimals = 3): string
{
    static $runtime = [];
    if ($end != '') {
        $runtime [$end] = microtime();
        return number_format($runtime [$end] - $runtime [$start], $decimals);
    }
    $runtime[$start] = microtime();
    return '';
}

/**
 * HTTP状态信息设置
 * @param Number $code 状态码
 */
function setHttpCode($code): void
{
    $state = [
        200 => 'OK', // Success 2xx
        // Redirection 3xx
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',
        // Client Error 4xx
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        // Server Error 5xx
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];
    if (isset($state[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $state[$code]);
        header('Status:' . $code . ' ' . $state[$code]);
    }
}

/**
 * 是否为SSL协议
 * @return boolean
 */
function is_ssl(): bool
{
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    }
    return false;
}

/**
 * 打印常量
 */
function print_const(): void
{
    $const = [];
    $define = get_defined_constants(true);
    foreach ($define['user'] as $k => $d) {
        $const[$k] = $d;
    }
    dump($const);
}

/**
 * 抛出异常
 * @throws Exception
 */

function halt($msg, $file = '', $line = ''): void
{
    \ay\drive\Error::instance()->init()->halt($msg, $file, $line);
}

/**
 * 无限级分类树
 */
function tree($arr, $id = 'id', $pid = 'pid'): array
{
    $refer = [];
    $tree = [];
    foreach ($arr as $k => $v) {
        $refer[$v[$id]] = &$v;
    }
    foreach ($arr as $k => $v) {
        $sid = $v[$pid];
        if ($sid == 0) {
            $tree[] = &$v;
        } else {
            if (isset($refer[$sid])) {
                $refer[$sid]['children'][] = &$arr[$k];
            }
        }
    }
    return $tree;
}

function lastTime($date, $template): string
{
    $s = (time() - $date) / 60;
    return match ($s) {
        $s < 60 => intval($s) . '分钟前',
        $s >= 60 && $s < (60 * 24) => intval($s / 60) . '小时前',
        $s >= (60 * 24) and $s < (60 * 24 * 3) => intval($s / 60 / 24) . '天前',
        default => date($template, $date),
    };
}

// 获取客户端IP地址
function getIp()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] as $xip):
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)):
                $ip = $xip;
                break;
            endif;
        endforeach;
    } else if (isset($_SERVER['HTTP_CLIENT_IP']) and preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) and preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } else if (isset($_SERVER['HTTP_X_REAL_IP']) and preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0): string
{
    $ckey_length = 4;

    $key = md5($key ? $key : 'anderyly');
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
        substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
        sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {

        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}
