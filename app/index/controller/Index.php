<?php

namespace app\index\controller;


use ay\lib\Dir;
use ay\lib\Env;
use ay\lib\Image;

use ay\lib\Json;
use ay\lib\Request;
use ay\lib\Session;
use ay\lib\Xml;

use ay\lib\Cache;

use ay\lib\Curl;

use ay\lib\Upload;

use ay\lib\Db;


class Index
{


    public function env()
    {
        dump(Env::get('data.debug'));
    }

    public function db()
    {
        $res = Db::name('tbl_user')->select();
        dump($res);
    }

    public function upload()
    {
        $upload = new Upload(PUB . 'upload/', ['txt']);
        dump($upload->operate('file'));
    }

    public function demo()
    {

        // 导入extends目录下扩展包
//        extends('pay/alipay.php');
//        $pay = new AliPay();


        // 分类树
        $arr = [
            ['id' => 1, 'pid' => 0, 'name' => '一级'],
            ['id' => 2, 'pid' => 1, 'name' => '二级'],
            ['id' => 3, 'pid' => 2, 'name' => '三级'],
        ];
        dump(tree($arr, 'id', 'pid'));

        // 获取来源ip
        dump(getIp());

        // 判断当前是否是ssl
        dump(is_ssl());

        // 抛出异常
        halt('手动抛出异常');

        // 跳转
        go('http://baidu.com/', 3, "系统正在重定向");
    }


    public function request()
    {
        // 获取所有post参数
        dump(R('post.'));

        // 验证post下ids参数是否存在
        dump(R('?post.ids'));

        // 获取所有get参数
        dump(R('get.'));

        // 验证get下id参数是否存在
        dump(R('?get.id'));

        // 获取所有参数 注意 会多返回一个s参数 此参数为当前路由
        dump(R('param'));

        // 获取所有文件参数
        dump(R('file'));

        // 验证file下file参数是否存在
        dump(R('?file.file'));

    }


    public function option()
    {
        success();
        fail();
    }

    public function curl()
    {
        $url = "https://blog.aaayun.cc/test.php";
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36';

        dump(
            Curl::url($url)
                ->userAgent($ua) // 设置ua
                ->referer('') // 设置来源
                ->param(['user' => '测试数据']) // 设置参数
                ->time(10) // 最大超时时间
                ->proxy('127.0.0.1', 1080) // 设置代理
                ->show(true) // 显示repose信息
                ->cert('') // 设置证书
                ->post()
        );


    }


    public function cache()
    {

        // 设置缓存
        dump(Cache::instance()->set('data', '123'));

        // 取出缓存
        dump(Cache::instance()->get('data'));

        // 删除指定缓存
        Cache::instance()->del('data');

        // 删除全部缓存
        Cache::instance()->delAll();

        dump(Cache::instance()->get('data'));

    }

    public function c()
    {
        // 输出全部预定义
        dump(C());

        // 改变指定并返回值
        dump(C('APP.DEBUG', 123));

        // 增加并返回值
        dump(C('AAA.ACV', 123));

        dump(C());

    }

    public function json()
    {

        $data = [
            "list" => [
                ["id" => 1],
                ["id" => 2],
                ["id" => 3],
            ]
        ];

        // 与最外层同级
        $arr = ["page" => 1];
        Json::msg(200, "success", $data, $arr);
    }

    public function session()
    {
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

    public function console()
    {
        for ($i = 0; $i < 10000; $i++) {
            echo $i . PHP_EOL;
        }
    }
}