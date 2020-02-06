<?php

namespace Drupal\tb_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;




/**
 * Provides a Welcome User block.
 *
 * @Block(
 *   id = "dispatched_request_custom_block",
 *   admin_label = @Translation("Dispatched Request Custom Block"),
 * )
 */
class DispatchedRequestBlock extends BlockBase {

  /**
   * {@inheritdoc}
  */


  public function build() {
    $content = $fname = '';
    $user_roles = \Drupal::currentUser()->getRoles();
    $user       = User::load(\Drupal::currentUser()->id());
    $uid        = $user->id();
    if (in_array('administrator', $user_roles) || in_array('inventory_manager', $user_roles)) {

      $sql = db_query("SELECT npc.entity_id as rid,CONCAT('(',co.field_playc_value,') ',nd.title) AS play_center , dr.field_gr_date_of_request_value as date_request,
                        CONCAT(fn.field_first_name_value,' ',ln.field_last_name_value) as requested_by , SUM(rqt.field_req_game_quantity_value) as req_qty, pk.field_gr_date_of_packed_value as packed_date
                        FROM `tban_node__field_play_center` AS npc
                        LEFT JOIN tban_node_field_data AS nd ON npc.field_play_center_target_id = nd.nid
                        LEFT JOIN tban_node__field_playc AS co ON co.entity_id = nd.nid
                        LEFT JOIN tban_node__field_gr_date_of_request as dr ON dr.entity_id = npc.entity_id
                        LEFT JOIN tban_node__field_gr_requested_by AS rq ON rq.entity_id = dr.entity_id
                        LEFT JOIN tban_user__field_first_name as fn ON fn.entity_id = rq.field_gr_requested_by_target_id
                        LEFT JOIN tban_user__field_last_name as ln ON ln.entity_id = rq.field_gr_requested_by_target_id
                        LEFT JOIN tban_node__field_game_request_status as rs ON rs.entity_id = dr.entity_id
                        LEFT JOIN tban_node__field_game_request_quantity as gqty ON gqty.entity_id = dr.entity_id
                        LEFT JOIN tban_field_collection_item__field_req_game_quantity AS rqt ON rqt.entity_id = gqty.field_game_request_quantity_value
                        LEFT JOIN tban_node__field_fo_status as fo ON fo.entity_id = dr.entity_id
                        LEFT JOIN tban_node__field_gr_date_of_packed AS pk ON pk.entity_id = dr.entity_id
                          WHERE rs.field_game_request_status_value = 'packed' AND (fo.field_fo_status_value = 'approved')
                          GROUP BY CONCAT('(',co.field_playc_value,') ',nd.title), dr.field_gr_date_of_request_value,CONCAT(fn.field_first_name_value,'',ln.field_last_name_value) ,
                          npc.entity_id,pk.field_gr_date_of_packed_value
                          ORDER BY dr.field_gr_date_of_request_value ASC LIMIT 5")->fetchAll();


      if (!empty($sql)) {
        $header = $rows = [];

        $header = ['RID', 'Play Center', 'Date of Request', 'Requested by (PO/WM)', 'Packed Quantity', 'Age (Days)','Action'];

        foreach ($sql as $k=>$val) {
          $reqdate = date("Y-m-d", $val->packed_date);
          $request_date = date("d/m/Y", $val->packed_date);
          $current_date = date("Y-m-d");
          $diff = strtotime($current_date) - strtotime($reqdate);
          $days = abs(round($diff / 86400));

          $action_url = new FormattableMarkup('<a href=":link">@name</a>', [':link' => '/packed-request-details/' . $val->rid, '@name' => 'View']);

          //~ $rows[] = [$val->rid, $val->play_center, $request_date, $val->requested_by, $val->req_qty, $days,$action_url];
              $rows[] = array(
                          array('data' => $val->rid),
                          array('data' => $val->play_center),
                          array('data' => $request_date),
                          array('data' => $val->requested_by ),
                          array('data' => $val->req_qty, 'class' => 'view-req-qty'),
                          array('data' => $days, 'class' => 'view-req-qty'),
                          array('data' => $action_url, 'class' => 'views-align-center'),
                        );
        }
        $variable['dispatched_request'] = array(
                '#type'    => 'table',
                //~ '#caption' => 'Latest Dispatched Requests',
                '#header'  => $header,
                '#rows'    => $rows,
        );
      }
       return array($variable);
    }
  }


  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    if (in_array('inventory_manager', $account->getRoles()) || in_array('administrator', $account->getRoles()))  {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden('');
    }
  }
}
