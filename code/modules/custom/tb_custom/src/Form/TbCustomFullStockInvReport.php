<?php

namespace Drupal\tb_custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements full stock inventory report form.
 */
class TbCustomFullStockInvReport extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'full_stocks_report_inv';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['form_start'] = [
      '#markup' => "<div class='view-filters form-group views-exposed-form'>",
    ];

    $form['fs_date_start'] = [
      '#title'       => t('Start Date'),
      '#type'        => 'date',
      '#date_format' => 'Y-m-d',
      '#required'    => TRUE,
      '#attributes'  => [
        'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
      ],
    ];

    $form['fs_date_end'] = [
      '#title'       => t('End Date'),
      '#type'        => 'date',
      '#date_format' => 'Y-m-d',
      '#attributes'  => [
        'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
      ],
    ];

    $form['fs_game'] = [
      '#title'                   => t('Game'),
      '#type'                    => 'textfield',
      '#autocomplete_route_name' => 'tb_custom.games_autocomplete',
      '#attributes'              => ['class' => ['game_fs_reports']],
      '#prefix'                  => '<div class="game_name_report">',
      '#suffix'                  => '</div>',
    ];

    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Apply',
      '#ajax'  => [
        'event'    => 'click',
        'callback' => '::fullStockInv',
        'wrapper'  => 'fullstock_inv_wrapper',
        'effect'   => 'fade',
      ],
    ];

    $form['form_end'] = [
      '#markup' => "</div>",
    ];

    $form['fullStockInven'] = [
      '#type'   => 'fieldset',
      '#prefix' => '<div id="fullstock_inv_wrapper" class="view-content quick-contact__form col-xs-12 col-md-12">',
      '#suffix' => '</div>',
      '#value'  => TbCustomFullStockInvReport::getFullStockData('', date('Y-m-d', REQUEST_TIME), ''),
    ];

    return $form;
  }

  /**
   * Returns full stock inventory data.
   */
  public function fullStockInv(array $form, FormStateInterface $form_state) {
    $content    = '';
    $start_date = $form_state->getValue('fs_date_start');
    $end_date   = $form_state->getValue('fs_date_end');
    $game_name  = $form_state->getValue('fs_game');

    if (!empty($start_date) && empty($end_date)) {
      $end_date = date('Y-m-d', REQUEST_TIME);
    }

    $content = TbCustomFullStockInvReport::getFullStockData($start_date, $end_date, $game_name);
    $form['fullStockInven']['#value'] = $content;
    return $form['fullStockInven'];
  }

  /**
   * Get full stock data.
   */
  public function getFullStockData($start_date, $end_date, $game_name) {
    $result              = $final_result = [];
    $game_name_condition = $content = "";

    if (!empty($game_name)) {
      if (strpos($game_name, '(')) {
        $game_id             = explode('(', $game_name);
        $game_id             = str_replace(')', '', $game_id[1]);
        $game_name_condition = "AND nd.nid = $game_id";
      }
    }

    if ((!empty($start_date) && !empty($end_date)) || !empty($end_date)) {
      if (!empty($start_date) && !empty($end_date)) {
        $start_date = strtotime($start_date);
        $end_date   = strtotime($end_date . ' 23:59');
        $fssql      = db_query("SELECT tx.name AS subcategory,ag.field_sub_catgeory_value AS category, nd.title AS gamename, nd.nid as game_id,qty.field_total_inventory_value AS total_qty
                                FROM tban_node__field_sub_catgeory ag
                                LEFT JOIN tban_node_field_data nd ON ag.entity_id = nd.nid
                                LEFT JOIN tban_node__field_category ct ON nd.nid = ct.entity_id
                                LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                                LEFT JOIN tban_node__field_inv_game_name as g ON g.field_inv_game_name_target_id = nd.nid
                                LEFT JOIN tban_node__field_total_inventory qty ON qty.entity_id = g.entity_id
                                WHERE nd.type = 'game' AND nd.created >= {$start_date} AND nd.created <= {$end_date} $game_name_condition")->fetchAll();
      }
      elseif (!empty($end_date) && empty($start_date)) {
        $end_date = strtotime($end_date . ' 23:59');
        $fssql    = db_query("SELECT tx.name AS subcategory, ag.field_sub_catgeory_value AS category, nd.title AS gamename, nd.nid as game_id, qty.field_total_inventory_value AS total_qty
                              FROM tban_node__field_sub_catgeory ag
                              LEFT JOIN tban_node_field_data nd ON ag.entity_id = nd.nid
                              LEFT JOIN tban_node__field_category ct ON nd.nid = ct.entity_id
                              LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                              LEFT JOIN tban_node__field_inv_game_name as g ON g.field_inv_game_name_target_id = nd.nid
                              LEFT JOIN tban_node__field_total_inventory qty ON qty.entity_id = g.entity_id
                              WHERE nd.type = 'game'AND nd.created <= {$end_date} $game_name_condition")->fetchAll();
      }

      if (!empty($fssql)) {
        global $base_url;
        $catg      = ['Pre-Primary', 'Primary', 'Secondary'];
        $sub_cat_q = db_query("SELECT name FROM tban_taxonomy_term_field_data WHERE vid = 'category'")->fetchAll();

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

        $content .= "<table class='tbl_inventory'>
                       <thead>
                         <tr><th colspan='8' rowspan='2'><img src='$base_url/sites/default/files/tb-logo_0.png'></th><th colspan='12' rowspan='2'></th><th colspan='6'>FY 2019-20</th></tr>
                         <tr><th colspan='20'>TOY INVENTORY AT WAREHOUSE</th></tr>
                         <tr><th rowspan='2'>DS</th><th rowspan='2'>WD</th><th colspan='6'>PP</th><th colspan='6'>PRI</th><th colspan='6'>SEC</th><th rowspan='2'>Details (Donor/Quantity)</th><th rowspan='2'>Added By (Date/Sign)</th></tr>
                         <tr><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th></tr>
                       </thead>
                       <tbody>
                         <tr><td></td><td></td>";

        foreach ($final_result as $v) {
          $content .= "<td>" . $v['Strategy'] . "</td>";
          $content .= "<td>" . $v['Puzzle'] . "</td>";
          $content .= "<td>" . $v['Block'] . "</td>";
          $content .= "<td>" . $v['Alphabetical'] . "</td>";
          $content .= "<td>" . $v['Numerical'] . "</td>";
          $content .= "<td>" . $v['General'] . "</td>";
        }

        $content .= "<td></td><td></td></tr></tbody></table>";
        $content .= "<br><div class='date-printed-on'>Printed on: " . date("d/m/Y") . "</div>";
        $content .= "<div class='report-print-wrapper'><button onclick='window.print();return false;'>Print</button></div>";
      }
      else {
        $content .= "<h3>Data not available<h3>";
      }

      return $content;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
  }

}
