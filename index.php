<?php
//ini_set("memory_limit","512M");
ini_set("default_charset","UTF-8");
//定义项目名称和路径
//define('APP_NAME', 'wiss');
//define('APP_PATH', './');
// 开启调试模式
define('APP_DEBUG',TRUE);

//环境 development/test/product
define('APP_ENV', 'development');

//define("GZIP_ENABLE",function_exists('ob_gzhandler'));
//ob_start(GZIP_ENABLE ? 'ob_gzhandler': null );
// 加载框架入口文件
require( "./ThinkPHP/ThinkPHP.php");