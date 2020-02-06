<?php

namespace Drupal\toybank_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Toybank custom controller.
 */
class ToybankCustomController extends ControllerBase {

  /**
   * Ajax return current inventory data.
   */
  public function currentInv() {
    $user_roles = \Drupal::currentUser()->getRoles();
    $nid     = $_POST['inv_nids'];
    $catg    = $_POST['catg'];
    $id      = explode('_', $nid);
    $allnid  = implode(",", $id);
    $content = NULL;
    $sql     = db_query("SELECT n.title, n.created, q.field_total_inventory_value, n.nid, c.field_game_code_value, tx.name AS category
                         FROM tban_node_field_data as n
                         LEFT JOIN tban_node__field_inv_game_name as g ON g.field_inv_game_name_target_id = n.nid
                         LEFT JOIN tban_node__field_total_inventory as q ON q.entity_id = g.entity_id
                         LEFT JOIN tban_node__field_game_code as c ON c.entity_id = n.nid
                         LEFT JOIN tban_node__field_category as cg ON cg.entity_id = n.nid
                         LEFT JOIN tban_taxonomy_term_field_data tx ON cg.field_category_target_id = tx.tid
                         WHERE n.type = 'game' AND n.nid IN ({$allnid}) AND q.field_total_inventory_value > 0 ORDER BY n.title")->fetchAll();

    $pending_query = db_query("SELECT gn.field_request_game_name_target_id, SUM(gq.field_req_game_quantity_value) as total_pending_qty
                                FROM tban_node__field_game_request_status AS rs
                                LEFT JOIN tban_node__field_fo_status AS fo ON fo.entity_id = rs.entity_id
                                LEFT JOIN tban_node__field_game_request_quantity AS rq ON rq.entity_id = rs.entity_id
                                LEFT JOIN tban_field_collection_item__field_request_game_name AS gn ON gn.entity_id = rq.field_game_request_quantity_value
                                LEFT JOIN tban_field_collection_item__field_req_game_quantity as gq ON gq.entity_id = gn.entity_id
                                WHERE rs.field_game_request_status_value = 'pending' AND fo.field_fo_status_value != 'denied'
                                GROUP BY gn.field_request_game_name_target_id")->fetchAllKeyed();


    $vid     = 'inventory_mark_issue';
    $con     = '';
    $terms   = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);

    foreach ($terms as $term) {
      $con .= "<option value={$term->tid}>{$term->name}</option>";
    }


    if (!empty($sql)) {
      if (in_array('inventory_manager', $user_roles) || in_array('administrator', $user_roles)) {
        $content .= "<table class='tbl_audit_issue table table-responsive sticky-header table table-hover table-striped '>
                       <thead>
                         <tr>
                           <th>#</th>
                           <th>Game Name</th>
                           <th>Category</th>
                           <th>Age</th>
                           <th>Requested Qty</th>
                           <th>System Qty</th>
                           <th>Expected Qty</th>
                           <th>Reported Qty</th>
                           <th>Mark issue</th>
                         </tr>
                       </thead>
                       <tbody>";

        foreach ($sql as $k=>$val) {
            if(!empty($pending_query[$val->nid])) {
              $pending_qty = $pending_query[$val->nid];
            }
            else {
              $pending_qty = 0;
            }

          $content .= "<tr>
                         <td>".($k + 1)."</td>
                         <td class='game-name' id=" . $val->nid . ">" . $val->title . " (" . $val->field_game_code_value . ")</td>
                         <td class='catg-ad'>" . $val->category . "</td>
                         <td class='catg-ad'>" . $catg . "</td>
                         <td class='request-qty'>".$pending_qty."</td>
                         <td class='system-qty'>" . $val->field_total_inventory_value . "</td>
                         <td class='exp-qty'>".($val->field_total_inventory_value + $pending_qty)."</td>
                         <td class='report-qty'><input type='number' name='reportedqty' id='report-qty' min = 0 max =" . ($val->field_total_inventory_value + $pending_qty) . " value=" . ($val->field_total_inventory_value + $pending_qty) . "></td>
                         <td>
                           <select class='mark-issue' id='markissue' name='markissue'>
                             <option value='none'>--None--</option>
                             $con
                           </select>
                         </td>
                         <td class='gamecode' id=" . $val->field_game_code_value . " style='display:none'></td>
                       </tr>";
        }

        $content .= "</tbody></table><button type='button' class='inv_issue_btn' onclick='issueMark()'>Submit</button>";
      }

      if (in_array('welfare_manager', $user_roles) || in_array('inventory_executive', $user_roles)) {
        $content .= "<table class='tbl_audit_issue table table-responsive'>
                       <thead>
                         <tr>
                           <th>#</th>
                           <th>Game Name</th>
                           <th>Category</th>
                           <th>Age</th>
                           <th>Game Age (in days)</th>
                           <th>Requested Qty</th>
                           <th>System Qty</th>
                           <th>Expected Qty</th>
                         </tr>
                       </thead>
                       <tbody>";

        foreach ($sql as $k=>$val) {
          $delvidate = date("Y-m-d", $val->created);
          $current_date = date("Y-m-d");
          $diff = strtotime($current_date) - strtotime($delvidate);
          $days = abs(round($diff / 86400));

          if(!empty($pending_query[$val->nid])) {
            $pending_qty = $pending_query[$val->nid];
          }
          else {
            $pending_qty = 0;
          }

          $content .= "<tr>
                         <td>".($k + 1)."</td>
                         <td class='game-name' id=" . $val->nid . ">" . $val->title . " (" . $val->field_game_code_value . ")</td>
                         <td class='catg-ad'>" . $val->category . "</td>
                         <td class='catg-ad'>" . $catg . "</td>
                          <td class ='game-age'>" . $days . " days </td>
                          <td>".$pending_qty."</td>
                         <td class='system-qty'>" . $val->field_total_inventory_value . "</td>
                         <td class='exp-qty'>".($val->field_total_inventory_value + $pending_qty)."</td>
                         <td class='gamecode' id=" . $val->field_game_code_value . " style='display:none'></td>
                       </tr>";
        }
      }
    }

    return new AjaxResponse($content);
  }

/*
  *
   * Mark game issue.
*/
  public function invAdMarkissue() {
    $markissue = $_POST['inv_vals'];

    if (!empty($markissue)) {
      foreach ($markissue as $val) {
        $nid_game     = $val['gname'];
        $reported_qty = $val['rqty'];
        $expected_qty  = $val['exp'];
        $requested_qty = $val['reqtqty'];
        $total_qty = 0;
        
        $sql          = db_query("SELECT q.field_total_inventory_value, n.nid, q.entity_id
                                  FROM tban_node_field_data as n
                                  LEFT JOIN tban_node__field_inv_game_name as g ON g.field_inv_game_name_target_id = n.nid
                                  LEFT JOIN tban_node__field_total_inventory as q ON q.entity_id = g.entity_id
                                  WHERE n.nid = {$nid_game}")->fetchAll();
        $pre_qty      = $sql[0]->field_total_inventory_value;  
       
     
        if (($reported_qty != '') && ($val['gissue'] != 'none')) {
          if (($reported_qty != $expected_qty) && ($reported_qty != 0)) {
            $total_qty      = $expected_qty - $reported_qty;
            $ideal_inv_node = Node::create([
              'type'                           => 'inventory_audit_mark_issue',
              'title'                          => $val['gcode'],
              'field_mark_issue_inventory_tax' => $val['gissue'],
              'field_reported_quantity'        => abs($total_qty),
              'field_system_quantity'          => $val['sqty'],
              'field_issue_game_name'          => $val['gname'],
            ]);
            $ideal_inv_node->save();
          }

          if ($reported_qty == 0) {
            $total_qty      = $expected_qty;			  
            $ideal_inv_node = Node::create([
              'type'                           => 'inventory_audit_mark_issue',
              'title'                          => $val['gcode'],
              'field_mark_issue_inventory_tax' => $val['gissue'],
              'field_reported_quantity'        => $total_qty,
              'field_system_quantity'          => 0,
              'field_issue_game_name'          => $val['gname'],
            ]);
            $ideal_inv_node->save();
          }

          if (!empty($sql)) {
            $inv_node = Node::load($sql[0]->entity_id);
            $system_qty = $reported_qty - $requested_qty;
            $inv_node->set('field_total_inventory', $system_qty);

            if ($expected_qty != $reported_qty) {
              $user_id = \Drupal::currentUser()->id();
              $inv_node->setNewRevision(TRUE);

              if (!empty($val['gissue'])) {
                $issue_nq = db_query("SELECT name FROM tban_taxonomy_term_field_data WHERE vid = 'inventory_mark_issue' AND tid = {$val['gissue']}")->fetchAssoc();

                if (!empty($issue_nq)) {
                  $inv_node->revision_log = '-'.abs($total_qty) . ' ' . $issue_nq['name'];
                }
              }

              $inv_node->setRevisionCreationTime(REQUEST_TIME);
              $inv_node->setRevisionUserId($user_id);
            }

            $inv_node->save();
          }
        }
      }
    }

    return new AjaxResponse();
  }

  /**
   * Return inventory status.
   */
  public function inventoryStatus() {
    $content = "";
    $date_e  = REQUEST_TIME;
    $sql     = db_query("SELECT tx.name AS category, ag.field_sub_catgeory_value AS subcategory, tn.field_total_inventory_value as qty , nd.title AS gamename, nd.nid
                         FROM tban_node__field_sub_catgeory ag
                         LEFT JOIN tban_node_field_data nd ON ag.entity_id = nd.nid
                         LEFT JOIN tban_node__field_category ct ON nd.nid = ct.entity_id
                         LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                         LEFT JOIN tban_node__field_inv_game_name gn ON gn.field_inv_game_name_target_id = nd.nid
                         LEFT JOIN tban_node__field_total_inventory tn ON tn.entity_id = gn.entity_id
                         WHERE nd.type = 'game' AND nd.created <= {$date_e} AND tn.field_total_inventory_value > 0
                         ORDER BY ag.field_sub_catgeory_value")->fetchAll();


    //~ $pending_query = db_query("SELECT gn.field_request_game_name_target_id, SUM(gq.field_req_game_quantity_value) as total_pending_qty
                                //~ FROM tban_node__field_game_request_status AS rs
                                //~ LEFT JOIN tban_node__field_game_request_quantity AS rq ON rq.entity_id = rs.entity_id
                                //~ LEFT JOIN tban_field_collection_item__field_request_game_name AS gn ON gn.entity_id = rq.field_game_request_quantity_value
                                //~ LEFT JOIN tban_field_collection_item__field_req_game_quantity as gq ON gq.entity_id = gn.entity_id
                                //~ WHERE rs.field_game_request_status_value = 'pending'
                                //~ GROUP BY gn.field_request_game_name_target_id")->fetchAllKeyed();

    $pending_query = db_query("SELECT gn.field_request_game_name_target_id, SUM(gq.field_req_game_quantity_value) as total_pending_qty
                                FROM tban_node__field_game_request_status AS rs
                                LEFT JOIN tban_node__field_fo_status AS fo ON fo.entity_id = rs.entity_id
                                LEFT JOIN tban_node__field_game_request_quantity AS rq ON rq.entity_id = rs.entity_id
                                LEFT JOIN tban_field_collection_item__field_request_game_name AS gn ON gn.entity_id = rq.field_game_request_quantity_value
                                LEFT JOIN tban_field_collection_item__field_req_game_quantity as gq ON gq.entity_id = gn.entity_id
                                WHERE rs.field_game_request_status_value = 'pending' AND fo.field_fo_status_value != 'denied'
                                GROUP BY gn.field_request_game_name_target_id")->fetchAllKeyed();

    //~ print_r($sql);  

    $final_result = $final_result_count = [];
    $theadarray   = [
      'Strategy',
      'Puzzle',
      'Block',
      'Alphabetical',
      'Numerical',
      'General',
    ];

    foreach ($theadarray as $th) {
      $al_total[$th] = 0;
      $al_total_req[$th] = 0;
    }

    if (!empty($sql)) {
      $content .= "<table class='tbl_inventory table table-responsive'>
                    <thead><tr>
                      <th></th>
                      <th title='#Expected-Qty (#Games)'>Strategy</th>
                      <th title='#Expected-Qty (#Games)'>Puzzle</th>
                      <th title='#Expected-Qty (#Games)'>Block</th>
                      <th title='#Expected-Qty (#Games)'>Alphabetical</th>
                      <th title='#Expected-Qty (#Games)'>Numerical</th>
                      <th title='#Expected-Qty (#Games)'>General</th>
                      <th>Total</th>
                    </tr></thead>
                    <tbody>";

      foreach ($sql as $val) {
        if (isset($pending_query[$val->nid])) {
          $val->qty = $val->qty + $pending_query[$val->nid];
        }

        $final_result[$val->subcategory][$val->category][] = $val->nid;
        $final_result_count[$val->subcategory][$val->category] = $final_result_count[$val->subcategory][$val->category] + $val->qty;
      }

      $col_total = $col_total_req = 0;

      foreach ($final_result as $r => $row) {
        $row_total = $row_total_req =  0;
        $content  .= "<tr><td>" . $r . "</td>";

        foreach ($theadarray as $th) {
          if (!empty($final_result[$r][$th])) {
            $content .= '<td class="inventory_adut">';
            $nid_arr  = [];

            foreach ($final_result[$r][$th] as $nid) {
              $nid_arr[] = $nid;
              $sql       = db_query("SELECT title as gamename FROM tban_node_field_data WHERE nid = {$nid}")->fetchAssoc();
            }

            $nids          = implode("_", $nid_arr);
            $content      .=  $final_result_count[$r][$th] ;
            $content      .= ' <span class="div_inv_status div_inv"  id="' . $nids . '" catg="' . $r . '">('.count($final_result[$r][$th]).')</span>';
            $content      .= '</td>';
            $row_total     = $row_total + count($final_result[$r][$th]);
            $row_total_req = $row_total_req + $final_result_count[$r][$th];
            $al_total[$th] = $al_total[$th] + count($final_result[$r][$th]);
            $al_total_req[$th] = $al_total_req[$th] + $final_result_count[$r][$th];
          }
          else {
            $content .= '<td><div class="div_inv_status ">0</div></td>';
          }
        }

        $content .= "<td>".$row_total_req." (". $row_total . ")</td></tr>";
      }

      $content .= "<tr><td>Total</td>";

      foreach ($theadarray as $th) {
        $content  .= "<td>".$al_total_req[$th]. " (". $al_total[$th] .")</td>";
        $col_total = $col_total + $al_total[$th];
        $col_total_req = $col_total_req + $al_total_req[$th];
      }

      $content .= "<td>".$col_total_req." (" . $col_total . ")</td></tr>";
      $content .= "</tbody></table>";
      $content .= "<br><br><div id='curr-inv-status'></div>";
    }
    else {
      $content .= "Data Not Available";
    }

    return ['#type' => 'markup', '#markup' => $content];
  }

  public function csvExport() {
    $data = [

       ['title', 'body'],
       ['1', '1'],
       ['2', '2'],

    ];

    foreach ($data as $v) {
      $rows[] = implode(',', $v);
    }
    //~ $rows[] = '"#","Partner Name","Partner Code","No of centers","No of kids","Partner since"';
    //~ $rows[] = '1,"Pinkan & Co.",PNK,3015,NGO,20/12/2019';
    //~ $rows[] = '2,"Morgan Stanley/ Maharashtra Housing Board",MHB,1624,"A Morgan Stanley supported project",02/12/2019';


    $content  = implode("\n", $rows);
    $response = new Response($content);
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition','attachment; filename="sample.csv"');

    return $response;
  }

  public function xlsExprt($pcid) {
    require('libraries/PhpSpreadsheet/vendor/autoload.php');

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Generate Request');

    $pc_code_query = db_query("SELECT field_playc_value FROM tban_node__field_playc WHERE entity_id = {$pcid}")->fetchAll();

    $sheet->setCellValue('A1', "Center Code");
    $sheet->setCellValue('B1', $pc_code_query[0]->field_playc_value);

    $sheet->setCellValue('A2', "Date of Report");
    $sheet->setCellValue('B2', date('d-m-Y'));

    $get_update_ideal_id = db_query("SELECT upinv.field_node_id_update_ideal_inv_value as IdealID
                                     FROM tban_node__field_node_id_update_ideal_inv upinv
                                     WHERE upinv.entity_id = {$pcid}")->fetchAll();
    $update_ideal_node = Node::load($get_update_ideal_id[0]->IdealID);

    //Ideal Inventory
    if (!empty($update_ideal_node)) {
      $shortfallnames["Ideal"]['Strategy'] = $update_ideal_node->get('field_strategy_update_ideal')->value;
      $shortfallnames["Ideal"]['Puzzle'] = $update_ideal_node->get('field_puzzle_update_ideal')->value;
      $shortfallnames["Ideal"]['Block'] = $update_ideal_node->get('field_block_update_ideal')->value;
      $shortfallnames["Ideal"]['Alphabetical'] = $update_ideal_node->get('field_alphabetical_update_ideal')->value;
      $shortfallnames["Ideal"]['Numerical'] = $update_ideal_node->get('field_numerical_update_ideal')->value;
      $shortfallnames["Ideal"]['General'] = $update_ideal_node->get('field_general_update_ideal')->value;
    }
    else {
      $shortfallnames["Ideal"] = [
        "Strategy" => 0,
        "Puzzle" => 0,
        "Block" => 0,
        "Alphabetical" => 0,
        "Numerical" => 0,
        "General" => 0,
      ];
    }

    $shortfallnames["Actual"] = [
      "Strategy" => 0,
      "Puzzle" => 0,
      "Block" => 0,
      "Alphabetical" => 0,
      "Numerical" => 0,
      "General" => 0,
    ];

    $get_game_and_qty = $get_actual_games = $actual_games_quantity = [];

    $get_actual = db_query("SELECT  pcn.entity_id as entity_id, gn.field_pc_inv_game_name_target_id as nid,gc.field_game_code_value as GameID, qty.field_pc_total_inventory_value as Quantity, tx.name AS Category, sc.field_sub_catgeory_value AS SubCategory, nd.title AS GameName
                            FROM tban_node__field_play_center_inventory_name AS pcn
                            LEFT JOIN tban_node__field_pc_total_inventory as qty ON pcn.entity_id = qty.entity_id
                            LEFT JOIN tban_node__field_pc_inv_game_name as gn ON gn.entity_id = pcn.entity_id
                            LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = gn.field_pc_inv_game_name_target_id
                            LEFT JOIN tban_node_field_data nd ON sc.entity_id = nd.nid
                            LEFT JOIN tban_node__field_category ct ON nd.nid = ct.entity_id
                            LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                            LEFT JOIN tban_node__field_game_code as gc ON gc.entity_id = nd.nid
                            WHERE pcn.field_play_center_inventory_name_target_id = {$pcid}
                            ORDER BY tx.weight,nd.title")->fetchAll();

    foreach ($get_actual as $key => $val) {
      $get_actual_games[$val->entity_id . '_' . $val->nid]['GameID'] = $val->nid;
      $get_actual_games[$val->entity_id . '_' . $val->nid]['GameCode'] = $val->GameID;
      $get_actual_games[$val->entity_id . '_' . $val->nid]['GameName'] = $val->GameName;
      $get_actual_games[$val->entity_id . '_' . $val->nid]['Category'] = $val->Category;

      if ($get_actual_games[$val->entity_id . '_' . $val->nid]['SubCategory']) {
        $get_actual_games[$val->entity_id . '_' . $val->nid]['SubCategory'] = $get_actual_games[$val->entity_id . '_' . $val->nid]['SubCategory'] . ', ' . $val->SubCategory;
      }
      else {
        $get_actual_games[$val->entity_id . '_' . $val->nid]['SubCategory'] = $val->SubCategory;
      }

      $get_actual_games[$val->entity_id . '_' . $val->nid]['Quantity'] = $val->Quantity;
    }

    foreach ($get_actual_games as $v) {
      $actual_games_quantity[$v['GameID']]['GameCode']    = $v['GameCode'];
      $actual_games_quantity[$v['GameID']]['GameName']    = $v['GameName'];
      $actual_games_quantity[$v['GameID']]['Category']    = $v['Category'];
      $actual_games_quantity[$v['GameID']]['SubCategory'] = $v['SubCategory'];
      $actual_games_quantity[$v['GameID']]['Quantity']   += $v['Quantity'];

      $get_game_and_qty[$v['Category']] += $v['Quantity'];
    }

    if (!empty($get_game_and_qty)) {
      foreach ($get_game_and_qty as $key => $val_qty) {
        $shortfallnames["Actual"][$key] = $val_qty;
      }
    }

    $shortfallnames["Shortfall"] = [
      "Strategy" => 0,
      "Puzzle" => 0,
      "Block" => 0,
      "Alphabetical" => 0,
      "Numerical" => 0,
      "General" => 0,
    ];

    foreach ($shortfallnames["Shortfall"] as $sfk => $sfv) {
      $shortfallnames["Shortfall"][$sfk] = $shortfallnames["Ideal"][$sfk] - $shortfallnames["Actual"][$sfk];
    }

    $sheet->setCellValue('A4', "");
    $sheet->setCellValue('B4', "Total");
    $sheet->setCellValue('C4', "S");
    $sheet->setCellValue('D4', "P");
    $sheet->setCellValue('E4', "B");
    $sheet->setCellValue('F4', "A");
    $sheet->setCellValue('G4', "N");
    $sheet->setCellValue('H4', "G");
    $sheet->setCellValue('A5', "Ideal");
    $sheet->setCellValue('A6', "Actual");
    $sheet->setCellValue('A6', "Shortfall");

    $sheet->setCellValue('A4', "");
    $sheet->setCellValue('B4', "Total");
    $sheet->setCellValue('B5', array_sum($shortfallnames["Ideal"]));
    $sheet->setCellValue('B6', array_sum($shortfallnames["Actual"]));
    $sheet->setCellValue('B7', array_sum($shortfallnames["Shortfall"]));
    $sheet->setCellValue('C5', $shortfallnames["Ideal"]['Strategy']);
    $sheet->setCellValue('D5', $shortfallnames["Ideal"]['Puzzle']);
    $sheet->setCellValue('E5', $shortfallnames["Ideal"]['Block']);
    $sheet->setCellValue('F5', $shortfallnames["Ideal"]['Alphabetical']);
    $sheet->setCellValue('G5', $shortfallnames["Ideal"]['Numerical']);
    $sheet->setCellValue('H5', $shortfallnames["Ideal"]['General']);
    $sheet->setCellValue('C6', $shortfallnames["Actual"]['Strategy']);
    $sheet->setCellValue('D6', $shortfallnames["Actual"]['Puzzle']);
    $sheet->setCellValue('E6', $shortfallnames["Actual"]['Block']);
    $sheet->setCellValue('F6', $shortfallnames["Actual"]['Alphabetical']);
    $sheet->setCellValue('G6', $shortfallnames["Actual"]['Numerical']);
    $sheet->setCellValue('H6', $shortfallnames["Actual"]['General']);
    $sheet->setCellValue('C7', $shortfallnames["Shortfall"]['Strategy']);
    $sheet->setCellValue('D7', $shortfallnames["Shortfall"]['Puzzle']);
    $sheet->setCellValue('E7', $shortfallnames["Shortfall"]['Block']);
    $sheet->setCellValue('F7', $shortfallnames["Shortfall"]['Alphabetical']);
    $sheet->setCellValue('G7', $shortfallnames["Shortfall"]['Numerical']);
    $sheet->setCellValue('H7', $shortfallnames["Shortfall"]['General']);
    $sheet->setCellValue('A5', "Ideal");
    $sheet->setCellValue('A6', "Actual");
    $sheet->setCellValue('A7', "Shortfall");

    if ($actual_games_quantity != null) {
      $sheet->setCellValue('A10', "#");
      $sheet->setCellValue('B10', "GCode");
      $sheet->setCellValue('C10', "GameName");
      $sheet->setCellValue('D10', "Category");
      $sheet->setCellValue('E10', "AgeGroup");
      $sheet->setCellValue('F10', "Quantity");
      $sheet->setCellValue('G10', "Age of Game");

      $i = 10;

      foreach ($actual_games_quantity as $k => $s) {
        $age = 0;

        $age_query = db_query("SELECT gs.entity_id, MIN(gs.revision_id), nr.changed
                               FROM tban_node_revision__field_game_request_status as gs
                               LEFT JOIN tban_node_field_revision as nr ON nr.nid = gs.entity_id AND nr.vid = gs.revision_id
                               LEFT JOIN tban_node__field_game_request_quantity as fc ON fc.entity_id = gs.entity_id
                               LEFT JOIN tban_field_collection_item__field_request_game_name as gn ON gn.entity_id = fc.field_game_request_quantity_value
                               WHERE (gs.field_game_request_status_value = 'partially_delivered' OR gs.field_game_request_status_value = 'delivered' OR gs.field_game_request_status_value = 'closed')
                                AND gn.field_request_game_name_target_id = {$k}
                                GROUP BY gs.entity_id, nr.changed")->fetchAll();

        if (!empty($age_query[0]->changed)) {
          $reqdate = date("Y-m-d", $age_query[0]->changed);
          $current_date = date("Y-m-d");
          $diff = strtotime($current_date) - strtotime($reqdate);
          $age = abs(round($diff / 86400));
        }

        $x = ++$i;
        $sheet->setCellValue('A' . $x, $x - 10);
        $sheet->setCellValue('B' . $x, $s['GameCode']);
        $sheet->setCellValue('C' . $x, $s['GameName']);
        $sheet->setCellValue('D' . $x, $s['Category']);
        $sheet->setCellValue('E' . $x, $s['SubCategory']);
        $sheet->setCellValue('F' . $x, $s['Quantity']);
        $sheet->setCellValue('G' . $x, $age);
      }
    }

    $spreadsheet->setActiveSheetIndex(0);
    $writer = new Xlsx($spreadsheet);
    $file_path = "sites/default/files/test.xlsx";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Generate_Report.xls"');
    $writer->save("php://output");
  }

  public function getTitle() {
    $user_roles = \Drupal::currentUser()->getRoles();
    $build = array();
    if (in_array('inventory_manager', $user_roles) || in_array('administrator', $user_roles)) {
      $build['#markup'] = 'Inventory Audit';
    }
    else {
      $build['#markup'] = 'Warehouse Inventory';
    }
    return $build;
  }

}




