
;(function ($, window, undefined) {
  $(document).foundation();



  // Hide address bar on mobile devices
  if (Modernizr.touch) {
    $(window).load(function () {
      setTimeout(function () {
        window.scrollTo(0, 1);
      }, 0);
    });
  }
  

  //$.cookieCuttr();
  
  
  //tracking with code
  // regular events CATEGORY_ACTION_LABEL_VALUE
  // sharing events share_SOCIALNETWORK_ACTION_BUTTON
  $("[trk]").each(function() {
      var id = $(this).attr("trk");
      //var target = $(this).attr("target");
      //var text = $(this).text();
      var thisEvent = null;
      if ($(this).is("[onclick]")) thisEvent = $(this).attr("onclick");
      
      $(this).click(function(event) { // when someone clicks these links
        gase(id);
        if (thisEvent) eval(thisEvent);
      });
  });
  
  
  
  // hide top bar
  if(typeof hideTopBar != 'undefined') {
    $('.top-bar-holder').css({ opacity: 0 });
    $(document).scrollTop(45);
  }
  
})(jQuery, this);

var scrollFirsttime = 0;

$(window).scroll(function () {
  if (!$('.top-bar-holder').is(":animated") && $('.top-bar-holder').css("opacity") == 0 && (scrollFirsttime)) {
     $('.top-bar-holder').animate({ opacity: 1 }, "slow");
  }
  if ($(document).scrollTop() == 45) scrollFirsttime = 1;
});



function contact(e){
	var pri = "@";
	e.href = "mailto:info";
	e.href += pri+"cofinder.eu";
}

function splitComa( val ) {
  return val.split( /,\s*/ );
}
		
function extractLast( term ) {
	return splitComa( term ).pop();
}


// ga function depending on debuging
function gase(id){
  //if (jQuery.cookie('cc_cookie_accept') != 'cc_cookie_accept') return;  //cookie compliance
  var trk = id.split("_");

  if (trk[0] == 'social'){
    //social_facebook_like_nameOfButton
    // add social event
    if (trk.length > 2) ga('send', 'social', trk[1], trk[2], window.location.pathname);
    // add extra event
    if (trk.length > 3) ga('send', 'event', trk[2], trk[1], trk[3], 1);
  }else{
    // regular events CATEGORY_ACTION_LABEL_VALUE
    if (trk.length > 3) ga('send', 'event', trk[0], trk[1], trk[2], trk[3]);
    else
    if (trk.length > 2) ga('send', 'event', trk[0], trk[1], trk[2]);
    else
    if (trk.length > 1) ga('send', 'event', trk[0], trk[1]);
  }
  
}
