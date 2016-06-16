<?php

/**
 * @filename URI.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-4-21 10:54:08
 * @version 1.0
 * @Description
 * URL解析器
 */
class URI {

    public $segments = array();
    public $directory = array();
    public $controller = 'IndexController';
    public $method = 'index';
    
    function __construct() {
        $uri = $this->_parseURL();
        $this->_setURI($uri);
        $this->_parseDirectory();
    }

    private function _parseURL() {
        if (!isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            return '';
        }
        $uri = parse_url('http://kang' . $_SERVER['REQUEST_URI']);
        $query = isset($uri['query']) ? $uri['query'] : '';
        $uri = isset($uri['path']) ? $uri['path'] : '';

        if (isset($_SERVER['SCRIPT_NAME'][0])) {
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
                $uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
                $uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
        }

        if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0) {
            $query = explode('?', $query, 2);
            $uri = $query[0];
            $_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
        } else {
            $_SERVER['QUERY_STRING'] = $query;
        }
        parse_str($_SERVER['QUERY_STRING'], $_GET);
        if ($uri === '/' OR $uri === '') {
            return '/';
        }

        $uris = array();
        $tok = strtok($uri, '/');
        while ($tok !== FALSE) {
            if ((!empty($tok) OR $tok === '0') && $tok !== '..') {
                $uris[] = $tok;
            }
            $tok = strtok('/');
        }
        return implode('/', $uris);
    }

    private function _setURI($str) {
        $uri_string = trim(removeInvisibleCharacters($str, FALSE), '/');
        if ($uri_string !== '') {
            $this->segments[0] = NULL;
            foreach (explode('/', trim($uri_string, '/')) as $val) {
                $val = trim($val);
                if ($val !== '') {
                    $this->segments[] = $val;
                }
            }

            unset($this->segments[0]);
        }
    }


    private function _parseDirectory() {
        if (!count($this->segments)) {
            return NULL;
        }
        $segments = array();
        foreach ($this->segments as $k => $item) {
            $path = APP_PATH . 'controller/';
            $path .= count($this->directory) ? join('/', $this->directory) . '/' . $item : $item;
            if (is_dir($path)) {
                $this->directory[] = $item;
                unset($this->segments[$k]);
            } else {
                $segments[] = $item;
            }
        }

        if ($segments) {
            $this->segments = $segments;
        }
        
        $this->controller = isset($this->segments[0]) ? ucfirst($this->segments[0]) . 'Controller' : $this->controller;
        $this->method = isset($this->segments[1]) ? $this->segments[1] : $this->method;
        $this->segments[0] = $this->controller;
        $this->segments[1] = $this->method;
    }

}
