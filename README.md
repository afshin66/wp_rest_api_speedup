# wp_rest_api_speedup
Speed Up Wordpress REST API

I create this plugin to speed up WordPress REST API. By checking the logs when request the WordPress REST API, I understanded that a large amount of files related to plugins and templates that do not need to be loaded at the time of request are loaded. With the help of this plugin, I could to control the installed plugins and templates that loaded unnecessarily when calling the WordPress REST API. 
To get started with My Plugin, just installes it and go to its settings page and check the plugin or templates that you want to disable  when calling the WordPress REST API.
I hope this plugin is useful for you