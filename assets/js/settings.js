jQuery(document).ready(function ($) {

  /***** Colour picker *****/

  $('.colorpicker').hide();
  $('.colorpicker').each(function () {
    $(this).farbtastic($(this).closest('.color-picker').find('.color'));
  });

  $('.color').click(function () {
    $(this).closest('.color-picker').find('.colorpicker').fadeIn();
  });

  $(document).mousedown(function () {
    $('.colorpicker').each(function () {
      var display = $(this).css('display');
      if (display == 'block')
        $(this).fadeOut();
    });
  });
});

// Range text output.
function updateTextInput(val) {
  // document.getElementById('changeMe').value = val; // Add as many of these
  // as needed. Change the ID as needed.
  document.getElementById('starsnumValue').value = val;
}