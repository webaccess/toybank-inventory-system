<?php

namespace Drupal\tb_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides a Welcome User block.
 *
 * @Block(
 *   id = "welcome_user_block",
 *   admin_label = @Translation("Welcome User Block"),
 * )
 */
class WelcomeUserBlock extends BlockBase {

  /**
   * {@inheritdoc}
  */
  public function build() {
    $content = $fname = '';
    $user_roles = \Drupal::currentUser()->getRoles();
    $user       = User::load(\Drupal::currentUser()->id());
    $uid        = $user->id();
    if($uid) {
      $userName = db_query("SELECT field_first_name_value as fname FROM `tban_user__field_first_name`
                            WHERE entity_id = $uid")->fetchAssoc();
      if($userName) {
        $fname = $userName['fname'];
      }
    }

    //~ if (in_array('inventory_manager', $user_roles) || in_array('inventory_executive', $user_roles)) {
      //~ $content = "<div class='wlcm-msg'>Welcome, $fname</div>";
    //~ }

    //~ if (in_array('field_officer', $user_roles)) {
      //~ $content = "<div class='wlcm-msg'>Welcome, Field Officer</div>";
    //~ }

    //~ if (in_array('welfare_manager', $user_roles)) {
      //~ $content = "<div class='wlcm-msg'>Welcome, Welfare Manager</div>";
    //~ }

    //~ if (in_array('data_entry_admin', $user_roles)) {
      //~ $content = "<div class='wlcm-msg'>Welcome, Data Entry Admin</div>";
    //~ }

      $content = "<div class='wlcm-msg'>Welcome, $fname</div>";


    return [
      '#markup' => $content,
      '#cache'  => ['max-age' => 0],
    ];
  }
}
