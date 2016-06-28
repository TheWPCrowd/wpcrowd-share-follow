function resizeSharing(){
        if(jQuery(".shareing-links").length && $(window).width() > 1279 ){
            cpos = jQuery("div.content").offset();
            jQuery(".shareing-links").css({"top":cpos.top, "left" : (cpos.left - 63)}); 
        }
    }
    
resizeSharing();

jQuery(window).resize(function(){
    resizeSharing();
});
    
     
(function($){    
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
            int = shareTools.roundUpNumbers(parseInt(countResult), 2) ;
            jQuery(".show-count .net-"+net+" .icon-" + net + " small").text(int);
            shareTools.addShareCountToEngage(net,parseInt(countResult));
        },
        
        addShareCountToEngage : function (net, count){
            shareTools.counts[net] = count;
            shareTools.updateEngage();
        },
        
        updateEngage : function(){
            total = 0;            
            for (var key in shareTools.counts) {
                total += shareTools.counts[key];
            }
            $(".engagement .count").text(shareTools.roundUpNumbers(total,2));
        },
        
        ajaxGetFacebookCurrentCount : function(url){    
            url = url.replace(/.*?:\/\//g, "");
            fburl = "https://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=http://"+url+",https://"+url;            
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
        
        ajaxGetFacebookGroupCount : function(){  
            links = '';
            counter = 0;
            urls = {};
            $(".engage-count-only").each(function(index){
                if(counter !== 0){
                    links += ',';
                }
                //console.log($(this));
                url = $(this).attr('data-link').replace(/.*?:\/\//g, "");    
                links += "http://"+url+",https://"+url;
                counter ++;
            }); 
            
            fburl = "https://api.facebook.com/restserver.php?method=links.getStats&format=json&urls="+links;            
            $.ajax({
              dataType: "json",
              url : fburl,
              success: 
                function( result ) {
                  totalshares = 0;                  
                  for(var url in result) { 
                    if(result[url].total_count !== undefined ){
                        thisUrl = String(result[url].url).replace(/.*?:\/\//g, "");
                        if(urls[thisUrl] !== undefined){
                            urls[thisUrl]['counts'].facebook = urls[thisUrl]['counts'].facebook + result[url].total_count;
                        }else{
                            urls[thisUrl]= {counts:{ facebook : result[url].total_count,comments:0}};
                        }
                    }
                  }
                  shareTools.addShareCountToEngageSpan(urls);
              }
            });
        },
        addShareCountToEngageSpan :function (urls){
            console.log(urls);
            for( url in urls) {
               working = $(".engage-count-only[data-link='"+ location.protocol +"//" + url +"']");               
               urls[url].counts.comments = parseInt(working.attr('data-share'));
               finalcount = 0;
               for(var networks in urls[url].counts){
                   finalcount += parseInt(urls[url].counts[networks]);
               }               
               working.find("span.count").text(finalcount) ;
            } 
        },
        init : function(){
            if($(".shareing-links").length){ 
               shareTools.link = $('.shareing-links').attr("data-link");
               shareTools.checkFB();
               shareTools.getServerStats();
               
               if(sharesettings['comments'] != undefined){
                    shareTools.addShareCountToLink('comments',  parseInt(sharesettings.comments));
               }
            }
            
            if($(".engage-count-only").length){ 
                shareTools.ajaxGetFacebookGroupCount();
            }
        },
        
        checkFB : function(){
            if($(".show-count .net-facebook").length){                 
                url = $(".show-count .net-facebook a").attr("data-link");                
                shareTools.ajaxGetFacebookCurrentCount(url);                                                        
            }
        },
        
        getLocation : function(href) {
            var l = document.createElement("a");
            l.href = href;
            return l;
        },
        
        getServerStats : function(){
            
            
            id = $('.shareing-links').attr("data-id");            
            statsUrl = sharesettings.stats_url +"?id="+ id + "&nonce="+sharesettings.nonce
            
            
            
            $.ajax({
              dataType: "json",
              url : statsUrl ,
              success: 
                function( result ) {
                  
                  if(result.success == false || result.success == undefined){
                      return false; 
                  }
                  
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
        }
    };
     
     shareTools.init();
     
})(jQuery);






(function($){
    
 var isMobile = (navigator.userAgent.match(/iPad|iPhone|iPod|Android|android/g) ? true : false );

function iOSversion() {
  if (/iP(hone|od|ad)/.test(navigator.platform)) {
    // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
    var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
    return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
  }else{
      return false;
  }
}

var iosver = iOSversion();


    if(!isMobile || (iosver !== false && iosver[0] < 6)  ) {
       if($(".net-whatsapp").length){
        $(".net-whatsapp").each(function(index){
            $(this).hide();
        });
       }
    }


    
})(jQuery);
