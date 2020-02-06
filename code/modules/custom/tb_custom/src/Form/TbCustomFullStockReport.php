<?php

namespace Drupal\tb_custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements full stock report form.
 */
class TbCustomFullStockReport extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'full_stocks_report';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['form_start'] = [
      '#markup' => "<div class='view-filters form-group views-exposed-form'>",
    ];

    //~ $form['fs_date'] = [
      //~ '#title'       => t('Date'),
      //~ '#type'        => 'date',
      //~ '#date_format' => 'Y-m-d',
      //~ '#required'    => TRUE,
      //~ '#attributes'  => [
        //~ 'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
      //~ ],
    //~ ];

    $form['fs_play_centers'] = [
      '#title'                   => t('Play Center'),
      '#type'                    => 'textfield',
      '#autocomplete_route_name' => 'tb_custom.playcenter_autocomplete',
      '#required'                => TRUE,
      '#attributes'              => ['class' => ['play_center_fullstock_rep']],
      '#prefix'                  => '<div class="pcenter_fullstock_rep">',
      '#suffix'                  => '</div>',
    ];

    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Apply',
      '#ajax'  => [
        'event'    => 'click',
        'callback' => '::ajaxGetFullStock',
        'wrapper'  => 'full_stock_wrapper',
        'effect'   => 'fade',
      ],
    ];

    $form['form_end'] = [
      '#markup' => "</div>",
    ];

    $form['full_stock'] = [
      '#type'   => 'fieldset',
      '#prefix' => '<div id="full_stock_wrapper" class="view-content quick-contact__form col-xs-12 col-md-12">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * Return full stock data.
   */
  public function ajaxGetFullStock(array $form, FormStateInterface $form_state) {
    $content     = $playc_name = '';
    $fs_play_cen = $form_state->getValue('fs_play_centers');
    $fs_play_cen = explode("(", $fs_play_cen);
    $fs_play_cen = str_replace(')', '', $fs_play_cen[1]);
    $result      = $final_result = [];

    if (!empty($fs_play_cen)) {
      global $base_url;
      //~ $fs_date = strtotime($form_state->getValue('fs_date') . ' 23:59');
      $fssql   = db_query("SELECT sc.field_sub_catgeory_value as category, tx.name as subcategory, pcgn.field_pc_inv_game_name_target_id as game_id, pcn.field_play_center_inventory_name_target_id as playcenter_id, tqty.field_pc_total_inventory_value as total_qty FROM tban_node__field_pc_inv_game_name as pcgn
                           LEFT JOIN tban_node__field_play_center_inventory_name as pcn ON pcgn.entity_id = pcn.entity_id
                           LEFT JOIN tban_node__field_pc_total_inventory as tqty ON tqty.entity_id = pcgn.entity_id
                           LEFT JOIN tban_node__field_category ct ON pcgn.field_pc_inv_game_name_target_id = ct.entity_id
                           LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                           LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = pcgn.field_pc_inv_game_name_target_id
                           LEFT JOIN tban_node_field_data nd ON nd.nid = pcgn.entity_id
                           WHERE pcn.field_play_center_inventory_name_target_id = {$fs_play_cen}")->fetchAll();

      $catg      = ['Pre-Primary', 'Primary', 'Secondary'];
      $sub_cat_q = db_query("SELECT name FROM tban_taxonomy_term_field_data WHERE vid='category'")->fetchAll();

      foreach ($catg as $c) {
        foreach ($sub_cat_q as $sc) {
          $final_result[$c][$sc->name] = 0;
        }
      }

      foreach ($fssql as $v) {
        $result[] = [
          'category'    => $v->category,
          'subcategory' => $v->subcategory,
          'quantity'    => $v->total_qty,
        ];
      }

      foreach ($result as $fr) {
        $final_result[$fr['category']][$fr['subcategory']] = $final_result[$fr['category']][$fr['subcategory']] + $fr['quantity'];
      }

      $play_cnt_name = db_query("SELECT title FROM tban_node_field_data WHERE type='play_center' AND nid='$fs_play_cen'")->fetchAssoc();

      if (!empty($play_cnt_name)) {
        $playc_name = $play_cnt_name['title'];
      }

      $content .= "<table class='tbl_inventory'>";
      $content .= "<thead>
                     <tr><th colspan='8' rowspan='2'><img src='$base_url/sites/default/files/tb-logo_0.png'></th><th colspan='12' rowspan='2'></th><th colspan='6'>FY 2019-20</th></tr>
                     <tr><th colspan='20'>TOY INVENTORY AT {$playc_name}</th></tr>
                     <tr><th rowspan='2'>DS</th><th rowspan='2'>WD</th><th colspan='6'>PP</th><th colspan='6'>PRI</th><th colspan='6'>SEC</th><th rowspan='2'>Details (Donor/Quantity)</th><th rowspan='2'>Added By (Date/Sign)</th></tr>";
      $content .= "<tr><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th></tr>";
      $content .= "</thead>";
      $content .= "<tbody><tr><td></td><td></td>";

      foreach ($final_result as $v) {
        $content .= "<td>" . $v['Strategy'] . "</td>";
        $content .= "<td>" . $v['Puzzle'] . "</td>";
        $content .= "<td>" . $v['Block'] . "</td>";
        $content .= "<td>" . $v['Alphabetical'] . "</td>";
        $content .= "<td>" . $v['Numerical'] . "</td>";
        $content .= "<td>" . $v['General'] . "</td>";
      }

      $content .= "<td></td><td></td>";
      $content .= "</tr>";
      $content .= "</tbody></table>";
      $content .= "<br><div class='date-printed-on'>Printed on: " . date("d/m/Y") . "</div>";
      $content .= "<div class='report-print-wrapper'><button onclick='window.print();return false;'>Print</button></div>";
    }

    $form['full_stock']['#value'] = $content;
    return $form['full_stock'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
