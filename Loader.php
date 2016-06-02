<?php

/**
 * @filename Loader.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-4-21 16:13:52
 * @version 1.0
 * @Description
 * 
 */
class Loader {

    static public $_class = array();
    static public $_models = array();

    private function __construct() {
        return NULL;
    }

    /**
     * 初始化自定义类
     * @param string $classname
     * @param mixed $params
     * @return object
     */
    public static function initClass($classname, $params = NULL) {
        if (isset(self::$_class[$classname])) {
            return self::$_class[$classname];
        }
        if (!file_exists(APP_PATH . 'library/' . $classname . '.php')) {
            error(500, APP_PATH . 'library/' . $classname . '.php Not Exists.');
        }
        include APP_PATH . 'library/' . $classname . '.php';
        return self::$_class[$classname] = new $classname($params);
    }

    /**
     * 加载并初始化用户model
     * @param string $classname
     * @param mixed $params
     * @return object|nulll
     */
    public static function model($classname, $params = NULL) {
        if (isset(self::$_models[$classname])) {
            return self::$_models[$classname];
        }

        if (!file_exists(APP_PATH . 'models/' . $classname . '.php')) {
            error(500, APP_PATH . 'models/' . $classname . '.php Not Exists.');
        }
        include APP_PATH . 'models/' . $classname . '.php';
        return self::$_models[$classname] = new $classname($params);
    }

}
