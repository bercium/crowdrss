function countdown(){
    if (stop_countdown){
		$('.redirect-holder').fadeOut();
		return;
	}
    $('.countdown').html($('.countdown').html() - 1);
    setTimeout(function(){countdown();},1000);
}


stop_countdown = false;
$(document).ready(function () {
    if (redirect_link){
        
        $(document).keyup(function(e) {
            if (e.keyCode == 27) {
              stop_countdown = true;
            }
        });
        setTimeout(function(){
            if (!stop_countdown) window.location = redirect_link;
        }, $('.countdown').html()*1000);
        setTimeout(function(){ 
            countdown(); 
        },1000);
    }
});
