<?php
/**
 *  Error handling class
 * 
 *  This class handles errors and exceptions, logs them and shows an error page
 * 
 * @filename classes/utilities/ErrorHandler.php
 * @package Utilities
 * @version 0.1
 * @author Sunnefa Lind <sunnefa_lind@hotmail.com>
 * @copyright Sunnefa Lind 2013
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
 */

/**
 * Handles errors and exceptions
 *
 * @author Sunnefa Lind
 * @incomplete See todo notes
 */
class ErrorHandler {
    
    /**
     * The error message
     * @var string
     */
    private $error_message;
    
    /**
     * A numerical representation of the error type
     * @var int
     */
    private $error_type;
    
    /**
     * The file the error occurred in
     * @var string
     */
    private $error_file;
    
    /**
     * The line of code in the file the error occurred in
     * @var int
     */
    private $error_line;
    
    /**
     * Is debug mode active? If true, the error message will be shown on the screen, if not it'll only be logged
     * @var boolean
     */
    private static $debug_mode = true;
    
    /**
     * Constructor, sets the error handler, exception handler and shutdown functions and error reporting level
     */
    public function __construct() {
        error_reporting(0);
        set_error_handler(array($this, "handle_error"));
        set_exception_handler(array($this, "handle_exception"));
        register_shutdown_function(array($this, "handle_shutdown"));
    }
    
    /**
     * Activate or deactivate debug mode
     */
    public function set_debug_mode() {
        self::$debug_mode = true;
        error_reporting(E_ALL|E_STRICT);
    }
    
    /**
     * The function called when an error occurrs
     * @param int $type
     * @param string $message
     * @param string $file
     * @param int $line
     */
    public function handle_error($type,$message,$file,$line) {
        $e = new ErrorException($message, $type, 0, $file, $line);
        $this->handle_exception($e);
    }
    
    /**
     * Handles uncaught exceptions
     * @param Exception $exception
     * @todo: Add support for different error types
     */
    public function handle_exception(Exception $exception) {
        $this->error_file = $exception->getFile();
        $this->error_line = $exception->getLine();
        $this->error_message = $exception->getMessage();
        $this->error_type = $exception->getCode();
        
        self::death($this->construct_error_message());
    }
    
    /**
     * Called once execution of the script is complete. The implementation allows us to log and custom die on fatal errors
     */
    public function handle_shutdown() {
        $error = error_get_last();
        if($error) {
            extract($error);
            $e = new ErrorException($message, $type, 0, $file, $line);
            $this->handle_exception($e);
        }
    }
    
    /**
     * Constructs an error message from the error information given to the error and exception handling methods
     * @return string
     */
    private function construct_error_message() {
        $error_type = $this->resolve_error_type($this->error_type);
        $error_date = date("d.m.Y, h:iA");
        
        $error_file = (defined(ROOT)) ? str_replace(ROOT, '', $this->error_file) : $this->error_file;
        
        $error_message = <<<EOT
/**
 * $error_type error occurred at $error_date
 * Message returned: $this->error_message
 * Error occurred in $error_file on line $this->error_line
 */
EOT;
        
        return $error_message;
        
    }
    
    /**
     * Resolves the numerical representation of an error type into a string
     * @param int $type
     * @return string
     * @todo: Add exception types in here as well
     */
    private function resolve_error_type($type) {
        switch($type) {
            case E_ERROR:
            case E_USER_ERROR:
                return 'FatalError';
                break;

            case E_WARNING:
            case E_USER_WARNING:
                return 'Warning';
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
                return 'Notice';
                break;

            case E_PARSE:
                return 'ParseError';
                break;

            case E_COMPILE_ERROR:
                return 'CompileError';
                break;

            case E_COMPILE_WARNING:
                return 'CompileWarning';
                break;

            case E_CORE_ERROR:
                return 'CoreError';
                break;

            case E_CORE_WARNING:
                return 'CoreWarning';
                break;

            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'Deprecated';
                break;

            case E_RECOVERABLE_ERROR:
                return 'RecoverableError';
                break;

            default:
                return 'UnknownError';
                break;
        }
    }
    
    /**
     * Writes the given error message to a log file
     * @param string $error_message
     * @todo Write error messages to a log file
     */
    private static function log_error($error_message) {
        echo 'Logging error';
    }
    
    /**
     * Alternative to die() wherein we can have better control over what happens such as logging, error output, style etc.
     * @param string $error_message
     * @param boolean $database_error
     * @todo Show a template or something like that
     */
    public static function death($error_message, $database_error = false) {
        @ob_end_clean();
        if(self::$debug_mode === true) {
            echo $error_message . '<br />';
        } else {
            echo 'There was an error';
        }
        exit;
    }
}

?>