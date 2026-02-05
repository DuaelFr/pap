$(function() {

  /* Checklists */
  if ($('.checklist').length) {

    $("#notam + label a, #begin-tower + label a, #begin-club + label a, #end-tower + label a, #end-club + label a").on("click", function() {
      $(this).parent().siblings('input').prop('checked', true);
    });

  }

});
