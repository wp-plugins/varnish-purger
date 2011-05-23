<?php
require_once('varnish-purger-abstract.class.php');

class WPVarnishPurgerCore extends WPVarnishPurgerAbstract{
   
   function mustActivate(){
      return true;
   }
   
   function addActions(){
    global $post;
            
    // When posts/pages are published, edited or deleted
    add_action('edit_post', array(&$this, 'WPVarnishPurgerPurgePost'), 99);
    add_action('edit_post', array(&$this, 'WPVarnishPurgerPurgePostDependencies'), 99);
    add_action('edit_post', array(&$this, 'WPVarnishPurgerPurgePostDependencies'), 99);
    add_action('transition_post_status', array(&$this,'WPVarnishPurgerPurgePostStatus'),99);

    // When comments are made, edited or deleted
    add_action('comment_post', array(&$this, 'WPVarnishPurgerPurgePostComments'),99);
    add_action('edit_comment', array(&$this, 'WPVarnishPurgerPurgePostComments'),99);
    add_action('trashed_comment', array(&$this, 'WPVarnishPurgerPurgePostComments'),99);
    add_action('untrashed_comment', array(&$this, 'WPVarnishPurgerPurgePostComments'),99);
    add_action('deleted_comment', array(&$this, 'WPVarnishPurgerPurgePostComments'),99);

    // When posts or pages are deleted
    add_action('deleted_post', array(&$this, 'WPVarnishPurgerPurgePost'), 99);
    add_action('deleted_post', array(&$this, 'WPVarnishPurgerPurgeCommonObjects'), 99);
    
    // When Theme is changed
    add_action('switch_theme',array(&$this, 'WPVarnishPurgerPurgeAll'), 99);

    // When Widgets are added or removed
    add_action('update_option_sidebars_widgets',array(&$this, 'WPVarnishPurgerPurgeAll'), 99);

    // When widget option is saved
    add_action('widgets.php',array(&$this, 'WPVarnishPurgerPurgeAll'), 99);

    // modification du theme courant
    $theme = get_option( 'stylesheet' );
    add_action("update_option_theme_mods_$theme",array(&$this, 'WPVarnishPurgerPurgeAll'), 99);
    
    // Links
    add_action("deleted_link",array(&$this, 'WPVarnishPurgerPurgeLink'), 99);
    add_action("edit_link",array(&$this, 'WPVarnishPurgerPurgeLink'), 99);
    add_action("add_link",array(&$this, 'WPVarnishPurgerPurgeLink'), 99);
    
    //Post Categories
    add_action("edit_category",array(&$this, 'WPVarnishPurgerPurgeCategory'), 99);       
    //Link Categories
    add_action("edit_link_category",array(&$this, 'WPVarnishPurgerPurgeLinkCategory'), 99);
    //Tag categories
    add_action("edit_post_tag",array(&$this, 'WPVarnishPurgerPurgeTagCategory'), 99);
  }     
  // WPVarnishPurgerPurgeAll - Using a regex, clear all blog cache. Use carefully.
  function WPVarnishPurgerPurgeAll() {
    $this->WPVarnishPurgerPurgeObject('/(.*)');
  }

  // WPVarnishPurgerPurgePost - Takes a post id (number) as an argument and generates
  // the location path to the object that will be purged based on the permalink.
  function WPVarnishPurgerPurgePost($wpv_postid) {
    $wpv_url = get_permalink($wpv_postid);
    $wpv_permalink = str_replace(get_bloginfo('wpurl'),"",$wpv_url);

    $this->WPVarnishPurgerPurgeObject($wpv_permalink);
  }

  // WPVarnishPurgerPurgePostStatus - input based on http://core.trac.wordpress.org/browser/trunk/wp-includes/post.php#L4461   
  function WPVarnishPurgerPurgePostStatus($new_status, $old_status, $post)  {
    $this->WPVarnishPurgerPurgePost($post->ID);
    $this->WPVarnishPurgerPurgePostDependencies($post->ID);
  }

