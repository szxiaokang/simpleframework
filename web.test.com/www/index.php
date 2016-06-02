<?php

/**
 * @filename index.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-21 10:54:48
 * @version 1.0
 * @Description
 * 
 */
session_start();
define('ENV', 'development');
define('APP_PATH', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/application/');
define('WWW_PATH', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/www/');
define('LIB_PATH', str_replace('\\', '/', dirname(dirname(dirname(__FILE__)))) . '/library/');
define('LOG_PATH', APP_PATH . 'logs/');
include LIB_PATH . 'SimpleFramework.php';

