
;(function ($, window, undefined) {
  $('[data-tooltip]').each(function(){
    $(this).removeAttr('data-tooltip');
    $(this).addClass('tip');
    $(this).attr('data-tip',$(this).attr('title'));
  });
  $('.tip').tipr();
  //$('*').removeAttr('data-tooltip');
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


if (Math.floor((Math.random() * 3)) == 1)
(function() {
  var hidden = "hidden";
  var title = '';

  // Standards:
  if (hidden in document)
    document.addEventListener("visibilitychange", onchange);
  else if ((hidden = "mozHidden") in document)
    document.addEventListener("mozvisibilitychange", onchange);
  else if ((hidden = "webkitHidden") in document)
    document.addEventListener("webkitvisibilitychange", onchange);
  else if ((hidden = "msHidden") in document)
    document.addEventListener("msvisibilitychange", onchange);
  // IE 9 and lower:
  else if ("onfocusin" in document)
    document.onfocusin = document.onfocusout = onchange;
  // All others:
  else
    window.onpageshow = window.onpagehide = window.onfocus = window.onblur = onchange;

  function onchange (evt) {
    if (title == '') title = document.title;
    var v = "visible", h = "hidden",
        evtMap = {
          focus:v, focusin:v, pageshow:v, blur:h, focusout:h, pagehide:h
        };

    evt = evt || window.event;
    if (evt.type in evtMap)
      type = evtMap[evt.type];
    else
      type = this[hidden] ? "hidden" : "visible";

    if (type == 'hidden') document.title = 'Hey come back!';
    else document.title = title;
  }

  // set the initial state (but only if browser supports the Page Visibility API)
  if( document[hidden] !== undefined )
    onchange({type: document[hidden] ? "blur" : "focus"});
})();


function contact(e){
	var pri = "@";
	e.href = "mailto:info";
	e.href += pri+"crowdfundingrss.com";
    $(e).html('info'+pri+'crowdfundingrss.com');
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
