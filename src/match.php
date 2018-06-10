<?php
namespace HappyStraw;

class Match
{
    // 所有方块
    public $squares = [];


    // 游戏区域图片
    public $image;

    private $starPosX = 0;
    private $starPosY = 0;

    // 设置所有方块列表
    public function setSquares($squares)
    {
        $this->squares = $squares;
        return $this;
    }

    // 设置游戏区域图片资源
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    // 设置游戏区域在全屏的起始位置
    public function setStartPos($x, $y)
    {
        $this->starPosX = $x;
        $this->starPosY = $y;
        return $this;
    }

    // 判断 2 个方块是否能连通
    protected function canMatch($x1, $y1, $x2, $y2)
    {
        // 如果坐标位置的图片内容为空, 则直接返回 false
        if ($this->squares[$x1][$y1] == '' || $this->squares[$x2][$y2] == '') {
            return false;
        }

        // 同一个坐标, 直接返回 false
        if ($x1 == $x2 && $y1 == $y2) {
            return false;
        }

        // 2 张方块不相同, 直接返回 false
        if ($this->squares[$x1][$y1] != $this->squares[$x2][$y2]) {
            return false;
        }

        $canMatch = $this->checkHorizontal($x1, $y1, $x2, $y2)
            || $this->checkVertical($x1, $y1, $x2, $y2)
            || $this->checkTrunOnce($x1, $y1, $x2, $y2)
            || $this->checkTurnTwice($x1, $y1, $x2, $y2);

        // 如果能连通, 将这 2 个坐标的图片内容设为空
        if ($canMatch) {
            $this->squares[$x1][$y1] = $this->squares[$x2][$y2] = '';
        }

        return $canMatch;
    }

    // 判断水平是否连通
    protected function checkHorizontal($x1, $y1, $x2, $y2)
    {
        // 同一个坐标, 直接返回 false
        if ($x1 == $x2 && $y1 == $y2) {
            return false;
        }

        // 纵坐标不同, 直接返回 false
        if ($y1 != $y2) {
            return false;
        }

        $max = max($x1, $x2);
        $min = min($x1, $x2);

        // 是否相邻
        if (($max - $min) == 1) {
            return true;
        }

        // 中间是否存在有非空方块
        for ($i = $min + 1; $i < $max; $i++) {
            if ($this->squares[$i][$y1] != '') {
                return false;
            }
        }

        return true;
    }

    // 判断垂直是否连通
    protected function checkVertical($x1, $y1, $x2, $y2)
    {
        // 同一个坐标, 直接返回 false
        if ($x1 == $x2 && $y1 == $y2) {
            return false;
        }

        // 横坐标不同, 直接返回 false
        if ($x1 != $x2) {
            return false;
        }

        $max = max($y1, $y2);
        $min = min($y1, $y2);

        // 是否相邻
        if (($max - $min) == 1) {
            return true;
        }

        // 中间是否存在有非空方块
        for ($i = $min + 1; $i < $max; $i++) {
            if ($this->squares[$x1][$i] != '') {
                return false;
            }
        }

        return true;
    }

    // 单个拐点判断
    protected function checkTrunOnce($x1, $y1, $x2, $y2)
    {
        // 同一个坐标, 直接返回 false
        if ($x1 == $x2 && $y1 == $y2) {
            return false;
        }

        // 不能是同行同列
        if ($x1 == $x2 || $y1 == $y2) {
            return false;
        }

        // -| 拐点判断
        if ($this->squares[$x2][$y1] == '') {
            return $this->checkHorizontal($x1, $y1, $x2, $y1) && $this->checkVertical($x2, $y1, $x2, $y2);
        }

        // |- 拐点判断
        if ($this->squares[$x1][$y2] == '') {
            return $this->checkVertical($x1, $y1, $x1, $y2) && $this->checkHorizontal($x1, $y2, $x2, $y2);
        }

        return false;
    }

    // 2个拐点判断
    protected function checkTurnTwice($x1, $y1, $x2, $y2)
    {
        // 同一个坐标, 直接返回 false
        if ($x1 == $x2 && $y1 == $y2) {
            return false;
        }

        // 遍历数组查询合适的拐点
        foreach ($this->squares as $col => $squares) {
            foreach ($squares as $row => $square) {
                // 不为空不能当拐点
                if ($square != '') {
                    continue;
                }
                // 不和 2 个方块处于同行同列, 不能当拐点
                if ($col != $x1 && $row != $y1 && $col != $x2 && $row != $y2) {
                    continue;
                }
                // 过滤单个拐点
                if (($col == $x1 && $row == $y2) || ($col == $x2 && $row == $y1)) {
                    continue;
                }

                if ($this->checkTrunOnce($x1, $y1, $col, $row)
                    &&
                    ($this->checkVertical($col, $row, $x2, $y2) || $this->checkHorizontal($col, $row, $x2, $y2))
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    // 模拟鼠标点击
    protected function click($x, $y)
    {
        // 行列乘以(方块宽高 + 行列/间距)得到游戏区域内的坐标, 并指向中间位置
        $x = $x * (SQUARE_WIDTH + GAME_COLUMN_SPACE) + ceil(SQUARE_WIDTH / 2);
        $y = $y * (SQUARE_HEIGHT + GAME_ROW_SPACE) + ceil(SQUARE_HEIGHT / 2);

        // 描点, 查看实际结果
        Image::recordPoint($this->image, $x, $y);

        // 加上全屏截图起始的坐标
        $x += $this->starPosX;
        $y += $this->starPosY;

        SysCall::click($x, $y);

        if (TIME_INTERVAL > 0) {
            sleep(TIME_INTERVAL);
        }
    }


    // 移除相同的方块
    protected function removeMatch($x, $y)
    {
        foreach ($this->squares as $col => $squares) {
            foreach ($squares as $row => $square) {
                if ($square == '') {
                    continue;
                }
                if ($this->canMatch($x, $y, $col, $row)) {
                    echo "Match: ({$x}, {$y}) -> ({$col}, {$row})" . PHP_EOL;
                    $this->click($x, $y);
                    $this->click($col, $row);
                    return true;
                }
            }
        }
        return false;
    }

    // 自动消除
    public function autoRemove()
    {
        // 遍历数组查询合适的拐点
        foreach ($this->squares as $col => $squares) {
            foreach ($squares as $row => $square) {
                if ($square == '') {
                    continue;
                }
                if ($this->removeMatch($col, $row)) {
                    return true;
                }
            }
        }
        return false;
    }
}
