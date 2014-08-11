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
   //$('#preview').show();
});