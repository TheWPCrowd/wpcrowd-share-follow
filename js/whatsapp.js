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
