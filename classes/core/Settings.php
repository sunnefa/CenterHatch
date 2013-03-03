<?php
/**
 *  Model for the settings table
 * 
 *  Represents the settings table. Setting values can be changed. Records in the settings table can be added
 *  but not deleted because that could lead to breakage of other parts of the framework. Setting records that
 *  are added but not used anywhere do not do a lot of harm, except take up space in the database, but care should
 *  be taken not to add setting records idly and if it turns out they are not needed they can always be deleted through
 *  PhpMyAdmin or other such similar interfaces.
 * 
 * @filename classes/core/Settings.php
 * @package Core models
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Represents the settings table
 *
 * @author Sunnefa Lind
 * @incomplete See @todo's
 */
class Settings {
    /**
     * The name of the settings table as it is defined in the database
     * @var string
     */
    private $table_name = 'core__settings';
    
    /**
     * An instance of DBWrapper
     * @var DBWrapper
     */
    private $db_wrapper;
    
    /**
     * Constructor
     * @param DBWrapper $db_wrapper
     */
    function __construct(DBWrapper $db_wrapper){
        $this->db_wrapper = $db_wrapper;
        
        $this->load_settings();
    }
    
    /**
     * Loads the settings from the database and creates a class property for each of them
     * @throws Exception
     */
    private function load_settings() {
        
        $results = $this->db_wrapper->select($this->table_name, '*');
        
        if($results === false) {
            throw new Exception('No setting records found');
        } else {
            foreach($results as $setting) {
                $this->$setting['setting_name'] = $setting['setting_value'];
            }
        }
        
    }
    
    /**
     * Adds a new setting record to the database
     * @param string $setting_name
     * @param string $setting_value
     * @return boolean
     * @throws Exception
     * @todo Implement
     */
    public function add_setting($setting_name, $setting_value) {
        
    }
    
    /**
     * Updates an existing setting record in the database
     * @param string $setting_name
     * @param string $setting_value
     * @return boolean
     * @throws Exception
     * @todo Implement
     */
    public function update_setting($setting_name, $setting_value) {
        
    }
}

?>