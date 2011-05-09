<?php
/*
 * Support for Wordress Recent Comments widget
 * Url http://111waystomakemoney.com/wordpress-recent-comments/
 * Version 4.14.01
 */
class WPVarnishPurger_WordpressRecentComments extends WPVarnishPurgerCore {
   
   function mustActivate(){      
      return $this->is_plugin_active('Wordpress-Recent-Comments/wordpress-recent-comments.php'); 
   }      
  
   function addActions(){     
        add_action('update_option_widget_recentcomments', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('update_option_wp_recentcomments_options', array(&$this, 'WPVarnishPurgerPurgeAll'), 99); 
   }
   
}
$WPVarnishPurger_WordpressRecentComments = & new WPVarnishPurger_WordpressRecentComments();

