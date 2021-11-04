<?php

//namespace to organize 
namespace Query_src;

use \PDO;
if (!defined(DB_HOST)) 
    require realpath(__DIR__) . '/../../../../../application/config/database.php';

/**
 * Configuration for: Database Connection
 * This is the place where your database constants are saved
 * @version 3
 */
class Config extends Run {

    protected static $factory;
    protected $database;

    /**
     * Multiple Database Conection
     * DB_HOST - database host, usually it's "127.0.0.1" or "localhost", some servers also need port info
     * DB_NAME - for set name of the database. please note: database and database table are not the same thing
     * DB_USER - for your database. the user needs to have rights for SELECT, UPDATE, DELETE and INSERT.
     * by the way, it's bad style to use "root", but for development it will work.
     * DB_PASS - the password of the above user
     * 
     * @access public
     * @var array 
     */
    public $Connections_Settings = array(
        'main' => array(
            'DB_HOST' => '',
            'DB_NAME' => '',
            'DB_USER' => '',
            'DB_PASS' => '',
            'DB_CHARSET' => ''
        )
    );

    /**
     * Create a Database connection using PDO
     * 
     * @access private
     * @return Void
     */
    private function getConnection($database) {
        if (empty($database))
            $value = array_shift($this->Connections_Settings);
        else
            $value = $this->Connections_Settings[$database];

        try {
            $options = array(
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$value['DB_CHARSET']}"
            );
            $this->database = new PDO("mysql:host={$value['DB_HOST']};dbname={$value['DB_NAME']};port=3306;charset={$value['DB_CHARSET']}", $value['DB_USER'], $value['DB_PASS'], $options);
        } catch (PDOException $e) {
            // Echo custom message. Echo error code gives you some info.
            echo 'Database connection can not be estabilished. Please try again later.' . '<br>';
            echo 'Error code: ' . $e->getCode();
            // Stop application :(
            // No connection, reached limit connections etc. so no point to keep it running
            exit;
        }
    }

    /**
     * Method magic create connection with database
     * 
     * @access public
     * @return void
     */
    public function __construct($database) {
        if (defined(DB_HOST) && defined(DB_NAME) && defined(DB_USER) && defined(DB_PASS)) {
            $this->Connections_Settings['main'] = array(
                'DB_HOST' => DB_HOST,
                'DB_NAME' => DB_NAME,
                'DB_USER' => DB_USER,
                'DB_PASS' => DB_PASS,
                'DB_CHARSET' => DB_CHARSET
            );
            if (defined(DB_NAME_Z)) {
                // database
                $this->Connections_Settings['cep'] = array(
                    'DB_HOST' => DB_HOST,
                    'DB_NAME' => DB_NAME_Z,
                    'DB_USER' => DB_USER,
                    'DB_PASS' => DB_PASS,
                    'DB_CHARSET' => DB_CHARSET
                );
            }
        }
        $this->getConnection($database);
    }

}
