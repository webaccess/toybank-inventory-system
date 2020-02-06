<?php

namespace Drupal\tb_custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a Data Admin manual block.
 *
 * @Block(
 *   id = "manual_data_admin_block",
 *   admin_label = @Translation("Manual DataAdmin"),
 * )
 */
class ManualDataAdminBlock extends BlockBase {

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
    $content .= '<span data-toggle="tab" href="#manage-users-wrapper">
                   <a href="#manage-users-wrapper" data-toggle="collapse"  class="dropdown-toggle help-toggle-active">Manage Users</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#manage-playcenter-wrapper">
                   <a href="#manage-playcenter-wrapper" data-toggle="collapse"  class="dropdown-toggle">Manage PlayCenter</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#update-ideal-invent-wrapper">
                   <a href="#update-ideal-invent-wrapper" data-toggle="collapse"  class="dropdown-toggle">Update Ideal Inventory</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#manage-kbi-wrapper">
                   <a href="#manage-kbi-wrapper" data-toggle="collapse"  class="dropdown-toggle">Manage KBI</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#manage-partners-wrapper">
                   <a href="#manage-partners-wrapper" data-toggle="collapse"  class="dropdown-toggle">Manage Partners</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#manage-source-pick-wrapper">
                   <a href="#manage-source-pick-wrapper" data-toggle="collapse"  class="dropdown-toggle">Manage Source Pickup</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#manage-clusters-wrapper">
                   <a href="#manage-clusters-wrapper" data-toggle="collapse"  class="dropdown-toggle">Manage Clusters</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#manage-all-master-wrapper">
                   <a href="#manage-all-master-wrapper" data-toggle="collapse"  class="dropdown-toggle">All Master</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '<span data-toggle="tab" href="#manage-activity-wrapper">
                   <a href="#manage-activity-wrapper" data-toggle="collapse"  class="dropdown-toggle">Activity Listing</a>
                 </span>';
    $content .= '<div class="divider"></div>';
    $content .= '</div>';
    $content .= '<div class="tab-content tabs righ-help-content">';

    // Manage Users.
    $content .= '<div role="tabpanel" class="tab-pane fade in active" id="manage-users-wrapper">';
    $content .= "<div class='manual-header'><h3>Manage Users</h3></div>";
    // Add User.
    $content .= "<div class='manual-sub-header'><strong>Add User</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To add a new user first, navigate to the <a href='/manage-users'>User Listing</a>  Page and click the 'Add User’ button.</p></li>
                   <li><p>Enter the name (required field), Email address, Username, Password, etc. Then check the roles as per the requirements and click on the ‘Create new account’ button.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#add_user' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_admin/add-user.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='add_user'><img src='/sites/default/files/manual_admin/add-user.png' height='750px' width='1000px'></div>
                 </div>";
    $content .= "<ul><li>After the user has been created, the user will be redirected to the user listing page.</ul></li>";
    $content .= "<a data-colorbox-inline='#user_list' data-width='1050px' data-height='800px' class='cboxElement'>
                  <img src='/sites/default/files/manual_admin/user-listing.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='user_list'><img src='/sites/default/files/manual_admin/user-listing.png'></div>
                 </div>";
    $content .= "</div>";
    // Edit User.
    $content .= "<div class='manual-sub-header'><strong>Edit User</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                <li><p>To update an existing user first, navigate to <a href='/manage-users'>User Listing</a> Page as shown above.</p></li>
                <li><p>Click on the edit link of the respective user name, and will be redirected to the user edit page.</p></li>
                </ul>";
    $content .= "<a data-colorbox-inline='#edit_user' data-width='1050px' data-height='800px'class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/edit-user.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_user'><img src='/sites/default/files/manual_admin/edit-user.png'></div>
                 </div>";
    $content .= "<ul><li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the user listing page.</p></li></ul>";
    $content .= "</div>";
    $content .= '</div>';

