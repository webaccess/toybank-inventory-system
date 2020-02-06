<?php

namespace Drupal\tb_custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a Welfare Manager manual block.
 *
 * @Block(
 *   id = "manual_welfare_manager_block",
 *   admin_label = @Translation("Manual WelfareManager"),
 * )
 */
class ManualWelfareManagerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content  = '';
    $content .= '<div class="help-section">
                  <div class="vertical-tab" role="tabpanel">
                    <!-- Nav tabs -->
                    <div class="nav nav-tabs left-help-menu" role="tablist">
                      <div class="sidebar-header">
                        <h3><a href="/user-manual"><i class="glyphicon glyphicon-question-sign" aria-hidden="true"></i>  Need Help</a></h3>
                      </div>';
    $content .= '<span data-toggle="tab" href="#verify-new-games-wrapper">
                   <a href="#verify-new-games-wrapper" data-toggle="collapse"  class="dropdown-toggle help-toggle-active">Verify New Games</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#generate-request-wrapper">
                  <a href="#generate-request-wrapper" data-toggle="collapse"  class="dropdown-toggle">Generate Request</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#game-request-wrapper">
                   <a href="#game-request-wrapper" data-toggle="collapse"  class="dropdown-toggle">Game Request</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#activity-listing-wrapper">
                   <a href="#activity-listing-wrapper" data-toggle="collapse"  class="dropdown-toggle">Activity Listing</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '</div>';
    $content .= '<div class="tab-content tabs righ-help-content">';

    // Verify New Games.
    $content .= '<div role="tabpanel" class="tab-pane fade in active" id="verify-new-games-wrapper">';
    $content .= "<div class='manual-header'><h3>Verify New Games</h3></div>";

    // Verify Game.
    $content .= "<div class='manual-sub-header'><strong>Verify Game</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To verify the requested game first, navigate to the <a href='/verify-new-games'>verify new game</a> listing page as shown below.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#verify_game_list' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/verify_game_list.png' height='200px' width='400px'>
                 </a>";
    $content .= " <div class='image-popup'>
                    <div id='verify_game_list'><img src='/sites/default/files/manual_welfare/verify_game_list.png'></div>
                  </div>";

    $content .= "<ul><li><p>To verify any game, click on the ‘Check’ link against the game, it will be redirected to the game edit page.</p></li></ul>";

    $content .= "<a data-colorbox-inline='#verify_game' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/verify_game.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='verify_game'><img src='/sites/default/files/manual_welfare/verify_game.png'></div>
                 </div>";
    $content .= "<ul><li><p>Make changes to the above game edit form and select the game status as ‘Active’, it will be redirected to the verify new game listing page with a status verified.</p></li></ul>";
    $content .= "</div></div>";

    // Generate Request.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="generate-request-wrapper">';
    $content .= "<div class='manual-header'><h3>Generate Request</h3></div>";

    // Generate Game Request.
    $content .= "<div class='manual-sub-header'><strong>Generate Game Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To generate the game request first, navigate to the <a href='/generate_request'>Game request</a> Page and Select the Playcenter and click on the ‘Show’ button.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#generate_request' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/generate_request.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='generate_request'><img src='/sites/default/files/manual_welfare/generate_request.png'></div>
                 </div>";
    $content .= "<ul>
                    <li><p>Click on the ‘Generate Requests from Available’ button, then click on the ‘Select Games’ button, enter the requested quantity and click on ‘Generate Request’.</p></li>
                    <li><p>On the successful generation of requests, it will be redirected to the ‘Manage Pending Request’ listing page.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#generate_req_redirect' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/generate_req_redirect.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='generate_req_redirect'><img src='/sites/default/files/manual_welfare/generate_req_redirect.png'></div>
                 </div>";
    $content .= "</div></div>";

    // Game Request.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="game-request-wrapper">';
    $content .= "<div class='manual-header'><h3>Game Request</h3></div>";

    // Manage Pending Request.
    $content .= "<div class='manual-sub-header'><strong>Manage Pending Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To view the pending game request navigate to the <a href='/pending-game-requests'>Pending Game Requests</a>.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#manage_pending_req' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/manage_pending_req.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='manage_pending_req'><img src='/sites/default/files/manual_welfare/manage_pending_req.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Click on the ‘view’ link against the play center for which status is ‘Waiting for Approval’, you will get the details of the requested game.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#pending_req_details' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_welfare/pending_req_details.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='pending_req_details'><img src='/sites/default/files/manual_welfare/pending_req_details.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Changed the status of the game either ‘Approved’ or ‘Denied’ and click on the ‘save’ button, it will be redirected to the pending game request listing page.</p></li>
                 </ul>";
    $content .= "</div>";

    // Manage Dispatched Request.
    $content .= "<div class='manual-sub-header'><strong>Manage Dispatched Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>When the requested game is approved and the game is Packed &  ‘Mark as Dispatched’ by ‘Inventory Manager’.</p></li>
                  <li><p>After the game is marked as dispatched, it will be visible to the <a href='/dispatched-game-requests'>Managed Dispatched Request</a> list.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#manage_dispatch_req' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/manage_dispatch_req.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='manage_dispatch_req'><img src='/sites/default/files/manual_welfare/manage_dispatch_req.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>To view the dispatched game details, click on the ‘view’ link against the above play center.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#dispatch_req_details' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/dispatch_req_details.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='dispatch_req_details'><img src='/sites/default/files/manual_welfare/dispatch_req_details.png'></div>
                 </div>";
    $content .= "</div>";

    // Manage Delivered Request.
    $content .= "<div class='manual-sub-header'><strong>Manage Delivered Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To view the delivered request, navigate to the <a href='/delivered-game-requests'>Manage Delivered Request</a>  listing page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#manage_deliver_req' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/manage_deliver_req.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='manage_deliver_req'><img src='/sites/default/files/manual_welfare/manage_deliver_req.png'></div>
                 </div>";
    $content .= "<ul><li><p>To view, the details of the delivered game request click on the ‘View’ link against the play center, as shown above, you will get the below details.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#deliver_req_details' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/deliver_req_details.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                    <div id='deliver_req_details'><img src='/sites/default/files/manual_welfare/deliver_req_details.png'></div>
                  </div>";
    $content .= "</div>";
    $content .= "</div>";

    // Activity Listing.
    $content .= "<div role='tabpanel' class='tab-pane fade' id='activity-listing-wrapper'>";
    $content .= "<div class='manual-header'><h3>Activity Listing</h3></div>";

    // Edit Activity.
    $content .= "<div class='manual-sub-header'><strong>Edit Activity</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To update the activity added by ‘Welfare Manager’, navigate to the <a href='/activity-listing'>Activity Listing</a> page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#activity_listing' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/activity_listing.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='activity_listing'><img src='/sites/default/files/manual_welfare/activity_listing.png'></div>
                 </div>";
    $content .= "<ul><li><p>Click on the ‘Edit’ link against the activity.You will be redirected to the edit activity page.</p></li></ul>";
    $content .= "<a data-colorbox-inline='#edit_activity' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_welfare/edit_activity.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_activity'><img src='/sites/default/files/manual_welfare/edit_activity.png'></div>
                 </div>";
    $content .= "<ul><li><p>Make changes to the above form and click on the ‘Save’ button, you will be redirected to the activity listing page.</p></li></ul>";
    $content .= "</div></div></div></div></div></div>";

    return [
      "#markup" => $content,
      "#cache"  => ["max-age" => 0],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    if (in_array("welfare_manager", $account->getRoles())) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden('');
    }
  }

}
