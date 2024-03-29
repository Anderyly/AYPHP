<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

class Route
{

    public static $get;
    public $mode;
    public $controller;
    public $action;
    private $field = '';

    private $request_url = '';

    public function __construct($request_url = '')
    {
        $this->router($request_url);
        $this->pathInfo();
    }

    //路由模式
    public function router($request_url = '')
    {

        if (!defined('RUN_METHOD')) {
            $request_url = $_SERVER['REQUEST_URI'];
        }

        $path = ROOT . '/' . APP_NAME . '/route.php';
        $path = str_replace('//', '/', $path);

        if (is_file($path)) {
            $rules = include_once $path;
        } else {
            $rules = [];
        }

        if (isset($request_url) and !empty($rules)) {
            $pathInfo = ltrim($request_url, "/");
            foreach ($rules as $k => $v) {
                $reg = "/" . $k . "/i";
                if (preg_match($reg, $pathInfo)) {
                    $res = preg_replace($reg, $v, $pathInfo);
                    $request_url = '/' . $res;
                }
            }
        }

        $this->request_url = $request_url;

    }

    // pathInfo处理
    public function pathInfo()
    {
        $request_url = $this->request_url;

        if (strpos($request_url, '?')) {
            $path = substr($request_url, 0, strrpos($request_url, '?'));
        } else {
            $path = $request_url;
        }

        if ($path != '/') {

            $path = str_replace('//', '/', $path);
            $path_arr = explode('/', trim($path, '/'));

            // 允许public文件访问
            if ($path_arr[0] == 'public') {
                file_get_contents(ROOT . $path);
                exit;
            }

            // 获取mode
            switch (true) {
                case (defined('BIND')):
                    $path_arr = $this->bq($path_arr);
                    $this->mode = str_replace('.php', '', $path_arr[0]);
                    unset($path_arr[0]);
                    break;
                case (defined('VIND')):
                    if (!isset($path_arr[1])) {
                        halt('版本控制使用错误');
                    }
                    $path_arr = $this->bq($path_arr, 2, 3);
                    $this->mode = str_replace('.php', '/' . $path_arr[1], $path_arr[0]);
                    unset($path_arr[0]);
                    unset($path_arr[1]);
                    break;
                default:
                    $path_arr = $this->bq($path_arr);
                    $this->mode = str_replace('.php', '', $path_arr[0]);
                    unset($path_arr[0]);
            }

            $path_arr = array_merge($path_arr);

            // 获取controller
            $this->controller = $path_arr[0];
            unset($path_arr[0]);

            // 获取action
            if (!str_contains($path_arr[1], '.' . C('APP.REWRITE')) and !empty(C('APP.REWRITE'))) halt('页面不存在');
            $this->action = str_replace('.' . C('APP.REWRITE'), '', $path_arr[1]);
            unset($path_arr[1]);

            // 获取get
            if (!empty($path_arr)) {
                $sum = 1;
                foreach ($path_arr as $item):
                    if ($sum % 2 != 0) {
                        $this->field = $item;
                    } else {
                        self::$get[$this->field] = $item;
                        $this->field = '';
                    }
                    $sum++;
                endforeach;
            } else {
                self::$get = false;
            }

            //
        } else {

            $this->mode = C('APP.MODE');
            $this->controller = C('APP.CONTROLLER');
            $this->action = C('APP.ACTION');
            self::$get = false;
        }
    }

    private function bq($path_arr, $one = 1, $two = 2)
    {
        // 补全
        $num = count($path_arr);
        if ($num == $one) {
            $path_arr[] = C('APP.CONTROLLER');
            $path_arr[] = C('APP.ACTION');
        } else if ($num == $two) {
            $path_arr[] = C('APP.ACTION') . 'lib' . C('APP.REWRITE');
        }
        return $path_arr;
    }

}
