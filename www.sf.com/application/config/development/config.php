<?php

/**
 * @filename config.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-4-21 14:16:24
 * @version 1.0
 * @Description
 * 开发环境配置
 */
//自动包含
$autoInclude = array('Constant');

//自动加载, 注意: controller类中不能含有此类名的属性
$autoLoader = array('Access');

//数据库配置
$db = array(
    'default' => array(
        'host' => '127.0.0.1',
        'database' => 'simpleFramework',
        'username' => 'root',
        'password' => '123456',
    ),
);

//默认模块
$defaultModule = 'front'; //[admin]

//后台分页 页大小
define('ADMIN_PAGESIZE', 10);

define('FRONT_PAGESIZE', 10);

define('IMAGE_URL', '/upload/');

define('IMAGE_DIR', WWW_PATH . 'upload/');

