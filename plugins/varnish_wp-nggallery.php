<?php
/*
 * Support NextGen Gallery plugin
 * Url http://alexrabe.de/?page_id=80
 * Version 1.7.4
 */
class WPVarnishPurger_NGGallery extends WPVarnishPurgerCore {
   
   function mustActivate(){
      return $this->is_plugin_active('nextgen-gallery/nggallery.php'); 
   }      
  
   function addActions(){      
        add_action('update_option_ngg_options', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        /**
         * At the moment we are a bit rough a purge all the blog at each update regardless of the kind of update
         */
        add_action('ngg_ajax_image_save', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('ngg_manage_gallery_custom_column', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('ngg_update_options_page', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('ngg_update_gallery', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('ngg_created_new_gallery', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('ngg_added_new_image', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('ngg_after_new_images_added  ', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('ngg_edit_album_settings', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('ngg_manage_gallery_settings', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
        add_action('ngg_manage_image_custom_column', array(&$this, 'WPVarnishPurgerPurgeAll'), 99);             
   }
   
}
$WPVarnishPurgerNGGallery = & new WPVarnishPurger_NGGallery();

