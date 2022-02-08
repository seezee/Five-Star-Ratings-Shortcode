jQuery(document).ready(function ($) {
  'use strict'; // Prevent accidental global variables.

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
  
  /***** Clipboard (copy shortcode button) *****/
  // Check for existence of ClipboardJS before assigning so we don't get an
  // an error in the FREE plugin.
  if (typeof ClipboardJS !== 'undefined') {
    var clipboard = new ClipboardJS(".copyBtn");
  }

  /***** Form Conditional Logic *****/
  $("input[type=\'radio\'][name=\'fsrs_reviewType\']").change(function() {
    if (this.value == "Product") {
      $("input[name^=\'fsrs_prod\']").prop("required", true);
      $("input[name^=\'fsrs_rest\']").prop("required", false);
      $("input[name^=\'fsrs_rec\']").prop("required", false);
    } else if(this.value == "Restaurant") {
      $("input[name^=\'fsrs_rest\']").prop("required", true);
      $("input[name^=\'fsrs_resrec\']").prop("required", true);
      $("input[name^=\'fsrs_prod\']").prop("required", false);
      $("input[name^=\'fsrs_rec\']").prop("required", false);
    } else if(this.value == "Recipe") {
      $("input[name^=\'fsrs_rec\']").prop("required", true);
      $("input[name^=\'fsrs_resrec\']").prop("required", true);
      $("input[name^=\'fsrs_prod\']").prop("required", false);
      $("input[name^=\'fsrs_rest\']").prop("required", false);
    }
  });

});

// Range text output 1.
function updateTextInput1(val) {
  // document.getElementById('changeMe').value = val; // Add as many of these
  // as needed. Change the ID as needed.
  document.getElementById('starsminValue').value = val;
}

// Range text output 2.
function updateTextInput2(val) {
  // document.getElementById('changeMe').value = val; // Add as many of these
  // as needed. Change the ID as needed.
  document.getElementById('starsmaxValue').value = val;
}

