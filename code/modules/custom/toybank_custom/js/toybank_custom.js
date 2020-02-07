/**
 * @file
 */

jQuery(document).ready(function () {
  if (jQuery(".view-empty").length) {
    if (jQuery(".feed-icons").length) {
      jQuery(".feed-icons").remove();
    }
  }
});

jQuery("select[id^='edit-field-multiple-games-']").each(function () {
  // Code to hide content type dropdown in add more field collection.
  if (this.value == 'article') {
    var cid = jQuery(this).attr("id");
    jQuery("#" + cid + " option[value='game']").attr("selected", "selected");
    jQuery("#" + cid).css("display", "none");
  }
  else if (this.value == 'game') {
    var gamewrp = jQuery(this).closest('div').attr("class");
    jQuery("." + gamewrp).css("display", "none");
    var cid = jQuery(this).attr("id");
    jQuery("#" + cid).css("display", "none");
  }
});

// Number validation for no.of kids.
jQuery("#edit-field-pre-primary-boys-0-value").keypress(function (e) {
  // If the letter is not digit then display error and don't type anything.
  if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    jQuery(this).attr("placeholder", "Digits Only");
    return false;
  }
  else {
    jQuery(this).attr("placeholder", "");
  }
});

jQuery("#edit-field-pre-primary-girls-0-value").keypress(function (e) {
  // If the letter is not digit then display error and don't type anything.
  if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    jQuery(this).attr("placeholder", "Digits Only");
    return false;
  }
  else {
    jQuery(this).attr("placeholder", "");
  }
});

jQuery("#edit-field-prima-0-value").keypress(function (e) {
  // If the letter is not digit then display error and don't type anything.
  if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    jQuery(this).attr("placeholder", "Digits Only");
    return false;
  }
  else {
    jQuery(this).attr("placeholder", "");
  }
});

jQuery("#edit-field-pri-girls-0-value").keypress(function (e) {
  // If the letter is not digit then display error and don't type anything.
  if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    jQuery(this).attr("placeholder", "Digits Only");
    return false;
  }
  else {
    jQuery(this).attr("placeholder", "");
  }
});

jQuery("#edit-field-second-0-value").keypress(function (e) {
  // If the letter is not digit then display error and don't type anything.
  if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    jQuery(this).attr("placeholder", "Digits Only");
    return false;
  }
  else {
    jQuery(this).attr("placeholder", "");
  }
});

jQuery("#edit-field-secondry-girls-0-value").keypress(function (e) {
  // If the letter is not digit then display error and don't type anything.
  if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    jQuery(this).attr("placeholder", "Digits Only");
    return false;
  }
  else {
    jQuery(this).attr("placeholder", "");
  }
});

jQuery(document).ajaxComplete(function () {
  jQuery(".form-select option[value='category']").remove();
  jQuery(".form-select option[value='tags']").remove();

  if (jQuery("td").hasClass("td_Strategy")) {
    jQuery('.btn_game_details_Strategy').appendTo('.td_Strategy');
  }

  if (jQuery("td").hasClass("td_Puzzle")) {
    jQuery('.btn_game_details_Puzzle').appendTo('.td_Puzzle');
  }

  if (jQuery("td").hasClass("td_Block")) {
    jQuery('.btn_game_details_Block').appendTo('.td_Block');
  }

  if (jQuery("td").hasClass("td_Alphabetical")) {
    jQuery('.btn_game_details_Alphabetical').appendTo('.td_Alphabetical');
  }

  if (jQuery("td").hasClass("td_Numerical")) {
    jQuery('.btn_game_details_Numerical').appendTo('.td_Numerical');
  }

  if (jQuery("td").hasClass("td_General")) {
    jQuery('.btn_game_details_General').appendTo('.td_General');
  }

  // Code to hide content type dropdown in add more field collection.
  jQuery("select[id^='edit-field-multiple-games-']").each(function () {
    if (this.value == 'article') {
      var cid = jQuery(this).attr("id");
      jQuery("#" + cid + " option[value='game']").attr("selected", "selected");
      jQuery("#" + cid).css("display", "none");
    }
    else if (this.value == 'game') {
      var gamewrp = jQuery(this).closest('div').attr("class");
      jQuery("." + gamewrp).css("display", "none");
      var cid = jQuery(this).attr("id");
      jQuery("#" + cid).css("display", "none");
    }
  });
});

