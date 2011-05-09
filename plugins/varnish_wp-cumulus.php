<?php
/*
 * Support for wp-cumulus widget
 * Url http://www.roytanck.com/2008/03/06/wordpress-plugin-wp-cumulus-flash-based-tag-cloud/
 * Version 1.23
 */
class WPVarnishPurger_WPCumulus extends WPVarnishPurgerCore {
   
   function mustActivate(){
      return $this->is_plugin_active('wp-cumulus/wp-cumulus'); 
   }      
  
   function addActions(){      
        add_action('wpcumulus_widget', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
   }
   
}
$WPVarnishPurgerCumulus = & new WPVarnishPurger_WPCumulus();

