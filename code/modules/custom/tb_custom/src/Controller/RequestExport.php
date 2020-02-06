<?php

namespace Drupal\tb_custom\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;
use Drupal\Core\Render;
use Drupal\Component\Serialization;
use Drupal\Component\Serialization\Json;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


/**
 * Implements TbCustom Controller.
 */
class RequestExport extends ControllerBase {

  public function RequestList($arg) {
    require('libraries/PhpSpreadsheet/vendor/autoload.php');
    $url = "?{$arg}";
    $url_components = parse_url($url);
    parse_str($url_components['query'], $params);

    $exposed_input = [];

    if (!empty($params)) {
      foreach ($params as $k => $v) {
        if (!empty($v) && $k != "_format") {
          $key = '';
          $key = str_replace("amp;", "", $k);

          if ($key == 'created') {
            //~ foreach ($v as $d => $date) {
              //~ $v[$d] = strtotime($date);
            //~ }

            $exposed_input[$key] = $v;
          }
          else {
            $exposed_input[$key] = $v;
          }
        }
      }
    }

    unset($exposed_input['_format']);

    //view 1
    $content = $arg;
    $data = [];
    $view = Views::getView('game_request_listing','data_export_1');

    if (is_object($view)) {
      $view->setDisplay('data_export_1');

      if (!empty($exposed_input)) {
        $view->setExposedInput($exposed_input);
      }
      $result = \Drupal::service('renderer')->render($view->render());
      $json = $result->jsonSerialize();
      $data = json_decode($json);


      $rids = [];
      if ($data) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('RequestsSummary');
        $sheet->getTabColor()->setRGB('FF0000');

        $sheet->setCellValue('A1', "#");
        $sheet->setCellValue('B1', "RID");
        $sheet->setCellValue('C1', "Play Center");
        $sheet->setCellValue('D1', "Days of Fulfillment");
        $sheet->setCellValue('E1', "Requested By");
        $sheet->setCellValue('F1', "Requested On");
        $sheet->setCellValue('G1', "Requested Qty");
        $sheet->setCellValue('H1', "Packed Qty");
        $sheet->setCellValue('I1', "Approved By");
        $sheet->setCellValue('J1', "Approved On");
        $sheet->setCellValue('K1', "Packed By");
        $sheet->setCellValue('L1', "Packed On");
        $sheet->setCellValue('M1', "Dispatched By");
        $sheet->setCellValue('N1', "Dispatched On");
        $sheet->setCellValue('O1', "Delivered By");
        $sheet->setCellValue('P1', "Delivered On");
        $sheet->setCellValue('Q1', "Closed By");
        $sheet->setCellValue('R1', "Closed On");
        $sheet->setCellValue('S1', "Status");

        $i = 1;
        foreach ($data as $d) {
          $rids[] = $d->nid;

          $i = ++$i;
          $sheet->setCellValue('A'.$i, $d->counter);
          $sheet->setCellValue('B'.$i, $d->nid);
          $sheet->setCellValue('C'.$i, $d->field_play_center);
          $sheet->setCellValue('D'.$i, $d->field_description_request_closed);
          $sheet->setCellValue('E'.$i, $d->field_last_name);
          $sheet->setCellValue('F'.$i, $d->created);
          $sheet->setCellValue('G'.$i, $d->field_fo_remarks);
          $sheet->setCellValue('H'.$i, $d->field_packed_qty_exp);
          $sheet->setCellValue('I'.$i, $d->field_last_name_1);
          $sheet->setCellValue('J'.$i, $d->field_gr_date_of_approval_denied);
          $sheet->setCellValue('K'.$i, $d->field_last_name_2);
          $sheet->setCellValue('L'.$i, $d->field_gr_date_of_packed);
          $sheet->setCellValue('M'.$i, $d->field_last_name_3);
          $sheet->setCellValue('N'.$i, $d->field_gr_date_of_dispatched);
          $sheet->setCellValue('O'.$i, $d->field_last_name_4);
          $sheet->setCellValue('P'.$i, $d->field_gr_date_of_delivered);
          $sheet->setCellValue('Q'.$i, $d->field_last_name_5);
          $sheet->setCellValue('R'.$i, $d->field_gr_date_of_closed);
          $sheet->setCellValue('S'.$i, $d->nothing);
        }

        if (!empty($rids)) {
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(1);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getTabColor()->setRGB('FF0000');
            $sheet->setTitle('RequestDetails');

              $sheet->setCellValue('A1', "#");
              $sheet->setCellValue('B1', "Req ID");
              $sheet->setCellValue('C1', "Center Code");
              $sheet->setCellValue('D1', "GCode");
              $sheet->setCellValue('E1', "Game Name");
              $sheet->setCellValue('F1', "Category");
              $sheet->setCellValue('G1', "Age Group");
              $sheet->setCellValue('H1', "Req Qty");
              $sheet->setCellValue('I1', "Packed/Delivered Qty");
          $counter = 0;
          $j=1;
          $nids =  implode(', ',$rids);
          //~ foreach ($rids as $r) {
            //~ $final_data = $view2 = [];
            //~ $view2 = Views::getView('request_details','data_export_1');
            //~ $view2->setDisplay('data_export_1');
            //~ $view2->setArguments(array('nid'=>$r));
            //~ $results = \Drupal::service('renderer')->render($view2->render());
            //~ $jsons = $results->jsonSerialize();
            //~ $final_data = json_decode($jsons);

            //view2 query
            $final_query = db_query("SELECT fc.entity_id as rid, co.field_playc_value as center_code, gc.field_game_code_value as game_code,
                                    (SELECT title FROM tban_node_field_data WHERE nid = gn.field_request_game_name_target_id) as game_name, tax.name as category,
                                    (SELECT GROUP_CONCAT(field_sub_catgeory_value) FROM tban_node__field_sub_catgeory WHERE entity_id = gn.field_request_game_name_target_id) as subcategory,
                                    rq.field_req_game_quantity_value as req_qty, pq.field_packed_quantity_value as pack_qty
                                    FROM tban_node__field_game_request_quantity as fc
                                    LEFT JOIN tban_node__field_play_center as pc ON pc.entity_id = fc.entity_id
                                    LEFT JOIN tban_node__field_playc as co ON co.entity_id = pc.field_play_center_target_id
                                    LEFT JOIN tban_field_collection_item__field_request_game_name as gn ON gn.entity_id = fc.field_game_request_quantity_value
                                    LEFT JOIN tban_node__field_game_code as gc ON gc.entity_id = gn.field_request_game_name_target_id
                                    LEFT JOIN tban_node__field_category as cat ON cat.entity_id = gn.field_request_game_name_target_id
                                    LEFT JOIN tban_taxonomy_term_field_data as tax ON tax.tid = cat.field_category_target_id
                                    LEFT JOIN tban_field_collection_item__field_req_game_quantity as rq ON rq.entity_id = fc.field_game_request_quantity_value
                                    LEFT JOIN tban_field_collection_item__field_packed_quantity as pq ON pq.entity_id = fc.field_game_request_quantity_value
                                      WHERE fc.entity_id IN ({$nids})
                                      ORDER BY fc.entity_id DESC, tax.weight, (SELECT title FROM tban_node_field_data WHERE nid = gn.field_request_game_name_target_id)")->fetchAll();


            if (!empty($final_query)) {

              foreach ($final_query as $f) {
                $pc_qty = '-';
                if(!empty($f->pack_qty)) {
                  $pc_qty = $f->pack_qty;
                }
                $j = ++$j;
                $counter = ++$counter;
                $sheet->setCellValue('A'.$j, $counter);
                $sheet->setCellValue('B'.$j, $f->rid);
                $sheet->setCellValue('C'.$j, $f->center_code);
                $sheet->setCellValue('D'.$j, $f->game_code);
                $sheet->setCellValue('E'.$j, $f->game_name);
                $sheet->setCellValue('F'.$j, $f->category);
                $sheet->setCellValue('G'.$j, $f->subcategory);
                $sheet->setCellValue('H'.$j, $f->req_qty);
                $sheet->setCellValue('I'.$j, $pc_qty);
              }
            }
          //~ }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        $file_path = "sites/default/files/test.xlsx";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Requests.xls"');
        $writer->save("php://output");
      }
    }
  }

}

?>
