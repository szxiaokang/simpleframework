<?php

/**
 * @filename Users.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-27 15:55:42
 * @version 1.0
 * @Description
 * 用户相关操作
 */
Class Users extends Model {

    public $userTable = 'simpleframework.users';

    function __construct() {
        parent::__construct();
    }

    /**
     * 获取最新的注册用户
     * @return array
     */
    public function getLastUser() {
        return $this->select('email, addtime, avatar, sex')->orderBy('uid', 'DESC')->limit(6)->getAll($this->userTable);
    }

    /**
     * 用户列表及分页
     * @param array $data 查询条件
     * @return array
     */
    public function lists($data) {
        $cols = 'uid, email, addtime, avatar, sex, last_login, login_num';
        $this->select($cols)->foundRows(TRUE);
        if (isset($data['keywords']) && !empty($data['keywords'])) {
            $this->like('email', $data['keywords']);
        }
        $this->orderBy('uid DESC');
        $query = $this->get($this->userTable, $data['page_size'], $data['page_num']);
        $row = $this->query('SELECT FOUND_ROWS() AS rows_num')->fetch();
        return array('rows' => $query->fetchAll(), 'count' => $row['rows_num']);
    }

    /**
     * 检测email是否存在
     * @param string $email
     * @return int 大于1则存在
     */
    public function checkEmail($email) {
        $row = $this->select('COUNT(1) num')->where('email', $email)->getRow($this->userTable);
        return $row['num'];
    }

    /**
     * 用户登录
     * @param array $data
     * @return array
     */
    public function login($data) {
        $row = $this->select('uid, email, addtime, sex, login_num')
                ->where('email', $data['email'])
                ->where('password', $data['password'])
                ->limit(1)
                ->getRow($this->userTable);
        if ($row) {
               $bind = array(
                   'last_login' => time(),
                   'last_ip' => getIP(),
                   'login_num' => $row['login_num'] + 1
               );
               $this->update($this->userTable, $bind, 'uid=' . $row['uid']);
        }
        return $row;
    }
    
    /**
     * 根据uid获取用户信息
     * @param int $uid
     * @return array
     */
    public function getUser($uid) {
        return $this->where('uid', $uid)->getRow($this->userTable);
    }
    
    /**
     * 统计用户数量
     * @return int
     */
    public function count() {
        $row = $this->select('COUNT(1) num')->getRow($this->userTable);
        return $row['num'];
    }
    
    /**
     * 获取最新的10个用户
     * @return array
     */
    public function getLastList() {
        return $this->select('uid, email, addtime')->orderBy('uid', 'DESC')->limit(10)->getAll($this->userTable);
    }

}