  // WPVarnishPurgerPurgePostComments - Purge all comments pages from a post
  function WPVarnishPurgerPurgePostComments($wpv_commentid) {
    $comment = get_comment($wpv_commentid);
    $wpv_commentapproved = $comment->comment_approved;

    // If approved or deleting...
    if ($wpv_commentapproved == 1 || $wpv_commentapproved == 'trash') {
       $wpv_postid = $comment->comment_post_ID;

       // Popup comments
       $this->WPVarnishPurgerPurgeObject('/\\\?comments_popup=' . $wpv_postid);

       // Also purges comments navigation
       if (get_site_option($this->wpv_update_commentnavi_optname) == 1) {
          $this->WPVarnishPurgerPurgeObject('/\\\?comments_popup=' . $wpv_postid . '&(.*)');
       }

    }
  }
  /*
   * All purge orders to be sent when a post is modified
   */
  function WPVarnishPurgerPurgePostDependencies($wpv_postid){
     $this->WPVarnishPurgerPurgeCommonObjects($wpv_postid);
     //Purge categories
     $this->WPVarnishPurgerPurgeCategories($wpv_postid);
     // Purges Archives
     $this->WPVarnishPurgerPurgeArchives($wpv_postid);
     //Purge Tags
     $this->WPVarnishPurgerPurgeTags($wpv_postid);
    

  }  
  
  // Purge category pages for a post
  function WPVarnishPurgerPurgeCategories($wpv_postid){
    $list=get_the_category($wpv_postid);
    foreach($list as $categoryObject){
       $this->WPVarnishPurgerPurgeCategory($categoryObject->cat_ID);
    }
  }
  
  // Purge a specific post category
  function WPVarnishPurgerPurgeCategory($catid){
     if (  is_active_widget(false,false,'categories') ){
        $this->WPVarnishPurgerPurgeAll();
     } else {
        $this->WPVarnishPurgerPurgeObject(str_replace(get_bloginfo('wpurl'),"",get_category_link($catid)));   
     }                    
  }

  // Purge a specific link category
  function WPVarnishPurgerPurgeLinkCategory($catid){
     if (is_active_widget(false,false,'links')){
        $this->WPVarnishPurgerPurgeAll();
     }
  }
  
  // Purge a specific post tag
  function WPVarnishPurgerPurgeTagCategory($catid){
        $this->WPVarnishPurgerPurgeObject(str_replace(get_bloginfo('wpurl'),"",get_tag_link($catid)));                       
  }
  
    // Purge tag pages for a post
  function WPVarnishPurgerPurgeTags($wpv_postid){
    $list=get_the_tags($wpv_postid);
    
    foreach($list as $tagObject){
       $this->WPVarnishPurgerPurgeTagCategory($tagObject->term_id);
    }
  }
  
  // Purge archives pages for a post
  function WPVarnishPurgerPurgeArchives($wpv_postid){
    $day=str_replace(get_bloginfo('wpurl'),"",get_day_link(get_post_time('Y',false,$wpv_postid), get_post_time('m',true,$wpv_postid),get_post_time('d',true,$wpv_postid)));
    $month=str_replace(get_bloginfo('wpurl'),"",get_month_link(get_post_time('Y',false,$wpv_postid), get_post_time('m',true,$wpv_postid)));
    $year=str_replace(get_bloginfo('wpurl'),"",get_year_link(get_post_time('Y',false,$wpv_postid)));

    $this->WPVarnishPurgerPurgeObject($day);
    $this->WPVarnishPurgerPurgeObject($month);
    $this->WPVarnishPurgerPurgeObject($year);

  }

 
  // Purge when links modified/edited/deleted
  function WPVarnishPurgerPurgeLink($linkId){
     // Purge all blog if widget links used in any sidebar
     if (  is_active_widget(false,false,'links')){
        $this->WPVarnishPurgerPurgeAll();      
     }
  } 
   
  function WPVarnishPurgerPurgeCommonObjects() {
    $this->WPVarnishPurgerPurgeObject("/");
    $this->WPVarnishPurgerPurgeObject("/feed/");
    $this->WPVarnishPurgerPurgeObject("/feed/atom/");    

    // Also purges page navigation
    if (get_site_option($this->wpv_update_pagenavi_optname) == 1) {
       $this->WPVarnishPurgerPurgeObject("/page/(.*)");
    }
  }
  
}

$WPVarnishPurgerCore = & new WPVarnishPurgerCore();