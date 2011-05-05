<?php

class WPVarnishCore extends WPVarnishAbstract{
   
   function mustActivate(){
      return true;
   }
   
   function addActions(){
    global $post;
            
    // When posts/pages are published, edited or deleted
    add_action('edit_post', array(&$this, 'WPVarnishPurgePost'), 99);
    add_action('edit_post', array(&$this, 'WPVarnishPurgePostDependencies'), 99);

    // When comments are made, edited or deleted
    add_action('comment_post', array(&$this, 'WPVarnishPurgePostComments'),99);
    add_action('edit_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
    add_action('trashed_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
    add_action('untrashed_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
    add_action('deleted_comment', array(&$this, 'WPVarnishPurgePostComments'),99);

    // When posts or pages are deleted
    add_action('deleted_post', array(&$this, 'WPVarnishPurgePost'), 99);
    add_action('deleted_post', array(&$this, 'WPVarnishPurgeCommonObjects'), 99);
    
    // When Theme is changed
    add_action('switch_theme',array(&$this, 'WPVarnishPurgeAll'), 99);

    // When Widgets are added or removed
    add_action('update_option_sidebars_widgets',array(&$this, 'WPVarnishPurgeAll'), 99);

    // When widget option is saved
    add_action('widgets.php',array(&$this, 'WPVarnishPurgeAll'), 99);

    // modification du theme courant
    $theme = get_option( 'stylesheet' );
    add_action("update_option_theme_mods_$theme",array(&$this, 'WPVarnishPurgeAll'), 99);
    
    // Links
    add_action("deleted_link",array(&$this, 'WPVarnishPurgeLink'), 99);
    add_action("edit_link",array(&$this, 'WPVarnishPurgeLink'), 99);
    add_action("add_link",array(&$this, 'WPVarnishPurgeLink'), 99);
    
    //Post Categories
    add_action("edit_category",array(&$this, 'WPVarnishPurgeCategory'), 99);       
    //Link Categories
    add_action("edit_link_category",array(&$this, 'WPVarnishPurgeLinkCategory'), 99);
    //Tag categories
    add_action("edit_post_tag",array(&$this, 'WPVarnishPurgeTagCategory'), 99);
  }     
  // WPVarnishPurgeAll - Using a regex, clear all blog cache. Use carefully.
  function WPVarnishPurgeAll() {
    $this->WPVarnishPurgeObject('/(.*)');
  }

  // WPVarnishPurgePost - Takes a post id (number) as an argument and generates
  // the location path to the object that will be purged based on the permalink.
  function WPVarnishPurgePost($wpv_postid) {
    $wpv_url = get_permalink($wpv_postid);
    $wpv_permalink = str_replace(get_bloginfo('wpurl'),"",$wpv_url);

    $this->WPVarnishPurgeObject($wpv_permalink);
  }

  // WPVarnishPurgePostComments - Purge all comments pages from a post
  function WPVarnishPurgePostComments($wpv_commentid) {
    $comment = get_comment($wpv_commentid);
    $wpv_commentapproved = $comment->comment_approved;

    // If approved or deleting...
    if ($wpv_commentapproved == 1 || $wpv_commentapproved == 'trash') {
       $wpv_postid = $comment->comment_post_ID;

       // Popup comments
       $this->WPVarnishPurgeObject('/\\\?comments_popup=' . $wpv_postid);

       // Also purges comments navigation
       if (get_option($this->wpv_update_commentnavi_optname) == 1) {
          $this->WPVarnishPurgeObject('/\\\?comments_popup=' . $wpv_postid . '&(.*)');
       }

    }
  }
  /*
   * All purge orders to be sent when a post is modified
   */
  function WPVarnishPurgePostDependencies($wpv_postid){
     $this->WPVarnishPurgeCommonObjects($wpv_postid);
     //Purge categories
     $this->WPVarnishPurgeCategories($wpv_postid);
     // Purges Archives
     $this->WPVarnishPurgeArchives($wpv_postid);
     //Purge Tags
     $this->WPVarnishPurgeTags($wpv_postid);
    

  }  
  
  // Purge category pages for a post
  function WPVarnishPurgeCategories($wpv_postid){
    $list=get_the_category($wpv_postid);
    foreach($list as $categoryObject){
       $this->WPVarnishPurgeCategory($categoryObject->cat_ID);
    }
  }
  
  // Purge a specific post category
  function WPVarnishPurgeCategory($catid){
     if (  is_active_widget(false,false,'categories') ){
        $this->WPVarnishPurgeAll();
     } else {
        $this->WPVarnishPurgeObject(str_replace(get_bloginfo('wpurl'),"",get_category_link($catid)));   
     }                    
  }

  // Purge a specific link category
  function WPVarnishPurgeLinkCategory($catid){
     if (is_active_widget(false,false,'links')){
        $this->WPVarnishPurgeAll();
     }
  }
  
  // Purge a specific post tag
  function WPVarnishPurgeTagCategory($catid){
        $this->WPVarnishPurgeObject(str_replace(get_bloginfo('wpurl'),"",get_tag_link($catid)));                       
  }
  
    // Purge tag pages for a post
  function WPVarnishPurgeTags($wpv_postid){
    $list=get_the_tags($wpv_postid);
    
    foreach($list as $tagObject){
       $this->WPVarnishPurgeTagCategory($tagObject->term_id);
    }
  }
  
  // Purge archives pages for a post
  function WPVarnishPurgeArchives($wpv_postid){
    $day=str_replace(get_bloginfo('wpurl'),"",get_day_link(get_post_time('Y',false,$wpv_postid), get_post_time('m',true,$wpv_postid),get_post_time('d',true,$wpv_postid)));
    $month=str_replace(get_bloginfo('wpurl'),"",get_month_link(get_post_time('Y',false,$wpv_postid), get_post_time('m',true,$wpv_postid)));
    $year=str_replace(get_bloginfo('wpurl'),"",get_year_link(get_post_time('Y',false,$wpv_postid)));

    $this->WPVarnishPurgeObject($day);
    $this->WPVarnishPurgeObject($month);
    $this->WPVarnishPurgeObject($year);

  }

 
  // Purge when links modified/edited/deleted
  function WPVarnishPurgeLink($linkId){
     // Purge all blog if widget links used in any sidebar
     if (  is_active_widget(false,false,'links')){
        $this->WPVarnishPurgeAll();      
     }
  } 
   
  function WPVarnishPurgeCommonObjects() {
    $this->WPVarnishPurgeObject("/");
    $this->WPVarnishPurgeObject("/feed/");
    $this->WPVarnishPurgeObject("/feed/atom/");    

    // Also purges page navigation
    if (get_option($this->wpv_update_pagenavi_optname) == 1) {
       $this->WPVarnishPurgeObject("/page/(.*)");
    }
  }
  
}

$wpvarnishCore = & new WPVarnishCore();