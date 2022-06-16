<?php

namespace app\index\controller;

use ay\drive\Dir;
use ay\drive\Image;

class Index
{

    public function index(): string
    {
        return '欢迎使用AYPHP框架';
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