<?php

/**
 * @filename Constant.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-5 15:36:10
 * @version 1.0
 * @Description
 * 错误代码
 */
class Constant {

    const SUCCESS = 0;
    //admin
    const INCOMPLETE_REQUEST_METHOD = -1;
    const PARAMSTER_EMPTY = -2;
    const CAPTCHA_ERROR = -3;
    const LOGIN_FAIL = -4;
    const EXISTS = -5;
    const ADD_FAIL = -6;
    //front
    //通用
    const UNKOWN_REQUEST = -7;
    const REGISTER_PASSWORD_ERROR = -8;
    const EMAIL_FORMAT_ERROR = -9;
    const EMAIL_EXISTS = -10;
    const REGISTER_FAILED = -11;
    const NOT_LOGIN = -12;


    const LOGS_ADMIN_DIR = 'admin';
    const LOGS_API_DIR = 'api';
    const UPLOAD_MAX_SIZE = 2097152; //2M
    const UPLOAD_FAILED = -500;
    const UPLOAD_ERR_INI_SIZE = -501;
    const UPLOAD_ERR_FORM_SIZE = -502;
    const UPLOAD_ERR_PARTIAL = -503;
    const UPLOAD_ERR_NO_FILE = -504;
    const UPLOAD_ERR_NO_TMP_DIR = -505;
    const UPLOAD_ERR_CANT_WRITE = -505;
    const UPLOAD_ERR_UNKOWN = -506;
    const UPLOAD_ERR_SIZE = -507;
    const UPLOAD_ERR_EMPTY_NAME = -508;
    const UPLOAD_ERR_NOT_SUPPORT_EXT = -509;
    const UPLOAD_ERR_CREATE_DIR_FAILED = -510;
    const UPLOAD_ERR_EXT_OFF = -511;
    const UPLOAD_ERR_FILES_NUMBER = -512;
    const UPLOAD_ERR_DIR_NOT_EXISTS = -513;
    const UPLOAD_ERR_WRITE_PERMISSION = -514;
    const UPLOAD_ERR_DIR_NAME_INCORRECT = -515;

    static public $maps = array(
        self::SUCCESS => 'OK',
        self::INCOMPLETE_REQUEST_METHOD => '不正确的请求方式',
        self::PARAMSTER_EMPTY => '参数有空值',
        self::CAPTCHA_ERROR => '验证码错误',
        self::LOGIN_FAIL => '登录失败, 用户名或密码错误',
        self::EXISTS => '此名称已经存在',
        self::ADD_FAIL => '添加失败',
        self::UNKOWN_REQUEST => '未知的请求',
        self::REGISTER_PASSWORD_ERROR => '两次输入的密码不一致',
        self::EMAIL_FORMAT_ERROR => 'Email格式不正确',
        self::EMAIL_EXISTS => '此Email已经存在',
        self::REGISTER_FAILED => '注册失败, 请稍后再试!',
        self::NOT_LOGIN => '用户没有登录!',
        
        self::UPLOAD_FAILED => '上传失败',
        self::UPLOAD_ERR_INI_SIZE => '超过php.ini允许的大小',
        self::UPLOAD_ERR_FORM_SIZE => '超过表单允许的大小',
        self::UPLOAD_ERR_PARTIAL => '图片只有部分被上传',
        self::UPLOAD_ERR_NO_FILE => '没有文件被上传',
        self::UPLOAD_ERR_NO_TMP_DIR => '找不到临时目录',
        self::UPLOAD_ERR_CANT_WRITE => '文件写入失败',
        self::UPLOAD_ERR_UNKOWN => '未知错误',
        self::UPLOAD_ERR_SIZE => '超出限制',
        self::UPLOAD_ERR_EMPTY_NAME => '文件名为空',
        self::UPLOAD_ERR_NOT_SUPPORT_EXT => '不支持该类型文件',
        self::UPLOAD_ERR_CREATE_DIR_FAILED => '创建目录失败',
        self::UPLOAD_ERR_EXT_OFF => '扩展未开启',
        self::UPLOAD_ERR_FILES_NUMBER => '超出最大上传数量(最多10张)',
        self::UPLOAD_ERR_DIR_NOT_EXISTS => '目录不存在',
        self::UPLOAD_ERR_WRITE_PERMISSION => '上传目录没有写权限',
        self::UPLOAD_ERR_DIR_NAME_INCORRECT => '目录名不正确',
    );

    static public function message($const, $data = '', $type = 'front', $upload_file = false) {
        self::logs($const, $data, $type);
        header("content-type:application/json;charset=utf-8");
        if ($upload_file) {
            echo json_encode(array('error' => $const, 'message' => self::$maps[$const], 'url' => $data));
            exit;
        }
        echo json_encode(array('code' => $const, 'msg' => self::$maps[$const], 'data' => $data));
        exit;
    }

    static public function adminMessage($const, $data = '', $type = 'admin') {
        self::message($const, $data, $type);
    }

    static public function uploadMessage($const, $data = '', $type = 'admin', $upload_file = true) {
        self::message($const, $data, $type, $upload_file);
    }

    /**
     * 
     * @param type $const
     * @param int|boolean $data
     */
    static public function logs($const, $data, $type = 'admin') {
        $logs_path = APP_PATH . 'logs/' . $type . '/' . date('Y-m') . '/';
        if (!file_exists($logs_path)) {
            mkdir($logs_path, 0777, TRUE);
        }
        $logs_file = $logs_path . date('j') . '.logs';
        $logs_data = date('[Y-m-d H:i:s]') . " " . $_SERVER['REQUEST_METHOD'] . " " . getIp() . ' ';
        $logs_data .= $_SERVER['REQUEST_URI'];
        if (isPost()) {
            $logs_data .= ' ';
            $tmp = array();
            foreach ($_POST as $k => $v) {
                $tmp[] = "{$k}=" . (is_string($v) ? (strlen($v) > 32 ? substr($v, 0, 29) . '...' : $v) : 'object');
            }
            $logs_data .= join('&', $tmp);
        }
        $logs_data .= ' ' . $const . '(' . self::$maps[$const] . ')';
        if (is_string($data)) {
            $logs_data .= ' ' . $data;
        }
        return file_put_contents($logs_file, $logs_data . "\n", FILE_APPEND);
    }

}
