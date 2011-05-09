<?php
/*
 * Support for purges for the wptouch plugins
 * Url http://wordpress.org/extend/plugins/wptouch/
 * Version: 1.9.25
 */


class WPVarnishPurger_WPTouch extends WPVarnishPurgerAbstract {
   
   function mustActivate(){
      return $this->is_plugin_active('wptouch/wptouch.php');       
   }
     
   function addActions(){
      remove_all_actions( 'WPVarnishPurger_purgeobject',99);
      add_action('WPVarnishPurger_purgeobject', array(&$this, 'WPtouchPurgeObject'), 99);             
   }
   
   // We need to purge the normal and the mobile version of the page
   function WPtouchPurgeObject($wpv_url){      
      foreach (array('Android','Windows NT 6.0') as $useragent){
         $this->_WPVarnishPurgerPurgeObject($wpv_url,$useragent);
      }
   }
}

$WPVarnishPurgerWPTouch = & new WPVarnishPurger_WPTouch();