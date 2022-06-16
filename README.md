# AYPHP
轻量级PHP框架

## 驱动库
### 图像处理
> 引入image类

```php
use ay\drive\Image;

// 初始化image类 详细初始化信息 看构造函数
$image = new Image();

// 生成缩略图并返回缩略图地址
echo $image->thumb(PUB . "static/water/h.png", PUB . "static/water/h1.png", 100, 100, 1);

// 添加水印
var_dump($image->water(PUB . "static/water/h.png", PUB . "static/water/h2.png", [100, 100], "", 1, "123"));
```