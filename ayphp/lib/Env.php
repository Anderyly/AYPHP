<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2022
 */

namespace ay\lib;

class Env
{
    /**
     * 获取环境变量值
     * @param string $name    环境变量名（支持二级 . 号分割）
     * @param string|null $default 默认值
     */
    public static function get(string $name, string $default = null): bool|array|string|null
    {
        $res = getenv('PHP_' . strtoupper(str_replace('.', '_', $name)));
        
        if (false !== $res) {
            if ('false' === $res) {
                $res = false;
            } elseif ('true' === $res) {
                $res = true;
            }

            return $res;
        }

        return $default;
    }
}
