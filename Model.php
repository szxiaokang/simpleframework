<?php

/**
 * @filename Model.php
 * @encoding UTF-8
 * @author 273030282@qq.com
 * @datetime 2016-4-21 11:23:58
 * @version 1.0
 * @Description
 * 数据库连接/SQL拼接
 * SQL语句仅拼接, 执行后获取数据的方法则是PDO原始方法
 */
class Model {

    public static $_instance = array();
    public $subdriver;
    public $char_set = 'utf8';
    public $dbcollat = 'utf8_general_ci';
    public $encrypt = FALSE;
    public $port = '';
    public $pconnect = FALSE;
    public $conn_id = FALSE;
    public $result_id = FALSE;
    public $db_debug = FALSE;
    public $benchmark = 0;
    public $query_count = 0;
    public $bind_marker = '?';
    public $save_queries = TRUE;
    public $queries = array();
    public $query_times = array();
    public $trans_enabled = TRUE;
    public $trans_strict = TRUE;
    protected $return_delete_sql = FALSE;
    protected $reset_delete_data = FALSE;
    protected $qb_select = array();
    protected $qb_distinct = FALSE;
    protected $qb_from = array();
    protected $qb_join = array();
    protected $qb_where = array();
    protected $qb_groupby = array();
    protected $qb_having = array();
    protected $qb_keys = array();
    protected $qb_limit = FALSE;
    protected $qb_offset = FALSE;
    protected $qb_orderby = array();
    protected $qb_set = array();
    protected $qb_aliased_tables = array();
    protected $qb_where_group_started = FALSE;
    protected $qb_where_group_count = 0;
    protected $qb_no_escape = array();
    protected $qb_found_rows = FALSE;
    protected $_trans_depth = 0;
    protected $_trans_status = TRUE;
    protected $_trans_failure = FALSE;
    protected $_protect_identifiers = TRUE;
    protected $_reserved_identifiers = array('*');
    protected $_escape_char = '`';
    protected $_like_escapeStr = " ESCAPE '%s' ";
    protected $_like_escape_chr = '!';
    protected $_random_keyword = array('RAND()', 'RAND(%d)');
    protected $_count_string = 'SELECT COUNT(*) AS ';

    /**
     * 传入config.php 配置的$db 数组中的key/连接数据库
     * @param String $key
     * @return null
     */
    function __construct($key = 'default') {
        if (empty($key)) {
            $key = 'default';
        }
        if (isset(self::$_instance[$key])) {
            $this->conn_id = self::$_instance[$key];
            return;
        }
        $conf = $GLOBALS['db'][$key];
        try {
            $this->conn_id = new PDO("mysql:dbname={$conf['database']};host={$conf['host']};charset=utf8", $conf['username'], $conf['password']);
            $this->conn_id->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error(500, 'connection database failed.');
        }
        self::$_instance[$key] = $this->conn_id;
    }

    public function query($sql, $binds = FALSE) {
        if ($sql === '') {
            return ($this->db_debug) ? $this->displayError('db_invalid_query') : FALSE;
        }

        if ($binds !== FALSE) {
            $sql = $this->compileBinds($sql, $binds);
        }
        if ($this->save_queries === TRUE) {
            $this->queries[] = $sql;
        }

        return $this->simpleQuery($sql);
    }

    public function protectIdentifiers($item, $prefix_single = FALSE, $protect_identifiers = NULL, $field_exists = TRUE) {
        if (!is_bool($protect_identifiers)) {
            $protect_identifiers = $this->_protect_identifiers;
        }

        if (is_array($item)) {
            $escaped_array = array();
            foreach ($item as $k => $v) {
                $escaped_array[$this->protectIdentifiers($k)] = $this->protectIdentifiers($v, $prefix_single, $protect_identifiers, $field_exists);
            }
            return $escaped_array;
        }

        if (strpos($item, '(') !== FALSE OR strpos($item, "'") !== FALSE) {
            return $item;
        }

        $item = preg_replace('/\s+/', ' ', $item);
        if ($offset = strripos($item, ' AS ')) {
            $alias = ($protect_identifiers) ? substr($item, $offset, 4) . $this->escapeIdentifiers(substr($item, $offset + 4)) : substr($item, $offset);
            $item = substr($item, 0, $offset);
        } elseif ($offset = strrpos($item, ' ')) {
            $alias = ($protect_identifiers) ? ' ' . $this->escapeIdentifiers(substr($item, $offset + 1)) : substr($item, $offset);
            $item = substr($item, 0, $offset);
        } else {
            $alias = '';
        }
        if (strpos($item, '.') !== FALSE) {
            $parts = explode('.', $item);
            if (in_array($parts[0], $this->qb_aliased_tables)) {
                if ($protect_identifiers === TRUE) {
                    foreach ($parts as $key => $val) {
                        if (!in_array($val, $this->_reserved_identifiers)) {
                            $parts[$key] = $this->escapeIdentifiers($val);
                        }
                    }
                    $item = implode('.', $parts);
                }

                return $item . $alias;
            }

            if ($protect_identifiers === TRUE) {
                $item = $this->escapeIdentifiers($item);
            }

            return $item . $alias;
        }
        if ($protect_identifiers === TRUE && !in_array($item, $this->_reserved_identifiers)) {
            $item = $this->escapeIdentifiers($item);
        }

        return $item . $alias;
    }

