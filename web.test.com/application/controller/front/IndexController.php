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
class IndexController extends Controller {

    private $news;
    private $user;

    function __construct() {
        parent::__construct();
        $this->news = Loader::model('News');
        $this->user = Loader::model('Users');
    }

    /**
     * 首页
     */
    public function index() {
        $data = array(
            'rows' => $this->news->getLastNews($this->get('type')),
            'users' => $this->user->getLastUser(),
            'type' => $this->get('type'),
            'pageTitle' => '首页'
        );
        $this->views('index', $data);
    }

    /**
     * 关于
     */
    public function about() {
        $this->views('about');
    }

}
