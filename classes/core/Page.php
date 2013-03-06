<?php
/**
 *  CRUD for the pages table
 * 
 *  The object represents a single page in the pages table, the class contains CRUD methods for the table, as well as
 *  methods for managing the relationship between modules and pages
 * 
 * @filename classes/core/Page.php
 * @package Core
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * A single page from the pages table
 *
 * @author Sunnefa Lind
 */
class Page {
    /**
     * The id of the loaded page
     * @var int
     */
    public $page_id;
    
    /**
     * The title of the page
     * @var string
     */
    public $page_title;
    
    /**
     * The status of the page
     * @var boolean
     */
    public $page_status;
    
    /**
     * The description of the page
     * @var string
     */
    public $page_description;
    
    /**
     * The page slug (url bit)
     * @var string
     */
    public $page_slug;
    
    /**
     * An array of modules the page has
     * @var array
     */
    public $page_modules;
    
    /**
     * The name of the pages table
     * @var string
     */
    private $table_name = 'core__pages';
    
    /**
     * Reference to the database wrapper
     * @var DBWrapper
     */
    private $db_wrapper;
    
    /**
     * Construct, sets the DBWrapper and loads the page with the given slug
     * @param DBWrapper $db_wrapper
     * @param string $page_slug
     */
    public function __construct(DBWrapper $db_wrapper, $page_slug = 0) {
        $this->db_wrapper = $db_wrapper;
        
        if($page_slug) {
            $this->get_page_by_slug($page_slug);
        }
    }
    
    /**
     * Loads the page with the given slug or throws an exception if there is no such page
     * @param string $page_slug
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function get_page_by_slug($page_slug) {
        if(!is_string($page_slug)) {
            throw new InvalidArgumentException('$page_slug in Page::get_page_by_slug must be a string');
        } else {
            $results = $this->db_wrapper->select($this->table_name, '*', "page_slug = '$page_slug'");
            if(!$results) {
                throw new Exception('No page matching the slug ' . $page_slug . ' was found in Page::get_page_by_slug');
            } else {
                $results = Functions::array_flat($results);
                $this->page_description = $results['page_description'];
                $this->page_id = $results['page_id'];
                $this->page_slug = $results['page_slug'];
                $this->page_title = $results['page_title'];
                $this->page_status = $results['page_status'];
                
                try {
                    $this->page_modules = $this->load_page_modules($this->page_id);
                } catch(InvalidArgumentException $e) {
                    throw new InvalidArgumentException($e->getMessage());
                } catch(Exception $e) {
                    throw new Exception($e->getMessage());
                }
                
            }
        }
    }
    
    /**
     * Loads a single page by id as an array, not an object
     * @param int $page_id
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function get_page_by_id($page_id) {
        if(!is_numeric($page_id)) {
            throw new InvalidArgumentException('$page_id in Page::get_page_by_id must be a number');
        } else {
            $results = $this->db_wrapper->select($this->table_name, '*', 'page_id = ' . $page_id);
            
            if(!$results) {
                throw new Exception('No page with the id ' . $page_id . ' was found in Page::get_page_by_id');
            } else {
                return Functions::array_flat($results);
            }
        }
    }
    
    /**
     * Returns all or $limit number of pages starting from $start as an array
     * @param int $start
     * @param int $limit
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function get_multiple_pages($start = 0, $limit = 0) {
        if(!is_numeric($start) || !is_numeric($limit)) {
            throw new InvalidArgumentException('$start and $limit in Page::get_multiple_pages must be numeric');
        } else {
            $limit_string = $start . ', ';
            $limit_string .= ($limit == 0) ? '18446744073709551615' : $limit;

            $results = $this->db_wrapper->select($this->table_name, '*', null, $limit_string);

            if(!$results) {
                throw new Exception('No pages were found in Page::get_multiple_pages');
            } else {
                return $results;
            }
        }
        
    }
    
    /**
     * Returns an array of Module objects
     * @param int $page_id
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function load_page_modules($page_id) {
        if(!is_numeric($page_id)) {
            throw new InvalidArgumentException('$page_id in Page::load_modules must be a number');
        } else {
            $results = $this->db_wrapper->select('core__pages_modules', 'module_id, display_order', 'page_id = ' . $page_id);
            
            if(!$results) {
                throw new Exception('No modules were found for a page with the id ' . $page_id . ' in Pages::load_modules');
            } else {
                $modules = array();
                foreach($results as $module) {
                    $modules[$module['display_order']] = new Module($this->db_wrapper, $module['module_id']);
                }
                return $modules;
            }
            
        }
    }
    
    /**
     * Updates a single page in the database
     * @param array $page_data
     * @return boolean
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws Exception
     */
    public function edit_page($page_data) {
        if(!is_array($page_data)) {
            throw new InvalidArgumentException('$page_data in Page::edit_page must be an array');
        } else {
            if(!isset($page_data['page_id']) || 
               !isset($page_data['page_title']) || 
               !isset($page_data['page_slug']) || 
               !isset($page_data['page_description']) || 
               !isset($page_data['page_status'])) {
                throw new OutOfBoundsException('The $page_data array in Page::edit_page must contain the following items: page_id, page_title, page_slug, page_description and page_status');
            } else {
                $update = $this->db_wrapper->update($this->table_name, array(
                    'page_title',
                    'page_slug',
                    'page_description',
                    'page_status'
                ), array(
                    $page_data['page_title'],
                    $page_data['page_slug'],
                    $page_data['page_description'],
                    $page_data['page_status']
                ), 'page_id = ' . $page_data['page_id']);
                
                if(!$update) {
                    throw new Exception('Could not edit page with id ' . $page_data['page_id'] . ' in Pages::edit_page');
                } else {
                    if(isset($page_data['new_modules']) && isset($page_data['old_modules'])) {
                        //first we add the new modules (this includes updates to the display order only)
                        foreach($page_data['new_modules'] as $display_order => $module_id) {
                            $exists = $this->check_page_module_relationship($page_data['page_id'], $module_id, $display_order);
                            if(!$exists) {
                                $this->add_page_module_relationship($page_data['page_id'], $module_id, $display_order);
                            }
                        }
                        
                        //then we remove any of the old modules that are not in the new modules
                        $difference = array_diff_assoc($page_data['old_modules'], $page_data['new_modules']);
                        foreach($difference as $old_order => $old_module_id) {
                            $this->remove_page_module_relationship($page_data['page_id'], $old_module_id, $old_order);
                        }
                    }
                    return true;
                }
            }
        }
    }
    
