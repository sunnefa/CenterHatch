<?php
/**
 *  Text table
 * 
 *  CRUD for the text table and a representation of a single text from that table. This class also takes care of the
 *  pages-text relationship
 * 
 * @filename classes/core/Text.php
 * @package Core
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * The text table
 *
 * @author Sunnefa Lind
 */
class Text {
    
    /**
     * The id of the text
     * @var int
     */
    public $text_id;
    
    /**
     * The name of the text - only for identification not used as a heading in any way
     * @var string
     */
    public $text_name;
    
    /**
     * The text itself
     * @var string
     */
    public $text;
    
    /**
     * Is the text active?
     * @var boolean
     */
    public $text_status;
    
    /**
     * The name of the text table
     * @var string
     */
    private $table_name = 'core__text';
    
    /**
     * Reference to the database wrapper
     * @var DBWrapper
     */
    private $db_wrapper;
    
    /**
     * Constructor, sets db_wrapper and selects the right text
     * @param DBWrapper $db_wrapper
     * @param array $page_data
     */
    public function __construct(DBWrapper $db_wrapper, $page_data = array()) {
        $this->db_wrapper = $db_wrapper;
        
        if(!empty($page_data)) {
            $this->select_text_by_page($page_data);
        }
    }
    
    /**
     * Selects a single text from the database
     * @param array $page_data
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws Exception
     */
    private function select_text_by_page($page_data) {
        if(!is_array($page_data)) {
            throw new InvalidArgumentException('$page_data in ' . __METHOD__ . ' must be an array');
        } elseif(!isset($page_data['page_id']) || !isset($page_data['display_order'])) {
            throw new OutOfBoundsException('$page_data in ' . __METHOD__ . ' must contain the following items: page_id and display_order');
        } else {
            $joins = $this->db_wrapper->build_joins('core__text_pages AS p', array('p.text_id', 't.text_id'), 'left');
            
            $results = $this->db_wrapper->select($this->table_name . ' AS t', '*', 
                    'p.page_id = ' . $page_data['page_id'] . ' AND p.display_order = ' . $page_data['display_order'], 
                    null, null, null, $joins);
            
            if(!$results) {
                throw new Exception('No text was found matching the page_id ' . $page_data['page_id'] . ' with the display_order ' . $page_data['display_order'] . 'in ' . __METHOD__);
            } else {
                $results = Functions::array_flat($results);
                
                $this->text_id = $results['text_id'];
                $this->text_name = $results['text_name'];
                $this->text = html_entity_decode($results['text']);
                $this->text_status = $results['text_status'];
                
            }
            
        }
    }
    
    /**
     * Returns an array with the text matching $text_id
     * @param int $text_id
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function select_text_by_id($text_id) {
        if(!is_numeric($text_id)) {
            throw new InvalidArgumentException('$text_id in ' . __METHOD__ . ' must be a number');
        } else {
            $results = $this->db_wrapper->select($this->table_name, '*', 'text_id = ' . $text_id);
            if(!$results) {
                throw new Exception('No text was found matching the text_id ' . $text_id . ' in ' . __METHOD__);
            } else {
                return Functions::array_flat($results);
            }
        }
    }
    
    /**
     * Selects multiple/all texts starting from $start limited by $limit
     * @param int $start
     * @param int $limit
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function select_multiple_texts($start = 0, $limit = 0) {
        if(!is_numeric($start) || !is_numeric($limit)) {
            throw new InvalidArgumentException('$start and $limit in ' . __METHOD__ . ' must be numeric');
        } else {
            $limit_string = $start . ', ';
            $limit_string .= ($limit == 0) ? '18446744073709551615' : $limit;
            
            $results = $this->db_wrapper->select($this->table_name, '*', null, $limit_string);
            
            if(!$results) {
                throw new Exception('No texts were found in ' . __METHOD__);
            } else {
                return $results;
            }
        }
    }
    
    /**
     * Edits a single text in the database
     * @param array $text_data
     * @return boolean
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws Exception
     */
    public function edit_text($text_data) {
        if(!is_array($text_data)) {
            throw new InvalidArgumentException('$text_data in ' . __METHOD__ . ' must be an array');
        } elseif(!isset($text_data['text_id']) ||
                 !isset($text_data['text_name']) ||
                 !isset($text_data['text']) ||
                 !isset($text_data['text_status'])) {
            throw new OutOfBoundsException('The $text_data array in ' . __METHOD__ . ' must contain the following items: text_id, text_name, text and text_status');
        } else {
            $update = $this->db_wrapper->update($this->table_name, array(
                'text',
                'text_name',
                'text_status'
            ), array(
                $text_data['text'],
                $text_data['text_name'],
                $text_data['text_status']
            ), 'text_id = ' . $text_data['text_id']);
            
            if(!$update) {
                throw new Exception('Could not edit text with id ' . $text_data['text_id'] . ' in ' . __METHOD__);
            } else {
                return true;
            }
        }
    }
    
