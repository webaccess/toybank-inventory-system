<?php

namespace Drupal\tb_custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a Inventory Manager manual block.
 *
 * @Block(
 *   id = "manual_inventory_manager_block",
 *   admin_label = @Translation("Manual InventoryManager"),
 * )
 */
class ManualInventoryManagerBlock extends BlockBase {

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
    $content .= '<span data-toggle="tab" href="#manage-inventory-wrapper">
                   <a href="#manage-inventory-wrapper" data-toggle="collapse"  class="dropdown-toggle help-toggle-active">Manage Inventory</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#inventory-audit-wrapper">
                   <a href="#inventory-audit-wrapper" data-toggle="collapse"  class="dropdown-toggle">Inventory Audit</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#game-requests-wrapper">
                   <a href="#game-requests-wrapper" data-toggle="collapse"  class="dropdown-toggle">Game Request</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '</div>';
    $content .= '<div class="tab-content tabs righ-help-content">';

    // Manage Inventory.
    $content .= '<div role="tabpanel" class="tab-pane fade in active" id="manage-inventory-wrapper">';
    $content .= "<div class='manual-header'><h3>Manage Inventory</h3></div>";
    // Add Game.
    $content .= "<div class='manual-sub-header'><strong>Add Game</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To add a new game first, navigate to the <a href='/inventory-listing'>Inventory</a> Listing Page and click on the 'Add Game’ button.</p></li>
                  <li><p>Enter the Game Name, Manufacturer, Select Category & Subcategory, etc. Then click on the ‘Save’ button.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#add_game' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/add_game.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                    <div id='add_game'><img src='/sites/default/files/manual_inventory/add_game.png'></div>
                  </div>";
    $content .= "<ul><li>After the game has been saved, it will be redirected to the Inventory Management listing page.</ul></li>";
    $content .= "<a data-colorbox-inline='#inventory_listing' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/inventory_listing.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                    <div id='inventory_listing'><img src='/sites/default/files/manual_inventory/inventory_listing.png'></div>
                  </div>";
    $content .= "</div>";

    // Edit Game.
    $content .= "<div class='manual-sub-header'><strong>Edit Game</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To update an existing game first, navigate to the <a href='/inventory-listing'>Inventory</a> Listing Page as shown above.</p></li>
                  <li><p>Click on the ‘Edit Game’ link of the respective game name, and will be redirected to the game edit page.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#edit_game' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/edit_game.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                    <div id='edit_game'><img src='/sites/default/files/manual_inventory/edit_game.png'></div>
                  </div>";
    $content .= "<ul><li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the Inventory Management listing page.</p></ul></li>";
    $content .= "</div>";

    // Add Inventory.
    $content .= "<div class='manual-sub-header'><strong>Add Inventory</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To add a game in the inventory first, navigate to the <a href='/inventory-listing'>Inventory</a> Listing Page and click on the 'Add Inventory’ button.</p></li>
                  <li><p>Enter the Source/Pickup, Select Donated/Purchased, Add new ‘Game’ OR Select ‘existing game’ and enter the Quantity. Then click on the ‘Save’ button.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#add_inventory' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_inventory/add_inventory.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='add_inventory'><img src='/sites/default/files/manual_inventory/add_inventory.png'></div>
                 </div>";
    $content .= "<ul><li><p>After the inventory has been saved, it will be redirected to the Inventory Management listing page.</p></ul></li>";
    $content .= "<a data-colorbox-inline='#inventory_listings' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/inventory_listing.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='inventory_listings'><img src='/sites/default/files/manual_inventory/inventory_listing.png'></div>
                 </div>";
    $content .= "</div>";

    // Edit Inventory.
    $content .= "<div class='manual-sub-header'><strong>Edit Inventory</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To update an existing inventory first, navigate to the <a href='/inventory-listing'>Inventory</a> Listing Page.</p></li>
                  <li><p>Click on the ‘Edit Inventory’ link of the respective game name, and will be redirected to the inventory details page.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#inventory_details' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/inventory_details.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                    <div id='inventory_details'><img src='/sites/default/files/manual_inventory/inventory_details.png'></div>
                  </div>";
    $content .= "<ul><li><p>Click on the edit links against the inventory details, you will be redirected to the inventory edit page.</p></li></ul>";
    $content .= "<a data-colorbox-inline='#inventory_edit' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/inventory_edit.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='inventory_edit'><img src='/sites/default/files/manual_inventory/inventory_edit.png'></div>
                 </div>";
    $content .= "<ul><li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the Inventory Management listing page.</p></ul></li>";
    $content .= "</div>";
    $content .= '</div>';

