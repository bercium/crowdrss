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


function previewForm(){
  var cat = plat = '';
  
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
  });
  $('#preview_platform').val(plat);
  $('#preview_category').val(cat);
  $('#preview_form').submit();
}


$(function() {
   if ($('.intro').height() + $('.intro-desc').height() < $("html").height()) $('.intro').height($("html").height()-$('.intro-desc').height()-50);
   $('#preview').show();
});