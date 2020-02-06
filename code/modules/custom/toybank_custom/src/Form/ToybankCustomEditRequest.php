<?php

namespace Drupal\toybank_custom\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\field_collection\Entity\FieldCollectionItem;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Implements generate game request form.
 */
class ToybankCustomEditRequest extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_request';
  }

  /**
   * Return category shortfall.
   */
  public function getShortfalls($selected_pc, $selected_fo) {
    // Set Ideal Row from update ideal content.
    $cluster_join = $cluster_where = "";

    // Only for field officer.
    if ($selected_fo != 0) {
      $cluster_join  = "LEFT JOIN tban_node__field_cluster cluster ON upinv.entity_id = cluster.entity_id
                        LEFT JOIN tban_node__field_associated_field_officer asso ON cluster.field_cluster_target_id = asso.entity_id";
      $cluster_where = " AND asso.field_associated_field_officer_target_id = {$selected_fo}";
    }

    $get_update_ideal_id = db_query("SELECT upinv.field_node_id_update_ideal_inv_value as IdealID
                                     FROM tban_node__field_node_id_update_ideal_inv upinv
                                     {$cluster_join}
                                     WHERE upinv.entity_id = {$selected_pc} {$cluster_where}")->fetchAll();
    $update_ideal_node   = Node::load($get_update_ideal_id[0]->IdealID);

    if (!empty($update_ideal_node)) {
      $shortfallnames["Ideal"]['Strategy']     = $update_ideal_node->get('field_strategy_update_ideal')->value;
      $shortfallnames["Ideal"]['Puzzle']       = $update_ideal_node->get('field_puzzle_update_ideal')->value;
      $shortfallnames["Ideal"]['Block']        = $update_ideal_node->get('field_block_update_ideal')->value;
      $shortfallnames["Ideal"]['Alphabetical'] = $update_ideal_node->get('field_alphabetical_update_ideal')->value;
      $shortfallnames["Ideal"]['Numerical']    = $update_ideal_node->get('field_numerical_update_ideal')->value;
      $shortfallnames["Ideal"]['General']      = $update_ideal_node->get('field_general_update_ideal')->value;
    }
    else {
      $shortfallnames["Ideal"] = [
        "Strategy"     => 0,
        "Puzzle"       => 0,
        "Block"        => 0,
        "Alphabetical" => 0,
        "Numerical"    => 0,
        "General"      => 0,
      ];
    }

    array_push($shortfallnames['Ideal'], array_sum($shortfallnames["Ideal"]));

    // Set Actual Row (after deleivery of games)
    $shortfallnames["Actual"] = [
      "Strategy"     => 0,
      "Puzzle"       => 0,
      "Block"        => 0,
      "Alphabetical" => 0,
      "Numerical"    => 0,
      "General"      => 0,
    ];
    $get_game_and_qty = [];
    $get_actual       = ToybankCustomGenerateRequest::getActual($selected_pc);

    foreach ($get_actual as $key => $val) {
      $get_game_and_qty[$val['GameID']] += $val['Quantity'];
    }

    if (!empty($get_game_and_qty)) {
      foreach ($get_game_and_qty as $key => $val_qty) {
        $game_info = Node::load($key);
        $cat_name  = $game_info->get('field_category')->entity->getName();

        if (array_key_exists($cat_name, $shortfallnames["Actual"])) {
          $shortfallnames["Actual"][$cat_name] += $val_qty;
        }
        else {
          $shortfallnames["Actual"][$cat_name] = 0;
        }
      }
    }

    array_push($shortfallnames['Actual'], array_sum($shortfallnames["Actual"]));

    // Set Shortfall Row(Ideal - Actual)
    foreach ($shortfallnames['Ideal'] as $key => $value) {
      $shortfallnames["Shortfall"][$key] = $shortfallnames['Ideal'][$key] - $shortfallnames['Actual'][$key];
    }

    // Set Available Row (from Inventory)
    $get_game_fc_available  = ToybankCustomGenerateRequest::getAvailable($selected_pc);
    $available_strategy_sum = $available_puzzle_sum = $available_block_sum = $available_alpha_sum = $available_numerical_sum = $available_general_sum = 0;

    foreach ($get_game_fc_available as $key => $val) {
      if ($val->CategoryName == "Strategy") {
        $available_strategy_sum = $available_strategy_sum + $val->Quantity;
      }

      if ($val->CategoryName == "Puzzle") {
        $available_puzzle_sum = $available_puzzle_sum + $val->Quantity;
      }

      if ($val->CategoryName == "Block") {
        $available_block_sum = $available_block_sum + $val->Quantity;
      }

      if ($val->CategoryName == "Alphabetical") {
        $available_alpha_sum = $available_alpha_sum + $val->Quantity;
      }

      if ($val->CategoryName == "Numerical") {
        $available_numerical_sum = $available_numerical_sum + $val->Quantity;
      }

      if ($val->CategoryName == "General") {
        $available_general_sum = $available_general_sum + $val->Quantity;
      }
    }

    $shortfallnames["Available"]['Strategy']     = $available_strategy_sum;
    $shortfallnames["Available"]['Puzzle']       = $available_puzzle_sum;
    $shortfallnames["Available"]['Block']        = $available_block_sum;
    $shortfallnames["Available"]['Alphabetical'] = $available_alpha_sum;
    $shortfallnames["Available"]['Numerical']    = $available_numerical_sum;
    $shortfallnames["Available"]['General']      = $available_general_sum;
    array_push($shortfallnames['Available'], array_sum($shortfallnames["Available"]));

    // If Available is negative replace with 0.
    foreach ($shortfallnames["Available"] as $key => $val) {
      if ($val < 0) {
        $shortfallnames["Available"][$key] = 0;
      }
    }

    return $shortfallnames;
  }

  /**
   * Return category term.
   */
  public function getCategotyTerm($vocabulary) {
    $vid             = $vocabulary;
    $custom_category = [];
    $terms           = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);

    foreach ($terms as $term) {
      $term_data[] = [
        'name' => $term->name,
      ];
    }

    if ($vid == "category") {
      foreach ($term_data as $val) {
        if ($val["name"] == "Strategy") {
          $custom_category[0] = "S";
        }

        if ($val["name"] == "Puzzle") {
          $custom_category[1] = "P";
        }

        if ($val["name"] == "Block") {
          $custom_category[2] = "B";
        }

        if ($val["name"] == "Alphabetical") {
          $custom_category[3] = "A";
        }

        if ($val["name"] == "Numerical") {
          $custom_category[4] = "N";
        }

        if ($val["name"] == "General") {
          $custom_category[5] = "G";
        }
      }

      ksort($custom_category);
    }

    return($custom_category);
  }

  /**
   * Get available games.
   */
  public function getAvailable($pcid) {
    $get_pc_sub_category   = db_query("SELECT field_primary_check_value FROM tban_node__field_primary_check WHERE entity_id = {$pcid} ORDER BY field_primary_check_value")->fetchAll();
    $get_game_fc_available = db_query("SELECT mfc.entity_id as InventoryID, mgamename.field_game_name_target_id as GameNodeId, qty.field_total_inventory_value as Quantity, fcategory.field_category_target_id as tid, taxname.name as CategoryName, gstat.field_game_status_value as Gstatus
                                       FROM tban_node__field_multiple_games mfc
                                       LEFT JOIN tban_field_collection_item__field_game_name mgamename ON mfc.field_multiple_games_value = mgamename.entity_id
                                       LEFT JOIN tban_node__field_category fcategory ON fcategory.entity_id = mgamename.field_game_name_target_id
                                       LEFT JOIN tban_taxonomy_term_field_data taxname ON taxname.tid = fcategory.field_category_target_id
                                       LEFT JOIN tban_node__field_inv_game_name gamename ON mgamename.field_game_name_target_id = gamename.field_inv_game_name_target_id
                                       LEFT JOIN tban_node__field_total_inventory qty ON qty.entity_id = gamename.entity_id
                                       LEFT JOIN tban_node__field_game_status gstat ON gstat.entity_id = mgamename.field_game_name_target_id
                                       WHERE gstat.field_game_status_value = 'Active'
                                       ORDER BY taxname.weight")->fetchAll();

    if (!empty($get_game_fc_available)) {
      // Code to get unique game id and get subcategory.
      $game_and_sub_cat = [];
      $temp_arr         = implode(",", array_unique(array_column($get_game_fc_available, 'GameNodeId')));
      $get_game_sub_cat = db_query("SELECT field_sub_catgeory_value as SubCategory, entity_id FROM tban_node__field_sub_catgeory WHERE entity_id IN ($temp_arr) ORDER BY field_sub_catgeory_value")->fetchAll();

      foreach ($get_game_sub_cat as $key => $val) {
        // Checked PC subcategory with inventory game subcategory.
        foreach ($get_pc_sub_category as $pc_val) {
          if ($val->SubCategory == $pc_val->field_primary_check_value) {
            $game_and_sub_cat1[$val->entity_id][] = $val->SubCategory;
            $game_and_sub_cat[$val->entity_id]    = implode(", ", $game_and_sub_cat1[$val->entity_id]);
          }
        }
      }

      foreach ($get_game_fc_available as $key => $val) {
        if (array_key_exists($val->GameNodeId, $game_and_sub_cat)) {
          // Added new key "SubCategory" in main array.
          $get_game_fc_available[$key]->SubCategory = $game_and_sub_cat[$val->GameNodeId];
        }
        else {
          unset($get_game_fc_available[$key]);
        }
      }
    }

    return $get_game_fc_available;
  }

  /**
   * Get actual games of playcenter.
   */
  public function getActual($selected_pc) {
    $get_actual_games = $get_actual = [];

    //~ $get_actual_query = db_query("SELECT gstatus.entity_id, gstatus.field_game_request_status_value as Status, game.field_request_game_name_target_id as GameID, gameName.title as GameName, code.field_game_code_value as GCode, quantity.field_packed_quantity_value as Quantity, subcat.field_sub_catgeory_value as SubCategory, tax.name as Category
                            //~ FROM tban_node__field_game_request_status gstatus
                            //~ LEFT JOIN tban_node__field_game_request_quantity fieldcoll ON gstatus.entity_id = fieldcoll.entity_id
                            //~ LEFT JOIN tban_field_collection_item__field_request_game_name game ON fieldcoll.field_game_request_quantity_value = game.entity_id
                            //~ LEFT JOIN tban_field_collection_item__field_packed_quantity quantity ON fieldcoll.field_game_request_quantity_value = quantity.entity_id
                            //~ LEFT JOIN tban_node__field_play_center pc ON pc.entity_id = gstatus.entity_id
                            //~ LEFT JOIN tban_node_field_data gameName ON game.field_request_game_name_target_id = gameName.nid
                            //~ LEFT JOIN tban_node__field_sub_catgeory subcat ON subcat.entity_id = game.field_request_game_name_target_id
                            //~ LEFT JOIN tban_node__field_game_code code ON code.entity_id = game.field_request_game_name_target_id
                            //~ LEFT JOIN tban_node__field_category fcategory ON fcategory.entity_id = game.field_request_game_name_target_id
                            //~ LEFT JOIN tban_taxonomy_term_field_data tax ON tax.tid = fcategory.field_category_target_id
                            //~ WHERE (gstatus.field_game_request_status_value = 'delivered' OR gstatus.field_game_request_status_value = 'closed')
                                  //~ AND quantity.field_packed_quantity_value != 0
                                  //~ AND pc.field_play_center_target_id = {$selected_pc}
                                  //~ AND subcat.field_sub_catgeory_value IN (SELECT field_primary_check_value FROM tban_node__field_primary_check WHERE entity_id = {$selected_pc})
                            //~ ORDER BY tax.weight, subcat.field_sub_catgeory_value, gameName.title")->fetchAll();

    $get_actual_query = db_query("SELECT pcn.entity_id as entity_id, gn.field_pc_inv_game_name_target_id as GameID, gc.field_game_code_value as GCode, qty.field_pc_total_inventory_value as Quantity, tx.name AS Category, sc.field_sub_catgeory_value AS SubCategory, nd.title AS GameName
                                  FROM tban_node__field_play_center_inventory_name AS pcn
                                  LEFT JOIN tban_node__field_pc_total_inventory as qty ON pcn.entity_id = qty.entity_id
                                  LEFT JOIN tban_node__field_pc_inv_game_name as gn ON gn.entity_id = pcn.entity_id
                                  LEFT JOIN tban_node__field_sub_catgeory as sc ON sc.entity_id = gn.field_pc_inv_game_name_target_id
                                  LEFT JOIN tban_node_field_data nd ON sc.entity_id = nd.nid
                                  LEFT JOIN tban_node__field_category ct ON nd.nid = ct.entity_id
                                  LEFT JOIN tban_taxonomy_term_field_data tx ON ct.field_category_target_id = tx.tid
                                  LEFT JOIN tban_node__field_game_code as gc ON gc.entity_id = nd.nid
                                  WHERE pcn.field_play_center_inventory_name_target_id = {$selected_pc}
                                  ORDER BY tx.weight, sc.field_sub_catgeory_value, nd.title")->fetchAll();

    foreach ($get_actual_query as $v) {
      $get_actual[$v->entity_id . '_' . $v->GameID]['entity_id'] = $v->entity_id;
      $get_actual[$v->entity_id . '_' . $v->GameID]['GameID'] = $v->GameID;
      //~ $get_actual[$v->entity_id . '_' . $v->GameID]['GameName'] = "(" . $v->GCode . ") " . $v->GameName;
      $get_actual[$v->entity_id . '_' . $v->GameID]['GameName'] = $v->GameName;
      $get_actual[$v->entity_id . '_' . $v->GameID]['GCode'] = $v->GCode;
      $get_actual[$v->entity_id . '_' . $v->GameID]['Quantity'] = $v->Quantity;

      if ($get_actual[$v->entity_id . '_' . $v->GameID]['SubCategory']) {
        $get_actual[$v->entity_id . '_' . $v->GameID]['SubCategory'] = $get_actual[$v->entity_id . '_' . $v->GameID]['SubCategory'] . ', ' . $v->SubCategory;
      }
      else {
        $get_actual[$v->entity_id . '_' . $v->GameID]['SubCategory'] = $v->SubCategory;
      }

      $get_actual[$v->entity_id . '_' . $v->GameID]['Category'] = $v->Category;
    }

    foreach ($get_actual as $val) {
      $get_actual_games[$val['GameID']]['entity_id']   = $val['entity_id'];
      $get_actual_games[$val['GameID']]['GameID']      = $val['GameID'];
      $get_actual_games[$val['GameID']]['GCode']       = $val['GCode'];
      $get_actual_games[$val['GameID']]['GameName']    = $val['GameName'];
      $get_actual_games[$val['GameID']]['Quantity']   += $val['Quantity'];
      $get_actual_games[$val['GameID']]['SubCategory'] = $val['SubCategory'];
      $get_actual_games[$val['GameID']]['Category']    = $val['Category'];
    }

    return $get_actual_games;
  }

  /**
   * Get available category.
   */
  public function getCategoryAvailable($get_game_from_avail) {
    // Code to get unique game id and get subcategory.
    $temp_arr    = array_unique(array_column($get_game_from_avail, 'GameNodeId'));
    $subcategory = array_unique(array_column($get_game_from_avail, 'SubCategory'));

    foreach (array_intersect_key($get_game_from_avail, $temp_arr) as $val) {
      $node = Node::load($val->GameNodeId);

      foreach ($node->get('field_sub_catgeory')->getValue() as $val1) {
        if ($val1['value'] == $subcategory[0]) {
          $game_id[$val->CategoryName][$val->GameNodeId][] = $val1['value'];
        }
      }

      $pc_generate_request['Category'][$val->CategoryName] = implode(", ", $game_id[$val->CategoryName][$val->GameNodeId]);
    }

    return $pc_generate_request;
  }

  /**
   * Get selected title.
   */
  public function getSelectedTitle($node_id, $user_id) {
    $title = [];

    if (!empty($node_id)) {
      $node      = Node::load($node_id);
      $type_name = $node->type->entity->label();

      if ($type_name == 'Game') {
        //~ $title[] = "(" . $node->get('field_game_code')->getValue()[0]['value'] . ") " . $node->getTitle();
        $title[] = $node->get('field_game_code')->getValue()[0]['value'];
        $title[] = $node->getTitle();
      }
      else {
        $title[] = $node->getTitle();
      }
    }

    if (!empty($user_id)) {
      $account = User::load($user_id);
      $fname   = $account->get('field_first_name')->value;
      $lname   = $account->get('field_last_name')->value;
      $title[] = $fname . " " . $lname;
    }

    return $title;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $rid = NULL) {
    $game_request = Node::load($rid);
    $fo_status = $game_request->get('field_fo_status')->value;

    if ($fo_status != 'waiting_for_approval') {
      $response = new RedirectResponse('/pending-game-requests');
      $response->send();
    }

    $game_request_info = db_query("SELECT pc.field_play_center_target_id, rb.field_gr_requested_by_target_id
                                   FROM tban_node__field_play_center as pc
                                   LEFT JOIN tban_node__field_gr_requested_by as rb ON rb.entity_id = pc.entity_id
                                   WHERE pc.entity_id = $rid")->fetchAll();
    $selected_pc = $game_request_info[0]->field_play_center_target_id;
    $selected_fo = $game_request_info[0]->field_gr_requested_by_target_id;

    $form['#tree'] = TRUE;

    $step  = $form_state->get('step');

    if ($step == NULL) {
      $step  = 2;

      $tempstore = \Drupal::service('user.private_tempstore')->get('toybank_custom');
      $oldtemp   = $tempstore->get("sel_key");

      foreach ($oldtemp as $v) {
        $tempstore->delete($v);
      }

      $tempstore->delete("sel_key");
    }

    $form['grid1_wrapper'] = [
      '#type'   => 'container',
      '#prefix' => '<div id="grid1_wrapper" class="quick-contact__form col-xs-12 col-md-12">',
      '#suffix' => '</div>',
    ];

    $submitted_values = $form_state->getValues();
    $pc_list          = ToybankCustomGenerateRequest::getCategotyTerm('category');

    $shortfallnames = $titles = [];

    // Get seleted titles.
    $titles = ToybankCustomGenerateRequest::getSelectedTitle($selected_pc, $selected_fo);

    $form['grid1_wrapper']['playcenter_label'] = [
      '#title'  => "Play Center: " . $titles[0],
      '#type'   => 'label',
      '#prefix' => '<div id ="selected_playcenter_label" class = "selected_playcenter_label">',
      '#suffix' => '</div>',
    ];

    $playcenter_kids_age = db_query("SELECT GROUP_CONCAT(' ', field_primary_check_value) as subcat FROM tban_node__field_primary_check WHERE entity_id = $selected_pc ORDER BY field_primary_check_value")->fetchAll();

    if (!empty($playcenter_kids_age)) {
      $sub_category = str_replace("Pre-Primary", "PP", $playcenter_kids_age[0]->subcat);
      $sub_category = str_replace("Primary", "Pri", $sub_category);
      $sub_category = str_replace("Secondary", "Sec", $sub_category);

      $form['grid1_wrapper']['playcenter_kids_age'] = [
        '#title'  => "Age: " . $sub_category,
        '#type'   => 'label',
      ];
    }

    $form['grid1_wrapper']['fieldofficer_label'] = [
      '#title'  => "Program Officer: " . $titles[1],
      '#type'   => 'label',
      '#value'  => 'Program Officer:',
      '#prefix' => '<div id ="selected_fieldofficer_label" class = "selected_fieldofficer_label">',
      '#suffix' => '</div>',
    ];


    $pending_request_query = db_query("SELECT pc.entity_id as rid, fos.field_fo_status_value as status, nd.created, CONCAT(fn.field_first_name_value, ' ', ln.field_last_name_value) as requested_by, SUM(rq.field_req_game_quantity_value) as requested_qty
                                       FROM tban_node__field_play_center AS pc
                                       LEFT JOIN tban_node_field_data AS nd ON nd.nid = pc.entity_id
                                       LEFT JOIN tban_user__field_first_name AS fn ON fn.entity_id = nd.uid
                                       LEFT JOIN tban_user__field_last_name AS ln ON ln.entity_id = nd.uid
                                       LEFT JOIN tban_node__field_game_request_status AS grs ON grs.entity_id = pc.entity_id
                                       LEFT JOIN tban_node__field_fo_status AS fos ON fos.entity_id = pc.entity_id
                                       LEFT JOIN tban_node__field_game_request_quantity AS gq ON gq.entity_id = pc.entity_id
                                       LEFT JOIN tban_field_collection_item__field_req_game_quantity AS rq ON rq.entity_id = gq.field_game_request_quantity_value
                                       WHERE pc.field_play_center_target_id = $selected_pc AND grs.field_game_request_status_value = 'pending' AND fos.field_fo_status_value != 'denied'
                                       GROUP BY pc.entity_id, fos.field_fo_status_value, nd.created, fn.field_first_name_value, ln.field_last_name_value
                                       ORDER BY pc.entity_id DESC")->fetchAll();

    if (!empty($pending_request_query)) {
      $pending_request_header = $pending_request_rows = [];

      $pending_request_header = ['RID', 'Date of Request', 'Requested by (PO/WM)', 'Requested Quantity', 'Status', 'Action'];

      foreach ($pending_request_query as $pr) {
        $status = $pr->status;

        if ($status == 'approved') {
          $status = 'Waiting to be Packed';
        }

        if ($status == 'waiting_for_approval') {
          $status = 'Waiting for Approval';
        }

        $action_url = new FormattableMarkup('<a href=":link" target="_blank">@name</a>', [':link' => '/pending-request-details/' . $pr->rid, '@name' => 'View']);

        $pending_request_rows[] = [$pr->rid, date('d/m/Y', $pr->created), $pr->requested_by, $pr->requested_qty, $status, $action_url];
      }

      $form['grid1_wrapper']['pending_game_requests'] = [
        '#type'       => 'table',
        '#caption'    => 'Pending Game Requests',
        '#attributes' => [
          'id'    => 'pc_pending_game_requests_table',
          'class' => ' tbl_generate_request',
        ],
        '#header' => $pending_request_header,
        '#rows'   => $pending_request_rows,
      ];
    }

    $shortfallnames = ToybankCustomGenerateRequest::getShortfalls($selected_pc, $selected_fo);

    // Print SPBANG Category.
    $pc_list_header   = [];
    $pc_list_header[] = "";

    foreach ($pc_list as $key => $val) {
      $pc_list_header[] = "{$val}";
    }

    $pc_list_header[] = "Total";
    $pc_list_value    = [];
    $pc_list_row      = [];

    foreach ($shortfallnames as $key => $val) {
      foreach ($val as $key1 => $val1) {
        $pc_list_value[$key1] = "{$val1}";
      }

      $pc_list_row[] = [
        "row1" => "{$key}",
        "row2" => $pc_list_value['Strategy'],
        "row3" => $pc_list_value['Puzzle'],
        "row4" => $pc_list_value['Block'],
        "row5" => $pc_list_value['Alphabetical'],
        "row6" => $pc_list_value['Numerical'],
        "row7" => $pc_list_value['General'],
        "row8" => $pc_list_value[0],
      ];
    }

    $form['grid1_wrapper']['generate_gamestats'] = [
      '#type'       => 'table',
      '#caption'    => 'Game Stats at Play Center',
      '#attributes' => [
        'id'    => 'tbl_generate_request',
        'class' => ' tbl_generate_request',
      ],
      '#header' => $pc_list_header,
      '#rows'   => array_values($pc_list_row),
    ];

    $form['grid1_wrapper']['grid2_wrapper'] = [
      '#type'   => 'container',
      '#prefix' => '<div id="grid2_wrapper" class="quick-contact__form col-xs-12 col-md-12">',
      '#suffix' => '</div>',
    ];

    $pc_list     = ToybankCustomGenerateRequest::getCategotyTerm('category');

    $get_game_from_avail = [];
    $get_game_from_avail = ToybankCustomGenerateRequest::getAvailable($selected_pc);
    $get_game_shortfall  = ToybankCustomGenerateRequest::getShortfalls($selected_pc, $selected_fo);

    foreach ($get_game_from_avail as $key => $val) {
      $pc_generate_request1['Category'][$val->CategoryName][] = $val->SubCategory;
      sort($pc_generate_request1['Category'][$val->CategoryName]);
      $pc_generate_request['Category'][$val->CategoryName]    = implode(", ", array_unique($pc_generate_request1['Category'][$val->CategoryName]));
      $pc_generate_request['Category'][$val->CategoryName]    = implode(", ", array_unique(explode(", ", $pc_generate_request['Category'][$val->CategoryName])));
    }

    /*Check shortfall is minus then not display game list.
     * If Available is 0
     **/
    foreach ($get_game_shortfall['Shortfall'] as $key => $val) {
      if ($get_game_shortfall['Available'][$key] == 0) {
        unset($get_game_shortfall['Shortfall'][$key]);
        unset($pc_generate_request['Category'][$key]);
      }

      if ($val > 0) {
        $pc_generate_request['Shortfall'][$key] = $val;
      }
      else {
        unset($pc_generate_request['Category'][$key]);
      }
    }

    $tbl_game_list = $games_stats_list = $games_stats_header = [];
    $gmaes_stats_total = 0;

    $requested_game_quantity = db_query("SELECT td.name as category, SUM(gq.field_req_game_quantity_value) as game_quantity
                                         FROM tban_node__field_game_request_quantity as gr
                                         LEFT JOIN tban_field_collection_item__field_request_game_name as gn ON gn.entity_id = gr.field_game_request_quantity_value
                                         LEFT JOIN tban_node__field_category as gc ON gc.entity_id = gn.field_request_game_name_target_id
                                         LEFT JOIN tban_taxonomy_term_field_data as td ON td.tid = gc.field_category_target_id
                                         LEFT JOIN tban_field_collection_item__field_req_game_quantity as gq ON gq.entity_id = gr.field_game_request_quantity_value
                                         WHERE gr.entity_id = $rid
                                         GROUP BY td.name")->fetchAllkeyed();

    foreach ($pc_generate_request['Category'] as $key => $val) {
      $sub_category = '';
      $sub_category = str_replace("Pre-Primary", "PP", $val);
      $sub_category = str_replace("Primary", "Pri", $sub_category);
      $sub_category = str_replace("Secondary", "Sec", $sub_category);

      $tbl_game_list[] = [
        'row1' => "{$key}",
        'row2' => "{$sub_category}",
        'row3' => "{$pc_generate_request['Shortfall'][$key]}",
        'row4' => [
          'class' => "td_{$key}",
        ],
      ];

      $requested_quantity    = isset($requested_game_quantity[$key]) ? $requested_game_quantity[$key] : 0;
      $games_stats_header[]  = $key;
      $gmaes_stats_total    += $pc_generate_request['Shortfall'][$key];
      $games_stats_list[0][] = new FormattableMarkup('<span class=":class">:qty</span> / :shortfall', [':class' => $key, ':shortfall' => $pc_generate_request['Shortfall'][$key], ':qty' => $requested_quantity]);
      $games_stats_list[1][$key] = ['class' => "td_{$key}",];
    }

    $games_stats_header[]  = 'Total';
    $games_stats_list[0][] = new FormattableMarkup('<span class="total">:sum</span> / :total', [':total' => $gmaes_stats_total, ':sum' => array_sum($requested_game_quantity)]);
    $games_stats_list[1][] = ['class' => "td_Total",];

    $form['grid1_wrapper']['grid2_wrapper']['selected_games_stats_wrapper'] = [
      '#type'       => 'fieldset',
      '#attributes' => [
        'id' => 'selected_games_stats_wrapper',
      ],
    ];

    $form['grid1_wrapper']['grid2_wrapper']['selected_games_stats_wrapper']['games_stats'] = [
      '#type'       => 'table',
      '#caption'    => 'Selected Games Stats',
      '#attributes' => [
        'id'    => 'selected_games_stats',
        'class' => ' selected_games_stats',
      ],
      '#header' => $games_stats_header,
      '#rows'   => array_values($games_stats_list),
    ];

    $form['grid1_wrapper']['grid2_wrapper']['selected_games_stats_wrapper']['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Update Request',
      '#attributes' => [
        'class'   => ['btn_final_generate_Request'],
        //~ 'style'   => ['display: none;'],
        'onclick' => 'if (!confirm("Update game request? This action cannot be undone.")) {return false;}',
      ],
    ];

    foreach ($pc_generate_request['Category'] as $key => $val) {
      $form['grid1_wrapper']['grid2_wrapper']["get_third_grid_{$key}"] = [
        '#type'       => 'submit',
        '#value'      => 'Select Games',
        '#name'       => "btn_game_details_{$key}",
        '#attributes' => [
          'class' => ["btn_game_detail btn_game_details_{$key}"],
        ],
        '#submit' => ["::getThirdgridSubmit{$key}"],
        '#ajax'   => [
          'callback' => "::ajaxGetThirdgrid",
          'wrapper'  => "grid3_wrapper",
          'effect'   => 'fade',
        ],
      ];
    }

    $form['grid1_wrapper']['grid2_wrapper']['grid3_wrapper'] = [
      '#type'   => 'container',
      '#prefix' => '<div id="grid3_wrapper" class="quick-contact__form col-xs-12 col-md-12">',
      '#suffix' => '</div>',
    ];

    // Step 3.
    if ($step > 2) {
      $submitted_values = $form_state->getValues();

      if (array_key_exists("btn_game_details_Strategy", $submitted_values)) {
        $selected_category = "Strategy";
      }
      elseif (array_key_exists("btn_game_details_Puzzle", $submitted_values)) {
        $selected_category = "Puzzle";
      }
      elseif (array_key_exists("btn_game_details_Block", $submitted_values)) {
        $selected_category = "Block";
      }
      elseif (array_key_exists("btn_game_details_Alphabetical", $submitted_values)) {
        $selected_category = "Alphabetical";
      }
      elseif (array_key_exists("btn_game_details_Numerical", $submitted_values)) {
        $selected_category = "Numerical";
      }
      elseif (array_key_exists("btn_game_details_General", $submitted_values)) {
        $selected_category = "General";
      }

      $form['grid1_wrapper']['grid2_wrapper']['grid3_wrapper']["grid3_wrapper_{$selected_category}"] = [
        '#type'   => 'container',
        '#prefix' => "<div id='grid3_wrapper_{$selected_category}' class='grid3_wrapper_table quick-contact__form col-xs-12 col-md-12'>",
        '#suffix' => '</div>',
      ];

      $game_and_qty        = [];
      $get_game_from_avail = ToybankCustomGenerateRequest::getAvailable($selected_pc);

      foreach ($get_game_from_avail as $key => $val) {
        if ($val->CategoryName == $selected_category) {
          $game_and_qty[$val->GameNodeId] = $val->Quantity;
        }
      }

      $game_request_details = db_query("SELECT gn.field_request_game_name_target_id as game_nid, gq.field_req_game_quantity_value as game_quantity
                                        FROM tban_node__field_game_request_quantity as gr
                                        LEFT JOIN tban_field_collection_item__field_request_game_name as gn ON gn.entity_id = gr.field_game_request_quantity_value
                                        LEFT JOIN tban_field_collection_item__field_req_game_quantity as gq ON gq.entity_id = gr.field_game_request_quantity_value
                                        WHERE gr.entity_id = $rid")->fetchAllkeyed();

      $tempstore = \Drupal::service('user.private_tempstore')->get('toybank_custom');

      foreach (array_unique($game_and_qty) as $key => $val) {
        if ($val > 0) {
          $temp_val = $tempstore->get("reqty_{$selected_category}_{$key}");
          $default_value = isset($temp_val) ? $temp_val : $game_request_details[$key];

          $titles = ToybankCustomGenerateRequest::getSelectedTitle($key, "");
          $reqqty = [
            'data' => [
              '#type' => 'number',
              '#min'  => 0,
              '#max'  => $val,
              '#attributes' => [
                'id'        => "reqty_{$key}",
                'class'     => ["reqty_gen_req"],
                'name'      => "reqty_{$selected_category}_{$key}",
                'data-bind' => "value:replyNumber",
              ],
              '#size'  => 7,
              '#value' => isset($default_value) ? $default_value : '',
            ],
          ];

          $game_selection_rows[$titles[1]] = [
            'name'        => "(" . $titles[0] . ") " . $titles[1],
            'current_qty' => '-',
            'age'         => '-',
            'system_qty'  => $val,
            'request_qty' => $reqqty,
          ];
        }
      }

      $get_actual_id = ToybankCustomGenerateRequest::getActual($selected_pc);

      foreach ($get_actual_id as $val_current) {
        if (strpos($val_current['Category'], $selected_category) !== false) {
          $get_game_req_chng_date                = db_query("SELECT changed as deliveredDate FROM tban_node_field_data WHERE nid = {$val_current['entity_id']}")->fetchAll();
          $delvidate                             = date("Y-m-d", $get_game_req_chng_date[0]->deliveredDate);
          $current_date                          = date("Y-m-d");
          $diff                                  = strtotime($current_date) - strtotime($delvidate);
          $days                                  = abs(round($diff / 86400));

          if ($game_selection_rows[$val_current['GameName']]) {
            $game_selection_rows[$val_current['GameName']]['current_qty'] = $val_current['Quantity'];
            $game_selection_rows[$val_current['GameName']]['age']         = $days;
          }
          else {
            $game_selection_rows[$val_current['GameName']] = [
              'name'        => "(" . $val_current['GCode'] . ") " . $val_current['GameName'],
              'current_qty' => $val_current['Quantity'],
              'age'         => $days,
              'system_qty'  => '-',
              'request_qty' => '-',
            ];
          }
        }
      }

      // To sort game names alphabetically.
      ksort($game_selection_rows);

      $game_selection_header = ['Games', 'C. Qty', 'Age', 'S. Qty', 'R. Qty'];

      $form['grid1_wrapper']['grid2_wrapper']['grid3_wrapper']["grid3_wrapper_{$selected_category}"]['mk'] = [
        '#type'   => 'markup',
        '#markup' => ('<h4>Game Selection: ' . $selected_category . '</h4>'),
      ];

      $form['grid1_wrapper']['grid2_wrapper']['grid3_wrapper']["grid3_wrapper_{$selected_category}"]['game_selection'] = [
        '#type'   => 'table',
        '#header' => $game_selection_header,
        '#rows'   => $game_selection_rows,
        '#sticky' => TRUE,
        '#empty'  => 'No data found',
        '#attributes' => [
          'class' => 'game-selection-table',
        ],
      ];

      $form['grid1_wrapper']['grid2_wrapper']['grid3_wrapper']['back_to_top'] = [
        '#type'   => 'markup',
        '#markup' => "<a href='#grid2_wrapper' class='btn gr_back_to_top'>Back to Top</a>",
      ];
    }

    return $form;
  }

  /**
   * Set step3.
   */
  public function getThirdgridSubmitStrategy(array &$form, FormStateInterface $form_state) {
    $form_state->set('step', 3);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Set step4.
   */
  public function getThirdgridSubmitPuzzle(array &$form, FormStateInterface $form_state) {
    $form_state->set('step', 4);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Set step5.
   */
  public function getThirdgridSubmitBlock(array &$form, FormStateInterface $form_state) {
    $form_state->set('step', 5);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Set step6.
   */
  public function getThirdgridSubmitAlphabetical(array &$form, FormStateInterface $form_state) {
    $form_state->set('step', 6);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Set step7.
   */
  public function getThirdgridSubmitNumerical(array &$form, FormStateInterface $form_state) {
    $form_state->set('step', 7);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Set step8.
   */
  public function getThirdgridSubmitGeneral(array &$form, FormStateInterface $form_state) {
    $form_state->set('step', 8);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Get grid3 wrapper.
   */
  public function ajaxGetThirdgrid(array &$form, FormStateInterface $form_state) {
    $tempstore        = \Drupal::service('user.private_tempstore')->get('toybank_custom');
    $submitted_values = $form_state->getUserInput();
    $sel_value        = (($tempstore->get("sel_key")) ? $tempstore->get("sel_key") : []);

    foreach ($submitted_values as $key => $value) {
      $exp_key = explode('_', $key);

      if ($exp_key[0] == 'reqty') {
        if (!empty($value)) {
          $tempstore->set($key, $value);
          $sel_value[$key] = $key;
        }
      }
    }

    $tempstore->set("sel_key", $sel_value);

    return $form['grid1_wrapper']['grid2_wrapper']['grid3_wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $build_info = $form_state->getBuildInfo();
    $rid = $build_info['args'][0];

    $submitted_values = $form_state->getUserInput();

    $tempstore              = \Drupal::service('user.private_tempstore')->get('toybank_custom');
    $submitted_store_values = $tempstore->get("sel_key");

    $arr_result        = $requested_cat_arr = [];

    $requested_quantity                = [];
    $requested_quantity[0]['value'][0] = [
      'Strategy',
      'Puzzle',
      'Block',
      'Alphabetical',
      'Numerical',
      'General',
      'Total',
    ];

    $requested_cat_arr['Strategy'] = 0;
    $requested_cat_arr['Puzzle'] = 0;
    $requested_cat_arr['Block'] = 0;
    $requested_cat_arr['Alphabetical'] = 0;
    $requested_cat_arr['Numerical'] = 0;
    $requested_cat_arr['General'] = 0;
    $requested_cat_arr['Total'] = 0;

    foreach ($submitted_values as $key => $value) {
      $exp_key = explode('_', $key);

      if ($exp_key[0] == 'reqty') {
        if (!empty($value)) {
          $arr_result[$exp_key[2]] = $value;
          $requested_cat_arr[$exp_key[1]] = $requested_cat_arr[$exp_key[1]] + $value;
        }
      }
    }

    foreach ($submitted_store_values as $key => $value) {
      $exp_key = explode('_', $key);
      $values  = $tempstore->get($key);

      if ($exp_key[0] == 'reqty') {
        if (!empty($values)) {
          if (empty($arr_result[$exp_key[2]])) {
            $requested_cat_arr[$exp_key[1]] = $requested_cat_arr[$exp_key[1]] + $values;
          }

          $arr_result[$exp_key[2]] = $values;
        }
      }
    }

    $requested_cat_arr['Total'] = $requested_cat_arr['Strategy'] + $requested_cat_arr['Puzzle'] + $requested_cat_arr['Block'] + $requested_cat_arr['Alphabetical'] + $requested_cat_arr['Numerical'] + $requested_cat_arr['General'];

    if (empty($arr_result)) {
      return FALSE;
    }

    // Requested quantity in the SPBANG order.
    $requested_quantity[0]['value'][1]        = array_values($requested_cat_arr);
    $requested_quantity[0]['format']          = '';
    $requested_quantity[0]['caption']         = '';
    $requested_quantity[0]['rebuild']['cols'] = 7;
    $requested_quantity[0]['rebuild']['rows'] = 2;


    // Update request
    $game_request = Node::load($rid);

    $game_request->set('field_requested_quantity', $requested_quantity );

    $game_inv_query = db_query("SELECT fc1.entity_id as item_id, gi.entity_id, fc1.field_request_game_name_target_id as game_nid, fc3.field_req_game_quantity_value
                                FROM tban_node__field_game_request_quantity as fc
                                LEFT JOIN tban_field_collection_item__field_request_game_name as fc1 ON fc1.entity_id = fc.field_game_request_quantity_value
                                LEFT JOIN tban_field_collection_item__field_req_game_quantity as fc3 ON fc3.entity_id = fc.field_game_request_quantity_value
                                LEFT JOIN tban_node__field_inv_game_name as gi ON gi.field_inv_game_name_target_id = fc1.field_request_game_name_target_id
                                WHERE fc.entity_id = $rid")->fetchAll();

    // Update existing requested quantity
    if (!empty($game_inv_query)) {
      $pc_query = db_query("SELECT nd.title as name, pcc.field_playc_value as code
                            FROM tban_node__field_play_center as pc
                            LEFT JOIN tban_node_field_data as nd ON nd.nid = pc.field_play_center_target_id
                            LEFT JOIN tban_node__field_playc as pcc ON pcc.entity_id = nd.nid
                            WHERE pc.entity_id = $rid")->fetchAll();

      if ($pc_query) {
        $pc_name = $pc_query[0]->name;
      }
      $user_roles = \Drupal::currentUser()->getRoles();
      $uid        = \Drupal::currentUser()->id();

      if ($uid) {
        $userName = db_query("SELECT CONCAT(field_first_name_value, ' ', field_last_name_value) as name FROM tban_user__field_first_name as f
                              LEFT JOIN tban_user__field_last_name as l ON l.entity_id = f.entity_id
                              WHERE f.entity_id  = $uid")->fetchAssoc();
        if ($userName) {
          $fname = $userName['name'];
        }
      }

      foreach ($game_inv_query as $k => $gi) {
        $game_inv_node = Node::load($gi->entity_id);
        $previous_qty  = $game_inv_node->get('field_total_inventory')->value;
        $prev_req_qty  = $gi->field_req_game_quantity_value;

        if ($arr_result[$gi->game_nid]) {
          $new_req_qty   = $arr_result[$gi->game_nid];
          unset($arr_result[$gi->game_nid]);
        }
        else {
          $new_req_qty   = 0;
        }

        if ($prev_req_qty != $new_req_qty) {
          $req_qty_diff = $new_req_qty - $prev_req_qty;
          $total_qty    = $previous_qty - $req_qty_diff;
          $game_inv_node->set('field_total_inventory', $total_qty);

          // revision
          $game_inv_node->setNewRevision(TRUE);

          if ($prev_req_qty < $new_req_qty) {
            $qty = $new_req_qty - $prev_req_qty;
            $game_inv_node->revision_log = '-'.abs($qty).' '.$pc_name.' Altered by '. $fname . ' (RID: '.$rid.')';
          }
          elseif ($prev_req_qty > $new_req_qty) {
            $qty = $prev_req_qty - $new_req_qty;
            $game_inv_node->revision_log = '+'.abs($qty).' '.$pc_name.' Altered by '. $fname . ' (RID: '.$rid.')';
          }

          $field_collection_item = FieldCollectionItem::load($gi->item_id);
          $field_collection_item->set('field_req_game_quantity', $new_req_qty);

          if ($new_req_qty == 0) {
            $field_collection_item->delete();
          }

          $game_inv_node->setRevisionCreationTime(REQUEST_TIME);
          $game_inv_node->setRevisionUserId($uid);
        }

        $game_inv_node->save();
      }
    }

    // Add new requested quantity
    if (!empty($arr_result)) {
      $pc_query = db_query("SELECT nd.title as name, pcc.field_playc_value as code
                            FROM tban_node__field_play_center as pc
                            LEFT JOIN tban_node_field_data as nd ON nd.nid = pc.field_play_center_target_id
                            LEFT JOIN tban_node__field_playc as pcc ON pcc.entity_id = nd.nid
                            WHERE pc.entity_id = $rid")->fetchAll();

      if ($pc_query) {
        $pc_name = $pc_query[0]->name;
      }

      foreach ($arr_result as $knid => $kvalue) {
        $field_collection_item = FieldCollectionItem::create(['field_name' => 'field_game_request_quantity']);
        $field_collection_item->setHostEntity($game_request);
        $field_collection_item->set('field_request_game_name', $knid);
        $field_collection_item->set('field_req_game_quantity', $kvalue);
        $game_request->field_game_request_quantity[] = ['field_collection_item' => $field_collection_item];

        $game_inv_query = db_query("SELECT entity_id FROM tban_node__field_inv_game_name WHERE field_inv_game_name_target_id = $knid")->fetchAll();

        if (!empty($game_inv_query)) {
          $game_inv_node = Node::load($game_inv_query[0]->entity_id);
          $previous_qty  = $game_inv_node->get('field_total_inventory')->value;
          $total_qty     = ($previous_qty) - ($kvalue);
          $game_inv_node->set('field_total_inventory', $total_qty);

          // revision
          $user_id = \Drupal::currentUser()->id();
          $game_inv_node->setNewRevision(TRUE);
          $game_inv_node->revision_log = '-'.abs($kvalue).' Requested by '.$pc_name.' ( RID: '.$rid.')';
          $game_inv_node->setRevisionCreationTime(REQUEST_TIME);
          $game_inv_node->setRevisionUserId($user_id);

          $game_inv_node->save();
        }
      }
    }

    $game_request->save();

    foreach ($submitted_store_values as $v) {
      $tempstore->delete($v);
    }

    $tempstore->delete("sel_key");

    drupal_set_message('Game Request (RID:' . $rid . ') has been updated successfully.', 'status', TRUE);
    $response = new RedirectResponse('/pending-game-requests');
    $response->send();
  }
}
