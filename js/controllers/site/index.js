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



$(function() {
   if ($('.intro').height() + $('.intro-desc').height() < $("html").height()) $('.intro').height($("html").height()-$('.intro-desc').height()-50);
});