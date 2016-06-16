<?php

/**
 * @filename Common.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-4-21 10:41:52
 * @version 1.0
 * @Description
 * 框架常用函数
 */

/**
 * 是否是POST请求
 * @return boolean
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * 替换隐形字符
 * @param string $str
 * @param boolean $url_encoded
 * @return string
 */
function removeInvisibleCharacters($str, $url_encoded = TRUE) {
    $non_displayables = array();
    if ($url_encoded) {
        $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
        $non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
    }
    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127
    do {
        $str = preg_replace($non_displayables, '', $str, -1, $count);
    } while ($count);

    return $str;
}

/**
 * 获取客户端IP
 * @return string
 */
function getIP() {
    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getURL($is_encode = TRUE) {
    $url = 'http://' . $_SERVER['SERVER_NAME'];
    $url .= ($_SERVER["SERVER_PORT"] == '80' || $_SERVER["SERVER_PORT"] == '443') ? '' : ':' . $_SERVER["SERVER_PORT"];
    $url .= $_SERVER["REQUEST_URI"];
    return $is_encode ? encodeURL($url) : $url;
}

/**
 * base64 编码URL
 * @param string $url
 * @return string
 */
function encodeURL($url) {
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($url));
}

/**
 * base64 解密URL
 * @param string $url
 * @return string
 */
function decodeURL($url) {
    $data = str_replace(array('-', '_'), array('+', '/'), $url);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

/**
 * 写入日志
 * @param string $data
 * @return int
 */
function logMessages($logs) {
    return file_put_contents(LOG_PATH . '/sys.log', date('[Y-m-d H:i:s]') . ' ' . $logs . "\n", FILE_APPEND);
}

/**
 * 返回错误信息并停止执行
 * @param string $code
 * @param string $text
 */
function error($code = NULL, $text = '') {
    $server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
    header($server_protocol . ' ' . $code, TRUE, $code);
    logMessages("IP: " . getIP() . ', ErrorCode: ' . $code . ', Error: ' . $text);
    include APP_PATH . 'views/common/error.php';
    exit;
}

function redirect($uri = '', $method = 'auto', $code = NULL) {
    if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
        $method = 'refresh';
    } elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code))) {
        if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
            $code = ($_SERVER['REQUEST_METHOD'] !== 'GET') ? 303 : 307;
        } else {
            $code = 302;
        }
    }

    switch ($method) {
        case 'refresh':
            header('Refresh:0;url=' . $uri);
            break;
        default:
            header('Location: ' . $uri, TRUE, $code);
            break;
    }
    exit;
}
