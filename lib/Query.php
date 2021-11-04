<?php

// Use Config class query
use Query_src\Config as Config;

/**
 * Class query
 * This class works with mysqli onlys, anti-SQL injection techniques were added.
 
 * @author Bruno Ribeiro <bruno.espertinho@gmail.com>
 * @author Zachbor       <zachborboa@gmail.com>
 * 
 * @version 3
 * @access public
 * @package Config
 * @todo Finish the functions : SUM, DISTINCT, and commands of tools to database.
 * */
class Query extends Config {

    private $inserted_id = NULL;
    private $results = [];
    private $SQL = '';

    /**
     * 
     * Method Magic
     * 
     * @access public
     * @param string $database Database ID see : config.php
     * @return void
     * 
     */
    public function __construct($database = NULL) {
        parent::__construct($database); // conect database
        $this->debug = defined('DEBUG') && DEBUG === true;
        $this->having = '';
    }

    public function select($select = '*') {
        $this->select = $select;
        return $this;
    }

    public function update($table, $set = array()) {
        $this->update = $table;
        $this->set = $set;
        return $this;
    }

    public function insert_into($table, $set) {
        $this->insert_into = $table;
        $this->set = $set;
        return $this;
    }

    public function customSQL($sql) {
        $this->customSQL = $sql;
        return $this;
    }

    public function insert($table, $set) {
        self::insert_into($table, $set);
        return $this;
    }

    /**
     * alias for get_inserted_id()
     * 
     * @access public
     * @return Integer
     */
    public function get_insert_id() {
        return self::get_inserted_id();
    }

    /**
     * alias for get_inserted_id()
     * 
     * @access public
     * @return Integer
     */
    public function get_inserted() {
        return self::get_inserted_id();
    }

    public function get_inserted_id() {
        return $this->inserted_id;
    }

    public function from($from = '*') {
        $this->from = $from;
        return $this;
    }

    /**
     * alias delete_from() 
     * 
     * @access public
     * @param string $table Used to define table name
     * @return \Query
     */
    public function delete($table) {
        return self::delete_from($table);
    }

    /**
     * Delete table
     * 
     * @access public
     * @param string $table Used to define table name
     * @return \Query
     */
    public function delete_from($table) {
        $this->delete_from = $table;
        return $this;
    }

    public function limit($limit = 1) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * alias instead of using both limit() && offset()
     * 
     * @access public
     * @param Integer $limit Limit page
     * @param Integer $offset Limit to show
     * @return \Query
     */
    public function range($limit, $offset) {
        self::limit($limit);
        self::offset($offset);
        return $this;
    }

    /**
     * Function Query offset
     * to use also requires using the limit
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * <b>LIMIT
     *           1</b>, 10
     * </code>
     * </pre>
     * 
     * @access public
     * @param integer $offset Used to get starting at offset, number of items to get
     * @return \Query
     */
    public function offset($offset) {
        $this->offset = (int) $offset;
        return $this;
    }

    public function inner_join($inner_join = []) {
        $this->inner_join = $inner_join;
        return $this;
    }

    public function left_join($left_join = []) {
        $this->left_join = $left_join;
        return $this;
    }

    public function group_by($group_by) {
        $this->group_by = $group_by;
        return $this;
    }

    /**
     * Function set page of query result
     * 
     * @access public
     * @param integer $page
     * @return \Query
     */
    public function page($page) {
        $this->page = (int) $page;
        return $this;
    }

    /**
     * Function Query order by
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * <b>ORDER BY</b>
     *           `Value ASC`
     * </code>
     * </pre>
     * 
     * @access public
     * @param String $order_by Used 'value ASC' or DESC.
     * @return \Query
     */
    public function order_by($order_by) {
        $this->order_by = $order_by;
        return $this;
    }

    /**
     * Function Query order RAND
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * <b>ORDER RAND()</b>
     * </code>
     * </pre>
     * 
     * @access public
     * @return \Query
     */
    public function order_rand() {
        $this->order_by = "RAND()";
        return $this;
    }

