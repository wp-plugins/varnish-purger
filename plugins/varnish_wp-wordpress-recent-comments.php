<?php
/*
 * Support for Wordress Recent Comments widget
 * Url http://111waystomakemoney.com/wordpress-recent-comments/
 * Version 4.14.01
 */
class WPVarnish_WordpressRecentComments extends WPVarnishCore {
   
   function mustActivate(){      
      return $this->is_plugin_active('Wordpress-Recent-Comments/wordpress-recent-comments.php'); 
   }      
  
   function addActions(){     
        add_action('update_option_widget_recentcomments', array(&$this, 'WPVarnishPurgeAll'), 99);
        add_action('update_option_wp_recentcomments_options', array(&$this, 'WPVarnishPurgeAll'), 99); 
   }
   
}
$wpvarnish_WordpressRecentComments = & new WPVarnish_WordpressRecentComments();

