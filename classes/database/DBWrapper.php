<?php
/**
 *  Abstract database wrapper class
 * 
 *  Abstract database wrapper class. Wrappers for different database engines can extend this class and implement the
 *  defined abstract functions. This way changing database engines does not pose a problem as the classes that use the
 *  database wrappers will not need to be aware of the database language or mechanics
 * 
 * @filename classes/database/DBWrapper.php
 * @package Database
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Abstract database wrapper
 *
 * @author Sunnefa Lind
 */
abstract class DBWrapper {
    /**
     * The database username
     * @var string 
     */
    protected $db_user;
    
    /**
     * The database host name
     * @var string
     */
    protected $db_host;
    
    /**
     * The database name
     * @var string 
     */
    protected $db_name;
    
    /**
     * The database password
     * @var string
     */
    protected $db_pass;
    
    /**
     * The database connection
     * @var link identifier 
     */
    protected $db_conn;

    abstract protected function connect();
    
    abstract protected function close();
    
    abstract public function select($table, $fields, $where = null, $limit = null, $order = null, $group = null, $join = null);
    
    abstract public function insert($table, $fields, $values);
    
    abstract protected function execute_query($query);
    
    abstract public function update($table, $fields, $values, $where);
    
    abstract public function delete($table, $where);
}

?>