    // Inventory Audit.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="inventory-audit-wrapper">';
    $content .= "<div class='manual-header'><h3>Inventory Audit</h3></div>";
    $content .= "<div class='manual-sub-header'><strong>Report Game Issue</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To raise an issue for the existing game first, navigate to the <a href='/current-inventory-status'>Inventory Audit</a> listing page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#inventory_audit' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/inventory_audit.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='inventory_audit'><img src='/sites/default/files/manual_inventory/inventory_audit.png'></div>
                 </div>";
    $content .= "<ul><li><p>Click on the game category list for which, we want to create an issue.</p></li>
                  <li><p>Select an issue in Mark Issue column and click on ‘submit’ button, you will be redirected to the game issue report.</p></li></ul>";
    $content .= "<a data-colorbox-inline='#game_issue_report' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/game_issue_report.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='game_issue_report'><img src='/sites/default/files/manual_inventory/game_issue_report.png'></div>
                 </div>";
    $content .= "</div>";
    $content .= '</div>';

    // Manage Game Requests.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="game-requests-wrapper">';
    $content .= "<div class='manual-header'><h3>Game Request</h3></div>";
    // Manage Pending Request.
    $content .= "<div class='manual-sub-header'><strong>Manage Pending Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To view the pending game request by ‘Field Officer’ & ‘Welfare Manager’ navigate to <a href='/pending-game-requests'>Pending Game Requests</a> </p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#manage_pending_request' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/manage_pending_request.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                    <div id='manage_pending_request'><img src='/sites/default/files/manual_inventory/manage_pending_request.png'></div>
                  </div>";
    $content .= "<ul><li><p>To view, the pending requested game details click on the ‘view’ link against the play center. You will get the below details.</p></li>
                  <li><p>Pending game requests, with ‘approved’ status, can be packed by clicking on the button ‘Request Complete- Mark as Packed’.</p></li></ul>";

    $content .= "<div><strong>Note</strong> Pending game request are approved by the ‘Welfare Manager’.</div>";

    $content .= "<a data-colorbox-inline='#pending_request_details' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/pending_request_details.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='pending_request_details'><img src='/sites/default/files/manual_inventory/pending_request_details.png'></div>
                 </div>";
    $content .= "<ul><li><p>After clicking on ‘Mark as Packed’, you will be redirected to the <a href='/packed-game-requests'>Manage Packed Requests</a> listing page.</p></li></ul>";
    $content .= "</div>";

    // Manage Packed Request.
    $content .= "<div class='manual-sub-header'><strong>Manage Packed Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To view the packed requested game details by ‘Field Officer’ & ‘Welfare Manager’ navigate to <a href='/packed-game-requests'>Packed Game Requests</a>.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#manage_packed_request' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/manage_packed_request.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='manage_packed_request'><img src='/sites/default/files/manual_inventory/manage_packed_request.png'></div>
                 </div>";
    $content .= "<ul><li><p>To view, the packed requested game details click on the ‘view’ link against the play center. You will get the below details.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#packed_req_details' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/packed_req_details.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='packed_req_details'><img src='/sites/default/files/manual_inventory/packed_req_details.png'></div>
                 </div>";
    $content .= "<ul><li><p>To dispatch the game click on the button ‘Mark as Dispatched’, you will be redirected to the Dispatched game request listing page.</p></li></ul>";
    $content .= "</div>";

    // Manage Dispatched Request.
    $content .= "<div class='manual-sub-header'><strong>Manage Dispatched Request</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                  <li><p>To view the dispatched game request by ‘Field Officer’ & ‘Welfare Manager’ navigate to <a href='/dispatched-game-requests'>Dispatched Game Requests</a>.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#manage_dispatch_req' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/manage_dispatch_req.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='manage_dispatch_req'><img src='/sites/default/files/manual_inventory/manage_dispatch_req.png'></div>
                 </div>";
    $content .= "<ul><li><p>To view the dispatched game details, click on the ‘view’ link against the play center. You will get the below details.</p></li>
                  </ul>";
    $content .= "<a data-colorbox-inline='#dispatch_req_details' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_inventory/dispatch_req_details.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='dispatch_req_details'><img src='/sites/default/files/manual_inventory/dispatch_req_details.png'></div>
                 </div>";
    $content .= "</div></div></div></div></div>";

    return [
      '#markup' => $content,
      '#cache'  => ['max-age' => 0],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    if (in_array('inventory_manager', $account->getRoles())) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden('');
    }
  }

}