(function ($, Drupal) {
  Drupal.behaviors.ThemeBehaviours = {
    attach: function (context, settings) {
      if (jQuery(".view").hasClass("view-game-request-listing")) {
        // Request List
        var href = jQuery(".path-game-requests-listing a.feed-icon").attr('href');
        var res =  href.replace("requests.csv?", "request-list-export/");
        jQuery(".path-game-requests-listing a.feed-icon").attr('href',res);
      }

      // Quantity integer validation js for add inventory.
      jQuery("#edit-field-multiple-games-wrapper .form-number").keypress(function (e) {
        // If the letter is not digit then display error and don't type anything.
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          jQuery(this).attr("placeholder", "Digits Only");
          return false;
        }
        else {
          jQuery(this).attr("placeholder", "");
        }
      });

      // Inventory form Donated/Purchased dependency handle.
      jQuery("#edit-field-donated-purchased").change(function () {
        var donVal = jQuery(this).val();
        if (donVal == 'Donated' || donVal == 'Recall' || donVal == '_none') {
          jQuery("#edit-field-vendor-taxo").val("_none").trigger("change");
        }
      });

      $(".path-inventory-history .inv_his").click(function () {
        var nid_txt = jQuery(this).html();
        var catg    = jQuery(this).attr("catg");
        jQuery(".inv-audit-throbber").remove();
        jQuery('#mark_issue_audit_wrapper').remove();
        jQuery('#inventory_audit_wrapper').append('<div id="mark_issue_audit_wrapper"></div>');
        jQuery(this).append('<img class="inv-audit-throbber" src="/core/modules/quickedit/images/icon-throbber.gif">');
        var nid = jQuery(this).attr("id");

        if (nid_txt == 0) {
           jQuery('#mark_issue_audit_wrapper').html("<div class='error-issue-mgs'>No Data Available</div>");
           jQuery(".inv-audit-throbber").remove();
        }
        else {
         $.ajax({
            url: "/ajax/inventory-ad-history",
            type: "POST",
            data: {"inv_nids" : nid, "catg":catg },
            success: function (data) {
              jQuery(".inv-audit-throbber").remove();
              jQuery('#mark_issue_audit_wrapper').html(data);
            }
          });
        }
      });

      $(".pc_followup").click(function () {
        var nid_txt = jQuery(this).html();
        var catg    = jQuery(this).attr("pc_catg");
        jQuery(".pc-audit-throbber").remove();
        jQuery('#mark_issue_pc_audit_wrapper').remove();
        jQuery('#pc_audit_wrapper').append('<div id="mark_issue_pc_audit_wrapper"></div>');
        jQuery(this).append('<img class="pc-audit-throbber" src="/core/modules/quickedit/images/icon-throbber.gif">');
        var nid  = jQuery(this).attr("id");
        var pcid = jQuery(this).attr("pc_id");

        if (nid_txt == 0) {
          jQuery('#mark_issue_pc_audit_wrapper').html("<div class='error-issue-mgs'>No Data Available</div>");
          jQuery(".pc-audit-throbber").remove();
        }
        else {
          $.ajax({
            url: "/ajax/playcenter-inv-ad",
            type: "POST",
            data: {"inv_nids" : nid, "pc_id" : pcid, "catg" : catg},
            success: function (data) {
              jQuery(".pc-audit-throbber").remove();
              jQuery('#mark_issue_pc_audit_wrapper').html(data);
            }
          });
        }
      });

      if (jQuery("td").hasClass("td_Strategy")) {
        jQuery('.btn_game_details_Strategy').appendTo('.td_Strategy');
      }

      if (jQuery("td").hasClass("td_Puzzle")) {
        jQuery('.btn_game_details_Puzzle').appendTo('.td_Puzzle');
      }

      if (jQuery("td").hasClass("td_Block")) {
        jQuery('.btn_game_details_Block').appendTo('.td_Block');
      }

      if (jQuery("td").hasClass("td_Alphabetical")) {
        jQuery('.btn_game_details_Alphabetical').appendTo('.td_Alphabetical');
      }

      if (jQuery("td").hasClass("td_Numerical")) {
        jQuery('.btn_game_details_Numerical').appendTo('.td_Numerical');
      }

      if (jQuery("td").hasClass("td_General")) {
        jQuery('.btn_game_details_General').appendTo('.td_General');
      }

      // Hide/Show "Generate Request" button on grid3 generation.
      jQuery(document).ajaxComplete(function () {
        check_selected_games_stats();
      });

      // Hide "Generate Request" button when quantity field is empty.
      jQuery(".reqty_gen_req").on('keyup change', function () {
        var input_name     = jQuery(this).attr('name').split('_');
        var category       = input_name[1];
        var category_count = 0;

        jQuery(".reqty_gen_req").each(function () {
          if (this.value !== '') {
            category_count += +this.value;
          }
        });

        jQuery("#selected_games_stats ." + category).html(category_count);

        check_selected_games_stats();
      });

      jQuery(".reqty_gen_req").keypress(function (e) {
        // If the letter is not digit then display error and don't type anything.
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          jQuery(this).attr("placeholder", "Digits Only");
          jQuery(".btn_final_generate_Request").css("display", "none");
          return false;
        }
        else {
          jQuery(this).attr("placeholder", "");
        }

        check_selected_games_stats();
      });

      // Hide operations column of games section on add/edit inventory page.
      jQuery("#edit-field-multiple-games-wrapper table tbody table tr").each(function () {
        jQuery("th:nth-child(4)").hide();
        jQuery("td:nth-child(4)").hide();
      });

      // Hide add game button in inventory.
      jQuery("#edit-field-multiple-games-wrapper .ief-entity-submit").each(function () {
        if (jQuery(this).val() == 'Add Game') {
          jQuery(this).hide();
        }
      });

      if (!jQuery(".view-empty").length) {
        // Add csv export link beside exposed filter manage-users
        jQuery(".path-manage-users .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-manage-users .feed-icons .csv-feed").html();
        jQuery(".path-manage-users .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter kbi
        jQuery(".path-kbi .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-kbi .feed-icons .csv-feed").html();
        jQuery(".path-kbi .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter partner
        jQuery(".path-partner .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-partner .feed-icons .csv-feed").html();
        jQuery(".path-partner .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter cluster
        jQuery(".path-cluster .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-cluster .feed-icons .csv-feed").html();
        jQuery(".path-cluster .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-kbi-category
        jQuery(".path-kbi-category .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-kbi-category .feed-icons .csv-feed").html();
        jQuery(".path-kbi-category .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-kids-background
        jQuery(".path-kids-background .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-kids-background .feed-icons .csv-feed").html();
        jQuery(".path-kids-background .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-type-of-center
        jQuery(".path-type-of-center .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-type-of-center .feed-icons .csv-feed").html();
        jQuery(".path-type-of-center .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-type-of
        jQuery(".path-type-of .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-type-of .feed-icons .csv-feed").html();
        jQuery(".path-type-of .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-type-of-school
        jQuery(".path-type-of-school .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-type-of-school .feed-icons .csv-feed").html();
        jQuery(".path-type-of-school .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-partner-type
        jQuery(".path-partner-type .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-partner-type .feed-icons .csv-feed").html();
        jQuery(".path-partner-type .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-type-of-activity
        jQuery(".path-type-of-activity .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-type-of-activity .feed-icons .csv-feed").html();
        jQuery(".path-type-of-activity .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-game-issue
        jQuery(".path-game-issue-report .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-game-issue-report .feed-icons .csv-feed").html();
        jQuery(".path-game-issue-report .view-filters .form-inline").not(".path-game-issue-report .view-filters .form-inline.form-type-date").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-playcenter-issue-report
        jQuery(".path-playcenter-issue-report .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-playcenter-issue-report .feed-icons .csv-feed").html();
        jQuery(".path-playcenter-issue-report .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-inventory-listing
        jQuery(".path-inventory-listing .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-inventory-listing .feed-icons .csv-feed").html();
        jQuery(".path-inventory-listing .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-activity-listing
        jQuery(".path-activity-listing .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-activity-listing .feed-icons .csv-feed").html();
        jQuery(".path-activity-listing .view-filters .form-inline").not(".path-activity-listing .view-filters .form-inline.form-type-date").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-verify-new-games
        jQuery(".path-verify-new-games .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-verify-new-games .feed-icons .csv-feed").html();
        jQuery(".path-verify-new-games .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter path-source-pickup
        jQuery(".path-source-pickup .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-source-pickup .feed-icons .csv-feed").html();
        jQuery(".path-source-pickup .view-filters .form-inline").not(".path-source-pickup .view-filters .form-inline.form-type-date").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter play-center
        jQuery(".path-playcenter .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-playcenter .feed-icons .csv-feed").html();
        jQuery(".path-playcenter .view-filters .form-inline").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter Activity listing
        jQuery(".path-activity-listings .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-activity-listings .feed-icons .csv-feed").html();
        jQuery(".path-activity-listings .view-filters .form-inline").not(".js-form-type-date").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");

        // Add csv export link beside exposed filter - Request List
        jQuery(".path-game-requests-listing .feed-icons").addClass("custom-feed-icon");
        jQuery(".path-game-requests-listing .view-filters .form-inline .custom-feed-icon").remove();
        var csvExportLink = jQuery(".path-game-requests-listing .feed-icons .json-feed").html();
        jQuery(".path-game-requests-listing .view-filters .form-inline").not(".path-game-requests-listing .view-filters .form-inline.form-type-date").append("<div class='custom-feed-icon' style='float:right;'>" + csvExportLink + "</div>");
      }

      // pending request details page - restrict packed qty > system qty
      jQuery(".path-pending-request-details .table tbody tr").each(function () {
        var sys_qty = jQuery(this).find(".views-field-field-req-game-quantity").text().trim();
        jQuery(this).find(".views-field-form-field-field-packed-quantity input").attr({"max" : sys_qty});
      });

      // Edit Game Request - Update Selected Games Stats
      if (jQuery(".path-edit-request .grid3_wrapper_table").length) {
        var id             = jQuery(".path-edit-request .grid3_wrapper_table").attr("id").split('_');
        var category       = id[2];
        console.log(category);
        var category_count = 0;

        jQuery(".reqty_gen_req").each(function () {
          if (this.value !== '') {
            category_count += +this.value;
          }
        });

        jQuery("#selected_games_stats ." + category).html(category_count);

        check_selected_games_stats();
      }

      if (jQuery(".grid3_wrapper_table").length) {
        jQuery(window).scroll(sticky_request_form);
        sticky_request_form();
      }
    }
  }
})(jQuery, Drupal);

function sticky_request_form() {
  var window_top = jQuery(window).scrollTop();
  var div_top = jQuery('#grid2_wrapper').offset().top;
  var get_table_width = jQuery('#grid2_wrapper').width();

  if (window_top > div_top) {
    jQuery('#selected_games_stats_wrapper').addClass('sticky');
    jQuery('#selected_games_stats_wrapper.sticky').css('width', get_table_width );
    jQuery('#selected_games_stats_wrapper.sticky').css('top', ( jQuery('#navbar-collapse').height() ) );
    jQuery('.grid3_wrapper_table .sticky-header').css('top', ( jQuery('#navbar-collapse').height() + jQuery('#selected_games_stats_wrapper').height()) );
    jQuery('.grid3_wrapper_table .sticky-header').css('visibility', 'visible' );
  } else {
    jQuery('#selected_games_stats_wrapper').removeClass('sticky');
  }
}


function issueMark() {
  var values = [];
  var html   = '';

  jQuery(".tbl_audit_issue tbody tr").each(function () {
    var gameName  = jQuery(this).find(".game-name").attr("id");
    var gn        = jQuery(this).find(".game-name").html();
    var sysqty    = jQuery(this).find(".system-qty").html();
    var reportqty = jQuery(this).find("#report-qty").val();
    var gameissue = jQuery(this).find("#markissue").val();
    var gamecode  = jQuery(this).find(".gamecode").attr("id");
  var expectedqty    = jQuery(this).find(".exp-qty").html();
  var requestqty = jQuery(this).find(".request-qty").html();

    if (reportqty) {
      if (reportqty != expectedqty  && gameissue == 'none') {
        html += "<div class='error-issue-mgs'>Please select issue for " + gn + "</div>";
      }
    }
    else {
      if (gameissue != 'none') {
        html += "<div class='error-issue-mgs'>Please enter reported quantity for " + gn + "</div>";
      }
    }

    values.push({gname:gameName, sqty:sysqty, rqty:reportqty, gissue:gameissue, gcode:gamecode, exp:expectedqty, reqtqty:requestqty});
  });

  if (html) {
    jQuery("table.tbl_audit_issue .form-pc-error").remove();
    jQuery("table.tbl_audit_issue").append("<div class='form-pc-error'>" + html + "</div>");
  }
  else {
    jQuery.ajax({
      url: "/ajax/inv-adu-markissue",
      type: "POST",
      data: {inv_vals : values},
      success: function (data) {
        window.location.href = "/game-issue-report";
      }
    });
  }
}

function pcissueMark() {
  var values = [];
  var html   = '';

  jQuery(".tbl_pc_audit_issue tbody tr").each(function () {
    var playcenter = jQuery("#pc_id").html();
    var gameName   = jQuery(this).find(".game-name").attr("id");
    var gn         = jQuery(this).find(".game-name").html();
    var sysqty     = jQuery(this).find(".system-qty").html();
    var reportqty  = jQuery(this).find("#report-qty").val();
    var gameissue  = jQuery(this).find("#markissue").val();
    var pcinvenid  = jQuery(this).find(".pc-inv-id").attr("id");

    if (reportqty) {
      if (reportqty != sysqty && gameissue == 'none') {
        html += "<div class='error-issue-mgs'>Please select issue for " + gn + ".</div>";
      }

      if (parseInt(reportqty) > parseInt(sysqty)) {
        html += "<div class='error-issue-mgs'>Reported quantity must be less than System quantity</div>";
      }
    }
    else {
      if (gameissue != 'none') {
        html += "<div class='error-issue-mgs'>Please enter reported quantity for " + gn + "</div>";
      }
    }

    values.push({gname:gameName, sqty:sysqty, rqty:reportqty, gissue:gameissue, pcid:playcenter, pcinvid:pcinvenid});
  });

  if (html) {
    jQuery("table.tbl_pc_audit_issue .form-pc-error").remove();
    jQuery("table.tbl_pc_audit_issue").append("<div class='form-pc-error'>" + html + "</div>");
  }
  else {
    jQuery.ajax({
      url: "/ajax/pc-inv-markissue",
      type: "POST",
      data: {inv_vals : values},
      success: function (data) {
        window.location.href = "/playcenter-issue-report";
      }
    });
  }
}

jQuery(".div_inv_status").click(function () {
  var nid_txt = jQuery(this).html();
  var catg    = jQuery(this).attr("catg");
  jQuery(".inv-audit-throbber").remove();
  jQuery('#mark_issue_audit_wrapper').remove();
  jQuery('#inventory_audit_wrapper').append('<div id="mark_issue_audit_wrapper"></div>');
  jQuery(this).append('<img class="inv-audit-throbber" src="/core/modules/quickedit/images/icon-throbber.gif">');
  var nid = jQuery(this).attr("id");

  if (nid_txt == 0) {
    jQuery('#curr-inv-status').html("<div class='error-issue-mgs'>No Data Available</div>");
    jQuery(".inv-audit-throbber").remove();
  }
  else {
    jQuery.ajax({
      url: "/ajax/current-inv",
      type: "POST",
      data: {"inv_nids" : nid,"catg":catg},
      success: function (data) {
        jQuery(".inv-audit-throbber").remove();
        jQuery('#curr-inv-status').html(data);
      }
    });
  }
});

// Hide/show.
jQuery(".path-generate-request-gen button.btn_game_detail").click(function () {
  var btnName      = jQuery(this).attr("name");
  var grid3wrapper = btnName.replace("btn_game_details_", "grid3_wrapper_");
  jQuery(".grid3_wrapper_table").hide();

  jQuery(document).ajaxComplete(function () {
    jQuery("#" + grid3wrapper).show();
  });
});

// Js for user manual.
jQuery('.help-section .dropdown-toggle').click(function () {
  jQuery('.help-section .dropdown-toggle').removeClass('help-toggle-active');
  jQuery(this).addClass('help-toggle-active');
});

// START: JS validation for No. of Kids section of Play Center.
jQuery("#edit-field-primary-check-pre-primary").change(function () {
  if (jQuery(this).prop("checked") == false) {
    var ppboys  = jQuery("#edit-field-pre-primary-boys-0-value").val();
    var ppgirls = jQuery("#edit-field-pre-primary-girls-0-value").val();

    if ((ppboys && ppboys != 0) || (ppgirls && ppgirls != 0)) {
      jQuery(this).prop("checked", "true");
      jQuery(this).parent().css("color", "#a94442");
      jQuery(".messages__wrapper.custom_no_kids_alert").remove();
      jQuery('<div class="messages__wrapper custom_no_kids_alert"><div class="alert alert-danger alert-dismissible" role="alert" aria-label="Error message"><button type="button" role="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><h2 class="sr-only">Error message</h2><p>De-selection of Pre-Primary is not allowed since number of Pre-Primary kids is not zero.</p></div></div>').insertBefore("#edit-field-primary-check--wrapper .fieldset-wrapper");
    }
    else {
      jQuery(this).parent().css("color", "#333333");
      jQuery(".messages__wrapper.custom_no_kids_alert").remove();
    }
  }
});

jQuery("#edit-field-primary-check-primary").change(function () {
  if (jQuery(this).prop("checked") == false) {
    var priboys  = jQuery("#edit-field-prima-0-value").val();
    var prigirls = jQuery("#edit-field-pri-girls-0-value").val();

    if ((priboys && priboys != 0) || (prigirls && prigirls != 0)) {
      jQuery(this).prop("checked", "true");
      jQuery(this).parent().css("color", "#a94442");
      jQuery(".messages__wrapper.custom_no_kids_alert").remove();
      jQuery('<div class="messages__wrapper custom_no_kids_alert"><div class="alert alert-danger alert-dismissible" role="alert" aria-label="Error message"><button type="button" role="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><h2 class="sr-only">Error message</h2><p>De-selection of Primary is not allowed since number of Primary kids is not zero.</p></div></div>').insertBefore("#edit-field-primary-check--wrapper .fieldset-wrapper");
    }
    else {
      jQuery(this).parent().css("color", "#333333");
      jQuery(".messages__wrapper.custom_no_kids_alert").remove();
    }
  }
});

jQuery("#edit-field-primary-check-secondary").change(function () {
  if (jQuery(this).prop("checked") == false) {
    var secboys  = jQuery("#edit-field-second-0-value").val();
    var secgirls = jQuery("#edit-field-secondry-girls-0-value").val();

    if ((secboys && secboys != 0) || (secgirls && secgirls != 0)) {
      jQuery(this).prop("checked", "true");
      jQuery(this).parent().css("color", "#a94442");
      jQuery(".messages__wrapper.custom_no_kids_alert").remove();
      jQuery('<div class="messages__wrapper custom_no_kids_alert"><div class="alert alert-danger alert-dismissible" role="alert" aria-label="Error message"><button type="button" role="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><h2 class="sr-only">Error message</h2><p>De-selection of Secondary is not allowed since number of Secondary kids is not zero.</p></div></div>').insertBefore("#edit-field-primary-check--wrapper .fieldset-wrapper");
    }
    else {
      jQuery(this).parent().css("color", "#333333");
      jQuery(".messages__wrapper.custom_no_kids_alert").remove();
    }
  }
});
// END: JS validation for No. of Kids section of Play Center.
// START: JS validation for Source: Pickup.
jQuery("#edit-field-source").change(function () {
  if (jQuery(this).val() == "Pickup") {
    jQuery("#edit-field-status-source-pickup").val("schedule");
  }
  else {
    jQuery("#edit-field-status-source-pickup").val("_none");
  }
});

// Source pickup date restriction.
jQuery("#edit-field-source").change(function () {
  var  min = max = '';
  jQuery('#edit-field-date-of-pickup-sp-0-value-date').attr({'min':'1900-01-01'});
  jQuery('#edit-field-date-of-pickup-sp-0-value-date').attr({'max':'2050-12-31'});
  var today = new Date();
  var dd    = (parseInt(today.getDate()) < 10) ? '0' + today.getDate() : today.getDate();
  var mm    = (parseInt(today.getMonth() + 1) < 10) ? '0' + (today.getMonth() + 1) : today.getMonth() + 1; // January is 0!
  var yyyy  = today.getFullYear();

  if (jQuery(this).val() == "Pickup") {
    min    = yyyy + '-' + mm + '-' + dd;
    maxd   = today.setMonth(today.getMonth() + 7);
    maxd   = new Date(maxd);
    maxm   = (parseInt(maxd.getMonth()) < 10) ? '0' + maxd.getMonth() : maxd.getMonth();
    maxday = (parseInt(maxd.getDate()) < 10) ? '0' + maxd.getDate() : maxd.getDate();
    max    = maxd.getFullYear() + '-' + maxm + '-' + maxday;
    jQuery('#edit-field-date-of-pickup-sp-0-value-date').attr({'min' : min});
    jQuery('#edit-field-date-of-pickup-sp-0-value-date').attr({'max' : max});
  }

  if (jQuery(this).val() == "Dropoff") {
    max    = yyyy + '-' + mm + '-' + dd;
    mind   = today.setMonth(today.getMonth() - 5);
    mind   = new Date(mind);
    minm   = (parseInt(mind.getMonth()) < 10) ? '0' + mind.getMonth() : mind.getMonth();
    minday = (parseInt(mind.getDate()) < 10) ? '0' + mind.getDate() : mind.getDate();
    min    = mind.getFullYear() + '-' + minm + '-' + minday;
    jQuery('#edit-field-date-of-pickup-sp-0-value-date').attr({'max' : max});
    jQuery('#edit-field-date-of-pickup-sp-0-value-date').attr({'min' : min});
  }
});

//resize
jQuery(document).ready(function () {
  jQuery(window).on("resize", function (e) {
    checkScreenSize();
  });

  checkScreenSize();

  function checkScreenSize(){
    var newWindowWidth = jQuery(window).width();

    if (newWindowWidth < 768) {
      jQuery('#block-welcomeuserblock').insertBefore('.navbar-header .navbar-toggle');
    }
    else {
      jQuery('#block-welcomeuserblock').insertBefore('#block-customlogout');
    }
  }
});

jQuery(".path-pending-request-details table tbody tr").not(".request-category-details table tbody tr").on('keyup change', function () {
  var sum = 0;
  var total_qty = jQuery(".total-req-qty").html();

  jQuery(".views-field-form-field-field-packed-quantity input").each(function() {
    sum += +jQuery(this).val();
  });

  var total_re_qty = total_qty - sum;

  jQuery(".total-rem-qty").html(total_re_qty);

  var category = jQuery(this).find(".views-field-field-category").html().trim();
  var cat_sum  = 0;

  jQuery(".path-pending-request-details table tbody tr").not(".request-category-details table tbody tr").each(function() {
    if (jQuery(this).find(".views-field-field-category").html().trim() == category) {
      cat_sum += +jQuery(this).find('input').val();
    }
  });

  jQuery(".path-pending-request-details .request-category-details table tbody ." + category).html(cat_sum);

  jQuery(".request-category-details .Total").html(sum);
});

jQuery(".views-field-form-field-field-packed-quantity input").keypress(function (e) {
  // If the letter is not digit then display error and don't type anything.
  if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    jQuery(this).attr("placeholder", "Digits Only");
    return false;
  }
  else {
    jQuery(this).attr("placeholder", "");
  }
});

jQuery(document).ready(function () {
  var i = 1;

  jQuery(".request-category-details table tbody tr td").each(function () {
    var x = jQuery(".request-category-details table thead tr th:nth-child(" + i + ")").html();
    jQuery(this).prepend("<span class='"+x+"'>0</span> / ");
    i++;
  });

  var total = 0;

  jQuery(".request-cat-details table tbody tr td").each(function () {
    var category = jQuery(".request-cat-details table thead tr th:nth-child(" + i + ")").html();
    var cat_sum = 0;

    jQuery(".view-game-request-details table tbody tr").not(".request-cat-details table tbody tr").each(function() {
      if (jQuery(this).find(".views-field-field-category").html().trim() == category) {
        if (jQuery.isNumeric(jQuery(this).find('.views-field-field-packed-quantity').html())) {
          cat_sum += +jQuery(this).find('.views-field-field-packed-quantity').html();
          total   += +jQuery(this).find('.views-field-field-packed-quantity').html();
        }
      }
    });

    jQuery(this).prepend("<span class='" + category + "'>" + cat_sum + "</span> / ");
    i++;
  });

  jQuery(".request-cat-details .Total").html(total);


  //packed-req-details
  var total_qty = jQuery(".path-packed-request-details .tot-req-qty").html();
  var packed_qty = jQuery(".path-packed-request-details .to-packed-qty").html();
  //~ jQuery(".im_u.path-packed-request-details .view-content ").after("<div class='footer-req-detail'><div class='tqty-pen-detail'>" + total_qty + "</div><div class='tqty-rem-detail'><span>Total Packed Quantity</span> : <span class='to-packed-qty'>" + packed_qty + "</span></div></div>" );

  //dispatched-req-details
  var total_qty = jQuery(".path-dispatched-request-details .tot-req-qty").html();
  var dispatch = jQuery(".path-dispatched-request-details .total-dis-qty").html();
  //~ jQuery(".im_u.path-dispatched-request-details .view-content").after("<div class='footer-req-detail'><div class='tqty-pen-detail'>" + total_qty + "</div><div class='tqty-rem-detail'><span>Total Dispatched Quantity</span> : <span class='total-rem-qty'>" + dispatch + "</span></div></div>" );

  //delivered-req-details
  var total_qty = jQuery(".path-delivered-request-details .tot-req-qty").html();
  var delivered = jQuery(".path-delivered-request-details .total-delivered-qty").html();
  //~ jQuery(".im_u.path-delivered-request-details .view-content ").after("<div class='footer-req-detail'><div class='tqty-pen-detail'>" + total_qty + "</div><div class='tqty-rem-detail'><span>Total Delivered Quantity</span> : <span class='total-rem-qty'>" + delivered + "</span></div></div>" );

  //closed-re-details
  var total_qty = jQuery(".path-closed-request-details .tot-req-qty").html();
  var delivered = jQuery(".path-closed-request-details .total-delivered-qty").html();
  //~ jQuery(".im_u.path-closed-request-details .view-content ").after("<div class='footer-req-detail'><div class='tqty-pen-detail'>" + total_qty + "</div><div class='tqty-rem-detail'><span>Total Delivered Quantity</span> : <span class='total-rem-qty'>" + delivered + "</span></div></div>" );

});

function check_selected_games_stats() {
  var hideflag = 0;
  var total_count = 0;

  jQuery("#selected_games_stats span").not("#selected_games_stats span.total").each(function () {
    if (jQuery(this).html() != 0) {
      total_count += +parseInt(jQuery(this).html());
      hideflag = 1;
    }
  });

  jQuery("#selected_games_stats span.total").html(total_count);

  if (hideflag == 0) {
    jQuery(".btn_final_generate_Request").css("display", "none");
  }
  else {
    jQuery(".btn_final_generate_Request").css("display", "block");
  }
}

function sticky_relocate() {
  var window_top = jQuery(window).scrollTop();
  var div_top = jQuery('#scroll-content').offset().top;
  var get_table_width = jQuery('.req-detail .view-content .table-responsive table').width();

  if (window_top > div_top) {
    jQuery('.req-detail .req-cat-detail-inner').addClass('stick1');
    jQuery('.req-detail .req-cat-detail-inner.stick1').css('top', ( jQuery('#navbar-collapse').height() ) );
    jQuery('.req-detail .req-cat-detail-inner, .req-detail .view-content thead').css('width', get_table_width );
    jQuery('.req-detail .sticky-header').css('top', ( jQuery('#navbar-collapse').height() + jQuery('.req-cat-detail-inner.stick1').height() - 20));
    jQuery('.req-detail .sticky-header').css('visibility', 'visible' );
  } else {
    jQuery('.req-detail .req-cat-detail-inner').removeClass('stick1');
  }
}

jQuery(function() {
  if (jQuery('.view').hasClass('req-detail')) {
    jQuery(window).scroll(sticky_relocate);
    sticky_relocate();
  }
});

