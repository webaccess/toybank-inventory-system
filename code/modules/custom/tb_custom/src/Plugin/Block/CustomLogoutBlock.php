<?php

namespace Drupal\tb_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Custom logout block.
 *
 * @Block(
 *   id = "custom_logout_block",
 *   admin_label = @Translation("Custom Logout"),
 * )
 */
class CustomLogoutBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => $this->t('<div class="logout-section"><a href="/user/logout"><span class="glyphicon glyphicon-log-out"></span></a></div>'),
      '#cache'  => ['max-age' => 0],
    ];
  }

}
