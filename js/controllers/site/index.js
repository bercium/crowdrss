/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function uncheckPlatforms(){
  if ($('#platformKS').is(':checked')) $('#platformKS').prop('checked', false);
  if ($('#platformIGG').is(':checked')) $('#platformIGG').prop('checked', false);
}