    public function compileBinds($sql, $binds) {
        if (empty($binds) OR empty($this->bind_marker) OR strpos($sql, $this->bind_marker) === FALSE) {
            return $sql;
        } elseif (!is_array($binds)) {
            $binds = array($binds);
            $bind_count = 1;
        } else {
            $binds = array_values($binds);
            $bind_count = count($binds);
        }
        $ml = strlen($this->bind_marker);

        if ($c = preg_match_all("/'[^']*'/i", $sql, $matches)) {
            $c = preg_match_all('/' . preg_quote($this->bind_marker, '/') . '/i', str_replace($matches[0], str_replace($this->bind_marker, str_repeat(' ', $ml), $matches[0]), $sql, $c), $matches, PREG_OFFSET_CAPTURE);
            if ($bind_count !== $c) {
                return $sql;
            }
        } elseif (($c = preg_match_all('/' . preg_quote($this->bind_marker, '/') . '/i', $sql, $matches, PREG_OFFSET_CAPTURE)) !== $bind_count) {
            return $sql;
        }

        do {
            $c--;
            $escaped_value = $this->escape($binds[$c]);
            if (is_array($escaped_value)) {
                $escaped_value = '(' . implode(',', $escaped_value) . ')';
            }
            $sql = substr_replace($sql, $escaped_value, $matches[0][$c][1], $ml);
        } while ($c !== 0);

        return $sql;
    }

    public function simpleQuery($sql) {
        $result = $this->conn_id->query($this->_prepQuery($sql));
        if (!$result) {
            $err = $this->conn_id->errorInfo();
            logMessages("SQL: " . str_replace("\n", "", $sql) . ", ERROR: {$err[2]}");
            $this->displayError('db_unknown_column');
        }
        return $result;
    }

    public function foundRows($flag = FALSE) {
        $this->qb_found_rows = $flag;
        return $this;
    }

    public function select($select = '*', $escape = NULL) {
        if (is_string($select)) {
            $select = explode(',', $select);
        }
        is_bool($escape) OR $escape = $this->_protect_identifiers;
        foreach ($select as $val) {
            $val = trim($val);
            if ($val !== '') {
                $this->qb_select[] = $val;
                $this->qb_no_escape[] = $escape;
            }
        }

        return $this;
    }

    public function distinct($val = TRUE) {
        $this->qb_distinct = is_bool($val) ? $val : TRUE;
        return $this;
    }

    public function from($from) {
        foreach ((array) $from as $val) {
            if (strpos($val, ',') !== FALSE) {
                foreach (explode(',', $val) as $v) {
                    $v = trim($v);
                    $this->_trackAliases($v);
                    $this->qb_from[] = $v = $this->protectIdentifiers($v, TRUE, NULL, FALSE);
                }
            } else {
                $val = trim($val);
                $this->_trackAliases($val);
                $this->qb_from[] = $val = $this->protectIdentifiers($val, TRUE, NULL, FALSE);
            }
        }

        return $this;
    }

    public function join($table, $cond, $type = '', $escape = NULL) {
        if ($type !== '') {
            $type = strtoupper(trim($type));

            if (!in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'), TRUE)) {
                $type = '';
            } else {
                $type .= ' ';
            }
        }
        $this->_trackAliases($table);
        is_bool($escape) OR $escape = $this->_protect_identifiers;
        if ($escape === TRUE && preg_match_all('/\sAND\s|\sOR\s/i', $cond, $m, PREG_OFFSET_CAPTURE)) {
            $newcond = '';
            $m[0][] = array('', strlen($cond));
            for ($i = 0, $c = count($m[0]), $s = 0; $i < $c; $s = $m[0][$i][1] + strlen($m[0][$i][0]), $i++) {
                $temp = substr($cond, $s, ($m[0][$i][1] - $s));
                $newcond .= preg_match("/([\[\]\w\.'-]+)(\s*[^\"\[`'\w]+\s*)(.+)/i", $temp, $match) ? $this->protectIdentifiers($match[1]) . $match[2] . $this->protectIdentifiers($match[3]) : $temp;
                $newcond .= $m[0][$i][0];
            }

            $cond = ' ON ' . $newcond;
        } elseif ($escape === TRUE && preg_match("/([\[\]\w\.'-]+)(\s*[^\"\[`'\w]+\s*)(.+)/i", $cond, $match)) {
            $cond = ' ON ' . $this->protectIdentifiers($match[1]) . $match[2] . $this->protectIdentifiers($match[3]);
        } elseif (!$this->_hasOperator($cond)) {
            $cond = ' USING (' . ($escape ? $this->escapeIdentifiers($cond) : $cond) . ')';
        } else {
            $cond = ' ON ' . $cond;
        }
        if ($escape === TRUE) {
            $table = $this->protectIdentifiers($table, TRUE, NULL, FALSE);
        }
        $this->qb_join[] = $join = $type . 'JOIN ' . $table . $cond;

        return $this;
    }

    public function where($key, $value = NULL, $escape = NULL) {
        return $this->_wh('qb_where', $key, $value, 'AND ', $escape);
    }

    public function orWhere($key, $value = NULL, $escape = NULL) {
        return $this->_wh('qb_where', $key, $value, 'OR ', $escape);
    }

    public function whereIn($key = NULL, $values = NULL, $escape = NULL) {
        return $this->_whereIn($key, $values, FALSE, 'AND ', $escape);
    }

    public function orWhereIn($key = NULL, $values = NULL, $escape = NULL) {
        return $this->_whereIn($key, $values, FALSE, 'OR ', $escape);
    }

    public function whereNotIn($key = NULL, $values = NULL, $escape = NULL) {
        return $this->_whereIn($key, $values, TRUE, 'AND ', $escape);
    }

