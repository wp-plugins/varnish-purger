<?php
/*
 * Support for purges for wp-socializer plugin 
 * Url http://www.aakashweb.com/wordpress-plugins/wp-socializer/
 * Version 1.0
 */


class WPVarnish_WPSocializer extends WPVarnishCore {
   
   function mustActivate(){
      return $this->is_plugin_active('wp-socializer/wp-socializer.php');       
   }
     
   function addActions(){
        add_action('update_option_wpsr_addthis_data', array(&$this, 'WPVarnishPurgeAll'), 99);
        add_action('update_option_wpsr_buzz_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_retweet_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_digg_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_facebook_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_socialbt_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_custom_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_template1_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_template2_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_settings_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_buzz_data', array(&$this, 'WPVarnishPurgeAll'), 99);     
        add_action('update_option_wpsr_active', array(&$this, 'WPVarnishPurgeAll'), 99);             
   }
   
}

$wpvarnishWPSocializer = & new WPVarnish_WPSocializer();