<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_config.php';
session_start();

$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

// Подключаем класс для работы с excel
require_once('PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('PHPExcel/Writer/Excel5.php');

// Создаем объект класса PHPExcel
$xls = new PHPExcel();

// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();
// Подписываем лист
$sheet->setTitle('Аварии');

$FontStyle11TNR = array(
    'font' => array(
        'bold' => true,
        'size' => 11,
        'name' => 'Times New Roman'
        ));

//стиль для ячеек с простым текстом
$FontStyle11TNRtext = array(
    'font' => array(
        'size' => 11,
        'name' => 'Times New Roman'
        ));
//стиль для оформления границ ячеек
$arrBorderStyle = array(
    'borders' => array(
        // внешняя рамка
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK,
            'color' => array(
                'rgb' => '00000000'
            )
        ),
        // внутренняя
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'rgb' => '00000000'
            )
        )
    )
);
//стиль для строки жирный 14 шрифт
$FontStyle14TNR = array(
    'font' => array(
        'bold' => true,
        'size' => 14,
        'name' => 'Times New Roman'
        ));




$sheet->getStyle('A1:Z900')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:Z900')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);



$sheet->setCellValue("A1", "Аварии: Нет данных по Теплу");
$sheet->MergeCells("A1:E1");
$sheet->getStyle("A1:E1")->applyFromArray($FontStyle14TNR);

$sheet->setCellValue("A2", "Проверка за период с " . $date1 . " по " . $date2 . "");
$sheet->MergeCells("A2:E2");
$sheet->getStyle("A2:E2")->applyFromArray($FontStyle14TNR);


$sheet->setCellValue("A3", "№");
$sheet->MergeCells("A3:A4");
$sheet->setCellValue("B3", "Учереждение");
$sheet->MergeCells("B3:B4");
$sheet->setCellValue("C3", "Адрес");
$sheet->MergeCells("C3:C4");
$sheet->setCellValue("D3", "Передача данных");
$sheet->MergeCells("D3:E3");

$sheet->setCellValue("D4", "Дата обновления");
$sheet->setCellValue("E4", "Статус");

/*

  $school_name = '';
  $school_hs = '';
  $school_str = '';
  $school_id = '';
  $sql_object_info = pg_query('SELECT
  "Places_cnt1"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Places_cnt1".plc_id
  FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
  WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND
  "PropPlc_cnt1".prop_id = 26 AND
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
  ORDER BY
  "Tepl"."Places_cnt".plc_id');
  while ($result_school_info = pg_fetch_row($sql_object_info)) {
  $school_name[] = $result_school_info[0];
  $school_hs[] = $result_school_info[2];
  $school_str[] = $result_school_info[1];
  $school_id[] = $result_school_info[3];
  }
  $sql_param_water = pg_query('SELECT DISTINCT
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."Places_cnt".plc_id
  FROM
  "Tepl"."ParamResPlc_cnt"
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
  WHERE
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 9 or
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" =  16
  ORDER BY
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."ParamResPlc_cnt"."ParamRes_id"');
  $water_param_id = '';
  while ($result_water_param = pg_fetch_row($sql_param_water)) {
  $water_param_id[] = $result_water_param[1];
  }

  $sql_water = pg_query('SELECT DISTINCT
  ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
  "Tepl"."Places_cnt".plc_id
  FROM
  "Tepl"."ParamResPlc_cnt"
  INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
  WHERE
  "Tepl"."Arhiv_cnt".typ_arh = 2 AND
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND
  "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'AND
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 9 OR
  "Tepl"."Arhiv_cnt".typ_arh = 2 AND
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND
  "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'AND
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 16
  ORDER BY
  "Tepl"."Places_cnt".plc_id');
  $water_date = '';
  $water_id = '';
  while ($result_water = pg_fetch_row($sql_water)) {
  $water_date[] = $result_water[0];
  $water_id[] = $result_water[1];
  }

 */

