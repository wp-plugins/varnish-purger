<?php
/*
 * Support for purges for the ajax calendar widget 
 * Url http://urbangiraffe.com/plugins/ajax-calendar/
 * Version: 2.5.1
 */


class WPVarnish_WPAjaxCalendar extends WPVarnishAbstract {
   
   function mustActivate(){
      return $this->is_plugin_active('ajax-calendar/ajax-calendar.php');       
   }
     
   function addActions(){
        add_action('edit_post', array(&$this, 'WPVarnishPurgeAjaxCalendar'), 99);     
   }
   
   // Purge Ajax Calendar for a post
   function WPVarnishPurgeAjaxCalendar($wpv_postid){             
        $month=str_replace(get_bloginfo('wpurl'),"",get_month_link(get_post_time('Y',false,$wpv_postid), get_post_time('m',true,$wpv_postid)));
        $this->WPVarnishPurgeObject($month.'?ajax=true');     
  }
}

$wpvarnishAjaxCalendar = & new WPVarnish_WPAjaxCalendar();