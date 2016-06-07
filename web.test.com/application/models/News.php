<?php

/**
 * @filename Mnews.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-4-27 10:43:37
 * @version 1.0
 * @Description
 * 
 */
class News extends Model {

    public $newsTable = 'simpleframework.news';
    public $typeTable = 'simpleframework.news_type';
    
    /**
     * 检测类型名称是否存在
     * @param string $name
     * @return boolean
     */
    public function typeCheck($name, $id = 0) {
        $this->select('COUNT(1) num')->where('name', $name);
        if ($id) {
            $this->where('id !=', $id);
        }
        $row = $this->getRow($this->typeTable);
        return $row['num'] > 0;
    }

    /**
     * 检查标题是否存在
     * @param string $title
     * @param int $id
     * @return boolean
     */
    public function check($title, $id = 0) {
        $this->select('COUNT(1) num')->where('title', $title);
        if ($id) {
            $this->where('id !=', $id);
        }
        $row = $this->getRow($this->newsTable);
        return $row['num'] > 0;
    }

   
    /**
     * 新闻分类列表 分页 关键字查询
     * @param array $data
     * @return array
     */
    public function typeList($data) {
        $cols = 'id, name, addtime, edittime';
        $this->select($cols)->foundRows(TRUE);
        if (isset($data['keywords']) && !empty($data['keywords'])) {
            $this->like('name', $data['keywords']);
        }
        $this->orderBy('id DESC');
        $query = $this->get($this->typeTable, $data['page_size'], $data['page_num']);
        $row = $this->query('SELECT FOUND_ROWS() AS rows_num')->fetch();
        return array('rows' => $query->fetchAll(), 'count' => $row['rows_num']);
    }

    /**
     * 新闻列表
     * @param array $data
     * @return array
     */
    public function lists($data) {
        $cols = 'id, title, type, addtime, edittime, images';
        $this->select($cols)->foundRows(TRUE);
        if (!empty($data['type'])) {
            $this->where('type', (int)$data['type']);
        }
        if (isset($data['keywords']) && !empty($data['keywords'])) {
            $this->like('title', $data['keywords']);
        }
        $this->orderBy('id DESC');
        $query = $this->get($this->newsTable, $data['page_size'], $data['page_num']);
        $row = $this->query('SELECT FOUND_ROWS() AS rows_num')->fetch();
        return array('rows' => $query->fetchAll(), 'count' => $row['rows_num']);
    }
    
    /**
     * 删除新闻分类/新闻
     * @param array $ids
     * @return boolean
     */
    public function deleteType($ids) {
        if (!count($ids)) {
            return FALSE;
        }
        $this->whereIn('id', $ids);
        $flag = $this->delete($this->typeTable);
        
        $this->whereIn('type', $ids);
        return $flag && $this->delete($this->newsTable);
    }

    /**
     * 获取分类信息
     * @param int $id
     * @return array
     */
    public function getType($id) {
        return $this->where('id', $id)->getRow($this->typeTable);
    }
    
    /**
     * 获取一行新闻
     * @param int $id
     * @return array
     */
    public function getNews($id) {
        return $this->where('id', $id)->getRow($this->newsTable);
    }

    /**
     * 获取分类列表
     */
    public function getTypeList() {
        $rows = $this->select('id, name')->getAll($this->typeTable);
        $data = array();
        foreach ($rows as $row) {
            $data[$row['id']] = $row['name'];
        }
        return $data;
    }

    /**
     * 统计新闻相关
     * 今日新闻新增条数/新闻总数/点击总数
     * 
     * @return array
     */
    public function count() {
        //新闻总数/点击总数
        $row = $this->select('COUNT(1) count_num, SUM(click) count_click')->getRow($this->newsTable);
        $tmp = $this->select('COUNT(1) add_num')->where('addtime >', strtotime(date('Y-m-d')))->getRow($this->newsTable);
        $row['add_num'] = $tmp['add_num'];
        return $row;
    }

    /**
     * 后台主页 最新新闻/最高点击量 前10新闻
     * @return array
     */
    public function getNewsList() {
        return array(
            'last_list' => $this->select('id, title')->orderBy('id', 'DESC')->limit(10)->getAll($this->newsTable),
            'click_list' => $this->select('id, title, click')->orderBy('click', 'DESC')->limit(10)->getAll($this->newsTable)
        );
    }
    
    /**
     * 返回最新的10条新闻
     * @param int $id 除此id外的新闻
     * @return array
     */
    public function getLastNews($type_id, $id = 0) {
        $this->select('id, title, images, addtime');
        if (!empty($type_id)) {
            $this->where('type', $type_id);
        }
        if ($id) {
            $this->where('id !=', $id);
        }
        return $this->orderBy('id', 'DESC')->limit(10)->getAll($this->newsTable);
    }

}
