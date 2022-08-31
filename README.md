# AYPHP
轻量级PHP框架

## 规范 
### 目录和文件
*   目录使用小写+下划线；
*   类库、函数文件统一以`.php`为后缀；
*   类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
*   类文件采用驼峰法命名（首字母大写），其它文件采用小写+下划线命名；
*   类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

### 函数和类、属性命名
*   类的命名采用驼峰法（首字母大写），例如`User`、`UserType`，默认不需要添加后缀，例如`UserController`应该直接命名为`User`；
*   函数的命名使用小写字母和下划线（小写字母开头）的方式，例如`get_client_ip`；
*   方法的命名使用驼峰法（首字母小写），例如`getUserName`；
*   属性的命名使用驼峰法（首字母小写），例如`tableName`、`instance`；
*   以双下划线“\_\_”打头的函数或方法作为魔术方法，例如`__call`和`__autoload`；

## 目录结构
~~~
project  应用部署目录
├─app           应用目录
│  ├─index              模块目录(可更改)
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  ├─view            视图目录
│  ├─function.php         应用（公共）自定义函数
├─ay              框架系统目录
│  ├─unity.php     核心函数
│  ├─drive               框架驱动文件
│  ├─lib               框架类文件
│  ├─Base.php           基础定义文件
│  ├─Core.php        控制台入口文件
│  └─ay.php          框架引导文件
├─config            框架配置目录
│  │  └─app.php          基础配置文件
│  │  └─file.php          上传配置
│  │  └─database.php          数据库配置文件
│  │  └─image.php          图片配置文件
│  │  └─redis.php           redis配置文件
│  │  └─route.php          路由配置文件
├─extend                扩展类库目录
├─public                公共资源目录
│  ├─static             静态资源存放目录(css,js,image)
│  ├─index.php          应用入口文件
│  ├─.htaccess          用于 apache 的重写
├─temp               应用的运行时目录（可写，可设置）
├─vendor                第三方类库目录（Composer）
├─README.md             README 文件
~~~

## 核心类

### Request请求类
> 引入Request类

```php
use ay\lib\Request;

// 获取全部参数                                            
dump(Request::instance()->param());

// 获取get参数参数
dump(Request::instance()->get());

// 获取post请求参数 status字段并格式化成int类型
dump(Request::instance()->post("status", "int"));

// 获取put请求正文
dump(Request::instance()->put());

// 获取delete请求参数
dump(Request::instance()->delete());       
```

### Session
> 引入Session类

```php
use ay\lib\Request;

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
```

### Json返回类
> 引入json类

```php
use ay\lib\Json;

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
```

返回
```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "list": [
      {
        "id": 1
      },
      {
        "id": 2
      },
      {
        "id": 3
      }
    ]
  },
  "page": 1
}
```

### Xml操作
> 引入Xml类

```php
use ay\lib\Xml;

// 生成xml
dump(Xml::instance()->create(["a" => 1, "d" => 2]));

// 转数组
dump(xml::instance()->toArray(xml字符串));

```

> 引入dir类

```php
use ay\lib\Dir;

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

```

### 图像处理
> 引入image类

```php
use ay\lib\Image;

// 初始化image类 详细初始化信息 看构造函数
$image = new Image();

// 生成缩略图并返回缩略图地址
echo $image->thumb(PUB . "static/water/h.png", PUB . "static/water/h1.png", 100, 100, 1);

// 添加水印
var_dump($image->water(PUB . "static/water/h.png", PUB . "static/water/h2.png", [100, 100], "", 1, "123"));
```

### 缓存类
> 引入Cache类

```php
use ay\lib\Cache;

// 设置指定缓存
dump(Cache::instance()->set('data', '123'));

// 取出指定缓存
dump(Cache::instance()->get('data'));

// 删除指定缓存
Cache::instance()->del('data');

// 删除全部缓存
Cache::instance()->delAll();

dump(Cache::instance()->get('data'));

```

## 助手函数

### C方法
```php
// 输出全部预定义
dump(C());

// 改变指定并返回值
dump(C('APP.DEBUG', 123));

// 增加并返回值
dump(C('AAA.ACV', 123));

dump(C());

```