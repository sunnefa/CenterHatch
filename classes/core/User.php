<?php
/**
 *  CRUD for users table
 * 
 *  Represents the users table, the object represents a single user, the class has regular crud methods for the users
 * table and the user_roles table
 * 
 * @filename classes/core/User.php
 * @package Core
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * The users table
 *
 * @author Sunnefa Lind
 */
class User {
    
    /**
     * The user's first name
     * @var string
     */
    public $user_first_name;
    
    /**
     * The user's last name
     * @var string
     */
    public $user_last_name;
    
    /**
     * The user's email address
     * @var string
     */
    public $user_email;
    
    /**
     * The user's id
     * @var int
     */
    public $user_id;
    
    /**
     * The user's password
     * @var string
     */
    public $user_password;
    
    /**
     * If the user is active or not
     * @var boolean
     */
    public $user_is_active;
    
    /**
     * The user's nickname
     * @var string
     */
    public $user_nick_name;
    
    /**
     * The date and time of the user's registration
     * @var string
     */
    public $user_registration_time;
    
    /**
     * The user's role, level of permission
     * @var string
     */
    public $user_role;
    
    /**
     * The user's temporary token for password recovery
     * @var string
     */
    public $user_temp_token;
    
    /**
     * The time the temporary token for password recovery expires
     * @var string
     */
    public $user_temp_token_expiry;
    
    /**
     * The name of the users table
     * @var string
     */
    private $table_name = 'core__users';
    
    /**
     * Reference to the database wrapper
     * @var DBWrapper
     */
    private $db_wrapper;
    
    /**
     * Constructor
     * @param DBWrapper $db_wrapper
     * @param int $user_id
     */
    public function __construct(DBWrapper $db_wrapper, $user_id = 0) {
        $this->db_wrapper = $db_wrapper;
        
        if($user_id) {
            $this->select_single_user($user_id);
        }
    }
    
    private function select_single_user($user_id) {
        
    }
    
    public function select_multiple_users($start, $limit) {
        
    }
    
    public function add_user($user_data) {
        
    }
    
    public function edit_user($user_data) {
        
    }
    
    public function deactivate_user($user_id) {
        
    }
    
    public function activate_user($user_id) {
        
    }
    
    public function login($user_data) {
        
    }
    
    public function logout() {
        
    }
    
    public function update_password($user_id, $new_password) {
        
    }
    
    public function get_all_user_roles() {
        
    }
    
    public function add_user_role($role_name) {
        
    }
    
    public function edit_user_role($role_id, $role_name) {
        
    }
    
    public function delete_user_role($role_id) {
        
    }
    
    public function set_user_role($user_id, $role_id) {
        
    }
}

?>