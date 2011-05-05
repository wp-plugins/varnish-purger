<?php
/*
 * Support for Yahoo Media Player plugin
 * Url http://www.8bitkid.com/downloads/yahoo-media-player-plugin/
 * Version 1.3
 */
class WPVarnish_YahooMediaPlayer extends WPVarnishCore {
   
   function mustActivate(){
      return $this->is_plugin_active('yahoo-media-player/yahoo_media_player_plugin.php'); 
   }      
  
   function addActions(){      
        add_action('update_option_location', array(&$this, 'WPVarnishPurgeAll'), 99);
        add_action('update_option_choice', array(&$this, 'WPVarnishPurgeAll'), 99);
        add_action('update_option_amazon_id', array(&$this, 'WPVarnishPurgeAll'), 99);
        add_action('update_option_auto_choice', array(&$this, 'WPVarnishPurgeAll'), 99);     
   }
   
}
$wpvarnishYahooMediaPlayer = & new WPVarnish_YahooMediaPlayer();

