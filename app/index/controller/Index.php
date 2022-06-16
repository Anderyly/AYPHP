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

    public function dir() {
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