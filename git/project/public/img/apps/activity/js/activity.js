$(function(){
   $('.need').each(function(){
      	  $(this).parent().prev().prepend('<span>*</span>&nbsp;&nbsp;').children().eq(0).css({color:'red'});
      }
   );
   $.getJSON(APP_URL+'?app=system&controller=content&action=stat&jsoncallback=?&contentid='+contentid, function(data){});

});
