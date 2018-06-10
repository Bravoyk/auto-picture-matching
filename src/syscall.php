<?php
namespace HappyStraw;

/**
 * 系统命令调用
 */
class SysCall
{
    /**
     * 截取全屏
     *
     * @param string $filename 图片文件保存的路径, 一定要保存至项目路径/img/screencapture.png
     * @return void
     */
    public static function captureScreen($filename)
    {
        exec(BIN_PATH . '/control --handle=capture --path=' . $filename);
    }

    /**
     * 模拟鼠标点击
     *
     * @param integer $x
     * @param integer $y
     * @return void
     */
    public static function click($x, $y)
    {
        exec(BIN_PATH . "/control --handle=click --cx={$x} --cy={$y}");
    }
}
