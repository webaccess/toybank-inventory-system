<?php

namespace Drupal\toybank_custom\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements inventory history form.
 */
class ToybankCustomInventoryHistory extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'example_form';
  }

  /**
   * Ajax callback to return inventory history data.
   */
  public function ajaxAudit() {
    $nid     = $_POST['inv_nids'];
    $catg    = $_POST['catg'];
    $id      = explode('_', $nid);
    $allnid  = implode(",", $id);
    $content = NULL;
    $sql     = db_query("SELECT n.title, q.field_total_inventory_value, n.nid,c.field_game_code_value, tx.name AS category FROM tban_node_field_data as n
                         LEFT JOIN tban_node__field_inv_game_name as g ON g.field_inv_game_name_target_id = n.nid
                         LEFT JOIN tban_node__field_total_inventory as q ON q.entity_id = g.entity_id
                         LEFT JOIN tban_node__field_game_code as c ON c.entity_id = n.nid
                         LEFT JOIN tban_node__field_category as cg ON cg.entity_id = n.nid
                         LEFT JOIN tban_taxonomy_term_field_data tx ON cg.field_category_target_id = tx.tid
                         WHERE n.type = 'game' AND n.nid IN ({$allnid}) AND q.field_total_inventory_value <>'' ")->fetchAll();

    $vid   = 'inventory_mark_issue';
    $con   = '';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);

    foreach ($terms as $term) {
      $con .= "<option value={$term->tid}>{$term->name}</option>";
    }

    if (!empty($sql)) {
      $content .= "
        <table class='tbl_audit_issue table table-responsive'>
          <thead>
            <tr>
              <th>Game Name</th>
              <th>Category</th>
              <th>Subcategory</th>
              <th>System Quantity</th>
            </tr>
          </thead>
          <tbody>";

      foreach ($sql as $val) {
        $content .= "
            <tr>
              <td class='game-name' id=" . $val->nid . ">" . $val->title . " (" . $val->field_game_code_value . ")</td>
              <td class='catg-ad'>" . $val->category . "</td>
              <td class='subcatg-ad'>" . $catg . "</td>
              <td class='system-qty'>" . $val->field_total_inventory_value . "</td>
              <td class='gamecode' id=" . $val->field_game_code_value . " style='display:none'></td>
            </tr>";
      }
      $content .= "</tbody>
              </table>";
    }
    return new AjaxResponse($content);
  }

  /**
   * Ajax callback to return inventory history data.
   */
  public function ajaxUpdateView(array $form, FormStateInterface $form_state) {
    $content    = '';
    $get_values = $form_state->getvalues();

    if (empty($get_values['start_date']) && empty($get_values['end_date'])) {

    }
    else {
      $start_date   = $get_values['start_date'];
      $end_date     = $get_values['end_date'] . " 23:59";
      $start_date   = strtotime($start_date);
      $end_date     = strtotime($end_date);
      $sql          = db_query("SELECT tx.name AS category, ag.field_sub_catgeory_value AS subcategory, nd.title AS gamename, nd.nid
                                FROM tban_node__field_sub_catgeory ag
                                LEFT JOIN tban_node_field_data nd ON ag.entity_id = nd.nid
                                LEFT JOIN tban_node__field_category ct ON nd.nid = ct.entity_id
                                LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                                WHERE nd.type='game' AND nd.created >= {$start_date} AND nd.created <= {$end_date}")->fetchAll();
      $final_result = [];
      $theadarray   = [
        'Alphabetical',
        'Block',
        'General',
        'Numerical',
        'Puzzle',
        'Strategy',
      ];

      foreach ($theadarray as $th) {
        $al_total[$th] = 0;
      }

      if (!empty($sql)) {
        $content .= "<table class='tbl_inventory table table-responsive'><thead><tr><th></th><th>Alphabetical</th><th>Block</th><th>General</th><th>Numerical</th><th>Puzzle</th><th>Strategy</th><th>Total</th></tr></thead><tbody>";

        foreach ($sql as $val) {
          $final_result[$val->subcategory][$val->category][] = $val->nid;
        }

        $col_total = 0;

        foreach ($final_result as $r => $row) {
          $row_total = 0;
          $content  .= "<tr><td>" . $r . "</td>";

          foreach ($theadarray as $th) {
            if (!empty($final_result[$r][$th])) {
              $content .= '<td class="inventory_adut">';
              $nid_arr  = [];

              foreach ($final_result[$r][$th] as $nid) {
                $nid_arr[] = $nid;
                $sql       = db_query("SELECT title as gamename FROM tban_node_field_data WHERE nid = {$nid}")->fetchAssoc();

                if (!empty($sql)) {
                  // $gamename[$nid] = $sql['gamename'];
                }
              }

              $nids          = implode("_", $nid_arr);
              $content      .= '<div class="div_inv inv_his" id="' . $nids . '" catg="' . $r . '">' . count($final_result[$r][$th]) . '</div>';
              $content      .= '</td>';
              $row_total     = $row_total + count($final_result[$r][$th]);
              $al_total[$th] = $al_total[$th] + count($final_result[$r][$th]);
            }
            else {
              $content .= '<td><div class="inv_his">0</div></td>';
            }
          }

          $content .= "<td>" . $row_total . "</td></tr>";
        }

        $content .= "<tr><td>Total</td>";

        foreach ($theadarray as $th) {
          $content  .= "<td>" . $al_total[$th] . "</td>";
          $col_total = $col_total + $al_total[$th];
        }

        $content .= "<td>" . $col_total . "</td></tr>";
        $content .= "</tbody></table>";
      }
      else {
        $content .= "Data Not Available";
      }
    }

    $form['inventory_audit']['#value'] = $content;
    return $form['inventory_audit'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['start_date'] = [
      '#title'       => 'Start Date',
      '#type'        => 'date',
      '#date_format' => 'Y-m-d',
      '#required'    => TRUE,
      '#prefix'      => '<div id ="startDate" class = "startDate">',
      '#suffix'      => '</div>',
    ];

    $form['end_date'] = [
      '#title'       => 'End Date',
      '#type'        => 'date',
      '#date_format' => 'Y-m-d',
      '#attributes'  => [
        'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
      ],
      '#prefix' => '<div id ="endDate" class = "endDate">',
      '#suffix' => '</div>',
    ];

    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Apply',
      '#ajax'  => [
        'event'    => 'click',
        'callback' => '::ajaxUpdateView',
        'wrapper'  => 'inventory_audit_wrapper',
        'effect'   => 'fade',
      ],
    ];

    $form['inventory_audit'] = [
      '#type'   => 'fieldset',
      '#prefix' => '<div id="inventory_audit_wrapper" class="quick-contact__form col-xs-12 col-md-12">',
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
