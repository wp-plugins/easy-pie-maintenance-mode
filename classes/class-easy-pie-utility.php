<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!class_exists('Easy_Pie_Utility')) {

    /**
     * Simple utility class
     *
     * @author Bob Riley
     */
    class Easy_Pie_Utility {

        /**
         * Set option value if it isn't already set
         */
        public static function set_default_option(&$option_array, $key, $value) {
            if (!array_key_exists($key, $option_array)) {
                $option_array[$key] = $value;
            }
        }

        /**
         * Sets an option in option array
         */
        public static function set_option(&$option_array, $key, $value) {
            $option_array[$key] = $value;
        }
    }
}
?>