    // Manage Play Centers.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="manage-playcenter-wrapper">';
    $content .= "<div class='manual-header'><h3>Manage Play Centers</h3></div>";
    // Add Play Center.
    $content .= "<div class='manual-sub-header'><strong>Add Play Center</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To add a new play center, navigate to the <a href='/playcenter'>Play Center</a> Listing Page and click the 'Add Play Center' button.</p></li>
                   <li><p>Enter the Name Of Center, Play Center Code, Address, Status, etc. Then enter the no of kids by selecting its category, enter Associated Partner and Clusters. Then click on the ‘Save’ button.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#add_playcenter' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/add-playcenter.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='add_playcenter'><img src='/sites/default/files/manual_admin/add-playcenter.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>After the play center has been created, it will be redirected to the ideal inventory edit page.</p></li>
                   <li><p>Make changes to the game category if needed and click on the 'Save' button.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#ideal_inventory' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/ideal-inventory.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='ideal_inventory'><img src='/sites/default/files/manual_admin/ideal-inventory.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>After saving the ideal inventory, it will be redirected to the play center listing page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#playcenter_list' data-width='1050px' data-height='800px' class='cboxElement'>
                    <img src='/sites/default/files/manual_admin/playcenter-list.png' height='200px' width='400px'>
                   </a>";
    $content .= "<div class='image-popup'>
                   <div id='playcenter_list'><img src='/sites/default/files/manual_admin/playcenter-list.png'></div>
                 </div>";
    $content .= "</div>";

    // Edit Play Center.
    $content .= "<div class='manual-sub-header'><strong>Edit Play Center</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To update an existing Play Center first, navigate to the <a href='/playcenter'>Play Center</a> Listing Page as shown above.</p></li>
                   <li><p>Click on the edit link of the respective ‘Play Center’ name, and will be redirected to the ‘Play Center’ edit page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#edit_playcenter' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/edit-playcenter.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_playcenter'><img src='/sites/default/files/manual_admin/edit-playcenter.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the play center listing page.</p></li>
                 </ul>";
    $content .= "</div>";
    $content .= '</div>';

    // Update Ideal Inventory.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="update-ideal-invent-wrapper">';
    $content .= "<div class='manual-header'><h3>Update Ideal Inventory</h3></div>";
    $content .= "<div class='manual-sub-header'></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To update an existing Ideal Inventory, navigate to the <a href='/playcenter'>Play Center</a> Listing Page as shown above.</p></li>
                   <li><p>Click on the ‘Update Ideal Inventory’ link of the respective ‘Play Center’ name, and will be redirected to the ‘Update Ideal Inventory’ edit page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#up_ideal_inventory' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/ideal-inventory.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='up_ideal_inventory'><img src='/sites/default/files/manual_admin/ideal-inventory.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Make changes to the above edit form and click on ‘Save’  button, it will redirect to the play center listing page.</p></li>
                 </ul>";
    $content .= "</div>";
    $content .= '</div>';

    // Manage KBI.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="manage-kbi-wrapper">';
    $content .= "<div class='manual-header'><h3>Manage KBI's</h3></div>";
    // Add KBI.
    $content .= "<div class='manual-sub-header'><strong>Add KBI</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To add a new KBI, navigate to the <a href='/kbi>KBI</a> Listing Page and click the 'Add KBI’ button.</p></li>
                   <li><p>Enter the KBI name, KBI Description and select the KBI Category. Then click on the ‘Save’ button.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#add_kbi' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/add-kbi.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='add_kbi'><img src='/sites/default/files/manual_admin/add-kbi.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>After the KBI has been saved, it will be redirected to the KBI listing page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#kbi_listing' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/kbi-listing.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='kbi_listing'><img src='/sites/default/files/manual_admin/kbi-listing.png'></div>
                 </div>";
    $content .= "</div>";

    // Edit KBI.
    $content .= "<div class='manual-sub-header'><strong>Edit KBI</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To update an existing KBI first, navigate to the <a href='/kbi'>KBI</a> Listing Page as shown above.</p></li>
                   <li><p>Click on the edit link of the respective ‘KBI’ name, and will be redirected to the ‘KBI’ edit page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#edit_kbi' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/edit-kbi.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_kbi'><img src='/sites/default/files/manual_admin/edit-kbi.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the KBI listing page.</p></li>
                 </ul>";
    $content .= "</div>";
    $content .= '</div>';

