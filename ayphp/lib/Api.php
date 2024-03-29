<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2019
 */

namespace ay\lib;

use ay\lib\Json;

class Api
{

    public array $data = [];
    public array $rule = [];
    public int $time = 6000;
    public string $mode = 'api';
    public string $key;

    /***
     * @param null $mode [模块]
     * @param null $rule [不验证路由]
     * @param null $key [密钥]
     * @param int $time [超时时间差 毫秒]
     * @param bool $status [不验证时间戳]
     * @param string $format [获取参数方式 param get post]
     */
    public function __construct($mode = null, $rule = null, $key = null, int $time = 60000, bool $status = true, string $format = 'param')
    {
        if (!is_null($key)) {
            $this->key = $key;
        } else {
            $this->key = C("APP.KEY");
        }

        if (!is_null($time)) {
            $this->time = $time;
        }

        if (!is_null($rule)) {
            $this->rule = $rule;
        }

        if (!is_null($mode)) {
            $this->mode = $mode;
        }

        $this->data = R($format);
        if ($status) {
            $this->checkTimeSign($this->data);
        }

    }

    private function checkTimeSign($data): void
    {

        $other = strtolower('/' . $this->mode . '/' . CONTROLLER . '/' . ACTION);
        unset($data[$other]);
        if (isset($data[$other])) {
            $other = strtolower('/' . $this->mode . '/' . CONTROLLER . '/' . ACTION . '/');
            unset($data[$other]);
        }

        // 验证规则是否不用认证
        if (!in_array($other, $this->rule)):
            // 时间戳验证
            if (!isset($data['timestamp']) or intval($data['timestamp']) <= 1) {
                Json::msg(400, "timestamp不能为空或不存在");
            } else {
                // 时间戳对比
                if ($this->getTime() - intval($data['timestamp']) > $this->time):
                    Json::msg(400, "请求超时");
                endif;
            }

            if (!isset($data['sign'])) {
                Json::msg(400, 'sign不存在');
            }

            $sign = $data['sign'];
            unset($data['sign']);

            // 排序
            ksort($data);
            foreach ($data as $k => $v):
                if (!empty($v)) {
                    $arr[] = $k . '=' . $v;
                }

            endforeach;
            $str = implode("&", $arr);
            $str .= '&key=' . $this->key;
            if ($sign != MD5($str)) {
                Json::msg(400, "sign验证错误");
            }

        endif;

        $this->data = $data;
    }

    private function getTime(): float
    {
        $arr = explode('.', microtime(true));
        return (float) ($arr[0] . substr($arr[1], 0, 3));
    }
}
