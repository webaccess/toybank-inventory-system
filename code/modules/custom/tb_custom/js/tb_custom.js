/**
 * @file
 */

jQuery(document).ready(function () {
  var str     = window.location.href;
  var res     = str.split("/inventory-details/");
  var gameNid = res[1];

  if (gameNid) {
    jQuery.ajax({
      url: "/get-game-title/" + gameNid,
      type: "POST",
      success: function (data) {
        if (data) {
          jQuery("#edit-field-multiple-games-add-more").hide();
          jQuery("#field-multiple-games-values .btn-danger").hide();

          // ~ jQuery("#field-multiple-games-values tbody tr").not("#field-multiple-games-values tbody .fieldset-wrapper tr").each(function() {
          jQuery("#field-multiple-games-values tbody tr").not("#field-multiple-games-values tbody .panel-body tr").each(function () {
            var flag = 0;

            if (jQuery(this).find('tbody .inline-entity-form-node-label').html() != data) {
              flag = 1;
            }

            if (flag == 1) {
              jQuery(this).hide();
            }
          });
        }
      }
    });
  }

  // To add print button on Dispatch Sheet Report.
  jQuery('<div class="report-print-wrapper" style="clear:both;"></div>').insertAfter('.path-dispatch-sheet-report .dispatch-report-wrapper');

  jQuery(".path-dispatch-sheet-report").each(function () {
    var contents = document.getElementsByClassName("dr-requested-id");
    var request = contents[0].innerText
    var avoid ="Request ID: "
    request = request.replace(avoid,'');
    jQuery('.report-print-wrapper').append('<button id="my_text">Print</button>')

    my_text.onclick = function() {
      var title = "Dispatch Sheet Report - "+request;
      try {
        document.title = window.parent.document.title = title;
        print();
      } catch (e) { 
        var p = window.open(location.href);
        p.onload = function() {
          p.document.title = "PP";
          function closePopup() {
            p.close();
          }
          if ('onafterprint' in p) {
            p.onafterprint = closePopup
          } else {
            var mediaQueryList = p.matchMedia('print');
            mediaQueryList.addListener(function(mql) {
              if (!mql.matches) {
                closePopup();
              }
            });
          }
          p.print();
        };
      }
    }
  });

  // Add/Edit Partner: Disable save button while file upload is in progress.
  if (jQuery("input[name='field_upload_documents_partner[0][field_upload_documents_partner][0][UPLOAD_IDENTIFIER]']").length) {
    jQuery(document).ajaxStart(function () {
      jQuery("button#edit-submit").attr("disabled", true);
    });

    jQuery(document).ajaxSuccess(function () {
      if (jQuery('.ajax-progress-bar').length == 0) {
        jQuery("button#edit-submit").attr("disabled", false);
      }
    });
  }



});


