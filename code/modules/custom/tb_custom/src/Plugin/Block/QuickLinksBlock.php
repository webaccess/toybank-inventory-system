<?php

namespace Drupal\tb_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a quick links block.
 *
 * @Block(
 *   id = "quick_links_block",
 *   admin_label = @Translation("Quick Links"),
 * )
 */
class QuickLinksBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content = '';

    return [
      '#markup' => $content,
      '#cache'  => ['max-age' => 0],
    ];
  }

}
