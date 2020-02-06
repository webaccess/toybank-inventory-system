<?php

namespace Drupal\tb_custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements request sheet report form.
 */
class TbCustomRequestSheetReport extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'request_sheet_report';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['form_start'] = [
      '#markup' => "<div class='view-filters form-group views-exposed-form'>",
    ];

    $form['rs_date'] = [
      '#title'       => t('Date'),
      '#type'        => 'date',
      '#date_format' => 'Y-m-d',
      '#required'    => TRUE,
      '#attributes'  => [
        'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
      ],
    ];

    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Apply',
      '#ajax'  => [
        'event'    => 'click',
        'callback' => '::ajaxGetRequestSheet',
        'wrapper'  => 'request_sheet_wrapper',
        'effect'   => 'fade',
      ],
    ];

    $form['form_end'] = [
      '#markup' => "</div>",
    ];

    $form['request_sheet'] = [
      '#type'   => 'fieldset',
      '#prefix' => '<div id="request_sheet_wrapper" class="view-content quick-contact__form col-xs-12 col-md-12">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * Return request sheet data.
   */
  public function ajaxGetRequestSheet(array $form, FormStateInterface $form_state) {
    global $base_url;
    $content = $content2 = $final_content = $games = '';
    $game    = [];

    if (!empty($form_state->getValue('rs_date'))) {
      $date_s    = strtotime($form_state->getValue('rs_date'));
      $date_e    = strtotime($form_state->getValue('rs_date') . " 23:59");
      $placent_q = db_query("SELECT fr.nid, fr.vid, pc.field_play_center_target_id, cn.field_playc_value as playcenter, CONCAT(cn.field_playc_value,'_',n.title) as center
                             FROM tban_node_revision__field_game_request_status as gr
                             LEFT JOIN tban_node_revision__field_play_center as pc ON (pc.entity_id = gr.entity_id AND pc.revision_id=gr.revision_id)
                             LEFT JOIN tban_node_field_revision as fr ON (fr.nid= pc.entity_id AND fr.vid=pc.revision_id)
                             LEFT JOIN tban_node__field_playc as cn ON cn.entity_id = pc.field_play_center_target_id
                             LEFT JOIN tban_node__field_cluster as cl ON cl.entity_id=pc.field_play_center_target_id
                             LEFT JOIN tban_node_field_data as n ON n.nid = cl.field_cluster_target_id
                             WHERE gr.field_game_request_status_value='pending' AND fr.changed BETWEEN ($date_s) AND ($date_e)")->fetchAll();

      if (!empty($placent_q)) {
        $catg      = ['Pre-Primary', 'Primary', 'Secondary'];
        $sub_cat_q = db_query("SELECT name FROM tban_taxonomy_term_field_data WHERE vid='category'")->fetchAll();
        $content  .= "<table class='tbl_inventory'>";
        $content  .= "<thead>
                      <tr>
                        <th colspan='3' rowspan='2'>
                          <div class='rs-report-header-logo'><img src='$base_url/sites/default/files/tb-logo_0.png'></div>
                          <div class='rs-report-header-title'>Toy List (Urban)</div>
                          <div class='rs-report-year'>FY- 2019-20</div>
                        </th></tr>
                      <tr></tr>";
        $content  .= "<tr><th>Center Code</th><th>Enter Toys Code Below</th><th>Sum</th></tr>";
        $content  .= "</thead>";
        $content2 .= "<table class='tbl_inventory req-sheet-report'>";
        $content2 .= "<thead>
                      <tr><th colspan='3' rowspan='3'><img src='$base_url/sites/default/files/tb-logo_0.png'></th><th colspan='6'>Toy Request Form</th>
                      <th colspan ='6'>Requested By/On</th><th colspan='6'>Approved By/On</th><th colspan='3'>FY 2018-19</th></tr>";
        $content2 .= "<tr><th colspan ='6'>PP</th><th colspan ='6'>Pri</th><th colspan='6'>Sec</th><th rowspan='2'>Sum</th><th rowspan='2'>Packed (Date/Sign)</th><th rowspan='2'>Sent (Date/Sign)</th></tr>";
        $content2 .= "<tr><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th><th>S</th><th>P</th><th>B</th><th>A</th><th>N</th><th>G</th></tr>";
        $content2 .= "</thead>";
        $content  .= "<tbody>";
        $content2 .= "<tbody>";

        foreach ($placent_q as $p) {
          $total_sum   = $total_sum_p = 0;
          $game        = $game_pack = $final_result = $game_c = $final_result2 = [];
          $games       = '';
          $game_q      = db_query("SELECT  sc.field_sub_catgeory_value as catg, tx.name AS subcatg, qty.field_req_game_quantity_value as quantity, gc.field_game_code_value as gamecode
                                   FROM tban_node_revision__field_game_request_quantity as gq
                                   LEFT JOIN tban_field_collection_item__field_request_game_name as gn ON gn.entity_id = gq.field_game_request_quantity_value
                                   LEFT JOIN tban_field_collection_item__field_req_game_quantity as qty ON qty.entity_id = gn.entity_id
                                   LEFT JOIN tban_node__field_category as ct ON gn.field_request_game_name_target_id = ct.entity_id
                                   LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = ct.entity_id
                                   LEFT JOIN tban_node__field_game_code as gc ON gc.entity_id=ct.entity_id
                                   LEFT JOIN tban_taxonomy_term_field_data as tx ON ct.field_category_target_id = tx.tid
                                   WHERE gq.revision_id= {$p->vid} AND gq.entity_id = {$p->nid}")->fetchAll();
          $game_pack_q = db_query("SELECT  sc.field_sub_catgeory_value as catg, tx.name AS subcatg, qty.field_packed_quantity_value as quantity, gc.field_game_code_value as gamecode
                                   FROM tban_node_revision__field_game_request_quantity as gq
                                   LEFT JOIN tban_node_revision__field_game_request_status as gs ON gs.entity_id= gq.entity_id
                                   LEFT JOIN tban_field_collection_item__field_request_game_name as gn ON gn.entity_id = gq.field_game_request_quantity_value
                                   LEFT JOIN tban_field_collection_item__field_packed_quantity as qty ON qty.entity_id = gn.entity_id
                                   LEFT JOIN tban_node__field_category as ct ON gn.field_request_game_name_target_id = ct.entity_id
                                   LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = ct.entity_id
                                   LEFT JOIN tban_node__field_game_code as gc ON gc.entity_id=ct.entity_id
                                   LEFT JOIN tban_taxonomy_term_field_data as tx ON ct.field_category_target_id = tx.tid
                                   WHERE gq.entity_id={$p->nid} AND gs.field_game_request_status_value = 'packed'")->fetchAll();

          if (!empty($game_q)) {
            foreach ($catg as $c) {
              foreach ($sub_cat_q as $sc) {
                $final_result[$c][$sc->name]  = 0;
                $final_result2[$c][$sc->name] = 0;
              }
            }

            foreach ($game_q as $g) {
              $game[$p->nid . '_' . $p->playcenter . '_' . $g->gamecode] = [
                'gamecode' => $g->gamecode,
                'catg'     => $g->catg,
                'subcatg'  => $g->subcatg,
                'quantity' => $g->quantity,
              ];
            }

            if (!empty($game_pack_q)) {
              foreach ($game_pack_q as $gp) {
                $game_pack[$p->nid . '_' . $p->playcenter . '_' . $gp->gamecode] = [
                  'gamecode' => $gp->gamecode,
                  'catg'     => $gp->catg,
                  'subcatg'  => $gp->subcatg,
                  'quantity' => $gp->quantity,
                ];
              }
            }

            if (!empty($game)) {
              $game_c = array_column($game, 'gamecode');
              $games  = implode(', ', $game_c);

              foreach ($game as $gm) {
                $final_result[$gm['catg']][$gm['subcatg']] = $final_result[$gm['catg']][$gm['subcatg']] + $gm['quantity'];
              }

              // Game pack.
              if (!empty($game_pack)) {
                foreach ($game_pack as $pck) {
                  $final_result2[$pck['catg']][$pck['subcatg']] = $final_result[$pck['catg']][$pck['subcatg']] + $pck['quantity'];
                }
              }

              if (!empty($final_result)) {
                $content2 .= "<tr>";
                $content2 .= "<td rowspan='2'>" . $p->playcenter . "</td><td rowspan='2'>" . $p->center . "</td><td>Req</td>";

                foreach ($final_result as $v) {
                  $content2  .= "<td>" . $v['Strategy'] . "</td>";
                  $content2  .= "<td>" . $v['Puzzle'] . "</td>";
                  $content2  .= "<td>" . $v['Block'] . "</td>";
                  $content2  .= "<td>" . $v['Alphabetical'] . "</td>";
                  $content2  .= "<td>" . $v['Numerical'] . "</td>";
                  $content2  .= "<td>" . $v['General'] . "</td>";
                  $total_sum += $v['Strategy'] + $v['Puzzle'] + $v['Block'] + $v['Alphabetical'] + $v['Numerical'] + $v['General'];
                }

                $content2 .= "<td>" . $total_sum . "</td><td></td><td></td></tr>";

                // Packed quantity.
                if (!empty($game_pack)) {
                  if (!empty($final_result2)) {
                    $content2 .= "<tr><td>Final</td>";

                    foreach ($final_result2 as $v2) {
                      $content2    .= "<td>" . $v2['Strategy'] . "</td>";
                      $content2    .= "<td>" . $v2['Puzzle'] . "</td>";
                      $content2    .= "<td>" . $v2['Block'] . "</td>";
                      $content2    .= "<td>" . $v2['Alphabetical'] . "</td>";
                      $content2    .= "<td>" . $v2['Numerical'] . "</td>";
                      $content2    .= "<td>" . $v2['General'] . "</td>";
                      $total_sum_p += $v2['Strategy'] + $v2['Puzzle'] + $v2['Block'] + $v2['Alphabetical'] + $v2['Numerical'] + $v2['General'];
                    }

                    $content2 .= "<td>" . $total_sum_p . "</td><td></td><td></td></tr>";
                  }
                }
                else {
                  $content2 .= "<tr><td>Final</td><td>-</td><td>-</td><td>-</td>
                                  <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                  <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td></td><td></td></tr>";
                }
              }
            }
          }

          if (!empty($games)) {
            $content .= "<tr>";
            $content .= "<td>" . $p->playcenter . "</td>";
            $content .= "<td>" . $games . "</td><td></td></tr>";
          }
        }

        $content      .= "</tbody></table>";
        $content2     .= "</tbody></table>";
        $content2     .= "<br><div class='date-printed-on'>Printed on: " . date("d/m/Y") . "</div>";
        $content2     .= "<div class='report-print-wrapper'><button onclick='window.print();return false;'>Print</button></div>";
        $final_content = $content . "<br/><br/>" . $content2;
      }
      else {
        $final_content = "<div>Data Not Avaliable</div>";
      }

    }

    $form['request_sheet']['#value'] = $final_content;
    return $form['request_sheet'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
