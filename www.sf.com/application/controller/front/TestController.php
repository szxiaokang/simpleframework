<?php

/**
 * @filename IndexController.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-4-22 14:31:13
 * @version 1.0
 * @Description
 * 
 */
class TestController extends Controller {

    private $user;

    function __construct() {
        parent::__construct();
        $this->user = Loader::model('Users');
    }

    /**
     * 首页
     */
    public function index() {
	$data = $this->user->getUser(12);
	$logs = join(', ', $data);
	file_put_contents(LOG_PATH . 'test.logs', $logs, FILE_APPEND);
        $this->views('index', array('row' => $data));
    }

    /**
     * 关于
     */
    public function about() {
        $this->views('about');
    }

}
