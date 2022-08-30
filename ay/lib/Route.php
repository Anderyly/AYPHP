<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Route
{

    public static $get;
    public $mode;
    public $controller;
    public $action;
    private $field = '';

    public function __construct()
    {
        $this->router();
        $this->pathInfo();
    }

    //路由模式
    public function router()
    {

        $path = ROOT . '/' . APP_NAME . '/route.php';
        $path = str_replace('//', '/', $path);

        if (is_file($path)) {
            $rules = include_once $path;
        } else {
            $rules = [];
        }

        if (isset($_SERVER['REQUEST_URI']) and !empty($rules)) {
            $pathInfo = ltrim($_SERVER['REQUEST_URI'], "/");
            foreach ($rules as $k => $v) {
                $reg = "/" . $k . "/i";
                if (preg_match($reg, $pathInfo)) {
                    $res = preg_replace($reg, $v, $pathInfo);
                    $_SERVER['REQUEST_URI'] = '/' . $res;
                }
            }
        }

    }

    // pathInfo处理
    public function pathInfo()
    {

        if (strpos($_SERVER['REQUEST_URI'], '?')) {
            $path = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '?'));
        } else {
            $path = $_SERVER['REQUEST_URI'];
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
            if (!strstr($path_arr[1], '.' . C('APP.REWRITE'))) halt('伪静态配置错误');
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
            $path_arr[] = C('APP.ACTION') . '.' . C('APP.REWRITE');
        }
        return $path_arr;
    }

}
