sub vcl_fetch {
    # INFO : the following lines should be added at the beginning of vcl_fetch                     #
    # ##############################################################################################
    # For html pages add Vary-useragent
    # cf http://www.varnish-cache.org/static/perbu/dirhtml/tutorial/increasing_your_hitrate/#vary
    # ##############################################################################################
    if (beresp.http.Content-Type ~ "^text/html; charset=UTF\-8$"){
        set beresp.http.Vary="User-Agent";
        set beresp.http.User-Agent=req.http.User-Agent;
    }
}

sub vcl_recv(){
     # INFO : the following lines should be added at the beginning of vcl_recv
     # ##############################################
     # 0) normalize le user-agent : mobile ou not
     # ##############################################
     if (req.http.user-agent ~ "(Android|CUPCAKE|bada|blackberry|dream|iPhone|iPod|incognito|s8000|webOS|webmate)" ) {
             set req.http.User-Agent="Android";
     } else {
             set req.http.User-Agent="Windows NT 6.0";
     }

}
    
    
    

    
