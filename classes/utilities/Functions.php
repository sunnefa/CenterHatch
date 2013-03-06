<?php
/**
 *  Static helper methods such as array_flat, nl2p, reload etc.
 * 
 *  This class contains only static helper methods that would otherwise have been declared in the procedural style.
 *  This way they all stay together in the same place
 * 
 * @filename classes/utilities/Functions.php
 * @package Utilities
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Some helpful methods
 *
 * @author Sunnefa Lind
 */
class Functions {
    
    /**
     * Takes a two dimensional array and converts it into one dimension. This is useful for when we are expecting
     * only one result from the database but the database selection method returns all data as multidimensional array's
     * because it doesn't know it's only got one result
     * @param array $array
     * @return array
     */
    public static function array_flat($array) {
        $single = array();
        foreach($array as $one) {
            foreach($one as $key => $value) {
                $single[$key] = $value;	
            }
        }
        return $single;
    }
    
    /**
     * Echo'es pre tags before and after the print_r output
     * @param array $array
     */
    public static function print_r($array) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
}

?>