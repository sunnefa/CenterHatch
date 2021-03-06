<?php
/**
 *  Implements parent class methods to work with the MySQL database engine
 * 
 *  This class implements the abstract methods in DBWrapper to work with the MySQL database engine. It also includes some
 *  methods that are specific to MySQL and do not apply to other database engines
 * 
 * @filename classes/database/MySQLWrapper.php
 * @package Database
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Database abstraction layer for MySQL
 *
 * @author Sunnefa Lind
 */
class MySQLWrapper extends DBWrapper {
    /**
     * Constructor - sets connection variables and connects to the database
     * @param string $db_user
     * @param string $db_host
     * @param string $db_pass
     * @param string $db_name 
     */
    public function __construct($db_user, $db_host, $db_pass, $db_name) {
        $this->db_user = $db_user;
        $this->db_host = $db_host;
        $this->db_pass = $db_pass;
        $this->db_name = $db_name;
        
        $this->connect();
    }
    
    /**
     * Closes the connection to the database 
     */
    protected function close() {
        mysqli_close($this->db_conn);
    }
    
    /**
     * Connects to the database or throws an exception
     * @throws mysqli_sql_exception
     */
    protected function connect() {
        
        $this->db_conn = mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
        
        if(!$this->db_conn) {
            throw new mysqli_sql_exception(mysqli_connect_error());
        }
    }
    
    
    /**
     * Executes a mysql query or throws an exception
     * @param string $query
     * @return mysql result set/boolean
     * @throws Exception
     */
    protected function execute_query($query) {
        $results = mysqli_query($this->db_conn, $query);
        
        if(!$results) throw new Exception($this->show_error());
        else return $results;
    }
    
    /**
     * Deletes a single row from the database
     * @param string $table
     * @param string $where
     * @return boolean 
     */
    public function delete($table, $where) {
        $sql = "DELETE FROM " . $table . " WHERE " . $where;
        
        $this->execute_query($sql);
        
        if(mysqli_affected_rows($this->db_conn) > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Inserts a row of data into the database
     * @param string $table
     * @param array $fields
     * @param array $values
     * @return boolean 
     */
    public function insert($table, $fields, $values) {
        
        $values = $this->sanity($values);
        
        $field_string = implode(', ', $fields);
        
        $value_string = implode("', '", $values);
        
        $sql = 'INSERT INTO ' . $table . ' (' . $field_string . ") VALUES ('" . $value_string . "')";
        
        $this->execute_query($sql);
        
        return mysqli_insert_id($this->db_conn);
    }
    
    /**
     * Selects data from the database based on the given arguments
     * This method returns a multidimensional array. When only one row is expected, 
     * use the Functions:array_flat method to make it into a single dimension
     * 
     * @param string $table
     * @param array/string $fields
     * @param string $where
     * @param string $limit
     * @param string $order
     * @param string $group
     * @param string $join
     * @return array 
     */
    public function select($table, $fields, $where = null, $limit = null, $order = null, $group = null, $join = null) {
        
        //the fields
        $field_string = (is_array($fields)) ? implode(', ', $fields) : $fields;
        
        //the where
        $where_string = ($where != null) ? 'WHERE ' . $where : '';
        
        //the limit
        $limit_string = ($limit != null) ? 'LIMIT ' . $limit : '';
        
        //the order
        $order_string = ($order != null) ? 'ORDER BY ' . $order : '';
        
        //the group
        $group_string = ($group != null) ? 'GROUP BY ' . $group : '';
        
        //the sql statement
        $sql = 'SELECT ' . $field_string . ' FROM ' . $table . ' ' . $join . ' ' . $where_string . ' ' . $group_string . ' ' . $order_string . ' ' . $limit_string;
        
        //the results
        $results = $this->execute_query($sql);

        //processing the results and returning
        if(mysqli_num_rows($results) != 0) {
            $data = array();
            while($row = mysqli_fetch_assoc($results)) {
                $data[] = $row;
            }
            return $data;
        } else {
            return false;
        }
    }
    
    /**
     * Updates a single row in the database
     * @param string $table
     * @param array $fields
     * @param array $values
     * @param string $where
     * @return boolean 
     */
    public function update($table, $fields, $values, $where) {
        $values = $this->sanity($values);
        
        $field_value_string = '';
        
        for($i = 0; $i < count($fields); $i++) {
            $field_value_string .= $fields[$i] . " = '" . $values[$i] . "'";
            if($i != count($fields) - 1) {
                $field_value_string .= ', ';
            }
        }

        $sql = 'UPDATE ' . $table . ' SET ' . $field_value_string . ' WHERE ' . $where;
        
        $this->execute_query($sql);
        
        if(mysqli_affected_rows($this->db_conn) > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Builds a join string
     * @param string $table
     * @param array $fields
     * @param string $direction
     * @return string 
     */
    public function build_joins($table, $fields, $direction) {
        return strtoupper($direction) . ' JOIN ' . $table . ' ON ' . $fields[0] . ' = ' . $fields[1];
    }
    
    /**
     * Returns the last error message from the database
     * @return type 
     */
    private function show_error() {
        return mysqli_error($this->db_conn);
    }
    
    /**
     * Sanitizes a string or an array of strings before insertion
     * @param string/array $data
     * @return string/array 
     */
    private function sanity($data) {
        if(is_array($data)) {
            $returning = array();
            foreach($data as $key => $piece) {
                if(get_magic_quotes_gpc()) {
                    $piece = stripslashes($piece);
                }
                $san_piece = mysqli_real_escape_string($this->db_conn, $piece);
                $returning[$key] = $san_piece;
            }
        } else {
            if(get_magic_quotes_gpc()) {
		$data = stripslashes($data);	
            }
            $returning = mysqli_real_escape_string($this->db_conn, $data);
        }
        return $returning;
    }
    
    /**
     * Closes the connection when the object is destructed
     */
    public function __destruct() {
        $this->close();
    }
}

?>