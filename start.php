<?php

require './src/config.php';
require './src/syscall.php';
require './src/image.php';
require './src/match.php';
require './src/app.php';

(new \HappyStraw\AutoPictureMatch)->run(__DIR__ . '/img');
