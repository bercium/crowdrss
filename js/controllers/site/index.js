/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function uncheckPlatforms(id){
  if (id == 0){
    $('[id^="plat_"]').each(function(index){
      if ($(this).attr('id') != 'plat_0') $(this).prop('checked', false);
    });
  }else{
    $('#plat_0').prop('checked', false);
  }
}

// set everything for preview
function previewForm(){
  var subcat = cat = plat = '';
  
  $.each($('#rssfeed_form').serializeArray(), 
  function(i, field) {
    if (field.name.indexOf('plat[') !== -1){
      if (plat !== '') plat += ',';
      plat += field.name.substring(5,field.name.length-1);
    }
    if (field.name.indexOf('cat[') !== -1){
      if (cat !== '') cat += ',';
      cat += field.name.substring(4,field.name.length-1);
    }
    if (field.name.indexOf('subcat[') !== -1){
      if (subcat !== '') subcat += ',';
      subcat += field.name.substring(7,field.name.length-1);
    }
  });
  $('#preview_platform').val(plat);
  $('#preview_category').val(cat);
  $('#preview_subcategory').val(subcat);
  $('#preview_rating').val($('#rating').val());
  $('#preview_form').submit();
}


function toggleSubCat(id){
  //alert('s');
  //$('[id^="subCatHolder_"]').slideUp();

  if ($('#cat_'+id).is(':checked')){
     $('#subCatLink_'+id).fadeIn();
     //$('#subCatHolder_'+id).slideDown();
  }else{
    $('#subCatLink_'+id).fadeOut();
    if ($('#subCatLink_'+id).children('i').hasClass('fa-sort-up')) showSubCat(id);
  }
}

function showSubCat(id){
  
  if ($('#subCatLink_'+id).children('i').hasClass('fa-sort-up')){
    $('#subCatHolder_'+id).slideUp();
    $('#subCatLink_'+id).children('i').removeClass('fa-sort-up');
    $('#subCatLink_'+id).children('i').addClass('fa-sort-down');
  }else{
    $('#subCatHolder_'+id).slideDown();
    $('#subCatLink_'+id).children('i').addClass('fa-sort-up');
    $('#subCatLink_'+id).children('i').removeClass('fa-sort-down');
  }
  
}

$(function() {
  if ($('.intro').height() + $('.intro-desc').height() < $("html").height()) $('.intro').height($("html").height()-$('.intro-desc').height()-50);
  $('#slider').foundation('slider', 'set_value', slider_value);
  
  setTimeout(function(){
    if ($('html, body').scrollTop() == 0){
      $('html, body').animate({scrollTop: 70},400,"swing",
        function(){
          $('html, body').animate({scrollTop: 0});
        }
      );
    }
  },300);
   //$('#preview').show();
});

$(window).scroll(function () {
  if (!$('.top-menu').is(":animated") && $('html, body').scrollTop() > 80) {
    $('.top-menu').addClass('bg');
     //$('.top-menu').animate({ background-color: '#222' }, "slow");
  }else{
    if (!$('.top-menu').is(":animated") && $('html, body').scrollTop() <= 80) {
      $('.top-menu').removeClass('bg');
    }
  }
  
  // hide top menu after intro
  $switch = 0;
  if ($('html, body').scrollTop() > $('.intro').height()+$('.intro-desc').height()+30){
    if ($('.top-menu').hasClass('fixed')) $('html, body').scrollTop($('html, body').scrollTop()+41+15);
    $('.top-menu').removeClass('fixed');
  }else{
    if (!$('.top-menu').hasClass('fixed')) $('html, body').scrollTop($('html, body').scrollTop()-41-15);
    $('.top-menu').addClass('fixed');
  }
});

var sessionID = '';
twttr.ready(function (twttr) {

    //######## trigger when the user publishes his tweet
    twttr.events.bind('tweet', function(event) {

        /*
        To make locked items little more private, let's send our base64 encoded session key
        which will work as key in send_resources.php to acquire locked items.
        */
        var data = {unlock_key : sessionID};
        $('[id^="subCatLink_"]').each(function(index){
          $(this).removeAttr('data-reveal-id');
          $(this).click(function(){
                          showSubCat($(this).attr('dref'));
                        });
        });
        $('#tweettounlock').foundation('reveal', 'close');
    
        //Load data from the server using a HTTP POST request.
        /*$.post("send_resources.php", data, function(data)
        {
            //Append unlocked content into div element
            $('#tweet_content').html(data);

        }).error(function(xhr, ajaxOptions, thrownError) {
            //Output any errors from server.
            alert( thrownError);
        });*/
    });

});