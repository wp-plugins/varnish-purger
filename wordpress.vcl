backend default {
  .host = "127.0.0.1";
  .port = "8080";
}

acl purge {
  "localhost";
}

sub vcl_recv {
        # ##############################################
        # 0) Normalize user-agent : mobile or not
        # ##############################################
        if (req.http.user-agent ~ "(Android|CUPCAKE|bada|blackberry|dream|iPhone|iPod|incognito|s8000|webOS|webmate)" ) {
                set req.http.User-Agent="Android";
        } else {
                set req.http.User-Agent="Windows NT 6.0";
        }
        
        
        # ##############################################
        # 00 Check purge rights
        # #############################################
        if (req.request == "PURGE") {
                if (!client.ip ~ purge) {
                        error 405 "Not allowed.";
                }
                return(lookup);
        }
        # #################
        # 1) ADMIN
        # #################
        if (req.url ~ "^/wp-admin"){
        # 1.1) Cacheable
                if (req.url ~ "(\.png|\.gif|\.jpg)(\?ver=[0-9]+)?$"){
                   return(lookup);
                } 
        # 1.2) Catch all admin not cacheable 
                return(pass);
        }

        # ##################
        # 3) POST
        # ##################
        if (req.request == "POST"){
            return(pass);
        }   
                
        # ##################
        # 3) Includes
        # ##################
        # 3.1) Include cacheable
        if (req.url ~ "^/wp\-includes/.*(\.js|\.css)(\?ver=[0-9]+)?$"){
            return(lookup);
        }

        # ##################
        # 4) Themes
        # ##################
        if (req.url ~ "^/wp\-content/themes/"){
            return(lookup);
        }
        
        # ##################
        # 5) PLUGIN : NextGenGallery
        # ##################
        if (req.url ~ "^/wp\-content/plugins/netgen\-gallery/js/.*\.js(\?ver=[0-9]+)?$"){
            return(lookup);
        }

        # ##################
        # 6) Favicon
        # ##################
        if (req.url == "/favicon.ico"){
            return(lookup);
        }


        # ##################
        # 98)Logged in used
        # ##################
        if ( req.http.Cookie ~ "wordpressuser=") {
            set req.http.Domaine = regsub(req.http.Cookie,"wordpressuser=([^;]+)","\1");
            if (req.http.Domaine == regsub(req.http.Host,"^([^\.]+)\.","\1")){
                return (pass);
            }
        }

        # 99) Catch all
        return (lookup);
  
}

sub vcl_fetch {
    if (!beresp.cacheable) {
        set beresp.http.X-Cacheable = "NO:Not Cacheable";

    # You don't wish to cache content for logged in users
    } elsif (req.http.Cookie ~ "wordressuser") {
        set beresp.http.X-Cacheable = "NO:Got Session";
        return(pass);

    # You are respecting the Cache-Control=private header from the backend
    } elsif (beresp.http.Cache-Control ~ "private") {
        set beresp.http.X-Cacheable = "NO:Cache-Control=private";
        return(pass);

    # You are extending the lifetime of the object artificially
    } elsif (beresp.ttl < 1s) {
        set beresp.ttl   = 5s;
        set beresp.grace = 5s;
        set beresp.http.X-Cacheable = "YES:FORCED";

    # Varnish determined the object was cacheable
    } else {
        set beresp.ttl   = 86400s;
        set beresp.grace = 86400s;
        set beresp.http.X-Cacheable = "YES";
    }


    return(deliver);

}
###
# Add headers for debugging purposes
##
sub vcl_hit {
        if (req.request == "PURGE") {
                set obj.ttl = 0s;
                error 200 "Purged.";
        }
        if (!obj.cacheable) {
                return(pass);
        }
        return(deliver);
}

sub vcl_miss {
        if (req.request == "PURGE") {
        error 404 "Not in cache.";
        }
}

sub vcl_deliver{
    if (obj.hits > 0) {
                set resp.http.X-Cache = "HIT";
        } else {
                set resp.http.X-Cache = "MISS";
        }
    return(deliver);
}

