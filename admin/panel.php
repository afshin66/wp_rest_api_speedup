<?php
  class speedup_admin_panel 
  {    
        // you can add special plugins name to this array if you want a special plugin doesn't show in plugin list on setting panel for inect code
        public static $hiddenPlugins = ['rest-speedup'] ; 
        public static $pluginName = 'speedup';

        /**
         * self::__construct()
         * 
         * @return
         */
        function __construct() 
        {         
           $this->plugin = new stdClass ;    
           //add rest speed up to menue     
           add_submenu_page( 'options-general.php','Speed Up REST API','Speed Up REST API' , 'manage_options', 'restspeedup', [ $this, 'admin_panel'] ) ;
        }


        /**
         *  self::admin_panel()
         *  Add styles and javastcripts liberary to plugin
         *  after than check if submited inject code
         * @return
         */
        public function admin_panel()
        {
            wp_enqueue_style('admin-styles', PLUGIN_URL .'/admin/css/jquery-ui.css');
            wp_enqueue_style('admin-styles', PLUGIN_URL.'/admin/css/style.css');
            wp_enqueue_script('custom-script', PLUGIN_URL . '/admin/js/tabs.js',array('jquery'));
            wp_enqueue_script('jquery-ui-tabs');
        

            // only admin user can access this page
            if ( ! current_user_can( 'administrator' ) )
            {
                   echo '<p>' . __( 'Sorry, you are not allowed to access this page.', 'speedup' ) . '</p>' ;
                return ;
            }

            // Save Settings
               
            if(isset($_POST['submit']) && $_POST['submit'] == "inject")
            {              
                          
                self::inject_code_to_themes();
                self::inject_code_to_plugins();
                //Set successful message
                $this->message = __( 'Settings Saved.', 'damsun' ) ;
            }  

            include_once ( 'view.php' ) ;
        }


        /**
         *  self::inject_code_to_plugins()
         *  check if plugin selcted  then  inject code to plugin          
         * @return
         */
        public static function inject_code_to_plugins()
        {
            $all_actived_plugins = array_unique(wp_get_active_and_valid_plugins());  

            foreach ($all_actived_plugins  as $plugin ) 
            {
                $plugin_name = basename(dirname($plugin));
                
                if(isset($_REQUEST["plugins"]) && in_array($plugin_name,$_REQUEST["plugins"]))
                {  
                   self::inject_code($plugin);         
                }
                else
                { 
                    self::remove_code($plugin);  
                }   
            }        
        }

        /**
         *  self::inject_code_to_plugins()
         *  check if theme selcted  then  inject code to theme             
         * @return
         */
        public static function inject_code_to_themes()
        { 
            $all_themes = wp_get_themes();       

            foreach ($all_themes as $key => $theme) 
            {  
                if(isset($_REQUEST["themes"]) && in_array($key,$_REQUEST["themes"]))
                {
                    $template = $theme->theme_root. '/' . $key . '/functions.php';
                    self::inject_code($template);         
                }
                else
                {                    
                    $template = $theme->theme_root. '/' . $key . '/functions.php';             
                    self::remove_code($template);  
                }
            } 
        }

        /**
         *  self::inject_code()
         *  this function injectes speed up code to plugins or themes           
         * @return
         */
        public static function inject_code($file)
        {
            if(file_exists($file))
            {
                $file_content = file_get_contents( $file );
                $str_to_insert = ' if (strpos($_SERVER["REQUEST_URI"], "/wp-json/")!== false) {return;} ';
            
                if(!strpos( $file_content,$str_to_insert))
                {
                    $file_content2 = substr_replace( $file_content, $str_to_insert, strpos( $file_content, '*/') + 2 , 0);
                    file_put_contents($file,substr_replace( $file_content, $str_to_insert, strpos( $file_content, '*/') + 2 , 0));            
                }   
            }
        }

         /**
         *  self::remove_code()
         *  this function removes speed up code from plugins or themes           
         * @return
         */
        public static function remove_code($file)
        {
            if(file_exists($file))
            {
                $file_content = file_get_contents( $file );
                $str_to_insert = ' if (strpos($_SERVER["REQUEST_URI"], "/wp-json/")!== false) {return;} ';
                    
                if(strpos( $file_content,$str_to_insert))
                {
                    file_put_contents($file, str_replace($str_to_insert,"",$file_content));              
                }   
            }
        }

         /**
         *  self::show_plugins()
         *  show plugins list for select to inject speed up code.          
         * @return
         */
        public static function show_plugins()
        {       
            if( ! function_exists('get_plugin_data') ){
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }
            $all_actived_plugins = array_unique(wp_get_active_and_valid_plugins()); 

            echo '
            <dl id="checkboxes">
            <dt>List of plugins</dt>'; 
            
            foreach ( $all_actived_plugins as $plugin )
            {   
                
                $plugin_data = get_plugin_data( $plugin );
                $plugin_full_name = sprintf("%s",$plugin_data['Title']);

                $plugin_name = basename(dirname($plugin));
                if(!in_array( $plugin_name ,self::$hiddenPlugins ))
                {      
                    $str_to_insert = ' if (strpos($_SERVER["REQUEST_URI"], "/wp-json/")!== false) {return;} ';
    
                    $checked = "";
                    $file_content = file_get_contents($plugin);

                    if(strpos( $file_content,$str_to_insert))
                    {
                        $checked = ' checked = "checked" ';   
                    }     
                    
                    echo sprintf('<dd><input type="checkbox" id="%s" name="plugins[]" value="%s" %s /><label for="%s">%s</label></dd>',$plugin_name,$plugin_name,$checked,$plugin_full_name,$plugin_name);
                }        
                               
            }
            echo '
            </dl>
            ';
        }

         /**
         *  self::show_themes()
         *  show themes list for select to inject speed up code.          
         * @return
         */
        public static function show_themes()
        {
            $all_themes = wp_get_themes();
            echo '
            <dl id="checkboxes">
            <dt>List of themes</dt>';

            foreach ($all_themes as $key => $theme) 
            {
                $template = $theme->theme_root. '/' . $key . '/functions.php';
                $file_content = file_get_contents($template);

                $str_to_insert = ' if (strpos($_SERVER["REQUEST_URI"], "/wp-json/")!== false) {return;} ';
      
                if(self::check_is_current_installed_theme ($key))
                {
                   $label = $key . '(main)';
                }
                else
                {
                    $label = $key ;
                }

                $checked = "";

                if(strpos( $file_content,$str_to_insert))
                { 
                    $checked = ' checked = "checked" ';   
                }    

                echo sprintf('<dd><input type="checkbox" id="%s" name="themes[]" value="%s" %s /><label for="%s">%s</label></dd>',$key,$key,$checked,$key,$label);
            }

            echo '
            </dl>
            ';

        }

        /**
         *  self::check_is_current_installed_theme()
         *  check themeName value is equal with current installed theme          
         * @return
         */

        public static function check_is_current_installed_theme ($themeName)
        {
            return (strtolower(get_current_theme()) == $themeName) ? true : false;
        }

    }