<?php

require_once BASE_PATH . '/extension/Medoo/src/Medoo.php'; //这里貌似识别不到../，使用index.php中定义的路径常量代替

use Medoo\Medoo;  //引入medoo命名空间

require_once 'config.php';
// 初始化配置
$database_medoo = new Medoo([
	// required
	'database_type' => $database_type,
	'database_name' => $db,
	'server' => $host,
	'username' => $user,
	'password' => $pass,
 
	// [optional]
	'charset' => 'utf8',
	'port' => 3306,
 
	// [optional] Table prefix
//	'prefix' => 'PREFIX_', //表前缀，不需要
 
	// [optional] Enable logging (Logging is disabled by default for better performance)
//	'logging' => true,
 
	// [optional] MySQL socket (shouldn't be used with server and port)
//	'socket' => '/tmp/mysql.sock',
 
	// [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
//	'option' => [
//		PDO::ATTR_CASE => PDO::CASE_NATURAL
//	],
 
	// [optional] Medoo will execute those commands after connected to the database for initialization
//	'command' => [
//		'SET SQL_MODE=ANSI_QUOTES'
//	]
]);
