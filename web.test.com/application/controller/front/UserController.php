<?php

/**
 * @filename UserController.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-24 13:32:41
 * @version 1.0
 * @Description
 * 
 */
class UserController extends Controller {

    private $user;
    private $pager;

    function __construct() {
        parent::__construct();
        $this->user = Loader::model('Users');
        $this->pager = Loader::initClass('Pager');
    }

    /**
     * 最新用户
     */
    public function index() {
        $filter = array(
            'page_size' => FRONT_PAGESIZE,
            'page_num' => $this->get('page_num'),
        );
        $data = $this->user->lists($filter);
        $this->pager->setParams($data['count'], FRONT_PAGESIZE);
        $data['page'] = $this->pager->getHtml(2);
        $this->views('index', $data);
    }

    /**
     * 用户登录
     * @return json | view
     */
    public function login() {
        if (!isPost()) {
            $this->views('login');
            return;
        }

        $action = $this->post('action');
        if ('login' != $action) {
            Constant::message(Constant::UNKOWN_REQUEST);
        }
        $captcha = strtolower($this->post('captch'));
        $server_captcha = strtolower($_SESSION['captcha1']);
        if ($captcha != $server_captcha) {
            Constant::message(Constant::CAPTCHA_ERROR);
        }
        $data = array(
            'email' => $this->post('email'),
            'password' => md5($this->post('password')),
        );
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Constant::message(Constant::EMAIL_FORMAT_ERROR);
        }
        if (empty($data['email']) || empty($this->post('password'))) {
            Constant::message(Constant::PARAMSTER_EMPTY);
        }
        $row = $this->user->login($data);
        if (!$row) {
            Constant::message(Constant::LOGIN_FAIL);
        }
        $_SESSION['uid'] = $row['uid'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['captcha1'] = NULL;
        Constant::message(Constant::SUCCESS);
    }

    /**
     * 用户注册并登录
     * @return json | view
     */
    public function register() {
        if (!isPost()) {
            $this->views('register');
            return;
        }

        $action = $this->post('action');
        if ('register' != $action) {
            Constant::message(Constant::UNKOWN_REQUEST);
        }
        $captcha = strtolower($this->post('captcha'));
        $server_captcha = strtolower($_SESSION['captcha1']);
        if ($captcha != $server_captcha) {
            Constant::message(Constant::CAPTCHA_ERROR);
        }
        $data = array(
            'email' => $this->post('email'),
            'password' => md5($this->post('password')),
            'sex' => $this->post('sex'),
            'addtime' => time(),
            'last_login' => time(),
            'last_ip' => getIP(),
            'login_num' => 1
        );
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Constant::message(Constant::EMAIL_FORMAT_ERROR);
        }
        if (empty($data['email']) || empty($this->post('password'))) {
            Constant::message(Constant::PARAMSTER_EMPTY);
        }
        if ($data['password'] != md5($this->post('repassword'))) {
            Constant::message(Constant::CAPTCHA_ERROR);
        }
        if ($this->user->checkEmail($data['email'])) {
            Constant::message(Constant::EMAIL_EXISTS);
        }

        $data['uid'] = (int) $this->user->insert($this->user->userTable, $data, NULL, TRUE);
        if (!$data['uid']) {
            Constant::message(Constant::REGISTER_FAILED);
        }
        foreach ($data as $k => $value) {
            $_SESSION[$k] = $value;
        }
        $_SESSION['captcha1'] = NULL;
        Constant::message(Constant::SUCCESS);
    }

    /**
     * 用户编辑信息
     */
    public function edit() {
        if (!isPost()) {
            $this->_checkLogin();
            $this->views('edit', array('row' => $this->user->getUser($_SESSION['uid'])));
        }

        $this->_checkLogin(TRUE);
        //头像上传
        if ('upload' == $this->get('action')) {
            $upload = Loader::initClass('FileUpload');
            $upload->upload();
            return;
        }

        //信息更新
        if ('edit' == $this->post('action')) {
            $data = array(
                'sex' => $this->post('sex'),
                'edittime' => time()
            );
            if ('######' != $this->post('password')) {
                $data['password'] = md5($this->post('password'));
            }
            if (!empty($this->post('avatar'))) {
                $data['avatar'] = $this->post('avatar');
            }
            $this->user->update($this->user->userTable, $data, 'uid = ' . $_SESSION['uid']);
            Constant::message(Constant::SUCCESS);
        }
    }

    /**
     * 个人信息
     */
    public function profile() {
        $this->_checkLogin();
        $this->views('profile', array('row' => $this->user->getUser($_SESSION['uid'])));
    }
    
    /**
     * 退出
     */
    public function logout() {
        unset($_SESSION['uid']);
        session_destroy();
        redirect('/user/login');
    }

    /**
     * 验证码
     */
    public function captcha() {
        $captcha = Loader::initClass('Captchas');
        $captcha->Style1();
    }

    /**
     * 检测用户是否登录
     * @return NULL
     */
    private function _checkLogin($return_json = FALSE) {
        if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
            if ($return_json) {
                Constant::message(Constant::NOT_LOGIN);
            }
            redirect('/user/login');
            return;
        }
    }

}
