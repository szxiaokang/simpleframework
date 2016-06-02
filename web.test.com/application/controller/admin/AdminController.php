<?php

/**
 * @filename AdminController.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-24 16:17:06
 * @version 1.0
 * @Description
 * 管理员相关操作
 */
class AdminController extends Controller {

    public $admins;
    private $pager;

    public function __construct() {
        parent::__construct();
        $this->admins = Loader::model('Admins');
        $this->pager = Loader::initClass('Pager');
    }

    /**
     * 管理员添加/编辑
     * @return NULL
     */
    public function add() {
        if (!isPost()) {
            $action = $this->get('action');
            if ('edit' == $action && $id = (int) $this->get('id')) {
                $data = array(
                    'row' => $this->admins->where('id', $id)->getRow($this->admins->adminTable),
                    'url' => decodeURL($this->get('url')),
                    'action' => 'edit'
                );
            }
            $this->views('add');
            return;
        }

        $action = $this->post('action');
        /* 检测管理员名称是否存在 ajax请求 */
        if ('check' == $action) {
            $username = $this->post('username');
            if ($this->admins->check($username)) {
                Constant::adminMessage(Constant::EXISTS);
            }
            Constant::adminMessage(Constant::SUCCESS);
        }
        /* 管理员添加、修改 */
        if ('add' == $action || 'edit' == $action) {
            $password = $this->post('password');
            $data = array(
                'username' => $this->post('username'),
                'password' => md5($password),
                'email' => $this->post('email'),
            );

            if ('add' == $action) {
                $data['addtime'] = time();
                Constant::adminMessage($this->admins->insert($this->admins->adminTable, $data) ? Constant::SUCCESS : Constant::ADD_FAIL);
            } elseif ('edit' == $action) {
                if ('######' == $password) {
                    unset($data['password']);
                }
                $data['edittime'] = time();
                unset($data['username']);
                $adminid = (int) $this->post('adminid');
                $this->admins->update($this->admins->adminTable, $data, "adminid={$adminid}");
                Constant::adminMessage(Constant::SUCCESS);
            }
        }
    }

    /**
     * 管理员列表/管理
     */
    public function lists() {
        // 删除
        if (isPost() && $this->post('action') == 'delete') {
            $ids = $this->post('ids');
            $url = decodeURL($this->post('url'));
            if (count($ids) && !empty($url)) {
                $this->admins->whereIn('adminid', $ids)->delete($this->admins->adminTable);
                redirect($url);
            }
        }
        
        $filter = array(
            'keywords' => $this->get('keywords'),
            'page_num' => $this->get('page'),
            'page_size' => ADMIN_PAGESIZE,
            'type' => $this->get('type'),
        );
        $res = $this->admins->lists($filter);
        $this->pager->setParams($res['count'], $filter['page_size']);
        $data = array(
            'page' => $this->pager->getHtml(),
            'rows' => $res['rows'],
            'url' => getURL(),
        );

        $this->views('lists', array_merge($data, $filter));
    }

}
