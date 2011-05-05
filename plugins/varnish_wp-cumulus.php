<?php
/*
 * Support for wp-cumulus widget
 * Url http://www.roytanck.com/2008/03/06/wordpress-plugin-wp-cumulus-flash-based-tag-cloud/
 * Version 1.23
 */
class WPVarnish_WPCumulus extends WPVarnishCore {
   
   function mustActivate(){
      return $this->is_plugin_active('wp-cumulus/wp-cumulus'); 
   }      
  
   function addActions(){      
        add_action('wpcumulus_widget', array(&$this, 'WPVarnishPurgeAll'), 99);     
   }
   
}
$wpvarnishCumulus = & new WPVarnish_WPCumulus();

