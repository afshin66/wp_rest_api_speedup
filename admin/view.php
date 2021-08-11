<div class="wrap">


  <h2><?php echo __('Speed Up', 'speedup'); ?></h2>
    <?php
    if ( isset( $this->message ) ) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>
        <?php
    }
    if ( isset( $this->errorMessage ) ) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>
        <?php
    }
    ?>


    <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
              
                <div id="post-body-content">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox">                       
                            <div class="inside">
                            <form action="options-general.php?page=rest-speedup" method="post">
                                <div id="tabs">
                                    <ul>
                                        <li><a href="#plugins">Plugins</a></li>
                                        <li><a href="#themes">Themes</a></li> 
                                    </ul>

                                    <div id="plugins">      
                                         <?php 
                                         self::show_plugins();
                                         ?>     

                                    </div>

                                    <div id="themes">      
                                        <?php 
                                         self::show_themes();
                                         ?>         
                                    </div>

                                </div>
                                <hr/>
                                
                                <input name="submit" type="submit" name="Submit" class="button button-default" value="inject" />                             
                                <?php wp_nonce_field(self::$pluginName,  'speed_up_nonce' ); ?>
                                </form>
                            </div>
                        </div>                       
                    </div>                  
                </div>
   
                <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">                       
                            <div class="inside" style=" text-align: center;">
                                <p>
                                I create this plugin to speed up WordPress REST API. By checking the logs when request the WordPress REST API, I understanded that a large amount of files related to plugins and templates that do not need to be loaded at the time of request are loaded. With the help of this plugin, I could to control the installed plugins and templates that loaded unnecessarily when calling the WordPress REST API. 
                                To get started with My Plugin, just installes it and go to its settings page and check the plugin or templates that you want to disable  when calling the WordPress REST API.
                                I hope this plugin is useful for you
                                </p>
                            </div>
                        </div>
                </div>
              
            </div>
        </div>

    </div>