    // Manage Partners.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="manage-partners-wrapper">';
    $content .= "<div class='manual-header'><h3>Manage Partners</h3></div>";
    // Add Partner:
    $content .= "<div class='manual-sub-header'><strong>Add Partner</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To add a new Partner, navigate to the <a href='/partner'>Partner</a> Listing Page and click the 'Add Partner’ button.</p></li>
                   <li><p>Enter the Partner name, Partner Code, Partner Contact Details, etc. Then click on the ‘Save’ button.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#add_partner' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/add-partner.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='add_partner'><img src='/sites/default/files/manual_admin/add-partner.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>After the Partner has been saved, it will be redirected to the Partner listing page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#partner_listing' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/partner-listing.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='partner_listing'><img src='/sites/default/files/manual_admin/partner-listing.png'></div>
                 </div>";
    $content .= "</div>";

    // Edit Partner.
    $content .= "<div class='manual-sub-header'><strong>Edit Partner</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To update an existing Partner first, navigate to the <a href='/partner'>Partner</a> Listing Page as shown above.</p></li>
                   <li><p>Click on the edit link of the respective ‘Partner’ name, and will be redirected to the ‘Partner’ edit page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#edit_partner' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/edit-partner.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_partner'><img src='/sites/default/files/manual_admin/edit-partner.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the ‘Partner’ listing page.</p></li>
                 </ul>";
    $content .= "</div>";
    $content .= '</div>';

    // Manage Source Pickup.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="manage-source-pick-wrapper">';
    $content .= "<div class='manual-header'><h3>Manage Source/Pickups Location</h3></div>";
    // Add Source/Pickup.
    $content .= "<div class='manual-sub-header'><strong>Add Source/Pickup</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To add a new Source/Pickup first,  navigate to the <a href='/source-pickup'>Source/Pickup</a> Listing Page and click the 'Add Source/Pickup’ button.</p></li>
                   <li><p>Select the Source Type, Date Of Pickup, Enter Name, Select Status & Quality, Enter the address. Then click on the ‘Save’ button.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#add_source_pick' data-width='1050px' data-height='800px' class='cboxElement'>
                    <img src='/sites/default/files/manual_admin/add-source-pick.png' height='200px' width='400px'>
                   </a>";
    $content .= "<div class='image-popup'>
                   <div id='add_source_pick'><img src='/sites/default/files/manual_admin/add-source-pick.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>After the Source/Pickup has been saved, it will be redirected to the Source/Pickup listing page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#source_pick_list' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/source-pick-list.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='source_pick_list'><img src='/sites/default/files/manual_admin/source-pick-list.png'></div>
                 </div>";
    $content .= "</div>";

    // Edit Source/Pickup.
    $content .= "<div class='manual-sub-header'><strong>Edit Source/Pickup</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To update an existing Source/Pickup first, navigate to <a href='/source-pickup'>Source/Pickup</a> Listing Page as shown above.</p></li>
                   <li><p>Click on the edit link of the respective ‘Source/Pickup’ name, and will be redirected to the ‘Source/Pickup’ edit page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#edit_source_pickup' data-width='1050px' data-height='800px' class='cboxElement'>
                    <img src='/sites/default/files/manual_admin/edit-source-pickup.png' height='200px' width='400px'>
                   </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_source_pickup'><img src='/sites/default/files/manual_admin/edit-source-pickup.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the ‘Source/Pickup’ listing page.</p></li>
                 </ul>";
    $content .= "</div>";
    $content .= '</div>';

    // Manage Clusters.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="manage-clusters-wrapper">';
    $content .= "<div class='manual-header'><h3>Manage Clusters</h3></div>";
    // Add Cluster.
    $content .= "<div class='manual-sub-header'><strong>Add Cluster</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To add a new Cluster, navigate to the <a href='/cluster'>Cluster</a> Listing Page and click the 'Add Cluster’ button.</p></li>
                   <li><p>Enter the Name, Associated field officer. Then click on the ‘Save’ button.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#add_cluster' data-width='1050px' data-height='800px' class='cboxElement'>
                    <img src='/sites/default/files/manual_admin/add-cluster.png' height='200px' width='400px'>
                   </a>";
    $content .= "<div class='image-popup'>
                   <div id='add_cluster'><img src='/sites/default/files/manual_admin/add-cluster.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>After the Cluster has been saved, it will be redirected to the Cluster listing page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#cluster_listing' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/cluster-listing.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='cluster_listing'><img src='/sites/default/files/manual_admin/cluster-listing.png'></div>
                 </div>";
    $content .= "</div>";