    public function orWhereNotIn($key = NULL, $values = NULL, $escape = NULL) {
        return $this->_whereIn($key, $values, TRUE, 'OR ', $escape);
    }

    public function like($field, $match = '', $side = 'both', $escape = NULL) {
        return $this->_like($field, $match, 'AND ', $side, '', $escape);
    }

    public function notLike($field, $match = '', $side = 'both', $escape = NULL) {
        return $this->_like($field, $match, 'AND ', $side, 'NOT', $escape);
    }

    public function orLike($field, $match = '', $side = 'both', $escape = NULL) {
        return $this->_like($field, $match, 'OR ', $side, '', $escape);
    }

    public function groupBy($by, $escape = NULL) {
        is_bool($escape) OR $escape = $this->_protect_identifiers;

        if (is_string($by)) {
            $by = ($escape === TRUE) ? explode(',', $by) : array($by);
        }

        foreach ($by as $val) {
            $val = trim($val);
            if ($val !== '') {
                $val = array('field' => $val, 'escape' => $escape);
                $this->qb_groupby[] = $val;
            }
        }

        return $this;
    }

    public function having($key, $value = NULL, $escape = NULL) {
        return $this->_wh('qb_having', $key, $value, 'AND ', $escape);
    }

    public function orHaving($key, $value = NULL, $escape = NULL) {
        return $this->_wh('qb_having', $key, $value, 'OR ', $escape);
    }

    public function orderBy($orderby, $direction = '', $escape = NULL) {
        $direction = strtoupper(trim($direction));

        if ($direction === 'RANDOM') {
            $direction = '';


            $orderby = ctype_digit((string) $orderby) ? sprintf($this->_random_keyword[1], $orderby) : $this->_random_keyword[0];
        } elseif (empty($orderby)) {
            return $this;
        } elseif ($direction !== '') {
            $direction = in_array($direction, array('ASC', 'DESC'), TRUE) ? ' ' . $direction : '';
        }

        is_bool($escape) OR $escape = $this->_protect_identifiers;

        if ($escape === FALSE) {
            $qb_orderby[] = array('field' => $orderby, 'direction' => $direction, 'escape' => FALSE);
        } else {
            $qb_orderby = array();
            foreach (explode(',', $orderby) as $field) {
                $qb_orderby[] = ($direction === '' && preg_match('/\s+(ASC|DESC)$/i', rtrim($field), $match, PREG_OFFSET_CAPTURE)) ? array('field' => ltrim(substr($field, 0, $match[0][1])), 'direction' => ' ' . $match[1][0], 'escape' => TRUE) : array('field' => trim($field), 'direction' => $direction, 'escape' => TRUE);
            }
        }

        $this->qb_orderby = array_merge($this->qb_orderby, $qb_orderby);
        return $this;
    }

    public function limit($value, $offset = 0) {
        $this->qb_limit = (int) $value;
        $offset = (int) $offset;
        $offset = $this->qb_limit * ( $offset - 1);
        if ($offset > 0) {
            $this->qb_offset = (int) $offset;
        }
        return $this;
    }

    public function offset($offset) {
        empty($offset) OR $this->qb_offset = (int) $offset;
        return $this;
    }

    public function set($key, $value = '', $escape = NULL) {
        $key = $this->_objectToAarray($key);
        if (!is_array($key)) {
            $key = array($key => $value);
        }
        is_bool($escape) OR $escape = $this->_protect_identifiers;
        foreach ($key as $k => $v) {
            $this->qb_set[$this->protectIdentifiers($k, FALSE, $escape)] = ($escape) ? $this->escape($v) : $v;
        }

        return $this;
    }

    public function getCompiledSelect($table = '', $reset = TRUE) {
        if ($table !== '') {
            $this->_trackAliases($table);
            $this->from($table);
        }
        $select = $this->_compileSelect();
        if ($reset === TRUE) {
            $this->_resetSelect();
        }

        return $select;
    }

    public function get($table = '', $limit = NULL, $offset = NULL) {
        if ($table !== '') {
            $this->_trackAliases($table);
            $this->from($table);
        }

        if (!empty($limit)) {
            $this->limit($limit, $offset);
        }

        $result = $this->query($this->_compileSelect());
        $this->_resetSelect();
        return $result;
    }

    public function getRow($table = '', $limit = NULL, $offset = NULL) {
        if ($table !== '') {
            $this->_trackAliases($table);
            $this->from($table);
        }

        if (!empty($limit)) {
            $this->limit($limit, $offset);
        }

        $result = $this->simpleQuery($this->_compileSelect());
        $this->_resetSelect();
        return $result->fetch();
    }

