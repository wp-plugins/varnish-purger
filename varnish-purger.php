<?php
/*
Plugin Name: WordPress Varnish Purger
Plugin URI: https://github.com/ojdupuis/wp-varnish/tree/Support_Extensions
Version: 0.94
Author: <a href="http://github.com/ojdupuis/">Olivier Dupuis</a> for Le Monde interactif
Description: A plugin for purging Varnish cache when content is published or edited. Based on Varsnish plugins by pkhamre, wfelipe, eitch, but heavily forked for extensibility.

Based on Varsnish plugins by pkhamre, wfelipe, eitch, but heavily forked for extensibility.

WordPress Varnish Purges is a plugin that purges new and edited content, it's intent is to be usable for a multi-site installation, 
to support all necessary purge orders for wordpress and to be expandable via extensions.

This plugin purges your varnish cache it's intent is do do it for any action taken. 
This plugin supports what is called extensions that can be added to add special purges for specific wordpress plugins
*/

// List of url already purged
$WPVarnishPurger_url_purged=array();
$WPVarnishPurger_extension_activated=array();

require_once('varnish-purger-core.class.php');

if (!class_exists(WPVarnishPurger)){
   class WPVarnishPurger {
     public $wpv_addr_optname;
     public $wpv_port_optname;
     public $wpv_secret_optname;
     public $wpv_timeout_optname;
     public $wpv_update_pagenavi_optname;
     public $wpv_update_commentnavi_optname;
       
     function init(){
        // Localization init
       add_action('init', array(&$this, 'WPVarnishPurgerLocalization'));
   
       // Add Administration Interface
       add_action('admin_menu', array(&$this, 'VarnishPurgerAdminMenu'));
     }  
     function WPVarnishPurger() {
       global $post;
       
       $this->wpv_addr_optname = "WPVarnishPurger_addr";
       $this->wpv_port_optname = "WPVarnishPurger_port";
       $this->wpv_secret_optname = "WPVarnishPurger_secret";
       $this->wpv_timeout_optname = "WPVarnishPurger_timeout";
       $this->wpv_update_pagenavi_optname = "WPVarnishPurger_update_pagenavi";
       $this->wpv_update_commentnavi_optname = "WPVarnishPurger_update_commentnavi";
       $this->wpv_use_adminport_optname = "WPVarnishPurger_use_adminport";
       
       $wpv_addr_optval = array ("127.0.0.1");
       $wpv_port_optval = array (80);
       $wpv_secret_optval = array ("");
       $wpv_timeout_optval = 5;
       $wpv_update_pagenavi_optval = 1;
       $wpv_update_commentnavi_optval = 1;
       $wpv_use_adminport_optval = 0;
       
       if ( (get_site_option($this->wpv_addr_optname) == FALSE) ) {
         add_site_option($this->wpv_addr_optname, $wpv_addr_optval, '', 'yes');
       }
   
       if ( (get_site_option($this->wpv_port_optname) == FALSE) ) {
         add_site_option($this->wpv_port_optname, $wpv_port_optval, '', 'yes');
       }
   
       if ( (get_site_option($this->wpv_secret_optname) == FALSE) ) {
         add_site_option($this->wpv_secret_optname, $wpv_secret_optval, '', 'yes');
       }
   
       if ( (get_site_option($this->wpv_timeout_optname) == FALSE) ) {
         add_site_option($this->wpv_timeout_optname, $wpv_timeout_optval, '', 'yes');
       }
   
       if ( (get_site_option($this->wpv_update_pagenavi_optname) == FALSE) ) {
         add_site_option($this->wpv_update_pagenavi_optname, $wpv_update_pagenavi_optval, '', 'yes');
       }
   
       if ( (get_site_option($this->wpv_update_commentnavi_optname) == FALSE) ) {
         add_site_option($this->wpv_update_commentnavi_optname, $wpv_update_commentnavi_optval, '', 'yes');
       }
   
       if ( (get_site_option($this->wpv_use_adminport_optname) == FALSE) ) {
         add_site_option($this->wpv_use_adminport_optname, $wpv_use_adminport_optval, '', 'yes');
       }
         
       
         
           
     }
    
     function WPVarnishPurgerLocalization() {
       load_plugin_textdomain('varnishpurger',false,'varnishpurger/lang');
     }
   
     function VarnishPurgerAdminMenu() {
       if (!defined('VARNISH_HIDE_ADMINMENU')||(is_site_admin())) {
         add_options_page(__('Varnish Purger Configuration','varnishpurger'), 'Varnish Purger', 1, 'VarnishPurger', array(&$this, 'VarnishPurgerAdmin'));
       }
     }
   
     // WPVarnishPurgerAdmin - Draw the administration interface.
     function VarnishPurgerAdmin() {
       if ($_SERVER["REQUEST_METHOD"] == "POST") {
          if (current_user_can('administrator')) {
             if (isset($_POST['WPVarnishPurger_admin'])) {
                if (!empty($_POST["$this->wpv_addr_optname"])) {
                   $wpv_addr_optval = $_POST["$this->wpv_addr_optname"];
                   update_site_option($this->wpv_addr_optname, $wpv_addr_optval);
                }
   
                if (!empty($_POST["$this->wpv_port_optname"])) {
                   $wpv_port_optval = $_POST["$this->wpv_port_optname"];
                   update_site_option($this->wpv_port_optname, $wpv_port_optval);
                }
   
                if (!empty($_POST["$this->wpv_secret_optname"])) {
                   $wpv_secret_optval = $_POST["$this->wpv_secret_optname"];
                   update_site_option($this->wpv_secret_optname, $wpv_secret_optval);
                }
   
                if (!empty($_POST["$this->wpv_timeout_optname"])) {
                   $wpv_timeout_optval = $_POST["$this->wpv_timeout_optname"];
                   update_site_option($this->wpv_timeout_optname, $wpv_timeout_optval);
                }
   
                if (!empty($_POST["$this->wpv_update_pagenavi_optname"])) {
                   update_site_option($this->wpv_update_pagenavi_optname, 1);
                } else {
                   update_site_option($this->wpv_update_pagenavi_optname, 0);
                }
   
                if (!empty($_POST["$this->wpv_update_commentnavi_optname"])) {
                   update_site_option($this->wpv_update_commentnavi_optname, 1);
                } else {
                   update_site_option($this->wpv_update_commentnavi_optname, 0);
                }
   
                if (!empty($_POST["$this->wpv_use_adminport_optname"])) {
                   update_site_option($this->wpv_use_adminport_optname, 1);
                } else {
                   update_site_option($this->wpv_use_adminport_optname, 0);
                }
             }
   
             if (isset($_POST['WPVarnishPurger_clear_blog_cache'])){
                $varnishcore=new WPVarnishPurgerCore();
                $varnishcore->WPVarnishPurgerPurgeAll();
                unset($varnishcore);
             }
                
   
             ?><div class="updated"><p><?php echo __('Settings Saved!','wp-varnish-purger' ); ?></p></div><?php
          } else {
             ?><div class="updated"><p><?php echo __('You do not have the privileges.','wp-varnish-purger' ); ?></p></div><?php
          }
       }
   
            $wpv_timeout_optval = get_site_option($this->wpv_timeout_optname);
            $wpv_update_pagenavi_optval = get_site_option($this->wpv_update_pagenavi_optname);
            $wpv_update_commentnavi_optval = get_site_option($this->wpv_update_commentnavi_optname);
            $wpv_use_adminport_optval = get_site_option($this->wpv_use_adminport_optname);
       ?>
       <div class="wrap">
         <script type="text/javascript" src="<?php echo add_site_option('siteurl'); ?>/wp-content/plugins/varnish-purger/varnish-purger.js"></script>
         <h2><?php echo __("WordPress Varnish Purger Administration",'wp-varnish-purger'); ?></h2>
         <h3><?php echo __("IP address and port configuration",'wp-varnish-purger'); ?></h3>
         <h3><?php echo __("WARNING : those settings are nework-wide if multi-site",'wp-varnish-purger'); ?></h3>
         <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
       <?php
             // Can't be edited - already defined in wp-config.php
             global $varnish_servers;
             if (is_array($varnish_servers)) {
                echo "<p>" . __("These values can't be edited since there's a global configuration located in <em>wp-config.php</em>. If you want to change these settings, please update the file or contact the administrator.",'wp-varnish-purger') . "</p>\n";
                // Also, if defined, show the varnish servers configured (VARNISH_SHOWCFG)
                if (defined('VARNISH_SHOWCFG')) {
                   echo "<h3>" . __("Current configuration:",'wp-varnish-purger') . "</h3>\n";
                   echo "<ul>";
                   foreach ($varnish_servers as $server) {
                      list ($host, $port, $secret) = explode(':', $server);
                      echo "<li>" . __("Server: ",'wp-varnish-purger') . $host . "<br/>" . __("Port: ",'wp-varnish-purger') . $port . "</li>";
                   }
                   echo "</ul>";
                }
             } else {
             // If not defined in wp-config.php, use individual configuration.
       ?>
          <!-- <table class="form-table" id="form-table" width=""> -->
          <table class="form-table" id="form-table">
           <tr valign="top">
               <th scope="row"><?php echo __("Varnish Administration IP Address",'wp-varnish-purger'); ?></th>
               <th scope="row"><?php echo __("Varnish Administration Port",'wp-varnish-purger'); ?></th>
               <th scope="row"><?php echo __("Varnish Secret",'wp-varnish-purger'); ?></th>
           </tr>
           <script>
           <?php
             $addrs = get_site_option($this->wpv_addr_optname);
             $ports = get_site_option($this->wpv_port_optname);
             $secrets = get_site_option($this->wpv_secret_optname);
             echo "rowCount = $i\n";
             for ($i = 0; $i < count ($addrs); $i++) {
                // let's center the row creation in one spot, in javascript
                echo "addRow('form-table', $i, '$addrs[$i]', $ports[$i], '$secrets[$i]');\n";
           } ?>
           </script>
   	</table>
   
         <br/>
   
         <table>
           <tr>
             <td colspan="3"><input type="button" class="" name="WPVarnishPurger_admin" value="+" onclick="addRow ('form-table', rowCount)" /> <?php echo __("Add one more server",'wp-varnish-purger'); ?></td>
           </tr>
         </table>
         <?php
            }
         ?>
         <p><?php echo __("Timeout",'wp-varnish-purger'); ?>: <input class="small-text" type="text" name="WPVarnishPurger_timeout" value="<?php echo $wpv_timeout_optval; ?>" /> <?php echo __("seconds",'wp-varnish-purger'); ?></p>
   
         <p><input type="checkbox" name="WPVarnishPurger_use_adminport" value="1" <?php if ($wpv_use_adminport_optval == 1) echo 'checked '?>/> <?php echo __("Use admin port instead of PURGE method.",'wp-varnish-purger'); ?></p>
   
         <p><input type="checkbox" name="WPVarnishPurger_update_pagenavi" value="1" <?php if ($wpv_update_pagenavi_optval == 1) echo 'checked '?>/> <?php echo __("Also purge all page navigation (it will include a bit more load on varnish servers.)",'wp-varnish-purger'); ?></p>
   
         <p><input type="checkbox" name="WPVarnishPurger_update_commentnavi" value="1" <?php if ($wpv_update_commentnavi_optval == 1) echo 'checked '?>/> <?php echo __("Also purge all comment navigation (it will include a bit more load on varnish servers.)",'wp-varnish-purger'); ?></p>
   
         <p class="submit"><input type="submit" class="button-primary" name="WPVarnishPurger_admin" value="<?php echo __("Save Changes",'wp-varnish-purger'); ?>" /></p>
         <p> <h3>For the current blog only:</h3></p>
         <p class="submit"><input type="submit" class="button-primary" name="WPVarnishPurger_clear_blog_cache" value="<?php echo __("Purge this blog's cache",'wp-varnish-purger'); ?>" /> <?php echo __("Use only if necessary, and carefully as this will include a bit more load on varnish servers.",'wp-varnish-purger'); ?></p>
         
         </form>
       </div>
     <?php
     }
   
     
   
     function WPAuth($challenge, $secret) {
       $ctx = hash_init('sha256');
       hash_update($ctx, $challenge);
       hash_update($ctx, "\n");
       hash_update($ctx, $secret . "\n");
       hash_update($ctx, $challenge);
       hash_update($ctx, "\n");
       $sha256 = hash_final($ctx);
   
       return $sha256;
     }
     
     function load_plugins_extensions(){
        $extension_root=WP_PLUGIN_DIR."/varnish-purger/plugins";
        if (is_dir($extension_root)){
           $handledir=opendir($extension_root);
           while ($file = readdir($handledir)){
              if (preg_match("/\.php$/",$file)){
                 require_once("$extension_root/$file");
              }
           }
        }
        do_action('WPVarnishPurger_init_extensions');
     }
                     
   }
}
if (!isset($WPVarnishPurger)){
   $WPVarnishPurger = & new WPVarnishPurger();
   $WPVarnishPurger->init();
   // Initialize main object
   // load basic purges
   require_once('varnish-purger-core.class.php');
   // load extension purges
   $WPVarnishPurger->load_plugins_extensions();
}

?>
