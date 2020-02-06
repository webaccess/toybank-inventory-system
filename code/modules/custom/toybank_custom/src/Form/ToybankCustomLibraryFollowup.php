<?php

namespace Drupal\toybank_custom\Form;

use Drupal\user\Entity\User;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Implements library followup form.
 */
class ToybankCustomLibraryFollowup extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'library_followup_form';
  }

  /**
   * Field officer autocomplete callback.
   */
  public function invAutocomplete(Request $request) {
    $results = [];
    $input   = $request->query->get('q');

    // Get the typed string from the URL, if it exists.
    if (!$input) {
      return new JsonResponse($results);
    }
    else {
      $user = User::load(\Drupal::currentUser()->id());
      $uid  = $user->get('uid')->value;

      if (in_array('administrator', $user->getRoles()) || in_array('welfare_manager', $user->getRoles())) {
        $sql = db_query("SELECT title, nid FROM tban_node_field_data WHERE type = 'play_center' AND title LIKE '%$input%' LIMIT 10")->fetchAll();
      }
      else {
        $sql = db_query("SELECT title.title as title ,title.nid as nid
                         FROM tban_node__field_associated_field_officer fo
                         LEFT JOIN tban_node__field_cluster clust ON fo.entity_id = clust.field_cluster_target_id
                         LEFT JOIN tban_node_field_data title ON title.nid = clust.entity_id
                         WHERE fo.field_associated_field_officer_target_id = {$uid} And title.type = 'play_center' AND title.title LIKE '%$input%' LIMIT 10")->fetchAll();
                         //~ print_r($uid); exit;
      }

      if (!empty($sql)) {
        foreach ($sql as $v) {
          $results[] = ['value' => $v->title . ' (' . $v->nid . ')', 'label' => $v->title];
        }
      }

      return new JsonResponse($results);
    }
  }

  /**
   * Returns playcenter audit data.
   */
  public function ajaxGetPcaudit(array $form, FormStateInterface $form_state) {
    $content = "";
    $pc_id   = $form_state->getValue('pc_name');
    $pc_id   = explode("(", $pc_id);
    $pc_id   = str_replace(')', '', $pc_id[1]);

    if (empty($pc_id)) {

    }
    else {
      $sql          = db_query("SELECT DISTINCT pcn.entity_id, gn.field_pc_inv_game_name_target_id as game_id, qty.field_pc_total_inventory_value as total_qty, tx.name AS category, sc.field_sub_catgeory_value AS subcategory, nd.title AS gamename
                                FROM tban_node__field_play_center_inventory_name AS pcn
                                LEFT JOIN tban_node__field_pc_total_inventory as qty ON pcn.entity_id = qty.entity_id
                                LEFT JOIN tban_node__field_pc_inv_game_name as gn ON gn.entity_id = pcn.entity_id
                                LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = gn.field_pc_inv_game_name_target_id
                                LEFT JOIN tban_node_field_data nd ON sc.entity_id = nd.nid
                                LEFT JOIN tban_node__field_category ct ON nd.nid = ct.entity_id
                                LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                                WHERE pcn.field_play_center_inventory_name_target_id ={$pc_id}")->fetchAll();
                                //~ print_r($sql);


      $final_result = $final_result_total = [];
      $theadarray   = [
        'Strategy',
        'Puzzle',
        'Block',
        'Alphabetical',
        'Numerical',
        'General',
      ];

      foreach ($theadarray as $th) {
        $al_total[$th] = $al_total_sum[$th] = 0;
      }

      if (!empty($sql)) {
        $content .= "<span id='pc_id' style='display:none;'>$pc_id</span>
                      <table class='tbl_inventory table table-responsive'>
                      <thead><tr>
                        <th></th>
                        <th title='#System-Qty (#Games)'>Strategy</th>
                        <th title='#System-Qty (#Games)'>Puzzle</th>
                        <th title='#System-Qty (#Games)'>Block</th>
                        <th title='#System-Qty (#Games)'>Alphabetical</th>
                        <th title='#System-Qty (#Games)'>Numerical</th>
                        <th title='#System-Qty (#Games)'>General</th>
                        <th>Total</th>
                      </tr></thead><tbody>";

        foreach ($sql as $val) {
          if (isset($delivered_query[$val->game_id])) {
            $val->total_qty = $val->total_qty + $delivered_query[$val->game_id];
          }

          $final_result[$val->subcategory][$val->category][] = $val->game_id;
          $final_result_total[$val->subcategory][$val->category] = $final_result_total[$val->subcategory][$val->category] + $val->total_qty;
        }

        $col_total = $col_total_sum = 0;

        foreach ($final_result as $r => $row) {
          $row_total = $row_sum_total = 0;
          $content  .= "<tr><td>" . $r . "</td>";

          foreach ($theadarray as $th) {
            if (!empty($final_result[$r][$th])) {
              $content .= '<td class="pc_audit">';
              $nid_arr  = [];

              foreach ($final_result[$r][$th] as $nid) {
                $nid_arr[] = $nid;
                $sql       = db_query("SELECT title as gamename FROM tban_node_field_data WHERE nid = {$nid}")->fetchAssoc();

                if (!empty($sql)) {
                  // $gamename[$nid] = $sql['gamename'];
                }
              }

              $nids          = implode("_", $nid_arr);
              $content      .= $final_result_total[$r][$th];
              $content      .= '<span class="div_pc_inv pc_followup" id="' . $nids . '" pc_id="' . $pc_id . '" pc_catg="' . $r . '">('. count($final_result[$r][$th]) . ')</span>';
              $content      .= '</td>';
              $row_total     = $row_total + count($final_result[$r][$th]);
              $row_sum_total = $row_sum_total + $final_result_total[$r][$th];
              $al_total[$th] = $al_total[$th] + count($final_result[$r][$th]);
              $al_total_sum[$th] = $al_total_sum[$th] + $final_result_total[$r][$th];

            }
            else {
              $content .= '<td><div class="pc_followup">0</div></td>';
            }
          }

          $content .= "<td>".$row_sum_total."(" . $row_total . ")</td></tr>";
        }

        $content .= "<tr><td>Total</td>";

        foreach ($theadarray as $th) {
          $content  .= "<td>".$al_total_sum[$th]."(" . $al_total[$th] . ")</td>";
          $col_total = $col_total + $al_total[$th];
          $col_total_sum = $col_total_sum + $al_total_sum[$th];
        }

        $content .= "<td>".$col_total_sum."(" . $col_total . ")</td></tr>";
        $content .= "</tbody></table>";
      }
      else {
        $content .= "<div>Data Not Avaliable</div>";
      }
    }

    $form['pc_audit_report']['#value'] = $content;
    return $form['pc_audit_report'];
  }

  /**
   * Ajax return playcenter audit data.
   */
  public function ajaxPcAudit() {
    $nid     = $_POST['inv_nids'];
    $pcid    = $_POST['pc_id'];
    $catg    = $_POST['catg'];
    $id      = explode('_', $nid);
    $allnid  = implode(",", $id);
    $content = NULL;
    $sql     = db_query("SELECT tx.name AS category, pcna.field_play_center_inventory_name_target_id, gn.entity_id ,nd.title, qty.field_pc_total_inventory_value as total_pc_qty, gn.field_pc_inv_game_name_target_id as game_id FROM tban_node__field_pc_total_inventory as qty
                         LEFT JOIN tban_node__field_pc_inv_game_name as gn ON gn.entity_id = qty.entity_id
                         LEFT JOIN tban_node__field_inv_game_name as g ON g.field_inv_game_name_target_id = gn.entity_id
                         LEFT JOIN tban_node_field_data as nd ON nd.nid = gn.field_pc_inv_game_name_target_id
                         LEFT JOIN tban_node__field_play_center_inventory_name as pcna ON pcna.entity_id = gn.entity_id
                         LEFT JOIN tban_node__field_category as cg ON cg.entity_id = nd.nid
                         LEFT JOIN tban_taxonomy_term_field_data tx ON cg.field_category_target_id = tx.tid
                         WHERE nd.type = 'game'AND nd.nid IN ({$allnid}) AND pcna.field_play_center_inventory_name_target_id = {$pcid}")->fetchAll();

                        //~ print_r($sql);exit;

    $vid     = 'inventory_mark_issue';
    $con     = '';
    $terms   = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);

    foreach ($terms as $term) {
      $con .= "<option value={$term->tid}>{$term->name}</option>";
    }

    if (!empty($sql)) {
      $content .= "<table class='tbl_pc_audit_issue tbl_audit_issue table table-responsive' >
                     <thead>
                       <tr>
                         <th>#</th>
                         <th>GameName</th>
                         <th>Category</th>
                         <th>Subcategory</th>
                         <th>System Qty</th>
                         <th>Reported Qty</th>
                         <th>Mark Issue</th>
                       </tr>
                     </thead>
                     <tbody>";

      foreach ($sql as $k=>$val) {

        $content .= "<tr>
                      <td>".($k + 1)."</td>
                       <td class='game-name' id=" . $val->game_id . ">" . $val->title . "</td>
                       <td class='catg'>" . $val->category . "</td>
                       <td class='subcatg'>" . $catg . "</td>
                       <td class='system-qty'>" . $val->total_pc_qty . "</td>
                       <td class='report-qty'><input type='number' name='reportedqty' id='report-qty' min = 0 max =". $val->total_pc_qty." value= " . $val->total_pc_qty . " ></td>
                       <td>
                         <select class='mark-issue' id='markissue' name='markissue'>
                           <option value='none'>--None--</option>
                           $con
                         </select>
                       </td>
                       <td class='pc-inv-id' id=" . $val->entity_id . " style='display:none'></td>
                     </tr>";
      }

      $content .= "</tbody></table><button type='button' class='pc_inv_issue_btn inv_issue_btn' onclick='pcissueMark()'>Submit</button>";
    }

    return new AjaxResponse($content);
  }

  /**
   * Ajax mark game issue.
   */
  public function ajaxPcInvMarkissue() {
    $markissue = $_POST['inv_vals'];

    if (!empty($markissue)) {
      foreach ($markissue as $val) {
        $nid_game     = $val['gname'];
        $reported_qty = $val['rqty'];
        $pcinv_id     = $val['pcinvid'];
        $pcid         = $val['pcid'];
        $sql          = db_query("SELECT nd.nid,q.field_pc_total_inventory_value  FROM tban_node_field_data as nd
                                  LEFT JOIN tban_node__field_pc_inv_game_name as gn ON nd.nid=gn.field_pc_inv_game_name_target_id
                                  LEFT JOIN tban_node__field_pc_total_inventory q ON q.entity_id = gn.entity_id
                                  LEFT JOIN tban_node__field_play_center_inventory_name pcid ON pcid.entity_id = q.entity_id
                                  WHERE nd.nid = {$nid_game} AND pcid.field_play_center_inventory_name_target_id = {$pcid}")->fetchAll();
        $pre_qty      = $sql[0]->field_pc_total_inventory_value;

        if ((!empty($reported_qty)) && ($val['gissue'] != 'none')) {
          if ($reported_qty != $pre_qty) {
            $total_qty      = $pre_qty - $reported_qty;
            $ideal_inv_node = Node::create([
              'type'                       => 'playcenter_audit_mark_issue',
              'title'                      => 'Playcenter Audit',
              'field_pc_mark_issue_tax'    => $val['gissue'],
              'field_pc_reported_quantity' => $total_qty,
              'field_pc_system_quantity'   => $val['sqty'],
              'field_game_name_issuepc'    => $val['gname'],
              'field_pc_name_issue_ad'     => $val['pcid'],
            ]);
            $ideal_inv_node->save();
          }

          if (!empty($pcinv_id)) {
            $inv_node = Node::load($pcinv_id);
            $pre_qty  = $inv_node->get('field_pc_total_inventory')->value;
            $inv_node->set('field_pc_total_inventory', $reported_qty);
            $inv_node->save();
          }
        }
      }
    }

    return new AjaxResponse();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['form_start'] = [
      '#markup' => '<div class="view-filters form-group views-exposed-form">',
    ];

    $form['pc_name'] = [
      '#title'                   => 'Play Center',
      '#type'                    => 'textfield',
      '#autocomplete_route_name' => 'tb_custom.playcenter_autocomplete',
      '#required'                => TRUE,
    ];

    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Apply',
      '#ajax'  => [
        'event'    => 'click',
        'callback' => '::ajaxGetPcaudit',
        'wrapper'  => 'pc_audit_wrapper',
        'effect'   => 'fade',
      ],
    ];

    $form['form_end'] = [
      '#markup' => '</div>',
    ];

    $form['pc_audit_report'] = [
      '#type'   => 'fieldset',
      '#prefix' => '<div id="pc_audit_wrapper" >',
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
