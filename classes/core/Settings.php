<?php
/**
 *  Model for the settings table
 * 
 *  Represents the settings table. Setting values can be changed. Records in the settings table can be added
 *  but not deleted because that could lead to breakage of other parts of the framework. Setting records that
 *  are added but not used anywhere do not do a lot of harm, except take up space in the database, but care should
 *  be taken not to add setting records idly and if it turns out they are not needed they can always be deleted through
 *  PhpMyAdmin or other such similar interfaces.
 *  Note: when creating the management module for the settings, use get_object_vars to get an array of all the settings
 *  in the database.
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
     * @throws InvalidArgumentException
     */
    public function add_setting($setting_name, $setting_value) {
        if(!is_string($setting_name) || !is_string($setting_value)) {
            throw new InvalidArgumentException('$setting_name and $setting_value in Settings::add_setting must both be strings');
        } else {
            $result = $this->db_wrapper->insert($this->table_name, 
                    array('setting_name', 'setting_value'), 
                    array($setting_name, $setting_value));
            
            if(!$result) throw new Exception('Could not add records to ' . $this->table_name . ' table in Settings::add_setting');
            else return true;
        }
    }
    
    /**
     * Updates an existing setting record in the database
     * @param string $setting_name
     * @param string $setting_value
     * @return boolean
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function update_setting($setting_name, $setting_value) {
        if(!is_string($setting_name) || !is_string($setting_value)) {
            throw new InvalidArgumentException('$setting_name and $setting_value in Settings:update_setting must both be strings');
        } else {
            $result = $this->db_wrapper->update($this->table_name, 
                    array('setting_value'), 
                    array($setting_value), 
                    "setting_name = '$setting_name'");
            
            if(!$result) throw new Exception('Could not update records in ' . $this->table_name . ' table in Settings::update_setting');
            else return true;
        }
    }
}

?>