$sql_school_info = pg_query('SELECT 
        "Places_cnt1"."Name",
        "Tepl"."PropPlc_cnt"."ValueProp",
        "PropPlc_cnt1"."ValueProp",
        "Places_cnt1".plc_id,
        "Tepl"."Places_cnt"."Name",
        "Tepl"."Places_cnt".plc_id
      FROM
        "Tepl"."Places_cnt" "Places_cnt1"
        INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
        INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
        INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
        INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
      WHERE
        "Tepl"."PropPlc_cnt".prop_id = 27 AND 
        "PropPlc_cnt1".prop_id = 26 AND 
      "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
      "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
      ORDER BY
        "Tepl"."Places_cnt".plc_id');

while ($result_school_info = pg_fetch_row($sql_school_info)) {
    $school_info[] = array(
        'plc_id' => $result_school_info[3],
        'distinct' => $result_school_info[4],
        'id_dist' => $result_school_info[5],
        'name' => $result_school_info[0],
        'addres' => '' . $result_school_info[1] . ' ' . $result_school_info[2] . ''
    );
}


for ($i = 0; $i < count($school_info); $i++) {
    $key = array_search($school_info[$i]['plc_id'], array_column($_SESSION['main_form'], 'plc_id'));
    if ($key !== false) {
        $table[] = array(
            'plc_id' => $school_info[$i]['plc_id'],
            'distinct' => $school_info[$i]['distinct'],
            'id_dist' => $school_info[$i]['id_dist'],
            'name' => $school_info[$i]['name'],
            'addres' => $school_info[$i]['addres'],
            'date_t' => $_SESSION['main_form'][$key]['date_warm'],
            'error_t' => $_SESSION['main_form'][$key]['error_warm'],
            'date_w' => $_SESSION['main_form'][$key]['date_water'],
            'error_w' => $_SESSION['main_form'][$key]['error_water'],
        );
    }
}

$tmp1 = Array();
foreach ($table as &$ma) {
    $tmp1[] = &$ma["distinct"];
}
$tmp2 = Array();

foreach ($table as &$ma) {
    $tmp2[] = &$ma["name"];
}
$tmp3 = Array();

foreach ($table as &$ma) {
    $tmp3[] = &$ma["addres"];
}
array_multisort($tmp1, $tmp2, $tmp3, $table);





$n = 1;

for ($i = 0; $i < count($table); $i++) {
    if ($table[$i]['error_t'] > 0 and $table[$i]['error_t'] < 4) {
        $sheet->setCellValueByColumnAndRow(0, 5 + $n - 1, '' . $n . '');
        $sheet->setCellValueByColumnAndRow(1, 5 + $n - 1, '' . $table[$i]['name'] . '');
        $sheet->setCellValueByColumnAndRow(2, 5 + $n - 1, '' . $table[$i]['addres'] . '');
        if ($table[$i]['error_t'] == 1) {
            $sheet->setCellValueByColumnAndRow(3, 5 + $n - 1, '' . date("d.m.Y", strtotime($table[$i]['date_t'])) . '');
        } elseif ($table[$i]['error_t'] == 3) {
            $sheet->setCellValueByColumnAndRow(3, 5 + $n - 1, ' Нет данных');
        } elseif ($table[$i]['error_t'] == 4) {
            $sheet->setCellValueByColumnAndRow(3, 5 + $n - 1, '-');
        } elseif ($table[$i]['error_t'] == 0) {
            $sheet->setCellValueByColumnAndRow(3, 5 + $n - 1, '' . date("d.m.Y", strtotime($table[$i]['date_t'])) . '');
        }
        $sheet->setCellValueByColumnAndRow(4, 5 + $n - 1, 'Отсутствует: Тепло');
        $n++;
    }
}

for ($i = 0; $i < count($array_school); $i++) {
    $k = array_search($array_school[$i][plc_id], array_column($main_array, 'plc_id'));
    if ($k !== false) {
        if ($main_array[$k]['marker'] == 3) {

            $sheet->setCellValueByColumnAndRow(0, 5 + $n - 1, '' . $n . '');
            $sheet->setCellValueByColumnAndRow(1, 5 + $n - 1, '' . $array_school[$i][name] . '');
            $sheet->setCellValueByColumnAndRow(2, 5 + $n - 1, '' . $array_school[$i][addres] . '');
            $sheet->setCellValueByColumnAndRow(3, 5 + $n - 1, '' . date("d.m.Y", strtotime($main_array[$k]['date_warm'])) . '');
            $sheet->setCellValueByColumnAndRow(4, 5 + $n - 1, 'Отсутствует: Тепло');
            $n++;
        }
    } else {
        $sheet->setCellValueByColumnAndRow(0, 5 + $n - 1, '' . $n . '');
        $sheet->setCellValueByColumnAndRow(1, 5 + $n - 1, '' . $array_school[$i][name] . '');
        $sheet->setCellValueByColumnAndRow(2, 5 + $n - 1, '' . $array_school[$i][addres] . '');
        $sheet->setCellValueByColumnAndRow(3, 5 + $n - 1, '-');
        $sheet->setCellValueByColumnAndRow(4, 5 + $n - 1, 'Отсутствует: Тепло');
        //echo "n= " . $n++ . " s= " . $school_name[$j] . " d=" . $water_date[$w] . " <br>";

        $n++;
    }
}

/*
  for ($j = 0; $j < count($school_id); $j++) {
  $plc_err = 1;
  for ($i = 0; $i < count($_SESSION['err_plc']); $i++) {
  if ($school_id[$j] == $_SESSION['err_plc'][$i]) {
  $plc_err = 0;
  }
  }
  if ($plc_err == 1) {

  //echo "n= " . $n++ . "  " . $school_name[$j] . " <br>";
  for ($p = 0; $p < count($water_param_id); $p++) {
  if ($school_id[$j] == $water_param_id[$p]) {
  //echo "n= " . $n++ . " s= " . $school_name[$j] . " <br>";
  $water_error = 1;
  for ($w = 0; $w < count($water_id); $w++) {
  if ($school_id[$j] == $water_id[$w]) {
  if ($water_id[$w] != $water_id[$w + 1]) {
  $water_error = 0;
  if (strtotime($water_date[$w]) != strtotime($date2)) {


  $sheet->setCellValueByColumnAndRow(0, 5 + $n - 1, '' . $n . '');
  $sheet->setCellValueByColumnAndRow(1, 5 + $n - 1, '' . $school_name[$j] . '');
  $sheet->setCellValueByColumnAndRow(2, 5 + $n - 1, '' . $school_str[$j] . ' ' . $school_hs[$j] . '');
  $sheet->setCellValueByColumnAndRow(3, 5 + $n - 1, '' . date("d.m.Y", strtotime($water_date[$w])) . '');
  $sheet->setCellValueByColumnAndRow(4, 5 + $n - 1, 'Отсутствует: Тепло');


  $n++;
  }
  }
  }
  }
  if ($water_error == 1) {

  $sheet->setCellValueByColumnAndRow(0, 5 + $n - 1, '' . $n . '');
  $sheet->setCellValueByColumnAndRow(1, 5 + $n - 1, '' . $school_name[$j] . '');
  $sheet->setCellValueByColumnAndRow(2, 5 + $n - 1, '' . $school_str[$j] . ' ' . $school_hs[$j] . '');
  $sheet->setCellValueByColumnAndRow(3, 5 + $n - 1, 'Нет данных');
  $sheet->setCellValueByColumnAndRow(4, 5 + $n - 1, 'Отсутствует: Тепло');


  //echo "n= " . $n++ . " s= " . $school_name[$j] . " d= нет данных <br>";
  $n++;
  }
  }
  }
  }
  }

 */


$n = $n + 3;
$sheet->getStyle("A3:E" . $n . "")->applyFromArray($FontStyle11TNRtext);
$sheet->getStyle("A3:E" . $n . "")->applyFromArray($arrBorderStyle);


$sheet->getColumnDimension('A')->setWidth(8.57);
$sheet->getColumnDimension('B')->setWidth(60);
$sheet->getColumnDimension('C')->setWidth(40);
$sheet->getColumnDimension('D')->setWidth(16.29);
$sheet->getColumnDimension('E')->setWidth(30.71);

header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Нет данных по теплу.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
