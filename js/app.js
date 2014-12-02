
;(function ($, window, undefined) {
  $('*').removeAttr('data-tooltip');
  $(document).foundation();
  //$(document).foundation({tooltip : {disable_for_touch: true}});

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
        //if (thisEvent) eval(thisEvent);
      });
  });
  
  $('select').chosen({no_results_text: 'Oops, nothing found!', allow_single_deselect: true, width:'100%'});

  
})(jQuery, this);



function contact(e){
	var pri = "@";
	e.href = "mailto:info";
	e.href += pri+"crowdfundingrss.com";
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


/**
 * stop propagation of events down
 *
 * @param inEvent event - event
 */
function stopPropagation(inEvent){
  if (inEvent == null) return;
  inEvent.cancelBubble=true;
  if (inEvent.stopPropagation) inEvent.stopPropagation();
}