    /**
     * Execute Query
     * 
     * @access public
     * @return \Query
     */
    public function run() {
        $this->SQL = $this->SQL();
        try {
            $query = $this->database->prepare($this->SQL);
            $database = null;
            // close database connection
            $query->execute();
            // fetch query result
            if (!empty($this->select))
                $this->results = $query->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($this->insert_into))
                $this->inserted_id = $this->database->lastInsertId();

            $this->database = null;
        } catch (Exception $e) {
            $this->database = null;
            print $SQL;
            die("Query Error");
        }
    }

    public function get_selected() {
        return !empty($this->limit) && $this->limit === 1 ? (empty($this->results[0]) ? [] : $this->results[0]) : $this->results;
    }

    public function get_selected_count() {
        return count($this->results);
    }

    /**
     * Function Query where between
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `columnA` <b> BETWEEN </b> `min` AND `max`
     * </code>
     * </pre>
     * @param Array $where_between Used to compare strings, the second element have another array indicating the minimum and maximum.
     * @access public
     * @return \Query_src\Where
     */
    public function where_between($where_between) {
        $this->where_between = $where_between;
        return $this;
    }

    /**
     * Function Query where between
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `value` <b> BETWEEN </b> `columnA` AND `columnB`
     * </code>
     * </pre>
     * @param Array $where_between Used to compare strings, the second element have another array indicating the minimum and maximum.
     * @access public
     * @return \Query_src\Where
     */
    public function where_between_columns($where_between) {
        $this->where_between_columns = $where_between;
        return $this;
    }

    /**
     * Function Query where between or
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `value` <b> BETWEEN </b> `columnA` AND `columnB` OR
     *           `value` <b> BETWEEN </b> `columnA` AND `columnB`
     * </code>
     * </pre>
     * @param Array $where_between Used to compare strings, the second element have another array indicating the minimum and maximum.
     * @access public
     * @return \Query_src\Where
     */
    public function where_between_columns_or($where_between) {
        $this->where_between_columns_or = $where_between;
        return $this;
    }

    /**
     * Function Query where equal
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `columnA` <b> = </b> `valueA`
     * </code>
     * </pre>
     * @param Array $where_equal Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_equal($where_equal) {
        // alias for where_equal_to()
        return self::where_equal_to($where_equal);
    }

    /**
     * Function Query where equal or
     * 
     * Note: This function is diferrent of where_equal_or()
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           <b>(</b>`columnA` <b> = </b> `valueA` AND
     *           `columnB` <b> = </b> `valueB` <b>)</b> <b>OR</b>
     *           <b>(</b>`columnC` <b> = </b> `valueC` AND
     *           `columnD` <b> = </b> `valueD` <b>)</b>
     * </code>
     * </pre>
     * @param array $equal Collection array data in column name and value
     * @param array $orEqual Collection array data in column name and value to compare or
     * @access public
     * @return \Query_src\Where_src\Where
     */
    public function where_equal_to_and_or(array $equal, array $orEqual) {
        $this->where_equal_to_and_or = array($equal, $orEqual);
        return $this;
    }

    /**
     * Function Query where equal or
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *        (
     *           `columnA` <b> = </b> `valueA` OR
     *           `columnB` <b> = </b> `valueB`
     *        )
     * </code>
     * </pre>
     * @param Array $where_equal_or Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_equal_or($where_equal_or) {
        $this->where_equal_or = $where_equal_or;
        return $this;
    }

    /**
     * Function Query custom where
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *        (
     *           <b>`column` = `value`</b>
     *        )
     * </code>
     * </pre>
     * @param string $where custom criteria where
     * @access public
     * @return \Query_src\Where
     */
    public function where($where) {
        $this->where = $where;
        return $this;
    }

    /**
     * Function Query where equal to
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> = </b> `value`
     * </code>
     * </pre>
     * @param Array $where_equal_to Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_equal_to($where_equal_to) {
        $this->where_equal_to = $where_equal_to;
        return $this;
    }

    /**
     * Function Query where greater than
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> > </b> `value`
     * </code>
     * </pre>
     * @param Array $where_greater_than Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_greater_than($where_greater_than) {
        $this->where_greater_than = $where_greater_than;
        return $this;
    }

    /**
     * Function Query where greater than or equal to
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> >= </b> `value`
     * </code>
     * </pre>
     * @param Array $where_greater_than_or_equal_to Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_greater_than_or_equal_to($where_greater_than_or_equal_to) {
        $this->where_greater_than_or_equal_to = $where_greater_than_or_equal_to;
        return $this;
    }

    /**
     * Function Query where in
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> IN</b>(value)
     * </code>
     * </pre>
     * @param Array $where_in Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_in($where_in) {
        $this->where_in = $where_in;
        return $this;
    }

    /**
     * Function Query where less than
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> < </b> `value`
     * </code>
     * </pre>
     * @param Array $where_less_than Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_less_than($where_less_than) {
        $this->where_less_than = $where_less_than;
        return $this;
    }

    /**
     * Function Query where less than or equal to
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> <= </b> `value`
     * </code>
     * </pre>
     * @param Array $where_less_than_or_equal_to Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_less_than_or_equal_to($where_less_than_or_equal_to) {
        $this->where_less_than_or_equal_to = $where_less_than_or_equal_to;
        return $this;
    }

    /**
     * Function Query where like
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> LIKE </b> `%value%`
     * </code>
     * </pre>
     * @param Array $where_like Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_like($where_like) {
        return self::where_like_both($where_like);
    }

    /**
     * Function Query where like after
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> LIKE </b> `value%`
     * </code>
     * </pre>
     * @param Array $where_like_after Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_like_after($where_like_after) {
        $this->where_like_after = $where_like_after;
        return $this;
    }

    /**
     * Function Query where like before
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> LIKE </b> `%value`
     * </code>
     * </pre>
     * @param Array $where_like_before Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_like_before($where_like_before) {
        $this->where_like_before = $where_like_before;
        return $this;
    }

    /**
     * Function Query where like both
     *
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `columnA` <b> LIKE </b> "%valueA%" AND
     *           `columnB` <b> LIKE </b> "%valueB%"
     * </code>
     * </pre>
     * @param Array $where_like_both Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_like_both($where_like_both) {
        $this->where_like_both = $where_like_both;
        return $this;
    }

    /**
     * Function Query where like binary
     *
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> LIKE BINARY </b> `value`
     * </code>
     * </pre>
     * @param Array $where_like_binary Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_like_binary($where_like_binary) {
        $this->where_like_binary = $where_like_binary;
        return $this;
    }

    /**
     * Function Query where like or
     *
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     * 
     *  (
     *           `column` <b> LIKE </b> `%value%` OR
     *           `id` <b> LIKE  </b> "%1%"
     *  )
     * </code>
     * </pre>
     * @param Array $where_like_or Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_like_or($where_like_or) {
        $this->where_like_or = $where_like_or;
        return $this;
    }

    /**
     * Function Query where <> Not equal to
     *
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     * 
     *  (
     *           `column` <b> <> </b> `%value%` OR
     *           `id` <b> <> </b> "%1%"
     *  )
     * </code>
     * </pre>
     * @param Array $where_not_equal_or Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_not_equal_or($where_not_equal_or) {
        $this->where_not_equal_or = $where_not_equal_or;
        return $this;
    }

    /**
     * Function Query where != Not equal to
     *
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     * 
     *  (
     *           `column` <b> != </b> `value`
     *  )
     * </code>
     * </pre>
     * @param Array $where_not_equal_to Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_not_equal_to($where_not_equal_to) {
        $this->where_not_equal_to = $where_not_equal_to;
        return $this;
    }

    /**
     * Function Query where not in
     * 
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b> NOT IN</b>(value)
     * </code>
     * </pre>
     * @param Array $where_in Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_not_in(array $where_not_in) {
        $this->where_not_in = $where_not_in;
        return $this;
    }

    /**
     * Function Query where not like
     *
     * Example Query output :
     * 
     * <pre>
     * <code>
     * SELECT 
     *          *
     * FROM
     *          `table`
     * WHERE
     *           `column` <b>NOT LIKE</b> `%value%` 
     * </code>
     * </pre>
     * @param Array $where_not_like Used to compare strings.
     * @access public
     * @return \Query_src\Where
     */
    public function where_not_like($where_not_like) {
        $this->where_not_like = $where_not_like;
        return $this;
    }

    /**
     *  Displaying SQL
     * 
     * @access public
     * @param boolean $echo Use false to return string or true to print
     * @return \Query|String
     */
    public function display($print = true) {
        if ($print)
            return print $this->SQL;

        return $this->SQL;
    }

    /**
     * alias display() 
     * 
     * @access public
     * @param boolean $echo Use false to return string or true to print
     * @return Object
     */
    public function show($print = true) {
        return self::display($print);
    }

    /**
     * Alias page()
     * 
     * @access public 
     * @return integer
     */
    public function get_page() {
        return $this->page;
    }

    /**
     * Alias pages()
     * 
     * @access public 
     * @return integer
     */
    public function get_pages() {
        return $this->pages;
    }

    /**
     * Alias perpage()
     * 
     * @access public 
     * @return integer
     */
    public function get_perpage() {
        return $this->perpage;
    }

    /**
     * Alias total()
     * 
     * @access public 
     * @return integer
     */
    public function get_total() {
        return $this->total;
    }

}
