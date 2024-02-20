<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Image
{

    //是否应用水印
    private bool $waterOn;
    //水印图片
    public string $waterImg;
    //水印的位置
    public int $waterPos;
    //水印的透明度
    public int $waterPct;
    //图像的压缩比
    public int $waterQuality;
    //水印文字内容
    public string $waterText;
    //水印文字大小
    public float $waterTextSize;
    //水印文字的颜色
    public string $waterTextColor;
    //水印的文字的字体
    public string $waterTextFont;
    //生成缩略图的方式
    public int $thumbType;
    //缩略图的宽度
    public float $thumbWidth;
    //缩略图的高度
    public float $thumbHeight;
    //生成缩略图文件名后缀
    public string $thumbEndFix;
    //缩略图文件前缀
    public string $thumbPreFix;

    /**
     * 构造函数
     */
    public function __construct()
    {
        //水印参数
        $this->waterOn = C("IMAGE.WATER_ON");
        $this->waterImg = C("IMAGE.WATER_IMG");
        $this->waterPos = C("IMAGE.WATER_POS");
        $this->waterPct = C("IMAGE.WATER_PCT");
        $this->waterQuality = C("IMAGE.WATER_QUALITY");
        $this->waterText = C("IMAGE.WATER_TEXT");
        $this->waterTextColor = C("IMAGE.WATER_TEXT_COLOR");
        $this->waterTextSize = C("IMAGE.WATER_TEXT_SIZE");
        $this->waterTextFont = C("IMAGE.WATER_FONT");
        //缩略图参数
        $this->thumbType = C("IMAGE.THUMB_TYPE");
        $this->thumbWidth = C("IMAGE.THUMB_WIDTH");
        $this->thumbHeight = C("IMAGE.THUMB_HEIGHT");
        $this->thumbPreFix = C("IMAGE.THUMB_PREFIX");
        $this->thumbEndFix = C("IMAGE.THUMB_END_FIX");
    }

    /**
     * 环境验证
     * @param $img string
     * @return bool
     */
    private function check(string $img): bool
    {
        $type = array(".jpg", ".jpeg", ".png", ".gif");
        $imgType = strtolower(strrchr($img, '.'));
        return extension_loaded('gd') && file_exists($img) && in_array($imgType, $type);
    }

    /**
     * 获得缩略图的尺寸信息
     * @param float $imgWidth 原图宽度
     * @param float $imgHeight 原图高度
     * @param float $thumbWidth 缩略图宽度
     * @param float $thumbHeight 缩略图的高度
     * @param int $thumbType 处理方式
     * 1 固定宽度  高度自增 2固定高度  宽度自增 3固定宽度  高度裁切
     * 4 固定高度 宽度裁切 5缩放最大边 原图不裁切
     * @return array
     */
    private function thumbSize(float $imgWidth,float $imgHeight, float $thumbWidth,float $thumbHeight, int $thumbType): array
    {
        //初始化缩略图尺寸
        $w = $thumbWidth;
        $h = $thumbHeight;
        //初始化原图尺寸
        $formerWidth = $imgWidth;
        $formerHeight = $imgHeight;
        switch ($thumbType) {
            case 1:
                //固定宽度  高度自增
                $h = $thumbWidth / $imgWidth * $imgHeight;
                break;
            case 2:
                //固定高度  宽度自增
                $w = $thumbHeight / $imgHeight * $imgWidth;
                break;
            case 3:
                //固定宽度  高度裁切
                $formerHeight = $imgWidth / $thumbWidth * $thumbHeight;
                break;
            case 4:
                //固定高度  宽度裁切
                $formerWidth = $imgHeight / $thumbHeight * $thumbWidth;
                break;
            case 5:
                //缩放最大边 原图不裁切
                if (($imgWidth / $thumbWidth) > ($imgHeight / $thumbHeight)) {
                    $h = $thumbWidth / $imgWidth * $imgHeight;
                } elseif (($imgWidth / $thumbWidth) < ($imgHeight / $thumbHeight)) {
                    $w = $thumbHeight / $imgHeight * $imgWidth;
                }
                break;
            default:
                //缩略图尺寸不变，自动裁切图片
                if (($imgHeight / $thumbHeight) < ($imgWidth / $thumbWidth)) {
                    $formerWidth = $imgHeight / $thumbHeight * $thumbWidth;
                } elseif (($imgHeight / $thumbHeight) > ($imgWidth / $thumbWidth)) {
                    $formerHeight = $imgWidth / $thumbWidth * $thumbHeight;
                }
//            }
        }
        $arr [0] = $w;
        $arr [1] = $h;
        $arr [2] = $formerWidth;
        $arr [3] = $formerHeight;
        return $arr;
    }

    /**
     * 图片裁切处理
     * @param string $img 原图
     * @param string $outFile 另存文件名
     * @param float $thumbWidth 缩略图宽度
     * @param float $thumbHeight 缩略图高度
     * @param int $thumbType 裁切图片的方式
     * 1 固定宽度  高度自增 2固定高度  宽度自增 3固定宽度  高度裁切
     * 4 固定高度 宽度裁切 5缩放最大边 原图不裁切 6缩略图尺寸不变，自动裁切最大边
     * @return bool|string
     */
    public function thumb(string $img, string $outFile = '', float $thumbWidth = 0, float $thumbHeight = 0, int $thumbType = 1): bool|string
    {
        if (!$this->check($img)) {
            return false;
        }
        //基础配置
        $thumbType = $thumbType ? $thumbType : $this->thumbType;
        $thumbWidth = $thumbWidth ? $thumbWidth : $this->thumbWidth;
        $thumbHeight = $thumbHeight ? $thumbHeight : $this->thumbHeight;
        //获得图像信息
        $imgInfo = getimagesize($img);
        $imgWidth = $imgInfo [0];
        $imgHeight = $imgInfo [1];
        $imgType = image_type_to_extension($imgInfo [2]);
        //获得相关尺寸
        $thumb_size = $this->thumbSize($imgWidth, $imgHeight, $thumbWidth, $thumbHeight, $thumbType);
        //原始图像资源
        $func = "imagecreatefrom" . substr($imgType, 1);
        $resImg = $func($img);
        //缩略图的资源
        if ($imgType == '.gif') {
            $res_thumb = imagecreate($thumb_size [0], $thumb_size [1]);
            $color = imagecolorallocate($res_thumb, 255, 0, 0);
        } else {
            $res_thumb = imagecreatetruecolor($thumb_size [0], $thumb_size [1]);
            imagealphablending($res_thumb, false); //关闭混色
            imagesavealpha($res_thumb, true); //储存透明通道
        }
        //绘制缩略图X
        if (function_exists("imagecopyresampled")) {
            imagecopyresampled($res_thumb, $resImg, 0, 0, 0, 0, $thumb_size [0], $thumb_size [1], $thumb_size [2], $thumb_size [3]);
        } else {
            imagecopyresized($res_thumb, $resImg, 0, 0, 0, 0, $thumb_size [0], $thumb_size [1], $thumb_size [2], $thumb_size [3]);
        }
        //处理透明色
        if ($imgType == '.gif') {
            imagecolortransparent($res_thumb, $color);
        }
        //配置输出文件名
        $imgInfo = pathinfo($img);
        $outFile = $outFile ? $outFile : dirname($img) . 'Image.php/' . $this->thumbPreFix . $imgInfo['filename'] . $this->thumbEndFix . "." . $imgInfo['extension'];

        Dir::instance()->create(dirname($outFile));
        $func = "image" . substr($imgType, 1);
        $func($res_thumb, $outFile);
        if (isset($resImg)) {
            imagedestroy($resImg);
        }
        if (isset($res_thumb)) {
            imagedestroy($res_thumb);
        }
        return $outFile;
    }

    /**
     * 水印处理
     * @param string $img 操作的图像
     * @param string $outImg 另存的图像 不设置时操作原图
     * @param int|array $pos 水印位置
     * @param string $waterImg 水印图片
     * @param int $pct 透明度
     * @param string $text 文字水印内容
     * @return bool
     */
    public function water(string $img, string $outImg = '', int|array $pos = 1, string $waterImg = '', int $pct = 0, string $text = ""): bool
    {
        // 验证原图像
        if (!$this->check($img)) {
            return false;
        }
        // 验证水印图像
        if ($this->waterOn) $waterImg = $waterImg ? $waterImg : $this->waterImg;
        $waterImgOn = $this->check($waterImg) ? 1 : 0;
        // 判断另存图像
        $outImg = $outImg ? $outImg : $img;
        // 水印位置
        $pos = $pos ? $pos : $this->waterPos;
        // 水印文字
        $text = $text ? $text : $this->waterText;
        // 水印透明度
        $pct = $pct ? $pct : $this->waterPct;
        $imgInfo = getimagesize($img);
        $imgWidth = $imgInfo [0];
        $imgHeight = $imgInfo [1];
        //获得水印信息
        if ($waterImgOn) {
            $waterInfo = getimagesize($waterImg);
            $waterWidth = $waterInfo [0];
            $waterHeight = $waterInfo [1];
            $w_img = match ($waterInfo[2]) {
                1 => imagecreatefromgif($waterImg),
                2 => imagecreatefromjpeg($waterImg),
                3 => imagecreatefrompng($waterImg),
                default => 0,
            };
        } else {
            if (empty($text) || strlen($this->waterTextColor) != 7) {
                return false;
            }
            echo 123;
            $textInfo = imagettfbbox($this->waterTextSize, 0, $this->waterTextFont, $text);
            $waterWidth = $textInfo[2] - $textInfo[6];
            $waterHeight = $textInfo[3] - $textInfo[7];
        }
        // 建立原图资源
        if ($imgHeight < $waterHeight || $imgWidth < $waterWidth) {
            return false;
        }
        $resImg = match ($imgInfo[2]) {
            1 => imagecreatefromgif($img),
            2 => imagecreatefromjpeg($img),
            3 => imagecreatefrompng($img),
            default => 0,
        };
        //水印位置处理方法
        switch ($pos) {
            case 1:
                $x = $y = 25;
                break;
            case 2:
                $x = ($imgWidth - $waterWidth) / 2;
                $y = 25;
                break;
            case 3:
                $x = $imgWidth - $waterWidth;
                $y = 25;
                break;
            case 4:
                $x = 25;
                $y = ($imgHeight - $waterHeight) / 2;
            // no break
            case 5:
                $x = ($imgWidth - $waterWidth) / 2;
                $y = ($imgHeight - $waterHeight) / 2;
                break;
            case 6:
                $x = $imgWidth - $waterWidth;
                $y = ($imgHeight - $waterHeight) / 2;
                break;
            case 7:
                $x = 25;
                $y = $imgHeight - $waterHeight;
                break;
            case 8:
                $x = ($imgWidth - $waterWidth) / 2;
                $y = $imgHeight - $waterHeight;
                break;
            case 9:
                $x = $imgWidth - $waterWidth - 10;
                $y = $imgHeight - $waterHeight;
                break;
            default:
                $x = $pos[0];
                $y = $pos[1];
        }
        if ($waterImgOn) {
            if ($waterInfo[2] == 3) {
                imagecopy($resImg, $w_img, $x, $y, 0, 0, $waterWidth, $waterHeight);
            } else {
                imagecopymerge($resImg, $w_img, $x, $y, 0, 0, $waterWidth, $waterHeight, $pct);
            }
        } else {
            $r = hexdec(substr($this->waterTextColor, 1, 2));
            $g = hexdec(substr($this->waterTextColor, 3, 2));
            $b = hexdec(substr($this->waterTextColor, 5, 2));
            $color = imagecolorallocate($resImg, $r, $g, $b);
            imagettftext($resImg, $this->waterTextSize, 0, $x, $y, $color, $this->waterTextFont, $text);
        }
        switch ($imgInfo[2]) {
            case 1:
                imagegif($resImg, $outImg);
                break;
            case 2:
                imagejpeg($resImg, $outImg, $this->waterQuality);
                break;
            case 3:
                imagepng($resImg, $outImg);
                break;
        }
        if (isset($resImg)) {
            imagedestroy($resImg);
        }
        if (isset($w_img)) {
            imagedestroy($w_img);
        }
        return true;
    }
}
