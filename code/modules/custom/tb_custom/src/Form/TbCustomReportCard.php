<?php

namespace Drupal\tb_custom\Form;

use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Implements report card form.
 */
class TbCustomReportCard extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'request_sheet_report';
  }

  /**
   * Playcenter autocomplete.
   */
  public function reportAutocomplete(Request $request) {
    $results = [];
    $input   = $request->query->get('q');

    if (isset($input)) {
      $user = User::load(\Drupal::currentUser()->id());
      $uid  = $user->get('uid')->value;

      if (in_array('administrator', $user->getRoles()) || in_array('welfare_manager', $user->getRoles())) {
        $sql = db_query("SELECT n.title,n.nid, pc.field_playc_value as pcode
                         FROM tban_node_field_data as n
                         LEFT JOIN tban_node__field_playc as pc ON pc.entity_id= n.nid
                         LEFT JOIN tban_node__field_status as s ON s.entity_id = n.nid
                         WHERE n.type = 'play_center' AND (pc.field_playc_value LIKE '%$input%' OR n.title LIKE '%$input%') AND s.field_status_value = 'Active' ORDER BY pc.field_playc_value LIMIT 10")->fetchAll();
      }
      else {
        $sql = db_query("SELECT nd.title as title ,nd.nid as nid,  pc.field_playc_value as pcode
                         FROM tban_node__field_associated_field_officer fo
                         LEFT JOIN tban_node__field_cluster clust ON fo.entity_id = clust.field_cluster_target_id
                         LEFT JOIN tban_node_field_data nd ON nd.nid = clust.entity_id
                         LEFT JOIN tban_node__field_playc as pc ON pc.entity_id= nd.nid
                         LEFT JOIN tban_node__field_status as s ON s.entity_id = nd.nid
                         WHERE fo.field_associated_field_officer_target_id = {$uid} And nd.type = 'play_center' AND (nd.title LIKE '%$input%' OR pc.field_playc_value LIKE '%$input%') AND s.field_status_value = 'Active' LIMIT 10")->fetchAll();
      }

      if (!empty($sql)) {
        foreach ($sql as $v) {
          $results[] = ['value' => '[' . $v->pcode . '] ' . $v->title . ' (' . $v->nid . ')', 'label' => '[' . $v->pcode . '] ' . $v->title];
        }
      }
    }

    return new JsonResponse($results);
  }

  /**
   * Return report card data.
   */
  public function ajaxGetReportCard(array $form, FormStateInterface $form_state) {
    $content = "";
    $pc_id   = $form_state->getValue('pc_name');
    $pc_id   = explode("(", $pc_id);
    $pc_id   = str_replace(')', '', $pc_id[1]);

    if (empty($pc_id)) {
      $content = '<div data-drupal-messages=""><div class="messages__wrapper"><div class="alert alert-danger alert-dismissible" role="alert" aria-label="Error message"><button type="button" role="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><h2 class="sr-only">Error message</h2><p>' . 'Select Play Center' . '</p></div></div></div>';
    }
    else {
      $sql = db_query("SELECT sc.field_sub_catgeory_value as category,tx.name as subcategory,pcgn.field_pc_inv_game_name_target_id as game_id, pcn.field_play_center_inventory_name_target_id as playcenter_id, tqty.field_pc_total_inventory_value as total_qty FROM tban_node__field_pc_inv_game_name as pcgn
                       LEFT JOIN tban_node__field_play_center_inventory_name as pcn ON pcgn.entity_id = pcn.entity_id
                       LEFT JOIN tban_node__field_pc_total_inventory as tqty ON tqty.entity_id = pcgn.entity_id
                       LEFT JOIN tban_node__field_category ct ON pcgn.field_pc_inv_game_name_target_id = ct.entity_id
                       LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                       LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = pcgn.field_pc_inv_game_name_target_id
                       LEFT JOIN tban_node_field_data nd ON nd.nid = pcgn.entity_id
                       WHERE pcn.field_play_center_inventory_name_target_id ={$pc_id}")->fetchAll();

      if (!empty($sql)) {
        global $base_url;
        $final_result = [];
        $catg         = ['Pre-Primary', 'Primary', 'Secondary'];
        $sub_cat_q    = db_query("SELECT name FROM tban_taxonomy_term_field_data WHERE vid='category'")->fetchAll();

        foreach ($catg as $c) {
          foreach ($sub_cat_q as $sc) {
            $final_result[$c][$sc->name] = 0;
          }
        }

        foreach ($sql as $v) {
          $result[] = [
            'category'    => $v->category,
            'subcategory' => $v->subcategory,
            'quantity'    => $v->total_qty,
          ];
        }

        foreach ($result as $fr) {
          $final_result[$fr['category']][$fr['subcategory']] = $final_result[$fr['category']][$fr['subcategory']] + $fr['quantity'];
        }

        // Code U226_SSS_MUMBAI query.
        $codesql = db_query("SELECT fc.entity_id, CONCAT(pc.field_playc_value, '_', nd.title) as name, nd.title
                             FROM tban_node__field_cluster as fc
                             LEFT JOIN tban_node_field_data as nd ON fc.field_cluster_target_id = nd.nid
                             LEFT JOIN tban_node__field_playc as pc ON pc.entity_id =fc.entity_id
                             LEFT JOIN tban_node__field_partner as p ON p.entity_id= fc.entity_id
                             WHERE fc.entity_id = {$pc_id}")->fetchAll();

        if (!empty($codesql)) {
          $name_code    = $codesql[0]->name;
          $cluster_name = $codesql[0]->title;
        }

        $content .= "<div class='report-card-page1'><div class = 'toy_logo2'><img src='$base_url/sites/default/files/tb-logo_0.png'></div>";
        $content .= "<div class ='toy_ft'><table class='tbl_inventory tbl_cl_yrs'>";
        $content .= "<tbody><tr><td colspan='3'>$name_code</td></tr><tr><td></td><td>$cluster_name</td><td> FY 2018-19 </td></tr></tbody></table></div><br>";
        $content .= "<div class='report-card-p-details'><div class='report-card-p-title'>Partner Details</div></div>";

        // Center detail tbl.
        $content .= "<div class ='center_detail_tb report-card-cen-details'><div class='report-card-p-title'>Center Details</div><table class='cen-de-tbl tbl_inventory'><tbody>";
        $content .= "<tr><td><span class='in_charge_label'>In Charge:</span> <span class='in_charge_val'></span></td></tr>
                     <tr><td><span class='address_label'>Address:</span>  <span class='address_val'></span></td></tr>
                     <tr>
                       <td><span class='ty_center_label'>Type of Center:</span><span class='ty_center_val'></span>
                       <span class='ty_kids_label'>Type of Kids:</span><span class='ty_kids_val'></span></td>
                     </tr>
                     <tr>
                       <td><span class='medium_label'>Medium:</span> <span class='medium_val'></span>
                       <span class='access_label'>Access:</span> <span class='access_val'></span></td>
                     </tr>
                     <tr><td><span class='setup_details_label'>Setup Details</span><span class='setup_details_val'></span></td></tr>
                     <tr><td><span class='playsession_label'>Play Sessions</span><span class='playsesion_val'></span></td></tr>
                     <tr><td><span class='closing_date_label'>Closing Date / Reason</span><span class='closing_date_val'></span></td></tr>";
        $content .= "</tbody></table></div><br>";

        // Toystock report.
        $content .= "<div class='toy_stock_rep'><table class='tbl_inventory'>";
        $content .= "<thead>
                       <tr><th>Date / Sign</th><th colspan='6'>PP</th><th colspan='6'>PRI</th><th colspan='6'>SEC</th></tr>";
        $content .= "<tr><th><u>TOYSTOCK</u></th>
                       <th class='vertical-text'><div><span>Strategy</span></div></th>
                       <th class='vertical-text'><div><span>Puzzle</span></div></th>
                       <th class='vertical-text'><div><span>Block</span></div></th>
                       <th class='vertical-text'><div><span>Alphabetical</span></div></th>
                       <th class='vertical-text'><div><span>Numerical</span></div></th>
                       <th class='vertical-text'><div><span>General</span></div></th>
                       <th class='vertical-text'><div><span>Strategy</span></div></th>
                       <th class='vertical-text'><div><span>Puzzle</span></div></th>
                       <th class='vertical-text'><div><span>Block</span></div></th>
                       <th class='vertical-text'><div><span>Alphabetical</span></div></th>
                       <th class='vertical-text'><div><span>Numerical</span></div></th>
                       <th class='vertical-text'><div><span>General</span></div></th>
                       <th class='vertical-text'><div><span>Strategy</span></div></th>
                       <th class='vertical-text'><div><span>Puzzle</span></div></th>
                       <th class='vertical-text'><div><span>Block</span></div></th>
                       <th class='vertical-text'><div><span>Alphabetical</span></div></th>
                       <th class='vertical-text'><div><span>Numerical</span></div></th>
                       <th class='vertical-text'><div><span>General</span></div></th>
                     </tr>";
        $content .= "</thead>";
        $content .= "<tbody><tr><td>" . date("d-m-Y") . "</td>";

        foreach ($final_result as $v) {
          $content .= "<td>" . $v['Strategy'] . "</td>";
          $content .= "<td>" . $v['Puzzle'] . "</td>";
          $content .= "<td>" . $v['Block'] . "</td>";
          $content .= "<td>" . $v['Alphabetical'] . "</td>";
          $content .= "<td>" . $v['Numerical'] . "</td>";
          $content .= "<td>" . $v['General'] . "</td>";
        }

        $content .= "</tr>";
        $content .= "<tr><td><u>TOY RECEIPT</u></td><td colspan='18'></td></tr>";
        $content .= "<tr><td>" . date("d-m-Y") . "</td>";

        foreach ($final_result as $v) {
          $content .= "<td></td><td></td><td></td><td></td><td></td><td></td>";
        }

        $content .= "</tr>";
        $content .= "</tbody></table></div>";
        $content .= "<br><div class='date-printed-on'>Printed on: " . date("d/m/Y") . "</div></div>";

        // toyimage2.
        $content .= "<div class = 'toy_logo_sec'><img src='$base_url/sites/default/files/tb-logo_0.png'></div>";
        $content .= "<div class ='pc_cls_cat'><span class= 'pc_cls'></span><span class ='date_sec'>FY 2018-19</span></div>";

        // Kids Details.
        $content .= "<div class='kids_details'><table class='tbl_inventory'>
                      <tr>
                        <th><u>Kids Details</u></th>
                        <th>Entered by -></th>
                        <th>&nbsp;&nbsp;</th>
                        <th></th>
                        <th></th>
                      </tr>
                      <tr>
                        <td></td>
                        <td>Current</td>
                        <td></td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr></tr>
                      <tr><td>PP(0-5)</td><td></td><td></td><td></td><td></td></tr>
                      <tr><td>Pri(5-10)</td><td></td><td></td><td></td><td></td></tr>
                      <tr><td>Sec(10-15)</td><td></td><td></td><td></td><td></td></tr>
                      </table></div><br>";

        // Feedback.
        $content .= "<div class='feedback_tbl'><table class='tbl_inventory'><tbody>";

        for ($i = 1; $i <= 8; $i++) {
          $feedback = $tr = '';

          if ($i == 1) {
            $feedback = "<span class='feedback_name'><u>Feedback:</u></span>";
          }

          if ($i == 5) {
            $feedback = "<span class='feedback_name'><u>Notes / Observation:</u></span>";
          }

          if ($i < 5) {
            $tr = "<tr><td><span></span></td></tr>";
          }

          $content .= "<tr>  <td>" . $feedback . "<span ></span><span>Date:</span></td></tr><tr><td><span></span><span>By:</span></td></tr>" . $tr;
        }

        $content .= "</tbody></table></div>";
        $content .= "<br><div class='date-printed-on'>Printed on: " . date("d/m/Y") . "</div>";
        $content .= "<div class='report-print-wrapper'><button onclick='window.print();return false;'>Print</button></div>";
      }
      else {
        $content .= "Data not available.";
      }
    }

    $form['report_card']['#value'] = $content;
    return $form['report_card'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['form_start'] = [
      '#markup' => "<div class='view-filters form-group views-exposed-form'>",
    ];

    $form['pc_name'] = [
      '#title'                   => t('Play Center'),
      '#type'                    => 'textfield',
      '#autocomplete_route_name' => 'tb_custom.pc_report_autocomplete',
    ];

    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Apply',
      '#ajax'  => [
        'event'    => 'click',
        'callback' => '::ajaxGetReportCard',
        'wrapper'  => 'report_card_wrapper',
        'effect'   => 'fade',
      ],
    ];

    $form['form_end'] = [
      '#markup' => "</div>",
    ];

    $form['report_card'] = [
      '#type'   => 'fieldset',
      '#prefix' => '<div id="report_card_wrapper" class="view-content quick-contact__form col-xs-12 col-md-12">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
