<?php

//namespace to organize

namespace Query_src;

/**
 * Class Query Running Query Application
 * @author Bruno Ribeiro <bruno.espertinho@gmail.com>
 * @author Zachbor       <zachborboa@gmail.com>
 * 
 * @version 2
 * @access public
 * @package Run
 * @subpackage Where
 */
class Run extends Where {

    /**
     * Get SQL Code
     * 
     * @access public
     * @return \Query
     */
    protected function SQL() {
        if (!empty($this->select)) {
            self::calc_offset();
            return $this->run_select();
        }
        if (!empty($this->set) && !empty($this->update))
            return $this->run_update();

        if (!empty($this->set) && !empty($this->insert_into))
            return $this->run_insert();

        if (!empty($this->delete_from))
            return $this->run_delete();

        if (!empty($this->customSQL))
            return $this->customSQL;

        return false;
    }

    /**
     * get total records without limit and offset
     * 
     * @access public
     * @return int
     */
    public function get_num_rows() {
        $SQL = $this->run_select(TRUE);
        try {
            $query = $this->database->prepare($SQL);
            // close database connection
            $query->execute();
            // fetch query result
            return $query->rowCount();
        } catch (Exception $e) {
            $this->database = null;
            print $SQL;
            die("Query Error");
        }
    }

    private function calc_offset() {
        if (!empty($this->page) || !empty($this->offset)) {
            // get total records
            $this->perpage = $this->limit; // for get_perpage()
            $results = self::get_num_rows();
            // for pagination:
            $this->total = $results; // for get_total()  
            // calculate pages
            $this->pages = (int) ceil($results / $this->limit);
            // set offset
            if (!isset($this->offset)) {
                //print ($this->page * $this->limit) - $this->limit;
                $this->offset(($this->page * $this->limit) - $this->limit);
            } else {
                // calculate page using offset and perpage
                // determine on what page the offset would be on
                for ($page = 1; $page <= $this->pages; $page++) {
                    if ($this->offset - 1 < $page * $this->perpage) {
                        $this->page = $page;
                        break;
                    }
                }
            }
        }
    }

    private function run_update() {
        $select = '*';
        if (is_array($this->set)) {
            $_set = [];
            foreach ($this->set as $column => $value) {
                $v = is_null($value) ? 'NULL' : $this->safeValue($value);
                $_set[] = "{$this->safeColumn($column)} = {$v}";
            }
            $set = implode(", \n\t", $_set);
        } else {
            $set = (string) $this->set;
        }

        return <<<EOF
UPDATE `{$this->update}` SET {$set}
 {$this->get_inner_join()}
 {$this->get_left_join()}
 {$this->get_where()}
 {$this->get_limit()}
EOF;
    }

    private function run_delete() {
        return <<<EOF
DELETE FROM `{$this->delete_from}`
 {$this->get_inner_join()}
 {$this->get_left_join()}
 {$this->get_where()}
 {$this->get_limit()}
EOF;
    }

    private function run_insert() {
        $select = '*';
        $_columns = [];
        $_values = [];
        if (is_array($this->set)) {
            foreach ($this->set as $column => $value) {
                $_columns[] = $this->safeColumn($column);
                $_values[] = is_null($value) ? 'NULL' : $this->safeValue($value);
                #var_dump($value);
            }
        }
        $columns = implode(",\n\t", $_columns);
        $values = implode(",\n\t", $_values);
        return <<<EOF
INSERT INTO `{$this->insert_into}` ({$columns}) VALUES ({$values})
EOF;
    }

    private function run_select($num_rows = FALSE) {
        // Query is select()
        $select = '*';
        if (is_array($this->select)) {
            $select = implode(", \n\t", $this->select);
        } else {
            $select = (string) $this->select;
        }

        return <<<EOF
SELECT {$select}
FROM `{$this->from}` 
 {$this->get_inner_join()}
 {$this->get_left_join()}
 {$this->get_where()}
 {$this->get_group_by()}
 {$this->get_order_by()}
 {$this->get_limit($num_rows)}
EOF;
    }

    private function get_group_by() {
        $group_by = '';
        if (!empty($this->group_by)) {
            if (is_array($this->group_by)) {
                $newList = [];
                foreach ($this->group_by as $group) {
                    $parts = explode(" ", str_replace("`", '', $group));
                    if (!empty($parts[0]) && !empty($parts[1]))
                        $newList[] = "`{$parts[0]}`.{$parts[1]}";
                }
                $group_by = sprintf("GROUP BY %s", implode(",", $newList));
            } else {
                $parts = explode(".", str_replace("`", '', $this->group_by));
                if (!empty($parts[0]) && !empty($parts[1]))
                    $group_by = "GROUP BY {$parts[0]}.{$parts[1]}";
            }
        }
        return $group_by;
    }

    /**
     * Get order by SQL command 
     * 
     * @return string
     */
    private function get_order_by() {
        $order_by = '';
        if (!empty($this->order_by)) {
            if (is_array($this->order_by)) {
                $newList = [];
                foreach ($this->order_by as $order) {
                    $parts = explode(" ", $order);
                    if (!empty($parts[0]) && !empty($parts[1]))
                        $newList[] = "`{$parts[0]}` {$parts[1]}";
                }
                $order_by = sprintf("\n ORDER BY %s", implode(",", $newList));
            } else {
                if ($this->order_by == "RAND()") {
                    $order_by = "\n ORDER BY RAND()";
                } else {
                    $parts = explode(".", str_replace("`", '', $this->order_by));
                    if (!empty($parts[0]) && !empty($parts[1])) {
                        $order_by = "\n ORDER BY `{$parts[0]}`.{$parts[1]}";
                    } else {
                        $parts = explode(" ", str_replace("`", '', $this->order_by));
                        $order_by = "\n ORDER BY `{$parts[0]}` {$parts[1]}";
                    }
                }
            }
        }
        return $order_by;
    }

    /**
     *  Get Limit SQL command
     * 
     * @return string
     */
    private function get_limit($num_rows = FALSE) {
        $limit = '';
        if ($num_rows)
            return $limit;

        if (!empty($this->limit)) {
            if (!empty($this->page)) {
                $offset = empty($this->offset) ? 0 : $this->offset;
                $limit = "\n LIMIT {$offset},{$this->limit}";
            } else {
                $limit = "\n LIMIT {$this->limit}";
            }
        }
        return $limit;
    }

    /**
     *  Get left join SQL command
     * 
     * @return string
     */
    private function get_left_join() {
        $left_join = '';
        if (empty($this->left_join))
            return $left_join;

        if (is_array($this->left_join)) {
            $left_join = sprintf(" \n LEFT JOIN %s \n\t", implode("\n LEFT JOIN \n\t", $this->left_join));
        } else {
            $left_join = " \n LEFT JOIN \n\t {$this->left_join}";
        }

        return $left_join;
    }

    /**
     *  Get left join SQL command
     * 
     * @return string
     */
    private function get_inner_join() {
        $inner_join = '';
        if (empty($this->inner_join))
            return $inner_join;

        if (is_array($this->inner_join)) {
            $inner_join = sprintf("\n INNER JOIN \n\t %s", implode("\n INNER JOIN \n\t", $this->inner_join));
        } else {
            $inner_join = " \n INNER JOIN \n\t {$this->inner_join}";
        }

        return $inner_join;
    }

}
