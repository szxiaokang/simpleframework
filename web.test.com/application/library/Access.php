<?php

/**
 * @filename Access.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-25 15:40:36
 * @version 1.0
 * @Description
 * 访问权限限制
 */
class Access {

    //后台限制访问目录
    private $adminDirectory = 'admin';
    //允许后台访问的action
    private $adminAllowAction = array('index', 'captcha', 'login');

    function __construct() {
        global $URI;
        $dir = join('/', $URI->directory);
        if ($dir == $this->adminDirectory) {
            if (!isset($_SESSION['adminid']) && !in_array($URI->method, $this->adminAllowAction)) {
                redirect('/admin/');
            }
        }
    }

}
