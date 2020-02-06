<?php

namespace Drupal\toybank_custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements inventory add stock report form.
 */
class ToybankCustomAddStock extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'addstock_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['form_start'] = [
      '#markup' => '<div class="view-filters form-group views-exposed-form">',
    ];

    $form['addstock_s_date'] = [
      '#title'       => 'Date',
      '#type'        => 'date',
      '#date_format' => 'Y-m-d',
      '#description' => t('Enter the Game added date'),
      '#required'    => TRUE,
      '#attributes'  => [
        'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
      ],
    ];

    $form['game_nid'] = [
      '#type'                    => 'textfield',
      '#title'                   => 'Game',
      '#autocomplete_route_name' => 'tb_custom.games_autocomplete',
      '#description'             => t('Filter the result by Game Name or Game Code'),
      '#prefix'                  => '<div class="game_name_report">',
      '#suffix'                  => '</div>',
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Apply',
      '#ajax'  => [
        'event'    => 'click',
        'callback' => '::ajaxGetAddstock',
        'wrapper'  => 'addstock_wrapper',
        'effect'   => 'fade',
      ],
    ];

    $form['form_end'] = [
      '#markup' => '</div>',
    ];

    $form['addstock_report'] = [
      '#type'   => 'fieldset',
      '#prefix' => '<div id="addstock_wrapper" >',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Ajax callback to return add stock data.
   */
  public function ajaxGetAddstock(array $form, FormStateInterface $form_state) {
    $content = "";
    $date_s  = $form_state->getValue('addstock_s_date');

    if (empty($date_s)) {

    }
    else {
      $date_s              = strtotime($form_state->getValue('addstock_s_date'));
      $date_e              = strtotime($form_state->getValue('addstock_s_date') . " 23:59");
      $result              = $result3 = $old_rev = $new_rev = $final_rev = [];
      $game_name           = $form_state->getValue('game_nid');
      $game_name_condition = '';

      if (!empty($game_name)) {
        if (strpos($game_name, '(')) {
          $game_id             = explode('(', $game_name);
          $game_id             = str_replace(')', '', $game_id[1]);
          $game_name_condition = "AND gn.field_game_name_target_id = $game_id";
        }
      }

      // New Revision.
      $sql = db_query("SELECT nr.nid, nr.vid, nr.changed FROM tban_node_field_revision as nr WHERE nr.nid IN (SELECT nid FROM tban_node_field_data WHERE type = 'inventory') AND nr.changed BETWEEN ($date_s) AND ($date_e)")->fetchAll();

      if (!empty($sql)) {
        foreach ($sql as $v) {
          if ($result[$v->nid]) {
            if ($v->vid > $result[$v->nid]) {
              $result[$v->nid] = $v->vid;
            }
          }
          else {
            $result[$v->nid] = $v->vid;
          }
        }

        $vids = implode(',', $result);
        $nids = implode(',', array_keys($result));
        $sql2 = db_query("SELECT mg.entity_id, mg.revision_id, gn.field_game_name_target_id as game_id, qt.field_quantity_value as quantity, tx.name AS subcatg , sc.field_sub_catgeory_value as catg
                          FROM tban_field_collection_item__field_game_name as gn
                          LEFT JOIN tban_node__field_category ct ON gn.field_game_name_target_id = ct.entity_id
                          LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = ct.entity_id
                          LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                          LEFT JOIN tban_field_collection_item_revision__field_quantity as qt ON gn.entity_id = qt.entity_id
                          LEFT JOIN tban_node_revision__field_multiple_games as mg ON mg.field_multiple_games_value = qt.entity_id AND qt.revision_id = mg.field_multiple_games_revision_id
                          WHERE mg.revision_id IN ($vids) $game_name_condition")->fetchAll();

        if (!empty($sql2)) {
          foreach ($sql2 as $v2) {
            $new_rev[$v2->entity_id . '_' . $v2->game_id] = [
              'catg'     => $v2->catg,
              'subcatg'  => $v2->subcatg,
              'quantity' => $v2->quantity,
            ];
          }
        }

        // Old Revision.
        $sql3 = db_query("SELECT nr.nid,nr.vid
                          FROM tban_node_field_revision as nr
                          WHERE nr.nid IN ($nids) AND nr.changed NOT BETWEEN ($date_s) AND ($date_e)")->fetchAll();

        if (!empty($sql3)) {
          foreach ($sql3 as $v3) {
            if ($result3[$v3->nid]) {
              if ($v3->vid > $result3[$v3->nid]) {
                $result3[$v3->nid] = $v3->vid;
              }
            }
            else {
              $result3[$v3->nid] = $v3->vid;
            }
          }

          $vids3 = implode(',', $result3);
          $sql4  = db_query("SELECT mg.entity_id, mg.revision_id,gn.field_game_name_target_id as game_id, qt.field_quantity_value as quantity, tx.name AS subcatg , sc.field_sub_catgeory_value as catg
                             FROM tban_field_collection_item__field_game_name as gn
                             LEFT JOIN tban_node__field_category ct ON gn.field_game_name_target_id = ct.entity_id
                             LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = ct.entity_id
                             LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                             LEFT JOIN tban_field_collection_item_revision__field_quantity as qt ON gn.entity_id = qt.entity_id
                             LEFT JOIN tban_node_revision__field_multiple_games as mg ON mg.field_multiple_games_value = qt.entity_id AND qt.revision_id = mg.field_multiple_games_revision_id
                             WHERE mg.revision_id IN ($vids3) $game_name_condition")->fetchAll();

          if (!empty($sql4)) {
            foreach ($sql4 as $v4) {
              $old_rev[$v4->entity_id . '_' . $v4->game_id] = [
                'catg'     => $v4->catg,
                'subcatg'  => $v4->subcatg,
                'quantity' => $v4->quantity,
              ];
            }
          }
        }
      }

      // Final Revision.
      if (!empty($old_rev)) {
        $final_rev = $new_rev;

        foreach ($new_rev as $nk => $nv) {
          if (array_key_exists($nk, $old_rev)) {
            $final_rev[$nk]['quantity'] = $final_rev[$nk]['quantity'] - $old_rev[$nk]['quantity'];
          }
        }
      }
      else {
        $final_rev = $new_rev;
      }

      $final_result = [];
      $catg         = ['Pre-Primary', 'Primary', 'Secondary'];
      $sub_cat_q    = db_query("SELECT name FROM tban_taxonomy_term_field_data WHERE vid='category'")->fetchAll();

      foreach ($catg as $c) {
        foreach ($sub_cat_q as $sc) {
          $final_result[$c][$sc->name] = 0;
        }
      }

      foreach ($final_rev as $fr) {
        $final_result[$fr['catg']][$fr['subcatg']] = $final_result[$fr['catg']][$fr['subcatg']] + $fr['quantity'];
      }

      if (!empty($final_rev)) {
        global $base_url;

        $content .= "<table class='tbl_inventory'>
                       <thead>
                         <tr><th colspan='8' rowspan='2'><img src='$base_url/sites/default/files/tb-logo_0.png'></th><th colspan='12' rowspan='2'></th><th colspan='6'>FY 2019-20</th></tr>
                         <tr><th colspan='20'>INVENTORY ADDITION</th></tr>
                         <tr><th rowspan='2'>DS</th><th rowspan='2'>WD</th><th colspan='6'>PP</th><th colspan='6'>PRI</th><th colspan='6'>SEC</th><th rowspan='2'>Details (Donor/Quantity)</th><th rowspan='2'>Added By (Date/Sign)</th></tr>
                         <tr><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th></tr>
                       </thead>
                       <tbody><tr><td></td><td></td>";

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
      else {
        $content .= "<div>Data Not Avaliable</div>";
      }
    }

    $form['addstock_report']['#value'] = $content;
    return $form['addstock_report'];
  }

}
