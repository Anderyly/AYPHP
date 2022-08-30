<?php

namespace app\index\controller;


use ay\lib\Dir;
use ay\lib\Image;

use ay\lib\Json;
use ay\lib\Request;
use ay\lib\Session;
use ay\lib\Xml;

class Index
{

    public function c() {
        dump(C());
    }

    public function json() {

        $data = [
            "list" => [
                ["id"=>1],
                ["id"=>2],
                ["id"=>3],
            ]
        ];

        // 与最外层同级
        $arr = ["page" => 1];
        Json::msg(200, "success", $data, $arr);
    }

    public function session() {
        // 设置
        dump(Session::set("user", "123"));
        // 获取
        dump(Session::get("user"));
        // 判断是否存在
        dump(Session::has("user"));
        // 取出并删除
        dump(Session::pull("user"));
        // 删除
        Session::delete("user");

    }

    public function param()
    {

        dump(Request::instance()->param());

        dump(Request::instance()->get());
        dump(Request::instance()->post());
        dump(Request::instance()->put());
        dump(Request::instance()->delete());

    }

    public function index(): string
    {
        return '欢迎使用AYPHP框架';
    }

    public function xml()
    {
        // 生成xml
        dump(Xml::instance()->create(["a" => 1, "d" => 2]));

        // 转数组
        dump(xml::instance()->toArray("<xml><appid><![CDATA[wxd898fcb01713c658]]></appid>
<bank_type><![CDATA[OTHERS]]></bank_type>
<cash_fee><![CDATA[1]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[N]]></is_subscribe>
<mch_id><![CDATA[1483469312]]></mch_id>
<nonce_str><![CDATA[8EYuWgYiwjnEhvOg]]></nonce_str>
<openid><![CDATA[oTgZpwaXTs2GzvkwMNDzbWIcrqjA]]></openid>
<out_trade_no><![CDATA[347889645665669422]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[18EE3825D2A3FD9A9DCBC60CAC131973]]></sign>
<time_end><![CDATA[20220514185750]]></time_end>
<total_fee>1</total_fee>
<trade_type><![CDATA[NATIVE]]></trade_type>
<transaction_id><![CDATA[4200001503202205144756789400]]></transaction_id>
</xml>"));
    }

    public function dir()
    {
        // 创建文件夹
        dump(Dir::instance()->create(PUB . "/s"));

        // 获取文件扩展名
        dump(Dir::instance()->getExt(PUB . "static/water/water.png"));

        // 显示目录树
        dump(Dir::instance()->treeDir(AY));

        // 遍历目录内容
        dump(Dir::instance()->tree(AY));

        // 删除文件夹
        dump(Dir::instance()->del(PUB . "/s"));

        // 获取目录大小
        dump(Dir::instance()->getDirSize(PUB . "static/water"));

        // 复制目录
        dump(Dir::instance()->copy(PUB . "static/water", PUB . "static/water1"));


    }

    // 生成水印图片
    public function water(): void
    {
        $image = new Image();

        // 生成缩略图
        echo $image->thumb(PUB . "static/water/h.png", PUB . "static/water/h1.png", 100, 100, 1);

        // 添加水印
        var_dump($image->water(PUB . "static/water/h.png", PUB . "static/water/h2.png", [100, 100], "", 1, "123"));
    }
}