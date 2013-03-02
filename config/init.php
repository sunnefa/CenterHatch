<?php
/**
 *  An initializer file
 * 
 *  This file does some neccessary preliminary leg-work such as including crucial files and instantiating crucial objects
 * 
 * @filename config/init.php
 * @package Configuration
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * The path definitions 
 */
require_once 'paths.php';

/**
 * The database definitions 
 */
require_once 'db.php';

/**
 * The autoload 
 */
require_once 'autoload.php';

/**
 * Instantiate the error handler
 */
$error_handler = new ErrorHandler();

/**
 * Get an instance of the MySQLWrapper object
 */
try {
    $sql = MySQLWrapper::get_instance();
} catch(mysqli_sql_exception $e) {
    ErrorHandler::death("Database connection error: " . $e->getMessage(), true);
}

try {
    $settings = new Settings($sql);
} catch(Exception $e) {
    ErrorHandler::death($e->getMessage());
}

if($settings->debug_mode == 1) {
    $error_handler->set_debug_mode();
}
?>