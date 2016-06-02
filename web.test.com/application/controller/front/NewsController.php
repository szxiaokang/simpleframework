<?php

/**
 * @filename NewsController.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-27 11:14:30
 * @version 1.0
 * @Description
 * 
 */
class NewsController extends Controller {

    private $news;
    private $pager;

    function __construct() {
        parent::__construct();
        $this->news = Loader::model('News');
        $this->pager = Loader::initClass('Pager');
    }

    /**
     * 新闻首页及搜索
     */
    public function index() {
        $filter = array(
            'keywords' => $this->get('keywords'),
            'type' => $this->get('type'),
            'page_size' => FRONT_PAGESIZE,
            'page_num' => $this->get('page_num'),
        );
        $data = $this->news->lists($filter);
        $this->pager->setParams($data['count'], FRONT_PAGESIZE);
        $data['keywords'] = $filter['keywords'];
        $data['page'] = $this->pager->getHtml(2);
        $data['types'] = $this->news->getTypeList();
        $data['type'] = $this->get('type');
        $data['pageTitle'] = '新闻';

        $this->views('index', $data);
    }

    /**
     * 新闻详情
     */
    public function detail() {
        $id = (int) $this->get('id');
        if (!$id) {
            return;
        }
        $data = array('row' => $this->news->getNews($id));
        $data['rows'] = $this->news->getLastNews($data['row']['type'], $id);
        $this->news->update($this->news->newsTable, array('click' => $data['row']['click'] + 1), 'id = ' . $id);
        $this->views('detail', $data);
    }

}
