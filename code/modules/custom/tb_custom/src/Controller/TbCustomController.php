<?php

namespace Drupal\tb_custom\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Implements TbCustom Controller.
 */
class TbCustomController extends ControllerBase {

  /**
   * Returns game title.
   */
  public function getGameTitle($nid) {
    $game_name = '';

    if (!empty($nid)) {
      $game_name_q = db_query("SELECT title FROM tban_node_field_data WHERE type='game' AND nid = {$nid}")->fetchAssoc();

      if (!empty($game_name_q)) {
        $game_name = $game_name_q['title'];
      }
    }

    return new AjaxResponse($game_name);
  }

  /**
   * Games autocomplete callback.
   */
  public function gamesAutocomplete(Request $request) {
    $results = [];
    $input   = $request->query->get('q');

    if ($input) {
      $games = db_query("SELECT gc.field_game_code_value as gcode, n.title, n.nid
                         FROM tban_node_field_data as n
                         LEFT JOIN tban_node__field_game_code as gc ON gc.entity_id = n.nid
                         WHERE n.type = 'game' AND (gc.field_game_code_value LIKE '%$input%' OR n.title LIKE '%$input%') ORDER BY gc.field_game_code_value LIMIT 10")->fetchAll();

      if (!empty($games)) {
        foreach ($games as $g) {
          $results[] = ['value' => '[' . $g->gcode . '] ' . $g->title . ' (' . $g->nid . ')', 'label' => '[' . $g->gcode . '] ' . $g->title];
        }
      }
    }

    return new JsonResponse($results);
  }

  /**
   * Games combine autocomplete callback.
   */
  public function gamesCombineAutocomplete(Request $request) {
    $results = [];
    $input   = $request->query->get('q');

    if ($input) {
      $games = db_query("SELECT gc.field_game_code_value as gcode, n.title, n.nid
                         FROM tban_node_field_data as n
                         LEFT JOIN tban_node__field_game_code as gc ON gc.entity_id = n.nid
                         WHERE n.type = 'game' AND (gc.field_game_code_value LIKE '%$input%' OR n.title LIKE '%$input%') ORDER BY gc.field_game_code_value LIMIT 10")->fetchAll();

      if (!empty($games)) {
        foreach ($games as $g) {
          $results[] = ['value' => $g->title , 'label' => '[' . $g->gcode . '] ' . $g->title];
        }
      }
    }

    return new JsonResponse($results);
  }

  /**
   * Playcenter auto complete callback.
   */
  public function playcenterAutocomplete(Request $request) {
    $user    = User::load(\Drupal::currentUser()->id());
    $uid     = $user->get('uid')->value;
    $results = [];
    $input   = $request->query->get('q');

    if ($input) {
      if (in_array('field_officer', $user->getRoles())) {
        $playcen = db_query("SELECT n.title ,n.nid,  pc.field_playc_value as pcode
                             FROM tban_node__field_associated_field_officer fo
                             LEFT JOIN tban_node__field_cluster clust ON fo.entity_id = clust.field_cluster_target_id
                             LEFT JOIN tban_node_field_data as n ON n.nid = clust.entity_id
                             LEFT JOIN tban_node__field_playc as pc ON pc.entity_id= n.nid
                             LEFT JOIN tban_node__field_status as s ON s.entity_id = n.nid
                             LEFT JOIN tban_node__field_close_date as cd ON cd.entity_id = n.nid
                             WHERE fo.field_associated_field_officer_target_id = {$uid} And n.type = 'play_center' AND s.field_status_value = 'Active' AND  cd.field_close_date_value >= CURDATE()  AND (n.title LIKE '%$input%' OR pc.field_playc_value LIKE '%$input%') LIMIT 10")->fetchAll();
      }
      else {
        $playcen = db_query("SELECT  pc.field_playc_value as pcode, n.title, n.nid
                             FROM tban_node_field_data as n
                             LEFT JOIN tban_node__field_playc as pc ON pc.entity_id = n.nid
                             LEFT JOIN tban_node__field_status as s ON s.entity_id = n.nid
                             LEFT JOIN tban_node__field_close_date as cd ON cd.entity_id = n.nid
                             WHERE n.type = 'play_center' AND s.field_status_value = 'Active'  AND (pc.field_playc_value LIKE '%$input%' OR n.title LIKE '%$input%') ORDER BY pc.field_playc_value LIMIT 10")->fetchAll();
      }

      if (!empty($playcen)) {
        foreach ($playcen as $p) {
          $results[] = ['value' => '[' . $p->pcode . '] ' . $p->title . ' (' . $p->nid . ')', 'label' => '[' . $p->pcode . '] ' . $p->title];
        }
      }
    }

    return new JsonResponse($results);
  }

  /**
   * Playcenter combine autocomplete callback.
   */
  public function playcenterCombineAutocomplete(Request $request) {
    $user    = User::load(\Drupal::currentUser()->id());
    $uid     = $user->get('uid')->value;
    $results = [];
    $input   = $request->query->get('q');

    if ($input) {
      if (in_array('field_officer', $user->getRoles())) {
        $playcen = db_query("SELECT n.title, n.nid, pc.field_playc_value as pcode
                             FROM tban_node__field_associated_field_officer fo
                             LEFT JOIN tban_node__field_cluster clust ON fo.entity_id = clust.field_cluster_target_id
                             LEFT JOIN tban_node_field_data as n ON n.nid = clust.entity_id
                             LEFT JOIN tban_node__field_playc as pc ON pc.entity_id = n.nid
                             LEFT JOIN tban_node__field_status as s ON s.entity_id = n.nid
                             WHERE fo.field_associated_field_officer_target_id = {$uid} AND n.type = 'play_center' AND s.field_status_value = 'Active' AND (n.title LIKE '%$input%' OR pc.field_playc_value LIKE '%$input%') ORDER BY pc.field_playc_value LIMIT 10")->fetchAll();
      }
      else {
        $playcen = db_query("SELECT pc.field_playc_value as pcode, n.title, n.nid
                             FROM tban_node_field_data as n
                             LEFT JOIN tban_node__field_playc as pc ON pc.entity_id= n.nid
                             LEFT JOIN tban_node__field_status as s ON s.entity_id = n.nid
                             WHERE n.type = 'play_center' AND s.field_status_value = 'Active' AND (pc.field_playc_value LIKE '%$input%' OR n.title LIKE '%$input%') ORDER BY pc.field_playc_value LIMIT 10")->fetchAll();
      }

      if (!empty($playcen)) {
        foreach ($playcen as $p) {
          $results[] = ['value' => $p->title, 'label' => '[' . $p->pcode . '] ' . $p->title];
        }
      }
    }

    return new JsonResponse($results);
  }

  /**
   * Partner autocomplete callback.
   */
  public function partnerAutocomplete(Request $request) {
    $results = [];
    $input   = $request->query->get('q');

    if ($input) {
      $prtcode = db_query("SELECT n.nid, n.title, p.field_partner_code_value as partcode
                           FROM tban_node_field_data AS n
                           LEFT JOIN tban_node__field_partner_code as p ON p.entity_id = n.nid
                           WHERE n.type = 'partner' AND ((CONCAT_WS(' ', n.title, ' ',  p.field_partner_code_value) LIKE '%$input%')) LIMIT 10")->fetchAll();

      if (!empty($prtcode)) {
        foreach ($prtcode as $p) {
          $results[] = ['value' => $p->title, 'label' => '[' . $p->partcode . '] ' . $p->title];
        }
      }
    }

    return new JsonResponse($results);
  }

  /**
   * Field officer autocomplete callback.
   */
  public function fieldOfficerAutocomplete(Request $request) {
    $results = [];
    $input   = $request->query->get('q');

    if ($input) {
      $prtcode = db_query("SELECT ud.uid, r.roles_target_id, ln.field_last_name_value AS lastname ,fn.field_first_name_value as firstname
                           FROM tban_users_field_data AS ud
                           LEFT JOIN tban_user__roles AS r ON ud.uid =r.entity_id
                           LEFT JOIN tban_user__field_first_name AS fn ON fn.entity_id = ud.uid
                           LEFT JOIN tban_user__field_last_name AS ln ON ln.entity_id = fn.entity_id
                           WHERE r.roles_target_id = 'field_officer' AND ((fn.field_first_name_value LIKE '%$input%') OR (ln.field_last_name_value LIKE '%$input%') OR (ud.mail LIKE '%$input%')) LIMIT 10")->fetchAll();

      if (!empty($prtcode)) {
        foreach ($prtcode as $p) {
          $results[] = ['value' => $p->firstname . ' ' . $p->lastname . ' (' . $p->uid . ')', 'label' => $p->firstname . ' ' . $p->lastname . ' ' . '[' . $p->uid . '] '];
        }
      }
    }

    return new JsonResponse($results);
  }

  /**
   * Data Entry autocomplete callback.
   */
  public function DataEntryPlaycenterCombineAutocomplete(Request $request) {
    $results = [];
    $input   = $request->query->get('q');

    if ($input) {
        $playcen = db_query("SELECT pc.field_playc_value as pcode, n.title, n.nid
                             FROM tban_node_field_data as n
                             LEFT JOIN tban_node__field_playc as pc ON pc.entity_id= n.nid
                             LEFT JOIN tban_node__field_status as s ON s.entity_id = n.nid
                             WHERE n.type = 'play_center' AND (pc.field_playc_value LIKE '%$input%' OR n.title LIKE '%$input%') ORDER BY pc.field_playc_value LIMIT 10")->fetchAll();

      if (!empty($playcen)) {
        foreach ($playcen as $p) {
          $results[] = ['value' => $p->title, 'label' => '[' . $p->pcode . '] ' . $p->title];
        }
      }
    }

    return new JsonResponse($results);

  }



  /**
   * Implements reports listing page.
   */
  public function reports() {
    $user     = \Drupal::currentUser();
    $content  = '';
    $content .= "<table class='tbl_inventory' id='tb_reports'><tbody>";

    if (in_array('administrator', $user->getRoles()) || in_array('welfare_manager', $user->getRoles()) || in_array('inventory_manager', $user->getRoles())) {
      $content .= "<tr><td>Add Stock Report</td><td><a class='btn' href='/addstock-report'>Run</a></td></tr>";
      $content .= "<tr><td>Full Stock Report</td><td><a class='btn' href='/full-stock-report'>Run</a></td></tr>";
      $content .= "<tr><td>Request Sheet Report</td><td><a class='btn' href='/request-sheet-report'>Run</a></td></tr>";
    }

    if (in_array('administrator', $user->getRoles()) || in_array('welfare_manager', $user->getRoles()) || in_array('field_officer', $user->getRoles())) {
      $content .= "<tr><td>Report Card</td><td><a class='btn' href='/report-card'>Run</a></td></tr>";
    }

    if (in_array('administrator', $user->getRoles()) || in_array('inventory_executive', $user->getRoles()) || in_array('inventory_manager', $user->getRoles())) {
      $content .= "<tr><td>Game Issue Report</td><td><a class='btn' href='/report-card'>Run</a></td></tr>";
    }

    $content .= "</tbody></table>";

    return [
      '#type'   => 'markup',
      '#markup' => $content,
    ];
  }

  /**
   * Mark game request as packed.
   */
  public function markAsPacked($nid) {
    $game_request_node = Node::load($nid);
    $game_request_node->set('field_game_request_status', 'packed');

    // Make this change a new revision.
    $user_id = \Drupal::currentUser()->id();
    $game_request_node->setNewRevision(TRUE);
    $game_request_node->setRevisionCreationTime(REQUEST_TIME);
    $game_request_node->setRevisionUserId($user_id);

    $game_request_node->save();

    drupal_set_message('Game Request (RID:' . $nid . ') has been mark as packed successfully.', 'status', TRUE);
    $response = new RedirectResponse('/packed-game-requests');
    $response->send();
  }

  /**
   * Mark game request as approved.
   */
  public function markAsApproved($nid) {
    $game_request_node = Node::load($nid);
    $game_request_node->set('status', 1);

    // Make this change a new revision.
    $user_id = \Drupal::currentUser()->id();

    $game_request_node->set('field_gr_approved_denied_by', $user_id);
    $game_request_node->set('field_gr_date_of_approval_denied', time());

    $game_request_node->setNewRevision(TRUE);
    $game_request_node->setRevisionCreationTime(REQUEST_TIME);
    $game_request_node->setRevisionUserId($user_id);

    $game_request_node->save();

    drupal_set_message('Game Rquest (RID:' . $nid . ') has been mark as approved successfully.', 'status', TRUE);
    $response = new RedirectResponse('/pending-game-requests');
    $response->send();
  }

  /**
   * Mark game request as dispatched.
   */
  public function markAsDispatched($nid) {
    global $base_url;
    $user_id = \Drupal::currentUser()->id();
    $date = time();
    $game_request_node = Node::load($nid);
    $game_request_node->set('field_gr_dispatched_by', $user_id);
    $game_request_node->set('field_gr_date_of_dispatched', $date);
    $game_request_node->set('field_game_request_status', 'dispatched');

    // Make this change a new revision.
    $user_id = \Drupal::currentUser()->id();
    $game_request_node->setNewRevision(TRUE);
    $game_request_node->setRevisionCreationTime(REQUEST_TIME);
    $game_request_node->setRevisionUserId($user_id);
    $uid = $game_request_node->getOwnerId();
    $game_request_node->save();

    // Send mail.
    if (!empty($uid)) {
      $sql = db_query("SELECT DISTINCT u.field_associated_field_officer_target_id as uid, us.mail, fn.field_first_name_value as fname
                       FROM tban_node__field_associated_field_officer as u
                       LEFT JOIN tban_users_field_data as us ON us.uid = u.field_associated_field_officer_target_id
                       LEFT JOIN tban_user__field_first_name as fn ON fn.entity_id = us.uid
                       WHERE (u.entity_id IN (SELECT entity_id FROM tban_node__field_associated_field_officer WHERE field_associated_field_officer_target_id = {$uid}))")->fetchAll();

      if (!empty($sql)) {
        foreach ($sql as $r) {
          $name     = $r->fname;
          $to       = $r->mail;
          $subject  = 'Dispatch Game Request';
          $body     = "<div style='width: 600px; margin: 0 auto; font-size: 14px; font-family: calibri; border: 1px solid #666;'>
                        <div><img src='" . $base_url . "/sites/default/files/tb-logo_0.png' alt='TOYBANK' moz-do-not-send='true' width='200px' height='68px'></div>
                        <div style='padding:20px;'>
                          Hello $name, <br/><br/>
                          <p>Game Request has been dispatched successfully.
                          Click <a href='" . $base_url . "/user/login?destination=/dispatched-request-details/$nid'>here</a> to view details.</p><br>
                          Regards,<br/>
                          Team Toybank<br/><br/>
                          [ Note: This is system generated automated message please do not reply ]
                        </div>
                      </div>";
          $headers  = "From: no-reply@toybank.com\r\n";
          $headers .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
          mail($to, $subject, $body, $headers);
        }
      }
    }

    drupal_set_message('Game Request (RID:' . $nid . ') has been mark as dispatched successfully.', 'status', TRUE);
    $response = new RedirectResponse('/dispatched-game-requests');
    $response->send();
  }

  /**
   * Mark game request as delivered.
   */
  public function markAsDelivered($nid) {
    $game_request_node = Node::load($nid);
    $date = time();
    $user_id = \Drupal::currentUser()->id();

    $query_status = db_query("SELECT COUNT(rs.field_request_status_value) as total_games, SUM(case when rs.field_request_status_value = 'ok' then 1 else 0 end) as ok_games
                              FROM tban_node__field_game_request_quantity as rq
                              LEFT JOIN tban_field_collection_item__field_request_status as rs ON rs.entity_id = rq.field_game_request_quantity_value
                              WHERE rq.entity_id = {$nid}")->fetchAll();

    if(($query_status[0]->total_games) == ($query_status[0]->ok_games)) {
      $game_request_node->set('field_game_request_status', 'delivered');
    }
    else {
      $game_request_node->set('field_game_request_status', 'partially_delivered');
    }

    $game_request_node->set('field_gr_delivered_by', $user_id);
    $game_request_node->set('field_gr_date_of_delivered', $date);
    unset($game_request_node->field_gr_date_of_closed);

    // Make this change a new revision.
    $game_request_node->setNewRevision(TRUE);
    $game_request_node->setRevisionCreationTime(REQUEST_TIME);
    $game_request_node->setRevisionUserId($user_id);

    $game_request_node->save();

    // Create/Update play center inventory.
    $pc_query = db_query("SELECT field_play_center_target_id FROM tban_node__field_play_center WHERE entity_id = $nid")->fetchAll();

    if (!empty($pc_query)) {
      $play_center_id     = $pc_query[0]->field_play_center_target_id;
      $get_game_qty_query = db_query("SELECT fc1.field_request_game_name_target_id, fc2.field_packed_quantity_value
                                      FROM tban_node__field_game_request_quantity as fc
                                      LEFT JOIN tban_field_collection_item__field_request_game_name as fc1 ON fc1.entity_id = fc.field_game_request_quantity_value
                                      LEFT JOIN tban_field_collection_item__field_packed_quantity as fc2 ON fc2.entity_id = fc.field_game_request_quantity_value
                                      WHERE fc.entity_id = $nid AND fc2.field_packed_quantity_value != 0")->fetchAll();

      if (!empty($get_game_qty_query)) {
        foreach ($get_game_qty_query as $v) {
          $game_id         = $v->field_request_game_name_target_id;
          $game_qty        = $v->field_packed_quantity_value;
          $check_pci_query = db_query("SELECT pcti.entity_id, pcti.field_pc_total_inventory_value
                                       FROM tban_node__field_pc_total_inventory as pcti
                                       LEFT JOIN tban_node__field_pc_inv_game_name as pcign ON pcign.entity_id = pcti.entity_id
                                       LEFT JOIN tban_node__field_play_center_inventory_name as ipc ON ipc.entity_id = pcti.entity_id
                                       WHERE ipc.field_play_center_inventory_name_target_id = $play_center_id AND pcign.field_pc_inv_game_name_target_id = $game_id")->fetchAll();

          if (!empty($check_pci_query)) {
            $pc_inventory_node = Node::load($check_pci_query[0]->entity_id);
            $total_pci_qty     = ($check_pci_query[0]->field_pc_total_inventory_value) + ($game_qty);
            $pc_inventory_node->set('field_pc_total_inventory', $total_pci_qty);
            $pc_inventory_node->save();
          }
          else {
            $new_pci = Node::create([
              'type'                             => 'play_center_inventory',
              'title'                            => 'Play Center Inventory',
              'field_pc_inv_game_name'           => $game_id,
              'field_play_center_inventory_name' => $play_center_id,
              'field_pc_total_inventory'         => $game_qty,
            ]);
            $new_pci->save();
          }
        }
      }
    }

    drupal_set_message('Game Rquest (RID:' . $nid . ') has been mark as delivered successfully.', 'status', TRUE);
    $response = new RedirectResponse('/delivered-game-requests');
    $response->send();
  }

  /**
   * Mark game request as closed.
   */
  public function markAsClosed($nid) {
    $game_request_node = Node::load($nid);
    $game_request_node->set('field_game_request_status', 'closed');

    // Make this change a new revision.
    $user_id = \Drupal::currentUser()->id();
    $game_request_node->setNewRevision(TRUE);
    $game_request_node->setRevisionCreationTime(REQUEST_TIME);
    $game_request_node->setRevisionUserId($user_id);

    $game_request_node->set('field_gr_date_of_closed', time());
    $game_request_node->set('field_gr_closed_by', $user_id);

    $game_request_node->save();

    drupal_set_message('Game Rquest (RID:' . $nid . ') has been mark as closed successfully.', 'status', TRUE);
    $response = new RedirectResponse('/closed-game-requests');
    $response->send();
  }

  /**
   * Implements dispatch sheet report.
   */
  public function dispatchSheetReport($nid) {
    $query = db_query("SELECT td.name as category, GROUP_CONCAT(sc.field_sub_catgeory_value) as subcategory, fd.title as game, co.field_game_code_value as code, pq.field_packed_quantity_value as quantity, td.weight
                       FROM tban_node__field_game_request_quantity as grq
                       LEFT JOIN tban_field_collection_item__field_request_game_name as gn ON gn.entity_id = grq.field_game_request_quantity_value
                       LEFT JOIN tban_field_collection_item__field_packed_quantity as pq ON pq.entity_id = grq.field_game_request_quantity_value
                       LEFT JOIN tban_node__field_category as gc ON gc.entity_id = gn.field_request_game_name_target_id
                       LEFT JOIN tban_taxonomy_term_field_data as td ON td.tid = gc.field_category_target_id
                       LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = gc.entity_id
                       LEFT JOIN tban_node_field_data as fd ON fd.nid = gn.field_request_game_name_target_id
                       LEFT JOIN tban_node__field_game_code as co ON co.entity_id = gn.field_request_game_name_target_id
                       WHERE grq.entity_id = $nid AND pq.field_packed_quantity_value > 0
                       GROUP BY gn.field_request_game_name_target_id, pq.field_packed_quantity_value, td.name, fd.title, co.field_game_code_value, td.weight
                       ORDER BY td.weight")->fetchAll();

    if (!empty($query)) {
      global $base_url;
      $category_count = $subcat = [];
      $headercode     = '';
      $table          = "<table class='tbl_inventory' id='dispatch-report-table'>
                           <thead>
                             <th>#</th><th>Name of the Game/Toy</th><th>Quantity</th><th>Opening Check</th>
                           </thead>
                           <tbody>";

      $category_count['Strategy'] = $category_count['Puzzle'] = $category_count['Block'] = $category_count['Alphabetical'] = $category_count['Numerical'] = $category_count['General'] = 0;
      $category_total_count['Strategy'] = $category_total_count['Puzzle'] = $category_total_count['Block'] = $category_total_count['Alphabetical'] = $category_total_count['Numerical'] = $category_total_count['General'] = 0;

      $game_quantity = 0;
      foreach ($query as $k => $v) {
        $category_count[$v->category]++;
        $category_total_count[$v->category] = $category_total_count[$v->category] + $v->quantity;
        $str = $v->category . " / " . $v->subcategory . " / " . $v->game . " / " . $v->code;
        $str = str_replace(",", "+", $str);

        if (strpos($str, 'Pre-Primary') !== FALSE) {
          $subcat['PP'] = 'PP';
        }

        if (strpos($str, 'Primary') !== FALSE) {
          $subcat['Pri'] = 'Pri';
        }

        if (strpos($str, 'Secondary') !== FALSE) {
          $subcat['Sec'] = 'Sec';
        }

        $str    = str_replace("Pre-Primary", "PP", $str);
        $str    = str_replace("Primary", "Pri", $str);
        $str    = str_replace("Secondary", "Sec", $str);
        $str    = str_replace("Pri+PP", "PP+Pri", $str);
        $game_quantity += $v->quantity;
        $row = $k + 1;
        $table .= "<tr><td>" . ($k + 1) . "</td><td>" . $str . "</td><td>" . $v->quantity . "</td><td></td></tr>";
      }

      $table .= "<tr><td></td><td>Initial Total = " . count($query) . "</td><td>Total = $game_quantity</td><td></td></tr>";
      $table .= "</tbody></table>";

      $sql = db_query("SELECT pc.field_playc_value, nd.title, pco.field_partner_code_value
                       FROM tban_node__field_cluster as fc
                       LEFT JOIN tban_node_field_data as nd ON fc.field_cluster_target_id = nd.nid
                       LEFT JOIN tban_node__field_playc as pc ON pc.entity_id = fc.entity_id
                       LEFT JOIN tban_node__field_play_center as grpc ON pc.entity_id = grpc.field_play_center_target_id
                       LEFT JOIN tban_node__field_partner as p ON p.entity_id = fc.entity_id
                       LEFT JOIN tban_node__field_partner_code as pco ON pco.entity_id = p.field_partner_target_id
                       WHERE grpc.entity_id = $nid")->fetchAll();

      if (!empty($sql)) {
        $headercode = $sql[0]->field_playc_value . "_" . $sql[0]->field_partner_code_value . "_" . $sql[0]->title . "_" . implode('+', $subcat);
      }

      $header = "<div class='dispatch-report-header'>
                   <div class='dr-header-left'><img src='$base_url/sites/default/files/toybank_logo.png'></div>
                   <div class='dr-header-right'>
                    <div class='dr-requested-id'><span class='request-id-dispatch'><span>Request ID</span>: " . $nid . "</span> <span class='total-dispatch-sheet'><span>Total</span>: ".$game_quantity."(".$row.")</span></div>
                     <div>" . $headercode . "</div>
                     <div><span class='category-count'>".$category_total_count['Strategy']. " (". $category_count['Strategy'] . ") - Strategy</span><span class='category-count'>" . $category_total_count['Block'] . " (" . $category_count['Block'] . ") - Block</span><span class='category-count'>" . $category_total_count['Numerical'] . " (" . $category_count['Numerical'] . ") - Numerical</span></div>
                     <div><span class='category-count'>".$category_total_count['Puzzle']." (" . $category_count['Puzzle'] . ") - Puzzle</span><span class='category-count'>" . $category_total_count['Alphabetical'] . " (" . $category_count['Alphabetical'] . ") - Alphabetical</span><span class='category-count'>" . $category_total_count['General'] . " (" . $category_count['General'] . ") - General</span></div>
                   </div>
                 </div>";

      $footer = "<div class='dispatch-report-footer'>
                   <div class='dr-footer-heading views-align-center'>AT THE TIME OF DISPATCH</div>
                   <div><span class='dr-footer-left'>Signature and Date Of Partner At Dispatch</span><span class='dr-footer-right'>Signature and Date Of From Toybank At Dispatch</span></div>
                   <div class='dr-footer-heading views-align-center'>AT THE TIME OF SETUP / REPLACEMENT</div>
                   <div><span class='dr-footer-left'>Signature and Date Of Toybank Authority</span><span class='dr-footer-right'>Signature Of Partner Authority</span></div>
                 </div>";

      $back_button = '<div class="back-but"><a href="/dispatched-game-requests"><span class="glyphicon glyphicon-repeat"></span> Back to List</a></div>';

      $element = [
        '#type'   => 'markup',
        '#markup' => $back_button . '<div class="dispatch-report-wrapper">' . $header . $table . $footer . '</div>',
        '#attached' => [
          'library' => [
            'tb_custom/toybank_customJS',
          ],
        ],
      ];

      return $element;
    }
  }

}
