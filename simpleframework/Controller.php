<?php

/**
 * @filename Controller.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-4-21 11:23:51
 * @version 1.0
 * @Description
 * 
 */
class Controller {

    protected $uri;
    protected $header;
    protected $requestMethod;
    protected $xssHash;

    function __construct() {
        $this->uri = $GLOBALS['URI'];
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->header = $this->getHeader();
    }

    /**
     * 封装$_POST
     * @param string|array $key
     * @return mixed
     */
    public function post($key = NULL) {
        if ($key === NULL) {
            return $_POST;
        }
        if (!isset($_POST[$key])) {
            return NULL;
        }
        return $this->_filter($_POST[$key]);
    }

    /**
     * 封装$_GET
     * @param string|array $key
     * @return mixed
     */
    public function get($key = NULL) {
        if (empty($key)) {
            return $_GET;
        }
        if (!isset($_GET[$key])) {
            return NULL;
        }
        return $this->_filter($_GET[$key]);
    }

    /**
     * 获取请求头信息
     * @return NULL
     */
    protected function getHeader() {
        $keys = array('cache_control', 'pragma', 'connection', 'accept_encoding', 'accept_language', 'accept', 'user_agent', 'host');
        foreach ($keys as $item) {
            $key = strtoupper('http_' . $item);
            if (isset($_SERVER[$key])) {
                $this->header[$item] = $_SERVER[$key];
            }
        }
        return NULL;
    }

    /**
     * 客户端参数过滤
     * @param stirng|array $str
     * @return mixed
     */
    protected function _filter($str) {
        $filter = new Filter();
        return $filter->xssClean($str);
    }

    /**
     * 后台加载视图文件
     * @global global $URI
     * @param string $file
     * @param array $data
     * @param string $layout
     */
    protected function views($file, $data = array()) {
        global $URI;
        
        $controller = strtolower(str_replace('Controller', '', $URI->controller));
        $view_file = APP_PATH . 'views/' . (count($URI->directory) ? join('/', $URI->directory) . '/' : '') . $controller . '/' . $file . '.php';
        if (!file_exists($view_file)) {
            $view_file = APP_PATH . 'views/' . (count($URI->directory) ? join('/', $URI->directory) . '/' : '') . $file . '.php';
        }
        if (!file_exists($view_file)) {
            $view_file = APP_PATH . 'views/' . $file . '.php';
        }
        if (!file_exists($view_file)) {
            error(500, 'File: ' . $view_file . ' Not Exists.');
        }
        if (!empty($data)) {
            extract($data);
        }
        unset($data);
        ob_start();
        include $view_file;
        $simpleFrameworkViewContents = ob_get_contents();
        ob_end_clean();
        include APP_PATH . 'views/layout/' . (count($URI->directory) ? strtolower($URI->directory[0]) : 'front') . '.php';
    }

    /**
     * 加载视图, 不使用通用层
     * @global global $URI
     * @param string $file
     * @param array $data
     */
    protected function view($file, $data = array()) {
        global $URI;
        $controller = strtolower(str_replace('Controller', '', $URI->controller));
        $view_file = APP_PATH . 'views/' . (count($URI->directory) ? join('/', $URI->directory) . '/' : '') . $controller . '/' . $file . '.php';
        if (!file_exists($view_file)) {
            $view_file = APP_PATH . 'views/' . (count($URI->directory) ? join('/', $URI->directory) . '/' : '') . $file . '.php';
        }
        if (!file_exists($view_file)) {
            $view_file = APP_PATH . 'views/' . $file . '.php';
        }
        if (!file_exists($view_file)) {
            error(500, 'File: ' . $view_file . ' Not Exists.');
        }
        if (!empty($data)) {
            extract($data);
        }
        include $view_file;
    }

    /**
     * 设置属性
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->$key = $value;
    }

}