    /**
     * Adds a new page to the database
     * @param array $page_data
     * @return boolean
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function add_page($page_data) {
        if(!is_array($page_data)) {
            throw new InvalidArgumentException('$page_data in Page::add_page must be an array');
        } else {
            if(!isset($page_data['page_title']) || 
               !isset($page_data['page_slug']) || 
               !isset($page_data['page_description']) || 
               !isset($page_data['page_status'])) {
                throw new OutOfBoundsException('The $page_data array in Page::add_page must contain the following items: page_title, page_slug, page_description and page_status');
            } else {
                $new_page_id = $this->db_wrapper->insert($this->table_name, array(
                    'page_title',
                    'page_slug',
                    'page_description',
                    'page_status'
                ), array(
                    $page_data['page_title'],
                    $page_data['page_slug'],
                    $page_data['page_description'],
                    $page_data['page_status']
                ));
                
                if(isset($page_data['modules'])) {
                    foreach($page_data['modules'] as $display_order => $module_id) {
                        $this->add_page_module_relationship($new_page_id, $module_id, $display_order);
                    }
                }
                return true;
            }
        }
    }
    
    /**
     * Deletes a page from the database, as well as all its module relationships
     * @param int $page_id
     * @throws InvalidArgumentException
     */
    public function delete_page($page_id) {
        if(!is_numeric($page_id)) {
            throw new InvalidArgumentException('$page_id in Page::delete_page must be a number');
        } else {
            $deleted = $this->db_wrapper->delete($this->table_name, 'page_id = ' . $page_id);
            if($deleted) {
                $this->remove_all_modules_from_page($page_id);
                return true;
            } else {
                return false;
            }
        }
    }
    
    /**
     * Adds a page module relationship to the database
     * @param int $page_id
     * @param int $module_id
     * @param int $display_order
     * @return boolean
     * @throws InvalidArgumentException
     */
    private function add_page_module_relationship($page_id, $module_id, $display_order) {
        if(!is_numeric($page_id) || !is_numeric($module_id) || !is_numeric($display_order)) {
            throw new InvalidArgumentException('$page_id, $module_id and $display_order in Page::add_page_module_relationship must all be numeric');
        } else {
            $results = $this->db_wrapper->insert('core__pages_modules', array('page_id', 'module_id', 'display_order'), array($page_id, $module_id, $display_order));
            if($results) return true;
            else return false;
        }
    }
    
    /**
     * Removes all page-module relationships that match the given page id
     * @param int $page_id
     * @throws InvalidArgumentException
     */
    private function remove_all_modules_from_page($page_id) {
        if(!is_numeric($page_id)) {
            throw new InvalidArgumentException('$page_id in Page::remove_all_modules_from_page must be a number');
        } else {
            $this->db_wrapper->delete('core__pages_modules', 'page_id = ' . $page_id);
        }
    }
    
    /**
     * Removes a page module relationship from the database
     * @param int $page_id
     * @param int $module_id
     * @param int $display_order
     * @return boolean
     * @throws InvalidArgumentException
     */
    private function remove_page_module_relationship($page_id, $module_id, $display_order) {
        if(!is_numeric($page_id) || !is_numeric($module_id) || !is_numeric($display_order)) {
            throw new InvalidArgumentException('$page_id, $module_id and $display_order in Page::remove_page_module_relationship must all be numeric');
        } else {
            $results = $this->db_wrapper->delete('core__pages_modules', "page_id = $page_id AND module_id = $module_id AND $display_order = $display_order");
            if($results) return true;
            else return false;
        }
    }
    
    /**
     * Checks if a page and a module have a relationship with each other
     * The relationship should be identifiable by the unique combination of $page_id, $module_id and $display_order
     * which should never repeat in the table (if it does we are doing something wrong, 
     * two modules cannot occupy the same space on the page)
     * @param int $page_id
     * @param int $module_id
     * @param int $display_order
     * @return boolean
     * @throws InvalidArgumentException
     */
    private function check_page_module_relationship($page_id, $module_id, $display_order) {
        if(!is_numeric($page_id) || !is_numeric($module_id) || !is_numeric($display_order)) {
            throw new InvalidArgumentException('$page_id, $module_id and $display_order in Page::check_page_module_relationship must all be numeric');
        } else {
            $results = $this->db_wrapper->select('core__pages_modules', 'page_id', "page_id = $page_id AND module_id = $module_id AND display_order = $display_order");
            if(is_array($results)) return true;
            else return false;
        }
    }
    
    /**
     * Checks if a page slug already exists in the database because we want the to be unique
     * @param string $page_slug
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function check_slug_exists($page_slug) {
        if(!is_string($page_slug)) {
            throw new InvalidArgumentException('$page_slug in Page::check_slug_exists must be a string');
        } else {
            $exists = $this->db_wrapper->select($this->table_name, 'page_id', "page_slug = '$page_slug'");
            if(is_array($exists)) {
                return true;
            } else {
                return false;
            }
        }
    }
}

?>