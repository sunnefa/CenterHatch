<?php
/**
 *  A single page module from the database
 * 
 *  This object represents a single page module from the database and contain CRUD methods for the modules table
 *  The module-page relationship is taken care of by the Page class
 * 
 * @filename classes/core/Module.php
 * @package Core
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Single module + CRUD
 *
 * @author Sunnefa Lind
 */
class Module {
    
    /**
     * The id of the selected module
     * @var int
     */
    public $module_id;
    
    /**
     * The name of the module
     * @var string
     */
    public $module_name;
    
    /**
     * The path to the module
     * @var string
     */
    public $module_path;
    
    /**
     * Is the module active or not
     * @var boolean
     */
    public $module_is_active;
    
    /**
     * The name of the module table
     * @var string
     */
    private $table_name = 'core__modules';
    
    /**
     * Reference to database wrapper
     * @var DBWrapper
     */
    private $db_wrapper;
    
    /**
     * Construct, sets DBWrapper and selects module if applicable
     * @param DBWrapper $db_wrapper
     * @param int/string $module
     */
    public function __construct(DBWrapper $db_wrapper, $module = null) {
        $this->db_wrapper = $db_wrapper;
        
        if($module) {
            $this->select_single_module($module);
        }
    }
    
    /**
     * Selects a single module from the database either by its name or its id
     * @param int/string $module_identifier
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function select_single_module($module_identifier) {
        if(is_numeric($module_identifier)) {
            $where = 'module_id = ' . $module_identifier;
        } elseif(is_string($module_identifier)) {
            $where = "module_name = '$module_identifier'";
        } else {
            throw new InvalidArgumentException('$module_identifier in Module::select_single_module must be either a string or a number');
        }
        
        $results = $this->db_wrapper->select($this->table_name, '*', $where);
        
        if(!$results) {
            throw new Exception('No module with identifier (name or id) ' . $module_identifier . ' was found in Module::select_single_module');
        } else {
            $results = Functions::array_flat($results);
            
            $this->module_id = $results['module_id'];
            $this->module_is_active = $results['module_is_active'];
            $this->module_name = $results['module_name'];
            $this->module_path = $results['module_path'];
        }
    }
    
    /**
     * Returns an array with the modules for the given page
     * @param int $page_id
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function select_modules_by_page_id($page_id) {
        if(!is_numeric(($page_id))) {
            throw new InvalidArgumentException('$page_id in Module::select_modules_by_page_id must be numeric');
        } else {
            $joins = $this->db_wrapper->build_joins('core__pages_modules AS p', array('p.module_id', 'm.module_id'), 'left');
            
            $results = $this->db_wrapper->select(
                        $this->table_name . ' AS m', 
                        array(
                            'm.module_name',
                            'm.module_path',
                            'm.module_id',
                            'm.module_is_active',
                            'p.display_order'
                        ), 
                        'p.page_id = ' . $page_id, null, 'p.display_order', null, $joins
                    );
            if(!$results) {
                throw new Exception('No modules were found for a page with the id ' . $page_id . ' in Module::select_modules_by_page_id');
            } else {
                return $results;
            }
        }
    }
    
    /**
     * Returns an array of all or specified limit of modules starting from $start
     * @param int $start
     * @param int $limit
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function select_multiple_modules($start = 0, $limit = 0) {
        if(!is_numeric($start) || !is_numeric($limit)) {
            throw new InvalidArgumentException('$start and $limit in Module::select_multiple_modules must both be numbers');
        } else {
            $limit_string = $start . ', ';
            $limit_string .= ($limit == 0) ? '18446744073709551615' : $limit;
            
            $results = $this->db_wrapper->select($this->table_name, '*', null, $limit_string);
            if(!$results) {
                throw new Exception('No modules were found in Module::select_multiple_modules');
            } else {
                return $results;
            }
        }
    }
    
    /**
     * Edits a single module in the database
     * @param array $module_data
     * @return boolean
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws Exception
     */
    public function edit_module($module_data) {
        if(!is_array($module_data)) {
            throw new InvalidArgumentException('$module_data in Module::edit_module must be an array');
        } elseif(!isset($module_data['module_id']) ||
                 !isset($module_data['module_name']) ||
                 !isset($module_data['module_path']) ||
                 !isset($module_data['module_is_active'])) {
            throw new OutOfBoundsException('The $module_data array in Module::edit_module must contain the following items: module_id, module_name, module_path and module_is_active');
        } else {
            $update = $this->db_wrapper->update($this->table_name, 
                    array(
                        'module_name',
                        'module_path',
                        'module_is_active'
                    ), 
                    array(
                        $module_data['module_name'],
                        $module_data['module_path'],
                        $module_data['module_is_active']
                    ), 
                    'module_id = ' . $module_data['module_id']);
            if(!$update) {
                throw new Exception('Could not edit the module with the id ' . $module_data['module_id'] . ' in Module::edit_module');
            } else {
                return true;
            }
        }
    }
    
    /**
     * Adds a new module to the database
     * @param array $module_data
     * @return boolean
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function add_module($module_data) {
        if(!is_array($module_data)) {
            throw new InvalidArgumentException('$module_data in Module::add_module must be an array');
        } elseif(!isset($module_data['module_name']) ||
                 !isset($module_data['module_path']) ||
                 !isset($module_data['module_is_active'])) {
            throw new OutOfBoundsException('The $module_data array in Module::add_module must contain the following items: module_name, module_path and module_is_active');
        } else {
            $insert = $this->db_wrapper->insert($this->table_name, 
                    array(
                        'module_name', 
                        'module_path', 
                        'module_is_active'
                    ), 
                    array(
                        $module_data['module_name'], 
                        $module_data['module_path'], 
                        $module_data['module_is_active']
                    ));
            
            if($insert) return true;
            else return false;
        }
    }
    
    /**
     * Deletes a module from the database
     * @param int $module_id
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function delete_module($module_id) {
        if(!is_numeric($module_id)) {
            throw new InvalidArgumentException('$module_id in Module::delete_module must be a number');
        } else {
            $deleted = $this->db_wrapper->delete($this->table_name, 'module_id = ' . $module_id);
            
            if($deleted) return true;
            else return false;
        }
    }
}

?>