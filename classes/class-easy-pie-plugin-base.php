<?php

if (!class_exists('Easy_Pie_Plugin_Base')) {
    
    /**
     * Base class of Easy Pie Plugins
     *
     * @author Bob Riley
     */
    class Easy_Pie_Plugin_Base {

        protected $prefix;
        
        function __construct($prefix) {
            
            $this->prefix = $prefix;
        }
        //Use WordPress Debugging log file. file is written to wp-content/debug.log
        //trace with tail command to see real-time issues.
        function debug($message) {

            if (WP_DEBUG === true) {
                if (is_array($message) || is_object($message)) {
                    error_log($this->prefix . ":" . print_r($message, true));
                } else {
                    error_log($this->prefix . ":" . $message);
                }
            }
        }
    }
}
?>
