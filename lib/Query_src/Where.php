<?php

//namespace to organize

namespace Query_src;

/**
 * Class Query Where
 * @author Bruno Ribeiro <bruno.espertinho@gmail.com>
 * @author Zachbor       <zachborboa@gmail.com>
 * 
 * @version 2
 * @access public
 * @package Where
 * @subpackage Pagination
 */
class Where extends Pagination {

    /**
     * Get where SQL command 
     * 
     * @access protected
     * @return string
     */
    protected function get_where() {
        if (!empty($where = $this->get_where_equal()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_equal_or()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_not_equal()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_not_in()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_in()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_like_both()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_like_before()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_like_after()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_like_or()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_greater_than_or_equal_to()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_greater_than()))
            $wheres[] = $where;
        
        if (!empty($where = $this->get_where_less_than_or_equal_to()))
            $wheres[] = $where;
        
        if (!empty($where = $this->get_where_less_than()))
            $wheres[] = $where;

        if (!empty($where = $this->get_where_like_binary()))
            $wheres[] = $where;

        if (empty($wheres))
            return '';

        $command = sprintf("\nWHERE \n\t %s", implode(" AND \n\t", $wheres));
        return $command;
    }

    private function get_where_like_binary() {
        $where = [];
        if (!empty($this->where_like_binary)) {
            if (is_array($this->where_like_binary)) {
                foreach ($this->where_like_binary as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} LIKE BINARY '{$v}'";
                }
            } else {
                $where = $this->where_like_binary;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_like_or() {
        $where = [];
        if (!empty($this->where_like_or)) {
            if (is_array($this->where_like_or)) {
                foreach ($this->where_like_or as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} LIKE '%{$v}%'";
                }
            } else {
                $where = $this->where_like_or;
            }
        }
        return count($where) > 0 ? sprintf("(%s)", implode(" OR \n\t", $where)) : (!is_array($where) ? $where : '');
    }

    private function get_where_like_before() {
        $where = [];
        if (!empty($this->where_like_before)) {
            if (is_array($this->where_like_before)) {
                foreach ($this->where_like_before as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} LIKE '%{$v}'";
                }
            } else {
                $where = $this->where_like_before;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_like_after() {
        $where = [];
        if (!empty($this->where_like_after)) {
            if (is_array($this->where_like_after)) {
                foreach ($this->where_like_after as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} LIKE '{$v}%'";
                }
            } else {
                $where = $this->where_like_after;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_like_both() {
        $where = [];
        if (!empty($this->where_like_both)) {
            if (is_array($this->where_like_both)) {
                foreach ($this->where_like_both as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} LIKE '%{$v}%'";
                }
            } else {
                $where = $this->where_like_both;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_equal() {
        $where = [];
        if (!empty($this->where_equal_to)) {
            if (is_array($this->where_equal_to)) {
                foreach ($this->where_equal_to as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = is_null($v) ? "{$kk} IS NULL" : "{$kk} = {$this->safeValue($v)}";
                }
            } else {
                $where = $this->where_equal_to;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_less_than_or_equal_to() {
        $where = [];
        if (!empty($this->where_less_than_or_equal_to)) {
            if (is_array($this->where_less_than_or_equal_to)) {
                foreach ($this->where_less_than_or_equal_to as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} <= {$this->safeValue($v)}";
                }
            } else {
                $where = $this->where_less_than_or_equal_to;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_greater_than_or_equal_to() {
        $where = [];
        if (!empty($this->where_greater_than_or_equal_to)) {
            if (is_array($this->where_greater_than_or_equal_to)) {
                foreach ($this->where_greater_than_or_equal_to as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} >= {$this->safeValue($v)}";
                }
            } else {
                $where = $this->where_greater_than_or_equal_to;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_greater_than() {
        $where = [];
        if (!empty($this->where_greater_than)) {
            if (is_array($this->where_greater_than)) {
                foreach ($this->where_greater_than as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} > {$this->safeValue($v)}";
                }
            } else {
                $where = $this->where_greater_than;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_less_than() {
        $where = [];
        if (!empty($this->where_less_than)) {
            if (is_array($this->where_less_than)) {
                foreach ($this->where_less_than as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} < {$this->safeValue($v)}";
                }
            } else {
                $where = $this->where_less_than;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_not_equal() {
        $where = [];
        if (!empty($this->where_not_equal_to)) {
            if (is_array($this->where_not_equal_to)) {
                foreach ($this->where_not_equal_to as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = is_null($v) ? "{$kk} IS NOT NULL" : "{$kk} != {$this->safeValue($v)}";
                }
            } else {
                $where = $this->where_not_equal_to;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_equal_or() {
        $where = [];
        if (!empty($this->where_equal_or)) {
            if (is_array($this->where_equal_or)) {
                foreach ($this->where_equal_or as $k => $v) {
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = is_null($v) ? "{$kk} IS NULL" : "{$kk} = {$this->safeValue($v)}";
                }
            } else {
                $where = $this->where_equal_or;
            }
        }
        return count($where) > 0 ? sprintf("(%s)", implode(" OR \n\t", $where)) : (!is_array($where) ? $where : '');
    }

    private function get_where_not_in() {
        $where = [];
        if (!empty($this->where_not_in)) {
            if (is_array($this->where_not_in)) {
                foreach ($this->where_not_in as $k => $v) {
                    $vv = [];
                    if (is_array($v)) {
                        foreach ($v as $value) {
                            $vv[] = $this->safeValue($value);
                        }
                    } else {
                        $vv[] = $v;
                    }
                    $final = implode(",", $vv);
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} NOT IN ({$final})";
                }
            } else {
                $where = $this->where_not_in;
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    private function get_where_in() {
        $where = [];
        if (!empty($this->where_in)) {
            if (is_array($this->where_in)) {
                foreach ($this->where_in as $k => $v) {
                    $vv = [];
                    if (is_array($v)) {
                        foreach ($v as $value) {
                            $vv[] = $this->safeValue($value);
                        }
                    } else {
                        $vv[] = $v;
                    }
                    $final = implode(",", $vv);
                    $kk = preg_match("/\./", $k) ? $k : "`{$k}`";
                    $where[] = "{$kk} IN ({$final})";
                }
            }
        }
        return count($where) > 0 ? implode(" AND \n\t", $where) : (!is_array($where) ? $where : '');
    }

    /**
     * Safe value for SQL 
     * 
     * @param mixed $v
     * @return mixed
     */
    protected function safeValue($v) {
        if (is_bool($v))
            return (int) $v;

        if (is_numeric($v))
            return str_replace(",", ".", $v);

        if (is_string(strval($v)))
            return sprintf("'%s'", str_replace("'", "\'", $v));

        return $v;
    }

    /**
     * Safe value for SQL 
     * 
     * @param mixed $v
     * @return mixed
     */
    protected function safeColumn($value) {
        $c = str_replace("`", '', $value);
        return preg_match("/\./", $c) ? $c : "`{$c}`";
    }

}
