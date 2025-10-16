jQuery(document).ready(function ($) {
  // Hide add rule form initially
  $(".pdfw-add-rule").hide();

  // Show form when Add New Rule button is clicked
  $("#pdfw-add-new-rule").on("click", function () {
    $(".pdfw-add-rule").slideDown();
    $(this).hide();
  });

  // Hide form when Cancel button is clicked
  $(".pdfw-cancel-add").on("click", function () {
    $(".pdfw-add-rule").slideUp(function () {
      $("#pdfw-add-new-rule").show();
      // Reset form values
      $("#quantity").val("2");
      $("#discount").val("10");
    });
  });

  // Form validation
  $(".pdfw-rule-form").on("submit", function (e) {
    var quantity = parseInt($("#quantity").val());
    var discount = parseInt($("#discount").val());

    if (isNaN(quantity) || quantity < 1) {
      alert(pdfw_vars.invalid_quantity);
      e.preventDefault();
      return false;
    }

    if (isNaN(discount) || discount < 0 || discount > 100) {
      alert(pdfw_vars.invalid_discount);
      e.preventDefault();
      return false;
    }
  });
});
