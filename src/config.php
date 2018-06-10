<?php

define('BIN_PATH', realpath(__DIR__ . '/../bin'));

// 每次消除间隔时间, 单位: 秒.
define('TIME_INTERVAL', 0);

// 游戏非方块区域的背景颜色(留白), 如: 0x304C70
define('EMPTY_AREA_COLOR', 0xab991d);

// 游戏区域起始纵坐标背景(留白)颜色的宽度, 如: 590
// 满足 等于或相似于 `EMPTY_AREA_COLOR` 颜色连续宽度为 `START_POS_WIDTH` 表示为起始位置
define('START_POS_WIDTH', 60);

// 游戏区域的起始坐标的偏移位置, 用来修正起始坐标, 如:２,5
define('PADDING_TOP', 0);
define('PADDING_LEFT', 0);

// 游戏区域距离顶部高度, 用来减少扫描量
define('MARGIN_TOP', 100);
// 游戏区域距离左侧宽度, 用来减少扫描量
define('MARGIN_LEFT', 13);

// 游戏区域的方块行列数
define('GAME_ROW', 6);
define('GAME_COLUMN', 11);

// 游戏区域的方块行间距
define('GAME_ROW_SPACE', 5);
// 游戏区域的方块列间距
define('GAME_COLUMN_SPACE', 5);

// 每个方格大小
define('SQUARE_WIDTH', 60);
define('SQUARE_HEIGHT', 60);

// 验证方块最大背景颜色宽度, 为 0 表示不检测, 即整个游戏区域都是非空方块
define('EMPTY_SQUARE_WIDTH', 0);
