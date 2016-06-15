(function($){    
    function resizeSharing(){
        if($(".shareing-links").length && $(window).width() > 1279 ){
            cpos = $("div.content").offset();
            $(".shareing-links").css({"top":cpos.top, "left" : (cpos.left - 63)}); 
        }
    }
    
    $(window).resize(function(){
        resizeSharing();
    });
     resizeSharing();
     
     
     
     
     
     var shareTools = {
        // counts object
        counts : {},
        //
        link :"",
        // make share numbers look nice
        
        roundUpNumbers : function(n,d){ 
            if(n <= 999){
              return n;
            }else{
              x=(''+n).length,p=Math.pow,d=p(10,d);
              x-=x%3;
              return Math.round(n*d/p(10,x))/d+" kMGTPE"[x/3];
            }
        },

        addShareCountToLink : function(net, countResult){
            if(countResult === 0){return; }
            int = shareTools.roundUpNumbers(countResult, 2) ;
            jQuery(".show-count .net-"+net+" .icon-" + net + " small").text(int);
            shareTools.addShareCountToEngage(net,countResult);
        },
        
        addShareCountToEngage : function (net, count){
            shareTools.counts[net] = count;
            shareTools.updateEngage();
        },
        
        updateEngage : function(){
            total = 0;
            console.log(shareTools.counts);
            for (var key in shareTools.counts) {
                total += shareTools.counts[key];
            }
            $(".engagement .count").text(shareTools.roundUpNumbers(total,2));
        },
        
        ajaxGetFacebookCurrentCount : function(url){
    
            fburl = "https://api.facebook.com/restserver.php?method=links.getStats&format=json&urls="+url;
            
            $.ajax({
              dataType: "json",
              url : fburl,
              success: 
                function( result ) {
                  totalshares = 0;
                  
                  for(var url in result) { 
                      
                    if(result[url].total_count !== undefined && parseInt(result[url].total_count) !== totalshares){
                        totalshares = totalshares + parseInt(result[url].total_count);
                    }
                  }           
                  shareTools.addShareCountToLink("facebook",totalshares);
              }                       
            });                   
        },
        
        init : function(){
            if($(".shareing-links").length){ 
               shareTools.link = $('.shareing-links').attr("data-link");
               shareTools.checkFB();
               shareTools.getServerStats();
               shareTools.getLinkedInStats();
            }
            
        },
        
        checkFB : function(){
            if($(".show-count .net-facebook").length){                 
                url = $(".show-count .net-facebook a").attr("data-url");                
                shareTools.ajaxGetFacebookCurrentCount(url);                                                        
            }
        },
        
        getLocation : function(href) {
            var l = document.createElement("a");
            l.href = href;
            return l;
        },
        
        getServerStats : function(){
            
            link = shareTools.getLocation(shareTools.link);
            id = $('.shareing-links').attr("data-id");            
            statsUrl = sharesettings.stats_url +"?id="+ id + "&slug=" + encodeURIComponent(link.pathname) +"&nonce="+sharesettings.nonce
            
            console.log(statsUrl);
            
            $.ajax({
              dataType: "json",
              url : statsUrl ,
              success: 
                function( result ) {
                  if(result["data"]['googleplus'] != undefined){
                      shareTools.addShareCountToLink("googleplus",result["data"]['googleplus']);
                  }
                  if(result["data"]['twitter'] != undefined){
                      shareTools.addShareCountToLink("googleplus",result["data"]['twitter']);
                  }
                  if(result["data"]['linkedin'] != undefined){
                      shareTools.addShareCountToLink("googleplus",result["data"]['linkedin']);
                  }
              }
                      
                      
            }); 
        },
        
        getLinkedInStats : function(){
            $.ajax({
              dataType: "json",
              url : "https://www.linkedin.com/countserv/count/share?format=json&url="+ shareTools.link ,
              success: 
                function( result ) {
                  if(result["count"]!= undefined){
                      shareTools.addShareCountToLink("linkedin",result["count"]);
                  }                 
              
                }
            });
              
            
        }
        
        
        
        
        
        
        };
     
     shareTools.init();
     
})(jQuery);





