<?php
namespace HappyStraw;

class AutoPictureMatch
{
    /**
     * 游戏配置区域, ['x' => 0, 'y' => 0, 'width' => 100, 'height' => '100']
     *
     * @var array
     */
    private $rectangle;

    // 游戏区域图片
    private $image;

    // 所有的方块数组
    private $squares = [];

    // 获取游戏区域在全屏截图起始坐标的位置
    protected function getGameAreaStartPos($image)
    {
        $width = Image::width($image);
        $height = Image::height($image);

        $starPosX = 0;
        $starPosY = 0;

        // 背景颜色, 用来查找起始坐标
        $bgColor = EMPTY_AREA_COLOR;

        for ($row = MARGIN_TOP; $row < $height; $row++) {
            $tempWidth = 0;
            $tempPosX = 0;
            for ($col = MARGIN_LEFT; $col < $width; $col++) {
                $color = Image::colorAt($image, $col, $row);
                // 当前坐标的颜色为游戏区域开始的颜色时, 记录颜色最大连续宽度
                // 如果是纯色背景(无色差), Image::colorSimilar 的第三个参数 $limit 为 0
                if (Image::colorSimilar($color, $bgColor, 4)) {
                    // 记录当前颜色第一个 x 坐标
                    if ($tempPosX == 0) {
                        $tempPosX = $col;
                    }
                    // 记录最大连续宽度
                    $tempWidth++;
                    // 替换下次检测相似度的背景颜色
                    $bgColor = $color;
                    // 当最大宽度满足游戏区域的宽度时, 则标记此坐标点为游戏区域起始坐标
                    if ($tempWidth == START_POS_WIDTH) {
                        $starPosX = $tempPosX;
                        // 加上起始区域颜色的高度
                        $starPosY = $row;
                        break 2;
                    }
                } else {
                    // 重置
                    $tempPosX = 0;
                    $tempWidth = 0;
                    $bgColor = EMPTY_AREA_COLOR;
                }
            }
        }
        return [$starPosX, $starPosY];
    }

    // 判断是否是留白的方块
    protected function isEmptySquare($square)
    {
        // 等于 0 表示这个游戏区域都是非空方块
        if (EMPTY_SQUARE_WIDTH == 0) {
            return false;
        }

        $tempWidth = 0;
        // 背景颜色, 用来查找判断是否是空方块
        $bgColor = EMPTY_AREA_COLOR;
        // 只要判断方块中间是不是有指定长度的背景颜色, 是的话表示这个方块是留白区域
        for ($i = 0; $i < SQUARE_WIDTH; $i++) {
            // 取 SQUARE_HEIGHT 的中间区域的连续颜色
            $color = Image::colorAt($square, $i, 19);
            // 当前坐标的颜色为背景颜色时, 记录颜色最大连续宽度
            if (Image::colorSimilar($color, $bgColor, 0)) {
                // 记录最大连续宽度
                $tempWidth++;
                $bgColor = $color;
                // 当最大宽度满足游戏区域的宽度时, 则标记此坐标点为游戏区域起始坐标
                if ($tempWidth == EMPTY_SQUARE_WIDTH) {
                    return true;
                }
            } else {
                $tempWidth = 0;
                $bgColor = EMPTY_AREA_COLOR;
            }
        }
        return false;
    }

    // 解析图片文件, 并找到游戏区域
    protected function load($filename)
    {
        if (!file_exists($filename)) {
            exit('screenshot file is not exists!');
        }

        // 获取截图信息
        $fullscreen = Image::create($filename);

        // 获取游戏区域的起始坐标
        list($rect['x'], $rect['y']) = $this->getGameAreaStartPos($fullscreen);
        if ($rect['x'] <= 0 || $rect['y'] <= 0) {
            exit('can not find game area positon!');
        }

        // 游戏区域的起始坐标的偏移位置, 修正起始坐标
        $rect['x'] += PADDING_LEFT;
        $rect['y'] += PADDING_TOP;

        // 所有方块区域宽/高 = 行/列 * 方块宽/高 + (行/列 -1) * 行/列间距
        $rect['width'] = GAME_COLUMN * (SQUARE_WIDTH + GAME_COLUMN_SPACE) - GAME_COLUMN_SPACE;
        $rect['height'] = GAME_ROW * (SQUARE_HEIGHT + GAME_ROW_SPACE) - GAME_ROW_SPACE;

        $this->rectangle = $rect;

        // 截取游戏区域, 保存数据
        $this->image = Image::crop($fullscreen, $rect);

        // 将截图的游戏区域保存
        Image::save($this->image, $this->path . '/gamearea.png');

        // 销毁全屏图片资源
        Image::destory($fullscreen);
    }

    protected function getSquares()
    {
        $squares = [];
        for ($row = 0; $row < GAME_ROW; $row++) {
            for ($col = 0; $col < GAME_COLUMN; $col++) {
                $square = Image::crop($this->image, [
                    'x' => $col * (SQUARE_WIDTH + GAME_COLUMN_SPACE),
                    'y' => $row * (SQUARE_HEIGHT + GAME_ROW_SPACE),
                    'width' => SQUARE_WIDTH,
                    'height' => SQUARE_HEIGHT,
                ]);
                // 是否是空白的区域
                if ($this->isEmptySquare($square)) {
                    $squares[$col][$row] = '';
                    continue;
                }

                // [x][y] => content
                $squares[$col][$row] = Image::getData($square);

                Image::destory($square);
            }
        }
        return $squares;
    }

    // 倒计时
    protected function countdown()
    {
        $i = 3;
        while ($i > 0) {
            echo $i, PHP_EOL;
            $i--;
            sleep(1);
        }
        echo 'Start !', PHP_EOL;
    }

    public function run($path)
    {
        $this->path = $path;

        $screencapture = $this->path . '/screencapture.png';

        // 在倒计时时间内需要手动把游戏窗口设为当前激活窗口
        $this->countdown();

        // 截取全屏 - 调用系统命令
        SysCall::captureScreen($screencapture);

        // 读取文件, 并获取到游戏区域的位置
        $this->load($screencapture);

        // 获取方块信息列表(含坐标与图片内容)
        $this->squares = $this->getSquares();

        // 自动消除连连看
        $match = new Match();
        $match->setSquares($this->squares)->setStartPos($this->rectangle['x'], $this->rectangle['y']);
        // 设置图片, 用于描点测试 @see Match::click
        $match->setImage($this->image);

        $i = 0;
        // 最多不超过 GAME_ROW * GAME_COLUMN / 2 对方块
        $max = ceil(GAME_ROW * GAME_COLUMN / 2);
        while ($i <= $max) {
            $match->autoRemove();
            $i++;
        }

        // 保存描点后的文件
        Image::save($this->image, $this->path . '/result.png');

        // 释放资源
        Image::destory($this->image);
    }
}
