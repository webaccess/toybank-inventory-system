<?php
namespace Drupal\tb_custom\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;

/**
 * Implements TbHelp Controller.
 */
class TbHelpController extends ControllerBase {

  /**
   * Returns help section
   */
  public function getHelpSection() {

    $user_roles = \Drupal::currentUser()->getRoles();

    if (in_array('super_admin', $user_roles) || in_array('administrator', $user_roles)) {
      $query_where = "WHERE n.type='help_section'";
    }
    else {
      $roles = join("','", \Drupal::currentUser()->getRoles());
      $query_where = "WHERE n.type='help_section' AND r.field_help_role_value IN ('$roles')";
    }


    $menus = $panel = $active = $node_edit = '';

    $tab_query = db_query("SELECT n.nid, n.title, tc.field_help_tab_content_value
                           FROM tban_node_field_data as n
                           LEFT JOIN tban_node__field_help_role as r ON r.entity_id = n.nid
                           LEFT JOIN tban_draggableviews_structure as s ON s.entity_id = n.nid
                           LEFT JOIN tban_node__field_help_tab_content as tc ON tc.entity_id = n.nid
                           $query_where
                           ORDER BY s.weight ASC")->fetchAll();

    if (!empty($tab_query)) {
      foreach ($tab_query as $k => $v) {
        if ($k == 0) {
          $active = 'help-toggle-active';
          $face_active = 'in active';
        }
        else {
          $active = $face_active = '';
        }

        if (in_array('super_admin', $user_roles) || in_array('administrator', $user_roles)) {
          $node_edit = '<div class="help-edit"><a href="/node/' . $v->nid . '/edit">Edit</a></div>';
        }
        else {
          $node_edit = '';
        }

        $wrapper = str_replace(" ", "-", strtolower($v->title)) . '-wrapper';
        $wrapper = str_replace("/", "-",$wrapper) . "-" . $v->nid;
        $menus .= '<span data-toggle="tab" href="#' . $wrapper . '">
                     <a href="#' . $wrapper . '" data-toggle="collapse" class="dropdown-toggle ' . $active . '">' . $v->title . '</a>
                   </span>
                   <div class="divider"></div>';

        $panel .= '<div role="tabpanel" class="tab-pane fade ' . $face_active . '" id="' . $wrapper . '">
                     ' . $node_edit . '
                     <div class="manual-header"><h3>' . $v->title . '</h3></div>
                     ' . $v->field_help_tab_content_value . '
                   </div>';
      }
    }

    $content  = '';
    $content .= '<div class="help-section" >
                   <div class="vertical-tab " role="tabpanel">
                     <div class="nav nav-tabs left-help-menu" role="tablist">
                       <div class="sidebar-header">
                        <h3><a href="/help-section"><i class="glyphicon glyphicon-question-sign" aria-hidden="true"></i>  Need Help</a></h3>
                       </div>';
    $content .= $menus;
    $content .= '</div><div class="tab-content tabs righ-help-content">' . $panel . '</div></div>';

    return [
      '#markup' => $content,
      '#cache'  => ['max-age' => 0],
    ];
  }

}
