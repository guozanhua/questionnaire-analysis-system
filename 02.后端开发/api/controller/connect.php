<?php

require_once 'config.php';
//连接数据库，得到连接句柄
$link = mysqli_connect($host, $user, $pass, $db, $port);
mysqli_set_charset($link, 'utf-8');