    /**
     * Adds a new text to the database
     * @param array $text_data
     * @return boolean
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws Exception
     */
    public function add_text($text_data) {
        if(!is_array($text_data)) {
            throw new InvalidArgumentException('$text_data in ' . __METHOD__ . ' must be an array');
        } elseif(!isset($text_data['text_name']) ||
                 !isset($text_data['text']) ||
                 ! isset($text_data['text_status'])) {
            throw new OutOfBoundsException('The $text_data array in ' . __METHOD__ . ' must contain the following items: text_name, text and text_status');
        } else {
            $added = $this->db_wrapper->insert($this->table_name, 
                    array('text_name', 'text', 'text_status'), 
                    array($text_data['text_name'], $text_data['text'], $text_data['text_status']));
            
            if(!$added) {
                throw new Exception('Could not add new text in ' . __METHOD__);
            } else {
                return true;
            }
        }
    }
    
    /**
     * Deletes a text
     * @param int $text_id
     * @throws InvalidArgumentException
     */
    public function delete_text($text_id) {
        if(!is_numeric($text_id)) {
            throw new InvalidArgumentException('$text_id in ' . __METHOD__ . ' must be a number');
        } else {
            $this->remove_all_text_from_pages($text_id);
            $this->db_wrapper->delete($this->table_name, 'text_id = ' . $text_id);
        }
    }
    
    /**
     * Adds a new relationship between a text and a page
     * @param int $text_id
     * @param int $page_id
     * @param int $display_order
     * @return boolean
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function add_text_page_relationship($text_id, $page_id, $display_order) {
        if(!is_numeric($text_id) || !is_numeric($page_id) || !is_numeric($display_order)) {
            throw new InvalidArgumentException('$text_id, $page_id and $display_order in ' . __METHOD__ . ' must all be numbers');
        } else {
            $add = $this->db_wrapper->insert('core__text_pages', 
                    array('text_id', 'page_id', 'display_order'), 
                    array($text_id, $page_id, $display_order));
            
            if(!$add) {
                throw new Exception('Could not add text_page_relationship in ' . __METHOD__);
            } else {
                return true;
            }
        }
    }
    
    /**
     * Deletes a relationship between a page and a text
     * @param int $text_id
     * @param int $page_id
     * @param int $display_order
     * @return boolean
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function remove_text_page_relationship($text_id, $page_id, $display_order) {
        if(!is_numeric($text_id) || !is_numeric($page_id) || !is_numeric($display_order)) {
            throw new InvalidArgumentException('$text_id, $page_id and $display_order in ' . __METHOD__ . ' must all be numbers');
        } else {
            $del = $this->db_wrapper->delete('core__text_pages', 'text_id = ' . $text_id . ' AND page_id = ' . $page_id . ' AND display_order = ' . $display_order);
            if(!$del) {
                throw new Exception('Could not delete text_page_relationship in ' . __METHOD__);
            } else {
                return true;
            }
        }
    }
    
    /**
     * Checks if a text and a page have a relationship
     * The relationship is defined by the unique combination of $text_id, $page_id and $display_order. Two texts cannot
     * occupy the same space on the same page, if they do we are doing something wrong
     * @param int $text_id
     * @param int $page_id
     * @param int $display_order
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function check_text_page_relationship($text_id, $page_id, $display_order) {
        if(!is_numeric($text_id) || !is_numeric($page_id) || !is_numeric($display_order)) {
            throw new InvalidArgumentException('$text_id, $page_id and $display_order in ' . __METHOD__ . ' must all be numbers');
        } else {
            $results = $this->db_wrapper->select('core__text_pages', 'text_id','text_id = ' . $text_id . ' AND page_id = ' . $page_id . ' AND display_order = ' . $display_order);
            if(is_array($results)) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    /**
     * Removes all page relationships from a particular text id (eg used if the text is deleted)
     * @param int $text_id
     * @throws InvalidArgumentException
     */
    public function remove_all_text_from_pages($text_id) {
        if(!is_numeric($text_id)) {
            throw new InvalidArgumentException('$text_id in ' . __METHOD__ . ' must be a number');
        } else {
            $this->db_wrapper->delete('core__text_pages', 'text_id = ' . $text_id);
        }
    }
    
    /**
     * Removes all page relationships from a particular page id (eg if the page is deleted)
     * @param int $page_id
     * @throws InvalidArgumentException
     */
    public function remove_all_pages_from_text($page_id) {
        if(!is_numeric($page_id)) {
            throw new InvalidArgumentException('$page_id in ' . __METHOD__ . ' must be a number');
        } else {
            $this->db_wrapper->delete('core__text_pages', 'page_id = ' . $page_id);
        }
    }
}
?>