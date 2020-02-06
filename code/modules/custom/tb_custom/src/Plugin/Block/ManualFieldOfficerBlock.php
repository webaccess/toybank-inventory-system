<?php

namespace Drupal\tb_custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a Field Officer manual block.
 *
 * @Block(
 *   id = "manual_field_officer_block",
 *   admin_label = @Translation("Manual FieldOfficer"),
 * )
 */
class ManualFieldOfficerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content  = '';
    $content .= '<div class="help-section">
                  <div class="vertical-tab " role="tabpanel">
                    <!-- Nav tabs -->
                    <div class="nav nav-tabs left-help-menu" role="tablist">
                      <div class="sidebar-header">
                        <h3><a href="/user-manual"><i class="glyphicon glyphicon-question-sign" aria-hidden="true"></i>  Need Help</a></h3>
                      </div>';
    $content .= '<span data-toggle="tab" href="#generate-request-wrapper">
                   <a href="#generate-request-wrapper" data-toggle="collapse"  class="dropdown-toggle help-toggle-active">Generate Request</a>
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
    $content .= '<span data-toggle="tab" href="#playcenter-followup-wrapper">
                   <a href="#playcenter-followup-wrapper" data-toggle="collapse"  class="dropdown-toggle">PlayCenter Followup</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '</div>';
    $content .= '<div class="tab-content tabs righ-help-content">';

    // Generate Request.
    $content .= '<div role="tabpanel" class="tab-pane fade in active" id="generate-request-wrapper">';
    $content .= "<div class='manual-header'><h3>Generate Request</h3></div>";
    $content .= "<div class='manual-sub-header'><strong>Generate Game Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To generate the game request first, navigate to the <a href='/generate_request'>Game Request</a> Page and Select the Playcenter and click on the ‘Show’ button.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#generate_game_req' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/generate-game-req.png' height='200px' width='400px'>
                 </a>";
    $content .= " <div class='image-popup'>
                    <div id='generate_game_req'><img src='/sites/default/files/manual_fieldofficer/generate-game-req.png'></div>
                  </div>";
    $content .= "<ul><li><p>Click on the ‘Generate Requests from Available’ button, then click on the ‘Select Games’ button, enter the requested quantity and click on ‘Generate Request’.</p></li>
                     <li><p>On the successful generation of requests, it will be redirected to the ‘Manage Pending Request’ listing page.</p></li></ul>";
    $content .= "<a data-colorbox-inline='#pending_req' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_fieldofficer/pending-req.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                    <div id='pending_req'><img src='/sites/default/files/manual_fieldofficer/pending-req.png'></div>
                  </div>";
    $content .= "<p><strong>Note</strong> The pending game request will be approved by the ‘Welfare Manager’.</p>";
    $content .= "</div>";
    $content .= '</div>';

    // Game Request.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="game-request-wrapper">';
    $content .= "<div class='manual-header'><h3>Game Request</h3></div>";
    // Pending Game Request.
    $content .= "<div class='manual-sub-header'><strong>Manage Pending Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To view the pending game request navigate to <a href='/pending-game-requests'>Pending Game Request</a>.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#pending_game_req' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/pending-game-list.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='pending_game_req'><img src='/sites/default/files/manual_fieldofficer/pending-game-list.png'></div>
                 </div>";
    $content .= "<ul><li><p>Click on the ‘view’ link against the play center, you will get the details of the requested game.</p></li>
                  </ul>";

    $content .= "<a data-colorbox-inline='#pending_game_details' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/pending-game-details.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='pending_game_details'><img src='/sites/default/files/manual_fieldofficer/pending-game-details.png'></div>
                 </div>";
    $content .= "</div>";

    // Manage Dispatched Request.
    $content .= "<div class='manual-sub-header'><strong>Manage Dispatched Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>When the requested game is approved by ‘Welfare Manager’, then the game is Packed &  ‘Mark as Dispatched’ by ‘Inventory Manager’.</p></li>
                  <li><p>After the game is marked as dispatched, it will be visible to the ‘Managed Dispatched Request’ list.</p></li></ul>";
    $content .= "<a data-colorbox-inline='#dispatched_game_list' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/dispatch-game-list.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='dispatched_game_list'><img src='/sites/default/files/manual_fieldofficer/dispatch-game-list.png'></div>
                 </div>";
    $content .= "<ul><li><p>To view the dispatched details and ‘Mark as Delivered’, click on the ‘view’ link against the above play center.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#dispatch_game_details' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/dispatch_game_details.png' height='200px' width='400px'>
                 </a>";
    $content .= " <div class='image-popup'>
                    <div id='dispatch_game_details'><img src='/sites/default/files/manual_fieldofficer/dispatch_game_details.png'></div>
                  </div>";
    $content .= "<ul><li><p>To mark as delivered, click on the button ‘Mark as Delivered’, it will be redirected to the delivered game request listing page.</p></li>
                  </ul>";
    $content .= "</div>";

    // Manage Delivered Request.
    $content .= "<div class='manual-sub-header'><strong>Manage Delivered Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To view the delivered request, navigate to the <a href='/delivered-game-requests'>Manage Delivered Request</a> listing page.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#deliver_game_list' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/delivered-game-list.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='deliver_game_list'><img src='/sites/default/files/manual_fieldofficer/delivered-game-list.png'></div>
                 </div>";
    $content .= "<ul><li><p>To view, the details of the delivered game request click on the ‘View’ link against the play center, as shown above, you will get the below details.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#deliver_game_details' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/delivered-game-details.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='deliver_game_details'><img src='/sites/default/files/manual_fieldofficer/delivered-game-details.png'></div>
                 </div>";
    $content .= "</div>";
    $content .= '</div>';

    // Activity Listing.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="activity-listing-wrapper">';
    $content .= "<div class='manual-header'><h3>Activity Listing</h3></div>";
    // Add Sctivity.
    $content .= "<div class='manual-sub-header'><strong>Add Activity</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To add a new Activity first, navigate to the <a href='/activity-listings'>Activity Listing</a> page and click on the ‘Add Activity’ button.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#add_activity' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/add_activity.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='add_activity'><img src='/sites/default/files/manual_fieldofficer/add_activity.png'></div>
                 </div>";
    $content .= "<ul><li><p>Select the Play Center, enter Date & Time, select Activity, enter no of kids attendant, check other attendants, etc. and click on the ‘Save’ button. You will be redirected to the activity listing page.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#activity_listing' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/activity_listing.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='activity_listing'><img src='/sites/default/files/manual_fieldofficer/activity_listing.png'></div>
                 </div>";
    $content .= "</div>";

    // Edit Activity.
    $content .= "<div class='manual-sub-header'><strong>Edit Activity</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To update the existing activity, navigate to the <a href='/activity-listings'>Activity Listing</a> page and click on the ‘Edit’ link against the activity. You will be redirected to the edit activity page.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#edit_activity' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_fieldofficer/edit_activity.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_activity'><img src='/sites/default/files/manual_fieldofficer/edit_activity.png'></div>
                 </div>";
    $content .= "<ul><li><p>Make changes to the above form and click on the ‘Save’ button, you will be redirected to the activity listing page.</p></li>
                  </ul>";
    $content .= "</div>";
    $content .= '</div>';

    // Playcenter Followup.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="playcenter-followup-wrapper">';
    $content .= "<div class='manual-header'><h3>Playcenter Followup</h3></div>";
    // Report Issue.
    $content .= "<div class='manual-sub-header'><strong>Report Issue</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To view the game delivered to the specific play center navigate to the <a href='/playcenter-followup'>Playcenter Followup</a> and search for the play center and click on the ‘Apply’ button.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#playcenter_followup' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/playcenter_followup.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='playcenter_followup'><img src='/sites/default/files/manual_fieldofficer/playcenter_followup.png'></div>
                 </div>";
    $content .= "<ul><li><p>Click on the game category and enter the reported quantity and select the issue for the mark, and click on the ‘Submit’ button.</p></li>
                  <li><p>After submitting the issue, it will be redirected to the <a href='/playcenter-issue-report'>Play Center issue report</a> listing page.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#playcenter_issue_report' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_fieldofficer/playcenter_issue_report.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='playcenter_issue_report'><img src='/sites/default/files/manual_fieldofficer/playcenter_issue_report.png'></div>
                 </div>";
    $content .= "</div>";
    $content .= '</div>';
    $content .= '</div>';
    $content .= '</div></div>';

    return [
      '#markup' => $content,
      '#cache'  => ['max-age' => 0],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    if (in_array('field_officer', $account->getRoles())) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden('');
    }
  }

}
