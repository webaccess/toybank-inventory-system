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
 * Implements TbHelp Controller.
 */
class TbHelpController extends ControllerBase {


  /**
   * Returns help section
   */
  public function getHelpSection() {
		$content = '';
		$content .= "hello";



		$element = [
        '#type'   => 'markup',
        '#markup' => $content,
        '#attached' => [
          'library' => [
            'tb_custom/toybank_customJS',
          ],
        ],
      ];

      return $element;
	}

}
