<?php
namespace HappyStraw;

class Image
{
    // 读取图片
    public static function create($filename)
    {
        return imagecreatefrompng($filename);
    }

    // 截图
    public static function crop($image, $rect)
    {
        return imagecrop($image, $rect);
    }

    // 获取宽度
    public static function width($image)
    {
        return imagesx($image);
    }

    // 获取高度
    public static function height($image)
    {
        return imagesy($image);
    }

    // 获取某个坐标的颜色
    public static function colorAt($image, $x, $y)
    {
        return imagecolorat($image, $x, $y);
    }

    // 图片上描点
    public static function recordPoint($image, $cx, $cy, $width = 10, $height = 10)
    {
        return imagefilledellipse($image, $cx, $cy, $width, $height, 0xFF0000);
    }

    // 保存文件
    public static function save($image, $filename)
    {
        return imagepng($image, $filename);
    }

    // RGB颜色的相似度
    public static function colorSimilar($rgb1, $rgb2, $limit = 10)
    {
        // 相似度误差值为 0 直接返回 2 个 RGB 颜色比较
        if ($limit == 0) {
            return $rgb1 == $rgb2;
        }

        $r1 = ($rgb1 >> 16) & 0xFF;
        $g1 = ($rgb1 >> 8) & 0xFF;
        $b1 = $rgb1 & 0xFF;
        $r2 = ($rgb2 >> 16) & 0xFF;
        $g2 = ($rgb2 >> 8) & 0xFF;
        $b2 = $rgb2 & 0xFF;
        return abs($r1 - $r2) < $limit && abs($b1 - $b2) < $limit && abs($g1 - $g2) < $limit;
    }

    // 获取图片内容
    public static function getData($image)
    {
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        return $imageData;
    }

    // 获取图片的 md5
    public static function getMd5($image)
    {
        if (is_string($image)) {
            return md5_file($image);
        }
        return md5(self::getData($image));
    }

    // 销毁资源
    public static function destory($image)
    {
        return imagedestroy($image);
    }
}
