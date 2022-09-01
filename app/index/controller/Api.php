<?php

/***
 * 内置api接口处理方法
 * timestamp 时间戳为13位精确到毫秒
 * sign签名为 所有参数a=1&b=2 加上&key=密钥md5生成
 */

namespace app\index\controller;

use ay\lib\Api as ApiC;

class Api extends ApiC
{
    public array $rule = [
        // 此处为数组形式 不验证的路由
        '/index/api/no'
    ];

    public function __construct()
    {
        parent::__construct('index', $this->rule);
    }


    public function index()
    {
        // 获取参数
        $data = $this->data;
        echo $data['a'];
    }

    public function no()
    {
        return 'no';
    }


}