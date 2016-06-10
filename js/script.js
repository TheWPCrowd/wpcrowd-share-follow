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