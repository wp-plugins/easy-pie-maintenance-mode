<?php
require_once("class-easy-pie-utility.php");
require_once("class-easy-pie-plugin-base.php");

if (!class_exists('Easy_Pie_Maintenance_Mode')) {

    /**
     * Main class of Easy Pie Maintenance Mode plugin
     *
     * @author Bob Riley
     */
    class Easy_Pie_Maintenance_Mode extends Easy_Pie_Plugin_Base {
        // Constants

        const OPTIONS_GROUP_NAME = 'easy-pie-mm-options-group';
        const OPTION_NAME = 'easy-pie-mm-options';
        const PAGE_KEY = 'easy-pie-mm';
        const PLUGIN_SLUG = 'easy-pie-maintenance-mode';
        const PLUGIN_VERSION = 0.5;

        // Variables        
        private $options;

        /**
         * Constructor
         */
        function __construct() {

            parent::__construct(Easy_Pie_Maintenance_Mode::PLUGIN_SLUG);

            $this->add_class_action('plugins_loaded', 'plugins_loaded_handler');            
            
            // RSR TODO Bug - is_admin just says if admin panel is attempting to be displayed - NOT to see if someone is an admin
            if (is_admin() == true) {
                                        
                //- Hook Handlers
                register_activation_hook(__FILE__, array('Easy_Pie_Maintenance_Mode', 'activate'));
                register_deactivation_hook(__FILE__, array('Easy_Pie_Maintenance_Mode', 'deactivate'));
                register_uninstall_hook(__FILE__, array('Easy_Pie_Maintenance_Mode', 'uninstall'));

                //- Actions
                $this->add_class_action('admin_init', 'admin_init_handler');
                $this->add_class_action('admin_menu', 'add_to_admin_menu');
            } else if ($this->get_option_value("enabled") == true) {
                
                $this->add_class_action('template_redirect', 'display_maintenance_page');
            }
        }

        function add_class_action($tag, $method_name) {

            return add_action($tag, array($this, $method_name));
        }

        /**
         * Display the maintenance page
         */        
        public function display_maintenance_page() {

            // For now 
            if (!is_user_logged_in()) {
               
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');
                header('Retry-After: 86400'); // RSR TODO: Put in the retry time later

                $key = $this->get_option_value("page_template_key");
                $dir = $this->get_template_path($key);
                $filename = $dir . "/page.html";

                $template_url = plugins_url() . "/easy-pie-maintenance-mode/mini-themes/" . $key;

                $contents = file_get_contents($filename);
                $contents = $this->replace_page_template_fields($contents, $template_url);

                if ($contents != false) {

                    echo $contents;
                } else {

                    $this->debug(__("Problem reading template ", Easy_Pie_Maintenance_Mode::PLUGIN_SLUG) . $key);
                }

                exit();
            }
        }

        private function replace_page_template_fields($contents, $template_url) {

            // rsr todo replace value per http://stackoverflow.com/questions/7980741/efficient-way-to-replace-placeholders-with-variables
            $option_array = get_option(Easy_Pie_Maintenance_Mode::OPTION_NAME);

            $headline = $option_array['headline'];
            $header = $option_array['header'];
            $message = $option_array['message'];
            $title = $option_array['title'];
            $logo_url = $option_array['logo_url'];

            $values = array(
                '{{headline}}' => $headline,
                '{{header}}' => $header,
                '{{message}}' => $message,
                '{{title}}' => $title,
                '{{logo_url}}' => $logo_url,
                './' => $template_url . "/"
            );

            return strtr($contents, $values);
        }

        // <editor-fold defaultstate="collapsed" desc="Hook Handlers">
        public static function activate() {

            // RSR TODO: Do initial setup of database/configuration
        }

        public static function deactivate() {
            //No actions needed yet
        }

        public static function uninstall() {
            
        }

        // </editor-fold>

        public function enqueue_scripts() {

            $jsRoot = plugins_url("/../js", __FILE__);

            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery.bxslider', $jsRoot . "/jquery.bxslider.min.js", array("jquery"));
            wp_enqueue_script("easy_pie_mm_functions", $jsRoot . "/functions.js", array("jquery", "jquery.bxslider"));

            wp_enqueue_media();
        }

        /**
         *  enqueue_styles
         *  Loads the required css links only for this plugin  */
        public function enqueue_styles() {
            $styleRoot = plugins_url("/../styles", __FILE__);

            wp_register_style('jquery.bxslider.css', $styleRoot . '/jquery.bxslider.css');
            wp_enqueue_style('jquery.bxslider.css');

            wp_register_style('easy-pie-styles.css', $styleRoot . '/easy-pie-styles.css');
            wp_enqueue_style('easy-pie-styles.css');
        }

        // <editor-fold defaultstate="collapsed" desc=" Action Handlers ">
        public function plugins_loaded_handler() {

            $this->init_localization();
            $this->upgrade_processing();
            $this->init_options();
        }

        public function init_localization() {
            
            load_plugin_textdomain(Easy_Pie_Maintenance_Mode::PLUGIN_SLUG, FALSE, Easy_Pie_Maintenance_Mode::PLUGIN_SLUG . '/languages/');
        }
        
        public function admin_init_handler() {

            register_setting(Easy_Pie_Maintenance_Mode::OPTIONS_GROUP_NAME, Easy_Pie_Maintenance_Mode::OPTION_NAME, array($this, 'validate_options'));

            $this->add_settings_sections();
            $this->add_filters();
        }

        private function add_filters() {

            add_filter('plugin_action_links', array($this, 'get_action_links'), 10, 2);
        }

        function get_action_links($links, $file) {

            if ($file == "easy-pie-maintenance-mode/easy-pie-maintenance-mode.php") {

                // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
                $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-pie-mm">Settings</a>';
                // add the link to the list
                array_unshift($links, $settings_link);
            }

            return $links;
        }

        private function get_option_value($subkey) {

            $optionArray = get_option(Easy_Pie_Maintenance_Mode::OPTION_NAME);

            return $optionArray[strtolower($subkey)];
        }

        // <editor-fold defaultstate="collapsed" desc=" Settings Logic ">

        function upgrade_processing() {
            // RSR: In future versions compare where we are at with what's in the system and take action            
        }

        function init_options() {

            $this->options = get_option(Easy_Pie_Maintenance_Mode::OPTION_NAME);

            if ($this->options == false) {

                $this->options = array();
            }

            Easy_Pie_Utility::set_option($this->options, 'plugin_version', Easy_Pie_Maintenance_Mode::PLUGIN_VERSION);

            Easy_Pie_Utility::set_default_option($this->options, 'enabled', false);
            Easy_Pie_Utility::set_default_option($this->options, 'page_template_key', "plain");
            Easy_Pie_Utility::set_default_option($this->options, 'title', null);
            Easy_Pie_Utility::set_default_option($this->options, 'header', null);
            Easy_Pie_Utility::set_default_option($this->options, 'headline', null);
            Easy_Pie_Utility::set_default_option($this->options, 'message', null);            
            Easy_Pie_Utility::set_default_option($this->options, 'logo_url', null);

            update_option(Easy_Pie_Maintenance_Mode::OPTION_NAME, $this->options);
        }

        public function add_settings_sections() {

            $section_id = 'easy-pie-mm-control-section';
            add_settings_section($section_id, 'Control', array($this, 'render_control_section'), Easy_Pie_Maintenance_Mode::PAGE_KEY);
            add_settings_field('easy-pie-mm-mode-on', __('Enabled', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG), array($this, 'render_checkbox_field'), Easy_Pie_Maintenance_Mode::PAGE_KEY, $section_id, array('id' => 'easy-pie-mm-mode-on', 'subkey' => 'enabled'));

            $section_id = 'easy-pie-mm-theme-section';
            add_settings_section($section_id, __('Visuals', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG), array($this, 'render_theme_section'), Easy_Pie_Maintenance_Mode::PAGE_KEY);
            add_settings_field('easy-pie-mm-theme', __('Page', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG), array($this, 'render_active_theme_selector'), Easy_Pie_Maintenance_Mode::PAGE_KEY, $section_id, array('id' => 'easy-pie-mm-theme', 'subkey' => 'page_template_key'));

            $section_id = 'easy-pie-mm-template_fields-section';
            add_settings_section($section_id, __('Fields', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG), array($this, 'render_template_fields_section'), Easy_Pie_Maintenance_Mode::PAGE_KEY);
            add_settings_field('easy-pie-mm-field-title', __('Title', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG), array($this, 'render_text_field'), Easy_Pie_Maintenance_Mode::PAGE_KEY, $section_id, array('id' => 'easy-pie-mm-field-title', 'subkey' => 'title'));
            add_settings_field('easy-pie-mm-field-header', __('Header', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG), array($this, 'render_text_field'), Easy_Pie_Maintenance_Mode::PAGE_KEY, $section_id, array('id' => 'easy-pie-mm-field-header', 'subkey' => 'header'));
            add_settings_field('easy-pie-mm-field-headline', __('Headline', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG), array($this, 'render_text_field'), Easy_Pie_Maintenance_Mode::PAGE_KEY, $section_id, array('id' => 'easy-pie-mm-field-headline', 'subkey' => 'headline'));
            add_settings_field('easy-pie-mm-field-message', __('Message', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG), array($this, 'render_text_area'), Easy_Pie_Maintenance_Mode::PAGE_KEY, $section_id, array('id' => 'easy-pie-mm-field-message', 'subkey' => 'message', 'size' => 80));
            add_settings_field('easy-pie-mm-field-logo', __('Logo', Easy_Pie_Maintenance_Mode::PLUGIN_SLUG), array($this, 'render_logo_field'), Easy_Pie_Maintenance_Mode::PAGE_KEY, $section_id, array('id' => 'easy-pie-mm-field-logo', 'subkey' => 'logo_url'));
        }

        public function render_text_field($args) {
            $options = get_option(Easy_Pie_Maintenance_Mode::OPTION_NAME);
            $subkey = $args['subkey'];
            $id = $args['id'];
            $optionExpression = Easy_Pie_Maintenance_Mode::OPTION_NAME . "[" . $subkey . "]";
            $currentValue = $options[$subkey];
            $size = 50;
            
            if(array_key_exists('size', $args)) {
                $size = $args['size'];                           
            }
            
            ?>
            <div>
                <input id="<?php echo $id; ?>" name='<?php echo $optionExpression; ?>' size="<?php echo $size; ?>" value="<?php echo $currentValue; ?>"/>
            </div>            
            <?php
        }
        
        public function render_text_area($args) {
            $options = get_option(Easy_Pie_Maintenance_Mode::OPTION_NAME);
            $subkey = $args['subkey'];
            $id = $args['id'];
            $optionExpression = Easy_Pie_Maintenance_Mode::OPTION_NAME . "[" . $subkey . "]";
            $currentValue = $options[$subkey];
            ?>
            <div>
                <textarea cols="53" rows="5" id="<?php echo $id; ?>" name='<?php echo $optionExpression; ?>'><?php echo $currentValue; ?></textarea>
                <p><small><?php _e("HTML tags are allowed. e.g. Add &lt;br/&gt; for break.", Easy_Pie_Maintenance_Mode::PLUGIN_SLUG); ?></small></p>
            </div>             
            <?php
        }
        

        // RSR: Why the hell do we need to set value to 1?
        public function render_checkbox_field($args) {
            $options = get_option(Easy_Pie_Maintenance_Mode::OPTION_NAME);
            $subkey = $args['subkey'];
            $id = $args['id'];

            if (array_key_exists('small_text', $args)) {

                $small_text = $args['small_text'];
            }


            $optionExpression = Easy_Pie_Maintenance_Mode::OPTION_NAME . "[" . $subkey . "]";
            $currentValue = $options[$subkey];

            $checkedText = checked(1, $options[$subkey], false);
            ?>
            <div>
                <input style="margin-right:5px;" value="1" id="<?php echo $id; ?>" type="checkbox" name="<?php echo $optionExpression; ?>" <?php echo $checkedText; ?> >Yes</input>
                <?php
                if (isset($small_text)) {
                    echo "<p><small>" . $small_text . "</small></p>";
                }
                ?>
            </div>            
            <?php
        }

        public function render_logo_field() {
            $options = get_option(Easy_Pie_Maintenance_Mode::OPTION_NAME);
            $optionExpression = Easy_Pie_Maintenance_Mode::OPTION_NAME . "[logo_url]";
            $currentValue = $options["logo_url"];
            ?>

            <div>
                <input id="easy-pie-mm-field-logo" type="text" name="<?php echo $optionExpression; ?>" size="40" value="<?php echo $currentValue; ?>" />
                <input id="easy-pie-mm-upload-logo-button" type="button" value="Upload" />

                <?php
                if (empty($currentValue)) {
                    $displayModifier = "display:none;";
                } else {
                    $displayModifier = "";
                }
                ?>

                <div >
                    <img id="easy-pie-mm-field-logo-preview" src="<?php echo $currentValue; ?>" style="<?php echo $displayModifier; ?>max-height:170px;max-width:170px;box-shadow: 1px 7px 20px -4px rgba(34,34,34,1);padding: 5px;border: black solid 1px;margin-top: 14px;"/>
                </div>                                                             
            </div>

            <?php
        }

        public function render_control_section() {
            //         echo 'TODO: Theme is used to change what is displayed. Blah blah blah..';
        }

        public function render_theme_section() {
            //       echo 'TODO: Theme is used to change what is displayed. Blah blah blah..';
        }

        public function render_template_fields_section() {
            //       echo 'TODO: Theme is used to change what is displayed. Blah blah blah..';
            echo '<small >' . __("All fields are optional", Easy_Pie_Maintenance_Mode::PLUGIN_SLUG) . '</small>';
        }

        private function get_template_path($page_template_key) {

            return __DIR__ . "/../mini-themes/" . $page_template_key;
        }

        public function render_active_theme_selector($args) {

            $path = __DIR__ . "/../mini-themes/";

            $dirs = glob($path . "*", GLOB_ONLYDIR);

            $options = get_option(Easy_Pie_Maintenance_Mode::OPTION_NAME);
            $subkey = $args['subkey'];
            $id = $args['id'];
            $optionExpression = Easy_Pie_Maintenance_Mode::OPTION_NAME . "[" . $subkey . "]";
            $currentValue = $options[$subkey];
            ?>

            <ul id="easy-pie-mm-bxslider">

                <?php
                $displayIndex = 0;
                $startingIndex = 0;

                $manifests = $this->get_manifests();

                foreach ($manifests as $manifest) {
                    $slidePath = plugins_url("../mini-themes/" . $manifest->key . "/" . $manifest->screenshot, __FILE__);

                    if ($manifest->key == $currentValue) {
                        $startingIndex = $displayIndex;
                    }
                    ?>
                    <li>                                                
                        <img idx="<?php echo $displayIndex; ?>" src="<?php echo $slidePath ?>" onclick="jQuery('#<?php echo $id; ?>').attr('value', '<?php echo $manifest->key; ?>');" />
                    </li>
                    <?php
                    $displayIndex++;
                }
                ?>
                <!--                ... (repeat for every image in the gallery)-->
            </ul>

            <div id="easy-pie-mm-bxpager">
            <!--              <a data-slide-index="0" href=""><img src="/images/thumbs/tree_root.jpg" /></a>
              <a data-slide-index="1" href=""><img src="/images/thumbs/houses.jpg" /></a>
              <a data-slide-index="2" href=""><img src="/images/thumbs/hill_fence.jpg" /></a>-->
                <?php
                $displayIndex = 0;
                foreach ($manifests as $manifest) {
                    $slidePath = plugins_url("../mini-themes/" . $manifest->key . "/" . $manifest->screenshot, __FILE__);
                    ?>

                    <a data-slide-index="<?php echo $displayIndex; ?>" href="">
                        <img src="<?php echo $slidePath ?>" onclick="jQuery('#<?php echo $id; ?>').attr('value', '<?php echo $manifest->key; ?>');" />
                    </a>
                    <?php
                    $displayIndex++;
                }
                ?>
            </div>

            <input displayIndex="<?php echo $startingIndex; ?>" style="visibility:hidden" id="<?php echo $id; ?>" name="<?php echo $optionExpression; ?>" value="<?php echo $currentValue; ?>"/>
            <?php
        }

        public function validate_options($raw_input_array) {

            // Create our array for storing the validated options  
            $output = array();

            $this->scrub_checkbox_value($raw_input_array, 'enabled');
            $this->scrub_checkbox_value($raw_input_array, '503_redirect');

            // Loop through each of the incoming options  
            foreach ($raw_input_array as $key => $value) {

                // Check to see if the current option has a value. If so, process it.  
                if (isset($raw_input_array[$key])) {

                    // Strip all HTML and PHP tags and properly handle quoted strings  
                    //$output[$key] = strip_tags(stripslashes($raw_input_array[$key]));
                    $output[$key] = $raw_input_array[$key];
                }
            }

            return apply_filters(Easy_Pie_Maintenance_Mode::PAGE_KEY, $output, $raw_input_array);
        }

        private function scrub_checkbox_value(&$array, $key) {

            if (!array_key_exists($key, $array)) {

                $array[$key] = false;
            }
        }

        // </editor-fold>

        public function add_to_admin_menu() {

            // RSR TODO: Localize main page title and menu entry
            $page_hook_suffix = add_options_page("Easy Pie Maintenance Mode", "Maintenance Mode", "manage_options", Easy_Pie_Maintenance_Mode::PAGE_KEY, array($this, 'display_options_page'));

            add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'enqueue_scripts'));

            //Apply Styles
            add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'enqueue_styles'));
        }

        // </editor-fold>

        function display_options_page() {

            include(__DIR__ . '/../pages/page-options.php');
        }

        private function get_manifests() {

            $manifest_array = array();
            $path = __DIR__ . "/../mini-themes/";
            $dirs = glob($path . "*", GLOB_ONLYDIR);

            sort($dirs);
            foreach ($dirs as $dir) {

                // rsr put this as a helper function in the manifest class $slidePath = plugins_url("../mini-themes/" . $themeKey . "/screenshot.jpg", __FILE__);

                $manifest_text = file_get_contents($dir . "/manifest.json");

                if ($manifest_text != false) {

                    $manifest = json_decode($manifest_text);

                    array_push($manifest_array, $manifest);
                } else {

                    $this->debug(__("problem reading manifest in ", Easy_Pie_Maintenance_Mode::PLUGIN_SLUG) . $dir . "(" . $dirs . ")");
                }
            }

            return $manifest_array;
        }
    }
}