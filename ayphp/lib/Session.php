<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Session
{

    /**
     * 设置Session
     * @param $name
     * @param string $var
     * @return bool
     */
    public static function set($name, string $var = ''): bool
    {
        $_SESSION[$name] = $var;
        if (Session::has($name)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取Session
     * @param $name
     * @return string
     */
    public static function get($name): string
    {
        if (Session::has($name)) {
            return $_SESSION[$name];
        } else {
            return '';
        }

    }

    /**
     * 判断Session存在
     * @param $name
     * @param string $var
     * @return bool
     */
    public static function has($name, string $var = ''): bool
    {
        if (!isset($_SESSION[$name])) {
            return false;
        } else if ($_SESSION[$name] == $var and !empty($var)) {
            return true;
        } else {
            return true;
        }
    }

    /**
     * 删除Session
     * @param string $name
     * @return void
     */
    public static function delete(string $name = ''): void
    {
        if (empty($name)) {
            $_SESSION = [];
        } else {
            unset($_SESSION[$name]);
        }
    }

    /**
     * 取出并删除Session
     * @param string $name
     * @return string
     */
    public static function pull(string $name = ''): string
    {
        if (empty($name)) {
            $_SESSION = [];
            $data = '';
        } else {
            $data = self::get($name);
            unset($_SESSION[$name]);
        }
        return $data;
    }
}
