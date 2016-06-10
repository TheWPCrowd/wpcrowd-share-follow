(function($){
    
    
})(jQuery);


function roundUpNumbers(n,d)
  { 
    if(n <= 999){
      return n;
    }else{
      x=(''+n).length,p=Math.pow,d=p(10,d);
      x-=x%3;
      return Math.round(n*d/p(10,x))/d+" kMGTPE"[x/3];
    }
  }

  function addShareCountToLink(net, countResult, index){
    if(countResult === 0){return; }
    html = "<small>" + roundUpNumbers(countResult, 2) + "</small>";
    jQuery(".show-count .net-"+net+" .icon-" + net).append(html);
  }
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */





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