    /**
     * 获取查询结果列表
     * @param string $table
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($table = '', $limit = NULL, $offset = NULL) {
        if ($table !== '') {
            $this->_trackAliases($table);
            $this->from($table);
        }

        if (!empty($limit)) {
            $this->limit($limit, $offset);
        }

        $result = $this->simpleQuery($this->_compileSelect());
        $this->_resetSelect();
        return $result->fetchAll();
    }

    public function getWhere($table = '', $where = NULL, $limit = NULL, $offset = NULL) {
        if ($table !== '') {
            $this->from($table);
        }

        if ($where !== NULL) {
            $this->where($where);
        }

        if (!empty($limit)) {
            $this->limit($limit, $offset);
        }

        $result = $this->query($this->_compileSelect());
        $this->_resetSelect();
        return $result;
    }

    public function insertBatch($table = '', $set = NULL, $escape = NULL) {
        if ($set !== NULL) {
            $this->setInsertBatch($set, '', $escape);
        }
        if (count($this->qb_set) === 0) {
            return ($this->db_debug) ? $this->displayError('db_must_use_set') : FALSE;
        }
        if ($table === '') {
            if (!isset($this->qb_from[0])) {
                return ($this->db_debug) ? $this->displayError('db_must_set_table') : FALSE;
            }
            $table = $this->qb_from[0];
        }
        $affected_rows = 0;
        for ($i = 0, $total = count($this->qb_set); $i < $total; $i += 100) {
            $query = $this->query($this->_insertBatch($this->protectIdentifiers($table, TRUE, $escape, FALSE), $this->qb_keys, array_slice($this->qb_set, $i, 100)));
            $affected_rows += $query->rowCount();
        }

        $this->_resetWrite();
        return $affected_rows;
    }

    public function setInsertBatch($key, $value = '', $escape = NULL) {
        $key = $this->_objectToArrayBatch($key);
        if (!is_array($key)) {
            $key = array($key => $value);
        }

        is_bool($escape) OR $escape = $this->_protect_identifiers;
        $keys = array_keys($this->_objectToAarray(current($key)));
        sort($keys);

        foreach ($key as $row) {
            $row = $this->_objectToAarray($row);
            if (count(array_diff($keys, array_keys($row))) > 0 OR count(array_diff(array_keys($row), $keys)) > 0) {
                $this->qb_set[] = array();
                return;
            }
            ksort($row);
            if ($escape !== FALSE) {
                $clean = array();
                foreach ($row as $value) {
                    $clean[] = $this->escape($value);
                }
                $row = $clean;
            }

            $this->qb_set[] = '(' . implode(',', $row) . ')';
        }

        foreach ($keys as $k) {
            $this->qb_keys[] = $this->protectIdentifiers($k, FALSE, $escape);
        }

        return $this;
    }

    public function getCompiledInsert($table = '', $reset = TRUE) {
        if ($this->_validateInsert($table) === FALSE) {
            return FALSE;
        }
        $sql = $this->_insert(
                $this->protectIdentifiers(
                        $this->qb_from[0], TRUE, NULL, FALSE
                ), array_keys($this->qb_set), array_values($this->qb_set)
        );

        if ($reset === TRUE) {
            $this->_resetWrite();
        }

        return $sql;
    }

    public function insert($table = '', $set = NULL, $escape = NULL, $return_last_id = FALSE) {
        if ($set !== NULL) {
            $this->set($set, '', $escape);
        }
        if ($this->_validateInsert($table) === FALSE) {
            return FALSE;
        }
        $sql = $this->_insert(
                $this->protectIdentifiers(
                        $this->qb_from[0], TRUE, $escape, FALSE
                ), array_keys($this->qb_set), array_values($this->qb_set)
        );

        $this->_resetWrite();
        if ($return_last_id) {
            $this->query($sql);
            return $this->conn_id->lastInsertId();
        }
        return $this->query($sql);
    }

    public function replace($table = '', $set = NULL) {
        if ($set !== NULL) {
            $this->set($set);
        }
        if (count($this->qb_set) === 0) {
            return ($this->db_debug) ? $this->displayError('db_must_use_set') : FALSE;
        }
        if ($table === '') {
            if (!isset($this->qb_from[0])) {
                return ($this->db_debug) ? $this->displayError('db_must_set_table') : FALSE;
            }
            $table = $this->qb_from[0];
        }
        $sql = $this->_replace($this->protectIdentifiers($table, TRUE, NULL, FALSE), array_keys($this->qb_set), array_values($this->qb_set));
        $this->_resetWrite();
        return $this->query($sql);
    }

    public function getCompiledUpdate($table = '', $reset = TRUE) {
        if ($this->_validateUpdate($table) === FALSE) {
            return FALSE;
        }
        $sql = $this->_update($this->protectIdentifiers($this->qb_from[0], TRUE, NULL, FALSE), $this->qb_set);
        if ($reset === TRUE) {
            $this->_resetWrite();
        }

        return $sql;
    }

    public function update($table = '', $set = NULL, $where = NULL, $limit = NULL) {
        if ($set !== NULL) {
            $this->set($set);
        }

        if ($this->_validateUpdate($table) === FALSE) {
            return FALSE;
        }

        if ($where !== NULL) {
            $this->where($where);
        }

        if (!empty($limit)) {
            $this->limit($limit);
        }

        $sql = $this->_update($this->protectIdentifiers($this->qb_from[0], TRUE, NULL, FALSE), $this->qb_set);
        $this->_resetWrite();
        return $this->query($sql);
    }

    protected function _update($table, $values) {
        foreach ($values as $key => $val) {
            $valstr[] = $key . ' = ' . $val;
        }

        return 'UPDATE ' . $table . ' SET ' . implode(', ', $valstr)
                . $this->_compileWh('qb_where')
                . $this->_compileOrderBy()
                . ($this->qb_limit ? ' LIMIT ' . $this->qb_limit : '');
    }

    public function updateBatch($table = '', $set = NULL, $index = NULL) {



        if ($index === NULL) {
            return ($this->db_debug) ? $this->displayError('db_must_use_index') : FALSE;
        }

        if ($set !== NULL) {
            $this->setUpdateBatch($set, $index);
        }

        if (count($this->qb_set) === 0) {
            return ($this->db_debug) ? $this->displayError('db_must_use_set') : FALSE;
        }

        if ($table === '') {
            if (!isset($this->qb_from[0])) {
                return ($this->db_debug) ? $this->displayError('db_must_set_table') : FALSE;
            }

            $table = $this->qb_from[0];
        }

        $affected_rows = 0;
        for ($i = 0, $total = count($this->qb_set); $i < $total; $i += 100) {
            $query = $this->query($this->_updateBatch($this->protectIdentifiers($table, TRUE, NULL, FALSE), array_slice($this->qb_set, $i, 100), $this->protectIdentifiers($index)));
            $affected_rows += $query->rowCount();
            $this->qb_where = array();
        }

        $this->_resetWrite();
        return $affected_rows;
    }

    public function setUpdateBatch($key, $index = '', $escape = NULL) {
        $key = $this->_objectToArrayBatch($key);
        is_bool($escape) OR $escape = $this->_protect_identifiers;
        foreach ($key as $k => $v) {
            $index_set = FALSE;
            $clean = array();
            foreach ($v as $k2 => $v2) {
                if ($k2 === $index) {
                    $index_set = TRUE;
                }

                $clean[$this->protectIdentifiers($k2, FALSE, $escape)] = ($escape === FALSE) ? $v2 : $this->escape($v2);
            }

            if ($index_set === FALSE) {
                return $this->displayError('db_batch_missing_index');
            }

            $this->qb_set[] = $clean;
        }

        return $this;
    }

    public function emptyTable($table = '') {
        if ($table === '') {
            if (!isset($this->qb_from[0])) {
                return ($this->db_debug) ? $this->displayError('db_must_set_table') : FALSE;
            }

            $table = $this->qb_from[0];
        } else {
            $table = $this->protectIdentifiers($table, TRUE, NULL, FALSE);
        }

        $sql = $this->_delete($table);
        $this->_resetWrite();
        return $this->query($sql);
    }

    public function truncate($table = '') {
        if ($table === '') {
            if (!isset($this->qb_from[0])) {
                return ($this->db_debug) ? $this->displayError('db_must_set_table') : FALSE;
            }

            $table = $this->qb_from[0];
        } else {
            $table = $this->protectIdentifiers($table, TRUE, NULL, FALSE);
        }

        $sql = $this->_truncate($table);
        $this->_resetWrite();
        return $this->query($sql);
    }

    public function getCompiledDelete($table = '', $reset = TRUE) {
        $this->return_delete_sql = TRUE;
        $sql = $this->delete($table, '', NULL, $reset);
        $this->return_delete_sql = FALSE;
        return $sql;
    }

    public function delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE) {

        if ($table === '') {
            if (!isset($this->qb_from[0])) {
                return ($this->db_debug) ? $this->displayError('db_must_set_table') : FALSE;
            }

            $table = $this->qb_from[0];
        } elseif (is_array($table)) {
            foreach ($table as $single_table) {
                $this->delete($single_table, $where, $limit, $reset_data);
            }
            return;
        } else {
            $table = $this->protectIdentifiers($table, TRUE, NULL, FALSE);
        }

        if ($where !== '') {
            $this->where($where);
        }

        if (!empty($limit)) {
            $this->limit($limit);
        }

        if (count($this->qb_where) === 0) {
            return ($this->db_debug) ? $this->displayError('db_del_must_use_where') : FALSE;
        }

        $sql = $this->_delete($table);
        if ($reset_data) {
            $this->_resetWrite();
        }

        return ($this->return_delete_sql === TRUE) ? $sql : $this->query($sql);
    }

    public function escapeIdentifiers($item) {
        if ($this->_escape_char === '' OR empty($item) OR in_array($item, $this->_reserved_identifiers)) {
            return $item;
        } elseif (is_array($item)) {
            foreach ($item as $key => $value) {
                $item[$key] = $this->escapeIdentifiers($value);
            }

            return $item;
        } elseif (ctype_digit($item) OR $item[0] === "'" OR ( $this->_escape_char !== '"' && $item[0] === '"') OR strpos($item, '(') !== FALSE) {
            return $item;
        }

        static $preg_ec = array();

        if (empty($preg_ec)) {
            if (is_array($this->_escape_char)) {
                $preg_ec = array(
                    preg_quote($this->_escape_char[0], '/'),
                    preg_quote($this->_escape_char[1], '/'),
                    $this->_escape_char[0],
                    $this->_escape_char[1]
                );
            } else {
                $preg_ec[0] = $preg_ec[1] = preg_quote($this->_escape_char, '/');
                $preg_ec[2] = $preg_ec[3] = $this->_escape_char;
            }
        }

        foreach ($this->_reserved_identifiers as $id) {
            if (strpos($item, '.' . $id) !== FALSE) {
                return preg_replace('/' . $preg_ec[0] . '?([^' . $preg_ec[1] . '\.]+)' . $preg_ec[1] . '?\./i', $preg_ec[2] . '$1' . $preg_ec[3] . '.', $item);
            }
        }

        return preg_replace('/' . $preg_ec[0] . '?([^' . $preg_ec[1] . '\.]+)' . $preg_ec[1] . '?(\.)?/i', $preg_ec[2] . '$1' . $preg_ec[3] . '$2', $item);
    }

    public function resetQuery() {
        $this->_resetSelect();
        $this->_resetWrite();
        return $this;
    }

    public function lastQuery() {
        return end($this->queries);
    }

    protected function _wh($qb_key, $key, $value = NULL, $type = 'AND ', $escape = NULL) {
        if (!is_array($key)) {
            $key = array($key => $value);
        }
        is_bool($escape) OR $escape = $this->_protect_identifiers;
        foreach ($key as $k => $v) {
            $prefix = (count($this->$qb_key) === 0) ? $this->_groupGetType('') : $this->_groupGetType($type);
            if ($v !== NULL) {
                if ($escape === TRUE) {
                    $v = ' ' . $this->escape($v);
                }
                if (!$this->_hasOperator($k)) {
                    $k .= ' = ';
                }
            } elseif (!$this->_hasOperator($k)) {
                $k .= ' IS NULL';
            } elseif (preg_match('/\s*(!?=|<>|IS(?:\s+NOT)?)\s*$/i', $k, $match, PREG_OFFSET_CAPTURE)) {
                $k = substr($k, 0, $match[0][1]) . ($match[1][0] === '=' ? ' IS NULL' : ' IS NOT NULL');
            }

            $this->{$qb_key}[] = array('condition' => $prefix . $k . $v, 'escape' => $escape);
        }

        return $this;
    }

    protected function _whereIn($key = NULL, $values = NULL, $not = FALSE, $type = 'AND ', $escape = NULL) {
        if ($key === NULL OR $values === NULL) {
            return $this;
        }
        if (!is_array($values)) {
            $values = array($values);
        }
        is_bool($escape) OR $escape = $this->_protect_identifiers;
        $not = ($not) ? ' NOT' : '';
        $where_in = array();
        foreach ($values as $value) {
            $where_in[] = $this->escape($value);
        }
        $prefix = (count($this->qb_where) === 0) ? $this->_groupGetType('') : $this->_groupGetType($type);
        $where_in = array(
            'condition' => $prefix . $key . $not . ' IN(' . implode(', ', $where_in) . ')',
            'escape' => $escape
        );
        $this->qb_where[] = $where_in;
        return $this;
    }

    protected function _like($field, $match = '', $type = 'AND ', $side = 'both', $not = '', $escape = NULL) {
        if (!is_array($field)) {
            $field = array($field => $match);
        }
        is_bool($escape) OR $escape = $this->_protect_identifiers;
        foreach ($field as $k => $v) {
            $prefix = (count($this->qb_where) === 0) ? $this->_groupGetType('') : $this->_groupGetType($type);
            $v = $this->escapeLikeStr($v);
            if ($side === 'none') {
                $like_statement = "{$prefix} {$k} {$not} LIKE '{$v}'";
            } elseif ($side === 'before') {
                $like_statement = "{$prefix} {$k} {$not} LIKE '%{$v}'";
            } elseif ($side === 'after') {
                $like_statement = "{$prefix} {$k} {$not} LIKE '{$v}%'";
            } else {
                $like_statement = "{$prefix} {$k} {$not} LIKE '%{$v}%'";
            }

            if ($this->_like_escapeStr !== '') {
                $like_statement .= sprintf($this->_like_escapeStr, $this->_like_escape_chr);
            }
            $this->qb_where[] = array('condition' => $like_statement, 'escape' => $escape);
        }

        return $this;
    }

    protected function _groupGetType($type) {
        if ($this->qb_where_group_started) {
            $type = '';
            $this->qb_where_group_started = FALSE;
        }

        return $type;
    }

    protected function _limit($sql) {
        return $sql . ' LIMIT ' . ($this->qb_offset ? $this->qb_offset . ', ' : '') . $this->qb_limit;
    }

    protected function _insertBatch($table, $keys, $values) {
        return 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES ' . implode(', ', $values);
    }

    protected function _validateInsert($table = '') {
        if (count($this->qb_set) === 0) {
            return ($this->db_debug) ? $this->displayError('db_must_use_set') : FALSE;
        }

        if ($table !== '') {
            $this->qb_from[0] = $table;
        } elseif (!isset($this->qb_from[0])) {
            return ($this->db_debug) ? $this->displayError('db_must_set_table') : FALSE;
        }

        return TRUE;
    }

    protected function _replace($table, $keys, $values) {
        return 'REPLACE INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
    }

    protected function _fromTables() {
        return implode(', ', $this->qb_from);
    }

    protected function _validateUpdate($table = '') {
        if (count($this->qb_set) === 0) {
            return ($this->db_debug) ? $this->displayError('db_must_use_set') : FALSE;
        }

        if ($table !== '') {
            $this->qb_from[0] = $table;
        } elseif (!isset($this->qb_from[0])) {
            return ($this->db_debug) ? $this->displayError('db_must_set_table') : FALSE;
        }

        return TRUE;
    }

    protected function _updateBatch($table, $values, $index) {
        $ids = array();
        foreach ($values as $key => $val) {
            $ids[] = $val[$index];

            foreach (array_keys($val) as $field) {
                if ($field !== $index) {
                    $final[$field][] = 'WHEN ' . $index . ' = ' . $val[$index] . ' THEN ' . $val[$field];
                }
            }
        }

        $cases = '';
        foreach ($final as $k => $v) {
            $cases .= $k . " = CASE \n"
                    . implode("\n", $v) . "\n"
                    . 'ELSE ' . $k . ' END, ';
        }

        $this->where($index . ' IN(' . implode(',', $ids) . ')', NULL, FALSE);

        return 'UPDATE ' . $table . ' SET ' . substr($cases, 0, -2) . $this->_compileWh('qb_where');
    }

    protected function _truncate($table) {
        return 'TRUNCATE ' . $table;
    }

    protected function _delete($table) {
        return 'DELETE FROM ' . $table . $this->_compileWh('qb_where')
                . ($this->qb_limit ? ' LIMIT ' . $this->qb_limit : '');
    }

    protected function _trackAliases($table) {

        if (is_array($table)) {
            foreach ($table as $t) {
                $this->_trackAliases($t);
            }
            return;
        }
        if (strpos($table, ',') !== FALSE) {
            return $this->_trackAliases(explode(',', $table));
        }

        if (strpos($table, ' ') !== FALSE) {
            $table = preg_replace('/\s+AS\s+/i', ' ', $table);
            $table = trim(strrchr($table, ' '));
            if (!in_array($table, $this->qb_aliased_tables)) {
                $this->qb_aliased_tables[] = $table;
            }
        }
    }

    protected function _compileSelect($select_override = FALSE) {

        if ($select_override !== FALSE) {
            $sql = $select_override;
        } else {
            $sql = (!$this->qb_distinct) ? 'SELECT ' : 'SELECT DISTINCT ';
            if ($this->qb_found_rows) {
                $sql .= "SQL_CALC_FOUND_ROWS ";
            }
            if (count($this->qb_select) === 0) {
                $sql .= '*';
            } else {
                foreach ($this->qb_select as $key => $val) {
                    $no_escape = isset($this->qb_no_escape[$key]) ? $this->qb_no_escape[$key] : NULL;
                    $this->qb_select[$key] = $this->protectIdentifiers($val, FALSE, $no_escape);
                }

                $sql .= implode(', ', $this->qb_select);
            }
        }


        if (count($this->qb_from) > 0) {
            $sql .= "\nFROM " . $this->_fromTables();
        }

        if (count($this->qb_join) > 0) {
            $sql .= "\n" . implode("\n", $this->qb_join);
        }

        $sql .= $this->_compileWh('qb_where')
                . $this->_compileGroupBy()
                . $this->_compileWh('qb_having')
                . $this->_compileOrderBy();

        if ($this->qb_limit) {
            return $this->_limit($sql . "\n");
        }

        return $sql;
    }

    protected function _compileWh($qb_key) {
        if (count($this->$qb_key) > 0) {
            for ($i = 0, $c = count($this->$qb_key); $i < $c; $i++) {
                if (is_string($this->{$qb_key}[$i])) {
                    continue;
                } elseif ($this->{$qb_key}[$i]['escape'] === FALSE) {
                    $this->{$qb_key}[$i] = $this->{$qb_key}[$i]['condition'];
                    continue;
                }

                $conditions = preg_split(
                        '/(\s*AND\s+|\s*OR\s+)/i', $this->{$qb_key}[$i]['condition'], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
                );

                for ($ci = 0, $cc = count($conditions); $ci < $cc; $ci++) {
                    if (($op = $this->_getOperator($conditions[$ci])) === FALSE OR !preg_match('/^(\(?)(.*)(' . preg_quote($op, '/') . ')\s*(.*(?<!\)))?(\)?)$/i', $conditions[$ci], $matches)) {
                        continue;
                    }
                    if (!empty($matches[4])) {
                        $this->_isLiteral($matches[4]) OR $matches[4] = $this->protectIdentifiers(trim($matches[4]));
                        $matches[4] = ' ' . $matches[4];
                    }

                    $conditions[$ci] = $matches[1] . $this->protectIdentifiers(trim($matches[2]))
                            . ' ' . trim($matches[3]) . $matches[4] . $matches[5];
                }

                $this->{$qb_key}[$i] = implode('', $conditions);
            }

            return ($qb_key === 'qb_having' ? "\nHAVING " : "\nWHERE ")
                    . implode("\n", $this->$qb_key);
        }

        return '';
    }

    protected function _compileGroupBy() {
        if (count($this->qb_groupby) > 0) {
            for ($i = 0, $c = count($this->qb_groupby); $i < $c; $i++) {
                if (is_string($this->qb_groupby[$i])) {
                    continue;
                }
                $this->qb_groupby[$i] = ($this->qb_groupby[$i]['escape'] === FALSE OR $this->_isLiteral($this->qb_groupby[$i]['field'])) ? $this->qb_groupby[$i]['field'] : $this->protectIdentifiers($this->qb_groupby[$i]['field']);
            }
            return "\nGROUP BY " . implode(', ', $this->qb_groupby);
        }
        return '';
    }

    protected function _compileOrderBy() {
        if (is_array($this->qb_orderby) && count($this->qb_orderby) > 0) {
            for ($i = 0, $c = count($this->qb_orderby); $i < $c; $i++) {
                if ($this->qb_orderby[$i]['escape'] !== FALSE && !$this->_isLiteral($this->qb_orderby[$i]['field'])) {
                    $this->qb_orderby[$i]['field'] = $this->protectIdentifiers($this->qb_orderby[$i]['field']);
                }

                $this->qb_orderby[$i] = $this->qb_orderby[$i]['field'] . $this->qb_orderby[$i]['direction'];
            }

            return $this->qb_orderby = "\nORDER BY " . implode(', ', $this->qb_orderby);
        } elseif (is_string($this->qb_orderby)) {
            return $this->qb_orderby;
        }

        return '';
    }

    protected function _objectToAarray($object) {
        if (!is_object($object)) {
            return $object;
        }

        $array = array();
        foreach (get_object_vars($object) as $key => $val) {

            if (!is_object($val) && !is_array($val) && $key !== '_parent_name') {
                $array[$key] = $val;
            }
        }

        return $array;
    }

    protected function _isLiteral($str) {
        $str = trim($str);

        if (empty($str) OR ctype_digit($str) OR (string) (float) $str === $str OR in_array(strtoupper($str), array('TRUE', 'FALSE'), TRUE)) {
            return TRUE;
        }

        static $_str;

        if (empty($_str)) {
            $_str = ($this->_escape_char !== '"') ? array('"', "'") : array("'");
        }

        return in_array($str[0], $_str, TRUE);
    }

    protected function _hasOperator($str) {
        return (bool) preg_match('/(<|>|!|=|\sIS NULL|\sIS NOT NULL|\sEXISTS|\sBETWEEN|\sLIKE|\sIN\s*\(|\s)/i', trim($str));
    }

    protected function _getOperator($str) {
        static $_operators;

        if (empty($_operators)) {
            $_les = ($this->_like_escapeStr !== '') ? '\s+' . preg_quote(trim(sprintf($this->_like_escapeStr, $this->_like_escape_chr)), '/') : '';
            $_operators = array(
                '\s*(?:<|>|!)?=\s*',
                '\s*<>?\s*',
                '\s*>\s*',
                '\s+IS NULL',
                '\s+IS NOT NULL',
                '\s+EXISTS\s*\([^\)]+\)',
                '\s+NOT EXISTS\s*\([^\)]+\)',
                '\s+BETWEEN\s+\S+\s+AND\s+\S+',
                '\s+IN\s*\([^\)]+\)',
                '\s+NOT IN\s*\([^\)]+\)',
                '\s+LIKE\s+\S+' . $_les,
                '\s+NOT LIKE\s+\S+' . $_les
            );
        }

        return preg_match('/' . implode('|', $_operators) . '/i', $str, $match) ? $match[0] : FALSE;
    }

    protected function _prepQuery($sql) {
        if (preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql)) {
            return trim($sql) . ' WHERE 1=1';
        }
        return $sql;
    }

    protected function _insert($table, $keys, $values) {
        return 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
    }

    public function escape($str) {
        if (is_array($str)) {
            $str = array_map(array(&$this, 'escape'), $str);
            return $str;
        } elseif (is_string($str) OR ( is_object($str) && method_exists($str, '__toString'))) {
            return "'" . $this->escapeStr($str) . "'";
        } elseif (is_bool($str)) {
            return ($str === FALSE) ? 0 : 1;
        } elseif ($str === NULL) {
            return 'NULL';
        }

        return $str;
    }


    public function escapeStr($str, $like = FALSE) {
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = $this->escapeStr($val, $like);
            }

            return $str;
        }

        $str = $this->_escapeStr($str);
        // escape LIKE condition wildcards
        if ($like === TRUE) {
            return str_replace(
                    array($this->_like_escape_chr, '%', '_'), array($this->_like_escape_chr . $this->_like_escape_chr, $this->_like_escape_chr . '%', $this->_like_escape_chr . '_'), $str
            );
        }

        return $str;
    }

    public function escapeLikeStr($str) {
        return $this->escapeStr($str, TRUE);
    }

    protected function _escapeStr($str) {
        return str_replace("'", "''", removeInvisibleCharacters($str));
    }

    protected function _resetRun($qb_reset_items) {
        foreach ($qb_reset_items as $item => $default_value) {
            $this->$item = $default_value;
        }
    }

    protected function _resetSelect() {
        $this->_resetRun(array(
            'qb_select' => array(),
            'qb_from' => array(),
            'qb_join' => array(),
            'qb_where' => array(),
            'qb_groupby' => array(),
            'qb_having' => array(),
            'qb_orderby' => array(),
            'qb_aliased_tables' => array(),
            'qb_no_escape' => array(),
            'qb_distinct' => FALSE,
            'qb_limit' => FALSE,
            'qb_offset' => FALSE
        ));
    }

    protected function _resetWrite() {
        $this->_resetRun(array(
            'qb_set' => array(),
            'qb_from' => array(),
            'qb_join' => array(),
            'qb_where' => array(),
            'qb_orderby' => array(),
            'qb_keys' => array(),
            'qb_limit' => FALSE
        ));
    }

    private function displayError($key) {
        $error = array(
            'db_invalid_connection_str' => 'Unable to determine the database settings based on the connection string you submitted.',
            'db_unable_to_connect' => 'Unable to connect to your database server using the provided settings.',
            'db_unable_to_select' => 'Unable to select the specified database: %s',
            'db_unable_to_create' => 'Unable to create the specified database: %s',
            'db_invalid_query' => 'The query you submitted is not valid.',
            'db_must_set_table' => 'You must set the database table to be used with your query.',
            'db_must_use_set' => 'You must use the "set" method to update an entry.',
            'db_must_use_index' => 'You must specify an index to match on for batch updates.',
            'db_batch_missing_index' => 'One or more rows submitted for batch updating is missing the specified index.',
            'db_must_use_where' => 'Updates are not allowed unless they contain a "where" clause.',
            'db_del_must_use_where' => 'Deletes are not allowed unless they contain a "where" or "like" clause.',
            'db_field_param_missing' => 'To fetch fields requires the name of the table as a parameter.',
            'db_unsupported_function' => 'This feature is not available for the database you are using.',
            'db_transaction_failure' => 'Transaction failure: Rollback performed.',
            'db_unable_to_drop' => 'Unable to drop the specified database.',
            'db_unsupported_feature' => 'Unsupported feature of the database platform you are using.',
            'db_unsupported_compression' => 'The file compression format you chose is not supported by your server.',
            'db_filepath_error' => 'Unable to write data to the file path you have submitted.',
            'db_table_name_required' => 'A table name is required for that operation.',
            'db_column_name_required' => 'A column name is required for that operation.',
            'db_column_definition_required' => 'A column definition is required for that operation.',
            'db_unable_to_set_charset' => 'Unable to set client connection character set: %s',
            'db_error_heading' => 'A Database Error Occurred',
            'db_unknown_column' => 'Unknown column in field list',
        );
        if (isset($error[$key])) {
            error(500, $error[$key]);
        }
        error(500, 'Unkown Error.');
    }

}
