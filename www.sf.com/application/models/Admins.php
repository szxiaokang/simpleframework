<?php

/**
 * @filename Admin.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-5-5 14:21:39
 * @version 1.0
 * @Description
 * 
 */
class Admins extends Model {

    public $adminTable = 'simpleframework.admin';

    /**
     * 管理员登录
     * @param array $data
     * @return array | NULL
     */
    public function login($data) {
        if (empty($data)) {
            return NULL;
        }
        $row = $this->select('adminid, username, email, menus, loginum')
                ->where('username', $data['username'])
                ->where('password', $data['password'])
                ->limit(1)
                ->getRow($this->adminTable);
        if (!empty($row)) {
            $bind = array('lastip' => getIp(), 'lastlogin' => time(), 'loginum' => $row['loginum'] + 1);
            $this->update($this->adminTable, $bind, array('adminid' => $row['adminid']));
        }
        return $row;
    }

    /**
     * 检测名称是否存在
     * @param string $username
     * @return boolean
     */
    public function check($username) {
        $row = $this->select('COUNT(1) num')
                ->where('username', $username)
                ->getRow($this->adminTable);
        return $row['num'] > 0;
    }

    /**
     * 统计管理员个数
     * @return int
     */
    public function count() {
        $row = $this->select('COUNT(1) num')->getRow($this->adminTable);
        return $row['num'];
    }

    /**
     * 管理员列表
     * @param array $data
     * @return array
     */
    public function lists($data) {
        $cols = 'adminid, username, email, lastlogin, addtime, lastip, loginum';
        $this->select($cols)->foundRows(TRUE);
        if (isset($data['keywords']) && !empty($data['keywords'])) {
            $this->like('username', $data['keywords'])->orLike('email', $data['keywords']);
        }
        $this->orderBy('adminid DESC');
        $rows = $this->getAll($this->adminTable, $data['page_size'], $data['page_num']);
        $row = $this->query('SELECT FOUND_ROWS() AS rows_num')->fetch();
        return array('rows' => $rows, 'count' => $row['rows_num']);
    }

}
