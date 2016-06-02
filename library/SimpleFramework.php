<?php

/**
 * @filename Core.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-4-21 13:59:14
 * @version 1.0
 * @Description
 * 
 */
define('VERSION', '1.0');
ini_set('zlib.output_compression', TRUE);

include APP_PATH . 'config/' . ENV . '/config.php';
include LIB_PATH . 'Common.php';
include LIB_PATH . 'Loader.php';
include LIB_PATH . 'URI.php';
include LIB_PATH . 'Filter.php';

$URI = new URI();
if (empty($URI->directory)) {
    $URI->directory[] = $defaultModule;
}
$directory = $URI->directory;
$controller = $URI->controller;
$method = $URI->method;

include LIB_PATH . 'Controller.php';
include LIB_PATH . 'Model.php';

$controller_file = APP_PATH . 'controller/' . (count($directory) ? join('/', $directory) . '/' : '') . $controller . '.php';
if (!file_exists($controller_file)) {
    error(500, ucfirst($controller) . ' Not Exists.');
}

//调用controller文件及方法
include $controller_file;
$controller_class = new $controller();

//自动包含, 不初始化
if (!empty($autoInclude)) {
    foreach ($autoInclude as $class) {
        if (!file_exists(APP_PATH . 'library/' . ucfirst($class) . '.php')) {
            logMessages("autoInclude: {$class} not exists.");
            error(500, 'autoInclude: ' . APP_PATH . 'library/' . ucfirst($class) . '.php Not Exists.');
        }
        include APP_PATH . 'library/' . ucfirst($class) . '.php';
    }
}

//自动加载类, 访问方法: 在controller的方法中 $this->classname->method()
if (!empty($autoLoader)) {
    foreach ($autoLoader as $class) {
        $class = ucfirst($class);
        if (!file_exists(APP_PATH . 'library/' . $class . '.php')) {
            logMessages("autoLoader: {$class} not exists.");
            error(500, APP_PATH . 'autoLoader: library/' . $class . '.php Not Exists.');
        }
        if (isset($controller_class->$class)) {
            logMessages("{$controller_class} already exists properties: {$class}");
            error(500, "{$controller_class} already exists properties: {$class}");
        }
        $controller_class->set($class, Loader::initClass($class));
    }
}


$controller_class->$method();
