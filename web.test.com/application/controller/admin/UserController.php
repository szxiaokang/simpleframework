<?php

/**
 * @filename UserController.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-6-2 9:35:47
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
     * 用户列表
     */
    public function index() {
        // POST方式 删除
        if (isPost() && $this->post('action') == 'delete') {
            $ids = $this->post('ids');
            $url = decodeURL($this->post('url'));
            if (count($ids) && !empty($url)) {
                $this->user->whereIn('uid', $ids)->delete($this->user->userTable);
                redirect($url);
            }
        }

        //GET
        $filter = array(
            'keywords' => $this->get('keywords'),
            'page_num' => $this->get('page'),
            'page_size' => ADMIN_PAGESIZE
        );
        $res = $this->user->lists($filter);
        $this->pager->setParams($res['count'], $filter['page_size']);
        $page = $this->pager->getHtml();
        $data = array(
            'page' => $page,
            'rows' => $res['rows'],
            'url' => getURL(),
        );

        $this->views('index', array_merge($data, $filter));
    }
    
    /**
     * 用户详情
     * @return NULL
     */
    public function detail() {
        if ('detail' != $this->get('action')) {
            return;
        }
        $uid = (int)$this->get('uid');
        if (!$uid) {
            return;
        }
        $this->views('detail', array('row' => $this->user->getUser($uid)));        
    }
    
    
}
