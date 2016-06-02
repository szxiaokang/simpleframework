<?php

/**
 * @filename NewsController.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-6 16:40:07
 * @version 1.0
 * @Description
 * 
 */
class NewsController extends Controller {

    private $news;
    private $pager;

    public function __construct() {
        parent::__construct();
        $this->news = Loader::model('News');
        $this->pager = Loader::initClass('Pager');
    }

    /**
     * 新闻管理
     */
    public function lists() {
        // 删除
        if (isPost() && $this->post('action') == 'delete') {
            $ids = $this->post('ids');
            $url = decodeURL($this->post('url'));
            if (count($ids) && !empty($url)) {
                $this->news->whereIn('id', $ids)->delete($this->news->newsTable);
                redirect($url);
            }
        }

        $filter = array(
            'keywords' => $this->get('keywords'),
            'page_num' => $this->get('page'),
            'page_size' => ADMIN_PAGESIZE,
            'type' => $this->get('type'),
        );
        $res = $this->news->lists($filter);
        $this->pager->setParams($res['count'], $filter['page_size']);
        $data = array(
            'page' => $this->pager->getHtml(),
            'rows' => $res['rows'],
            'url' => getURL(),
            'type_list' => $this->news->getTypeList(),
        );
        
        $this->views('list', array_merge($data, $filter));
    }

    /**
     * 添加新闻,图片上传
     * @return null
     */
    public function add() {
        $upload = Loader::initClass('FileUpload');
        //GET
        if (!isPost()) {
            $action = $this->get('action');
            //文件管理
            if ('manage' == $action) {
                $upload->manage();
                return;
            }
            if ('edit' == $action && $id = (int) $this->get('id')) {
                $data = array(
                    'row' => $this->news->getNews($id),
                    'url' => decodeURL($this->get('url')),
                    'action' => 'edit'
                );
            }
            $data['type_list'] = $this->news->getTypeList();
            $this->views('news/add', $data);
        }

        $action = $this->post('action');
        //文件上传
        if ('upload' == $this->get('action')) {
            $upload->upload();
            return;
        }
        /* 检测标题是否存在 */
        if ('check' == $action) {
            $name = $this->post('name');
            $id = $this->post('id');
            if ($this->news->check($name, $id)) {
                Constant::adminMessage(Constant::EXISTS);
            }
            Constant::adminMessage(Constant::SUCCESS);
        }
        $data = array(
            'title' => $this->post('title'),
            'intro' => $this->post('sub_title'),
            'type' => $this->post('type'),
            'images' => $this->post('images'),
            'content' => $this->post('content'),
        );
        //添加
        if ('add' == $action) {
            $data['addtime'] = time();
            if ($this->news->insert($this->news->newsTable, $data)) {
                Constant::adminMessage(Constant::SUCCESS);
            }
            Constant::adminMessage(Constant::ADD_FAIL);
        }
        //修改
        if ('edit' == $action) {
            $data['edittime'] = time();
            $id = (int) $this->post('id');
            $this->news->update($this->news->newsTable, $data, "id={$id}");
            Constant::adminMessage(Constant::SUCCESS);
        }
    }

    /**
     * 添加类型
     */
    public function addType() {
        if (!isPost()) {
            $action = $this->get('action');
            if ('edit' == $action && $id = (int) $this->get('id')) {
                $data = array(
                    'row' => $this->news->getType($id),
                    'url' => decodeURL($this->get('url')),
                    'action' => 'edit'
                );
            }
            $this->views('news/add_type', isset($data) ? $data : NULL);
            return;
        }

        $action = $this->post('action');

        // 检测名称是否存在
        if ('check' == $action) {
            $name = $this->post('name');
            $id = $this->post('id');
            if ($this->news->typeCheck($name, $id)) {
                Constant::adminMessage(Constant::EXISTS);
            }
            Constant::adminMessage(Constant::SUCCESS);
        }

        // 管理员添加、修改 
        if ('add' == $action || 'edit' == $action) {
            $data = array('name' => $this->post('name'));
            if ('add' == $action) {
                $data['addtime'] = time();
                Constant::adminMessage($this->news->insert($this->news->typeTable, $data) ? Constant::SUCCESS : Constant::ADD_FAIL);
            } elseif ('edit' == $action) {
                $data['edittime'] = time();
                $id = (int) $this->post('id');
                $this->news->update($this->news->typeTable, $data, array('id' => $id));
                Constant::adminMessage(Constant::SUCCESS);
            }
        }
    }

    /**
     * 类型列表
     */
    public function listType() {
        // POST方式 删除
        if (isPost() && $this->post('action') == 'delete') {
            $ids = $this->post('ids');
            $url = decodeURL($this->post('url'));
            if (count($ids) && !empty($url)) {
                $this->news->whereIn('id', $ids)->delete($this->news->typeTable);
                $this->news->whereIn('type', $ids)->delete($this->news->newsTable);
                redirect($url);
            }
        }

        //GET
        $filter = array(
            'keywords' => $this->get('keywords'),
            'page_num' => $this->get('page'),
            'page_size' => ADMIN_PAGESIZE
        );
        $res = $this->news->typeList($filter);
        $this->pager->setParams($res['count'], $filter['page_size']);
        $page = $this->pager->getHtml();
        $data = array(
            'page' => $page,
            'rows' => $res['rows'],
            'url' => getURL(),
        );

        $this->views('news/list_type', array_merge($data, $filter));
    }

}
