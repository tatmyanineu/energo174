<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_config.php';
session_start();

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




$sheet->getStyle('A1:G300')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:G300')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);



$sheet->setCellValue("A1", "Аварии: Нет связи с обьектом");
$sheet->MergeCells("A1:E1");
$sheet->getStyle("A1:E1")->applyFromArray($FontStyle14TNR);

$sheet->setCellValue("A3", "№");
$sheet->MergeCells("A3:A4");
$sheet->setCellValue("B3", "Учереждение");
$sheet->MergeCells("B3:B4");
$sheet->setCellValue("C3", "Адрес");
$sheet->MergeCells("C3:C4");
$sheet->setCellValue("D3", "Дата последней передачи");
$sheet->MergeCells("D3:E3");

$sheet->setCellValue("D4", "ТЕПЛО");
$sheet->setCellValue("E4", "ХВС");



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




$m = 0;
$n = 1;
for ($i = 0; $i < count($table); $i++) {
    if ($table[$i]['error_t'] > 0 and $table[$i]['error_t'] < 4 or $table[$i]['error_w'] > 0 and $table[$i]['error_w'] < 4) {


        $sheet->setCellValueByColumnAndRow(0, 5 + $m, '' . $n . '');
        $sheet->setCellValueByColumnAndRow(1, 5 + $m, '' . $table[$i]['name'] . '');
        $sheet->setCellValueByColumnAndRow(2, 5 + $m, '' . $table[$i]['addres'] . '');

        if ($table[$i]['error_t'] == 1) {
            $sheet->setCellValueByColumnAndRow(3, 5 + $m, '' . date("d.m.Y", strtotime($table[$i]['date_t'])) . '');
        } elseif ($table[$i]['error_t'] == 3) {
            $sheet->setCellValueByColumnAndRow(3, 5 + $m, 'Нет данных');
        } elseif ($table[$i]['error_t'] == 4) {
            $sheet->setCellValueByColumnAndRow(3, 5 + $m, '-');
        } elseif ($table[$i]['error_t'] == 0) {
            $sheet->setCellValueByColumnAndRow(3, 5 + $m, '' . date("d.m.Y", strtotime($table[$i]['date_t'])) . '');
        }

        if ($table[$i]['error_w'] == 1) {
            $sheet->setCellValueByColumnAndRow(4, 5 + $m, '' . date("d.m.Y", strtotime($table[$i]['date_w'])) . '');
        } elseif ($table[$i]['error_w'] == 3) {
            $sheet->setCellValueByColumnAndRow(4, 5 + $m, 'Нет данных');
        } elseif ($table[$i]['error_w'] == 4) {
            $sheet->setCellValueByColumnAndRow(4, 5 + $m, '-');
        } elseif ($table[$i]['error_w'] == 0) {
            $sheet->setCellValueByColumnAndRow(4, 5 + $m, '' . date("d.m.Y", strtotime($table[$i]['date_w'])) . '');
        }
        $m++;
        $n++;
    }
}

$m = $m + 4;
$sheet->getStyle("A3:E" . $m . "")->applyFromArray($FontStyle11TNRtext);
$sheet->getStyle("A3:E" . $m . "")->applyFromArray($arrBorderStyle);


$sheet->getColumnDimension('A')->setWidth(8.57);
$sheet->getColumnDimension('B')->setWidth(60);
$sheet->getColumnDimension('C')->setWidth(40);
$sheet->getColumnDimension('D')->setWidth(16.29);
$sheet->getColumnDimension('E')->setWidth(11.43);

header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Аварии.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
