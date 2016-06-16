<?php

/**
 * @filename Index.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-5 10:23:29
 * @version 1.0
 * @Description
 * 
 */
class IndexController extends Controller {

    private $admins;

    function __construct() {
        parent::__construct();
    }

    /**
     * 登录后首页
     * @return NULL
     */
    public function index() {
        if (!isset($_SESSION['adminid'])) {
            $this->login();
            return;
        }
        
        $news = Loader::model('News');
        $admins = Loader::model('Admins');
        $users = Loader::model('Users');
        $data = array(
            'news_list' => $news->getNewsList(),
            'news_count' => $news->count(),
            'admin_num' => $admins->count(),
            'user_num' => $users->count(),
            'user_list' => $users->getLastList()
        );
        $this->views('main', $data);
    }

    /**
     * 登录
     * @return NULL
     */
    public function login() {
        if (!isPost()) {
            $this->view('login');
            return;
        }
        $this->admins = Loader::model('Admins');
        $action = $this->post('action');
        if ('login' != $action) {
            Constant::adminMessage(Constant::INCOMPLETE_REQUEST_METHOD);
        }

        $sess_code = strtolower($_SESSION['captcha1']);
        $client_code = strtolower($this->post('code'));
        if ($sess_code != $client_code) {
            Constant::adminMessage(Constant::CAPTCHA_ERROR);
        }

        $data = array(
            'username' => $this->post('username'),
            'password' => md5($this->post('password')),
        );
        if (empty($data['username']) || empty($this->post('password'))) {
            Constant::adminMessage(Constant::PARAMSTER_EMPTY);
        }
        $row = $this->admins->login($data);
        if (!empty($row)) {
            foreach ($row as $key => $val) {
                $_SESSION[$key] = $val;
            }
            Constant::adminMessage(Constant::SUCCESS);
        }
        Constant::adminMessage(Constant::LOGIN_FAIL);
    }
    
    /**
     * 退出
     */
    public function logout() {
        unset($_SESSION['adminid']);
        session_destroy();
        redirect('/admin/');
    }

    /**
     * 验证码
     */
    public function captcha() {
        $captcha = Loader::initClass('Captchas');
        $captcha->Style1();
    }

}
