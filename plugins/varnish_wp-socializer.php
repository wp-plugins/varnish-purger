<?php
/*
 * Support for purges for wp-socializer plugin 
 * Url http://www.aakashweb.com/wordpress-plugins/wp-socializer/
 * Version 1.0
 */


class WPVarnishPurger_WPSocializer extends WPVarnishPurgerCore {
   
   function mustActivate(){
      return $this->is_plugin_active('wp-socializer/wp-socializer.php');       
   }
     
   function addActions(){
        add_action('update_option_wpsr_addthis_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('update_option_wpsr_buzz_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_retweet_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_digg_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_facebook_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_socialbt_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_custom_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_template1_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_template2_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_settings_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_buzz_data', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);     
        add_action('update_option_wpsr_active', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);             
   }
   
}

$WPVarnishPurgerWPSocializer = & new WPVarnishPurger_WPSocializer();