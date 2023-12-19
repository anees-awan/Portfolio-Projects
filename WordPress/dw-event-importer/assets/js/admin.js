
/**
 * @file
 * Plugin admin JS.
 *
 * Created by: Anees
 * https://dreamwarrior.com/
 */
jQuery.noConflict();
jQuery(document).ready(function($) {
  
  /**
   * Image uploader.
   */
  // Button trigger.
  $('.dwei-uploader').click(function() {
    $('.dwei-active-field').removeClass('dwei-active-field');
    $(this).prev('input[type=text]').addClass('dwei-active-field');
    tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
  });
  // Inserting image URL.
  window.send_to_editor = function(html) {
    imgurl = $('img',html).attr('src');
    $('.dwei-active-field').val(imgurl).removeClass('dwei-active-field');
    tb_remove();
  }
  
  /**
   * Fieldset tabs.
   */
  $('.dwei-tab-trigger').click(function(){
    var parentFieldset = $(this).parents('.dwei-fieldset-div');
    parentFieldset.find('.dwei-tab-trigger').removeClass('active');
    parentFieldset.find('.dwei-tab-content').hide();
    $(this).addClass('active');
    $('.' + $(this).attr('rel')).show();
  });
  $('.dwei-fieldset-div').each(function(){
    tabInCurrentFieldset = $(this).find('.dwei-tab-trigger');
    if (tabInCurrentFieldset.length) {
      tabInCurrentFieldset.eq(0).click();
    }
  });

});