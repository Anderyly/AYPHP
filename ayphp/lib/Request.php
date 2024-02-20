<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

use ay\drive\Route;

class Request
{

    public static function instance(): Request
    {
        return new self();
    }

    public function delete(string $field = '', $type = null): array|float|int|bool|string|null
    {
        parse_str($_SERVER['QUERY_STRING'], $data);
        unset($data['s']);
        if (empty($field)) {
            return $this->filter($data);
        } else {
            return $this->typeTo($this->filter($data[$field]), $type);
        }
    }

    public function put(string $field = '', $type = null): array|float|int|bool|string|null
    {
        parse_str(file_get_contents('php://input'), $data);
        unset($data['s']);
        if (empty($field)) {
            return $this->filter($data);
        } else {
            return $this->typeTo($this->filter($data[$field]), $type);
        }
    }

    /**
     * 获取get参数
     * @param string $field
     * @param null $type 类型
     * @return array|float|int|bool|string|null
     */
    public function get(string $field = '', $type = null): array|float|int|bool|string|null
    {
        $data = Route::$get;

        if (!$data) {
            $data = $GLOBALS['_GET'];
        }
        unset($data['s']);
        if (empty($field)) {
            return $this->filter($data);
        } else {
            if (!$this->has($field, 'get')) {
                return false;
            }
            return $this->typeTo($this->filter($data[$field]), $type);
        }

    }

    /**
     * 类型转换
     * @param float|array|bool|int|string $value 参数
     * @param string $type 类型
     * @return float|int|bool|array|string
     */
    private function typeTo(float|array|bool|int|string $value, string $type): float|int|bool|array|string
    {
        if (gettype($value) != 'array') {
            switch ($type) {
                case 'string':
                    $value = (string) ($value);
                    break;
                case 'int':
                    $value = (int) ($value);
                    break;
                case 'float':
                    $value = (float) ($value);
                    break;
                case 'double':
                    $value = (double) ($value);
                    break;
                case 'bool':
                    $value = (bool) ($value);
                    break;
                default:
            }
            return $value;
        } else {
            return $value;
        }
    }

    /**
     * @return string
     */
    public function url(): string
    {
        $str = $this->filter($_SERVER['PATH_INFO']);
        return substr($str, 0, strrpos($str, '.'));
    }

    /**
     * 获取post参数
     * @param string $field
     * @param null $type 类型
     * @return array|bool|float|int|string|string[]|null
     */
    public function post(string $field = '', $type = null): array|float|bool|int|string|null
    {

        $data = $GLOBALS['_POST'];
        unset($data['s']);
        if (empty($field)) {
            return $this->filter($data);
        } else {
            if (!$this->has($field, 'post')) {
                return false;
            }

            $value = $this->filter($data[$field]);
            return $this->typeTo($value, $type);
        }

    }

    /**
     * 获取全部参数
     * @return array
     */
    public function param(): array
    {
        return array_merge($this->get(), $this->post(), $this->delete(), $this->put(), ["s" => $this->url()]);
    }

    /**
     * 获取文件
     * @param null $field
     * @return array|string|string[]|null
     */
    public function file($field = null): array|string|null
    {
        if (empty($field)) {
            return $_FILES;
        } else {
            return $_FILES[$field];
        }
    }

    /**
     * 判断参数是否存在
     * @param $field
     * @param string $type 传递方式
     * @return bool
     */
    public function has($field, string $type = 'ALL'): bool
    {

        switch ($type) {
            case 'post':
                $return = array_key_exists($field, $this->post());
                break;
            case 'get':
                $return = array_key_exists($field, $this->get());
                break;
            case 'file':
                $return = array_key_exists($field, $this->file());
                break;
            case 'delete':
                $return = array_key_exists($field, $this->delete());
                break;
            case 'put':
                $return = array_key_exists($field, $this->put());
                break;
            default:
                if (array_key_exists($field, $this->param())) {
                    $return = true;
                } else {
                    $return = false;
                }
        }

        return $return;

    }

    /**
     * 过滤参数
     * @param float|bool|int|string|array|null $str  值
     * @return array|string|string[]|null
     */
    private function filter(float|bool|int|string|array|null $str): array|string|null
    {

        $farr = array(
            "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU", // xss
            "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU", // 特殊字符
            "/select\b|insert\b|update\b|delete\b|drop\b|create\b|like\b|and\b|or\b|values\b|set\b|exec\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is", // sql
        );
        if (is_array($str)) {
            //
            $arr = [];
            foreach ($str as $k => $v):
                if (is_array($v)) {
                    foreach ($v as $k1 => $v1):
                        $v1 = strip_tags(preg_replace($farr, '', $v1));
//                         if (empty($v1)) halt('参数含有非法字符，已被系统拦截');
                        $arr[$k][] = $v1;
                    endforeach;
                } else {
                    $v = strip_tags(preg_replace($farr, '', $v));
//                     if (empty($v)) halt('参数含有非法字符，已被系统拦截');
                    $arr[$k] = $v;
                }
            endforeach;
            return $arr;
            //
        } else {
            $str = preg_replace($farr, '', $str);
            return strip_tags($str);
        }

    }

}