    // Edit Cluster.
    $content .= "<div class='manual-sub-header'><strong>Edit Cluster</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To update an existing Cluster first, navigate to <a href='/cluster'>Cluster</a> Listing Page as shown above.</p></li>
                   <li><p>Click on the edit link of the respective ‘Cluster’ name, and will be redirected to the ‘Cluster’ edit page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#edit_cluster' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/edit-cluster.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_cluster'><img src='/sites/default/files/manual_admin/edit-cluster.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the ‘Cluster’ listing page.</p></li>
                 </ul>";
    $content .= "</div>";
    $content .= '</div>';

    // All Master.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="manage-all-master-wrapper">';
    $content .= "<div class='manual-header'><h3>All Master</h3></div>";
    // Add KBI Category.
    $content .= "<div class='manual-sub-header'><strong>Add KBI Category</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To add a new KBI Category, navigate to the <a href='/kbi_category'>KBI</a> Category Listing Page and click the 'Add KBI Category’ button.</p></li>
                   <li><p>Enter the Name and click on the ‘Save’ button.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#add_kbi_catg' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/add-kbi-catg.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='add_kbi_catg'><img src='/sites/default/files/manual_admin/add-kbi-catg.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>After the KBI Category has been saved, it will be redirected to the KBI Category listing page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#kbi_catg_list' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/kbi-catg-list.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='kbi_catg_list'><img src='/sites/default/files/manual_admin/kbi-catg-list.png'></div>
                 </div>";
    $content .= "</div>";

    // Edit KBI Category.
    $content .= "<div class='manual-sub-header'><strong>Edit KBI Category</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>To update an existing KBI Category first, navigate to <a href='/kbi_category'>KBI</a> Category Listing Page as shown above.</p></li>
                   <li><p>Click on the edit link of the respective ‘KBI Category’ name, and will be redirected to the ‘KBI Category’ edit page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#edit_kbi_catg' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/edit-kbi-catg.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_kbi_catg'><img src='/sites/default/files/manual_admin/edit-kbi-catg.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the ‘KBI Category’ listing page.</p></li>
                 </ul>";
    $content .= "</div>";
    $content .= '</div>';

    // Activity Listing.
    $content .= '<div role="tabpanel" class="tab-pane fade" id="manage-activity-wrapper">';
    $content .= "<div class='manual-header'><h3>Activity Listing</h3></div>";
    // Edit Activity Listing.
    $content .= "<div class='manual-sub-header'><strong>Edit Activity Listing</strong></div>";
    $content .= "<div class='manual-content'>";
    $content .= "<ul>
                   <li><p>Activity listing is added by the field officers.</p></li>
                   <li><p>Data Admin can only view or update the activity added by field officers.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#activity_listing' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/activity-listing.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='activity_listing'><img src='/sites/default/files/manual_admin/activity-listing.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>To update an existing activity first, navigate to <a href='/activity-listing'>Activity</a> Listing Page as shown above.</p></li>
                   <li><p>Click on the edit link of the respective ‘Activity’ name, and will be redirected to the ‘Activity’ edit page.</p></li>
                 </ul>";
    $content .= "<a data-colorbox-inline='#edit_activity' data-width='1050px' data-height='800px' class='cboxElement'>
                   <img src='/sites/default/files/manual_admin/edit-activity.png' height='200px' width='400px'>
                 </a>";
    $content .= "<div class='image-popup'>
                   <div id='edit_activity'><img src='/sites/default/files/manual_admin/edit-activity.png'></div>
                 </div>";
    $content .= "<ul>
                   <li><p>Make changes to the above edit form and click on ‘Save’ button, it will redirect to the ‘Activity’ listing page.</p></li>
                 </ul>";
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
    if (in_array('data_entry_admin', $account->getRoles())) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden('');
    }
  }

}
