<?php

/*
 * Abstract- class containing method related to varnish purging only 
 */
class WPVarnishPurgerAbstract {
  public $wpv_addr_optname;
  public $wpv_port_optname;
  public $wpv_secret_optname;
  public $wpv_timeout_optname;
  public $wpv_update_pagenavi_optname;
  public $wpv_update_commentnavi_optname;
    
  function WPVarnishPurgerAbstract() {
    global $post;

    $this->wpv_addr_optname = "WPVarnishPurger_addr";
    $this->wpv_port_optname = "WPVarnishPurger_port";
    $this->wpv_secret_optname = "WPVarnishPurger_secret";
    $this->wpv_timeout_optname = "WPVarnishPurger_timeout";
    $this->wpv_update_pagenavi_optname = "WPVarnishPurger_update_pagenavi";
    $this->wpv_update_commentnavi_optname = "WPVarnishPurger_update_commentnavi";
    $this->wpv_use_adminport_optname = "WPVarnishPurger_use_adminport";       
    
    add_action('WPVarnishPurger_purgeobject',array($this,'_WPVarnishPurgerPurgeObject'),99);
    add_action('plugins_loaded', array(&$this, 'activateExtension'), 99);
            
  }
  
  // Wrapper for _WPVarnishPurgerPurgeObject to be able to overload it
  function WPVarnishPurgerPurgeObject($wpv_url) {
     do_action('WPVarnishPurger_purgeobject',$wpv_url,null);         
  }  
  // WPVarnishPurgerPurgeObject - Takes a location as an argument and purges this object
  // from the varnish cache.
  function _WPVarnishPurgerPurgeObject($wpv_url,$useragent=null) {
    global $varnish_servers;
    // list of urls already purged
    global $WPVarnishPurger_url_purged;

    // if url already purged or purgeAll already sent
    if (in_array($wpv_url.$useragent,$WPVarnishPurger_url_purged)){   
       return;
    }
    
    if (is_array($varnish_servers)) {
       foreach ($varnish_servers as $server) {
          list ($host, $port) = explode(':', $server);
          $wpv_purgeaddr[] = $host;
          $wpv_purgeport[] = $port;
       }
    } else {
       $wpv_purgeaddr = get_site_option($this->wpv_addr_optname);
       $wpv_purgeport = get_site_option($this->wpv_port_optname);
       $wpv_secret = get_site_option($this->wpv_secret_optname);
    }

    $wpv_timeout = get_site_option($this->wpv_timeout_optname);
    $wpv_use_adminport = get_site_option($this->wpv_use_adminport_optname);

    $wpv_wpurl = get_bloginfo('wpurl');
    $wpv_replace_wpurl = '/^http:\/\/([^\/]+)(.*)/i';
    $wpv_host = preg_replace($wpv_replace_wpurl, "$1", $wpv_wpurl);
    $wpv_blogaddr = preg_replace($wpv_replace_wpurl, "$2", $wpv_wpurl);
    $wpv_url = $wpv_blogaddr . $wpv_url;

    for ($i = 0; $i < count ($wpv_purgeaddr); $i++) {
      $varnish_sock = fsockopen($wpv_purgeaddr[$i], $wpv_purgeport[$i], $errno, $errstr, $wpv_timeout);
      if (!$varnish_sock) {
        error_log("wp-varnish error: $errstr ($errno)");
        return;
      }

      if($wpv_use_adminport) {
        $buf = fread($varnish_sock, 1024);
        if(preg_match('/(\w+)\s+Authentication required./', $buf, &$matches)) {
          # get the secret
     $secret = "1beb871d-987a-4bbd-98aa-408e3de596cb";
          fwrite($varnish_sock, "auth " . $this->WPAuth($matches[1], $secret) . "\n");
     $buf = fread($varnish_sock, 1024);
          if(!preg_match('/^200/', $buf)) {
            error_log("wp-varnish error: authentication failed using admin port");
       fclose($varnish_sock);
       return;
     }
        }
        $out = "purge req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
      } else {
        $out = "PURGE $wpv_url HTTP/1.0\r\n";
        $out .= "Host: $wpv_host\r\n";
        if ($useragent != null){
           $out .= "User-Agent: $useragent\r\n";
        }
        $out .= "Connection: Close\r\n\r\n";
      }
      fwrite($varnish_sock, $out);
      fclose($varnish_sock);
    }
    // store url as purged
       $WPVarnishPurger_url_purged[]=$wpv_url.$useragent;

    
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
  
  function activateExtension(){
      global $WPVarnishPurger_extension_activated;  
      if ($this->mustActivate()){         
         $this->addActions();
      } 
  } 
  /*
   * Check if a plugins is activated sitewide or for the blog
   * 
   */
  function is_plugin_active($plugin){
     return array_key_exists($plugin, get_site_option( 'active_sitewide_plugins') )||in_array($plugin, get_option( 'active_plugins') );
  }
                 
}

