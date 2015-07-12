<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 设置项目根目录
define('APP_ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH', APP_ROOT . 'apps/');

//设置临时文件路径
define('RUNTIME_PATH', APP_ROOT . 'temp/');

// 绑定Thinkphp目录
define('THINK_PATH', APP_ROOT .'source/');

// 定义当前文件名，以防fcgi模式下发生错误
define('_PHP_FILE_',    rtrim($_SERVER['SCRIPT_NAME'],'/'));

// 引入ThinkPHP入口文件
require THINK_PATH . 'ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单