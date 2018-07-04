<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../../db_config.php';
session_start();


// Подключаем класс для работы с excel
require_once('../../PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('../../PHPExcel/Writer/Excel5.php');

// Создаем объект класса PHPExcel
$xls = new PHPExcel();

// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();
// Подписываем лист
$sheet->setTitle('Обьекты');


//стиль для ячеек которые будут заголовками
$arHeadStyle = array(
    'font' => array(
        'bold' => true,
        'size' => 12,
        'name' => 'Times New Roman'
        ));
//стиль для ячеек с простым текстом
$arTextStyle = array(
    'font' => array(
        'size' => 12,
        'name' => 'Times New Roman'
        ));

$sheet->getStyle('A1:L4')->applyFromArray($arHeadStyle);

$sheet->getStyle('A1:L4')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:L4')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);

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

$styleArray11 = array(
    'borders' => array(
        'inside' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'argb' => 'FFF'
            )
        ),
        'font' => array(
            'size' => 14,
            'name' => 'Times New Roman'
        ),
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'argb' => 'FFF'
            )
        )
    )
);

$sql_all_object = pg_query('SELECT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."Places_cnt".plc_id
FROM
  "Tepl"."Places_cnt"
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26');
while ($result = pg_fetch_row($sql_all_object)) {
    $objects[] = array(
        'plc_id' => $result[3],
        'name' => $result[0],
        'adress' => $result[1] . ' ' . $result[2]
    );
}


$sql_all_object_temp = pg_query('SELECT 
  public.temp_charts.plc_id,
  public.temp_charts.t1min,
  public.temp_charts.t2min,
  public.temp_charts.t1max,
  public.temp_charts.t2max,
  public.temp_charts.thpmin,
  public.temp_charts.thpmax,
  public.temp_charts.param1,
  public.temp_charts.param2
FROM
  public.temp_charts');
while ($result = pg_fetch_row($sql_all_object_temp)) {
    $temp_table[] = array(
        'plc_id' => $result[0],
        'p1' => $result[1],
        'p2' => $result[2],
        'p3' => $result[3],
        'p4' => $result[4],
        'p5' => $result[5],
        'p6' => $result[6],
        'p7' => $result[7],
        'p8' => $result[8],
    );
}
$m = 0;
$kol = 1;
for ($i = 0; $i < count($objects); $i++) {
    $k = array_search($objects[$i]['plc_id'], array_column($temp_table, 'plc_id'));
    if ($k !== false) {
        $sheet->setCellValueByColumnAndRow(0, 5 + $m, '' . $kol . '');
        $sheet->setCellValueByColumnAndRow(1, 5 + $m, '' . $objects[$i]['name'] . '');
        $sheet->setCellValueByColumnAndRow(2, 5 + $m, '' . $objects[$i]['adress'] . '');
        $sheet->setCellValueByColumnAndRow(3, 5 + $m, '' . $objects[$i]['plc_id'] . '');
        $sheet->setCellValueByColumnAndRow(4, 5 + $m, $temp_table[$k]['p1']);
        $sheet->setCellValueByColumnAndRow(5, 5 + $m, $temp_table[$k]['p2']);
        $sheet->setCellValueByColumnAndRow(6, 5 + $m, $temp_table[$k]['p3']);
        $sheet->setCellValueByColumnAndRow(7, 5 + $m, $temp_table[$k]['p4']);
        $sheet->setCellValueByColumnAndRow(8, 5 + $m, $temp_table[$k]['p5']);
        $sheet->setCellValueByColumnAndRow(9, 5 + $m, $temp_table[$k]['p6']);
        $sheet->setCellValueByColumnAndRow(10, 5 + $m, $temp_table[$k]['p7']);
        $sheet->setCellValueByColumnAndRow(11, 5 + $m, $temp_table[$k]['p8']);
    } else {
        $sheet->setCellValueByColumnAndRow(0, 5 + $m, '' . $kol . '');
        $sheet->setCellValueByColumnAndRow(1, 5 + $m, '' . $objects[$i]['name'] . '');
        $sheet->setCellValueByColumnAndRow(2, 5 + $m, '' . $objects[$i]['adress'] . '');
        $sheet->setCellValueByColumnAndRow(3, 5 + $m, '' . $objects[$i]['plc_id'] . '');
        $sheet->setCellValueByColumnAndRow(4, 5 + $m, ' -');
        $sheet->setCellValueByColumnAndRow(5, 5 + $m, ' -');
        $sheet->setCellValueByColumnAndRow(6, 5 + $m, ' -');
        $sheet->setCellValueByColumnAndRow(7, 5 + $m, ' -');
        $sheet->setCellValueByColumnAndRow(8, 5 + $m, ' -');
        $sheet->setCellValueByColumnAndRow(9, 5 + $m, '- ');
        $sheet->setCellValueByColumnAndRow(10, 5 + $m, ' -');
        $sheet->setCellValueByColumnAndRow(11, 5 + $m, ' -');
    }
    $m++;
    $kol++;
}



$sheet->setCellValue("A3", '№');
$sheet->mergeCells('A3:A4');

$sheet->setCellValue("B3", 'Учереждение');
$sheet->mergeCells('B3:B4');

$sheet->setCellValue("C3", 'Адрес');
$sheet->mergeCells('C3:C4');

$sheet->setCellValue("D3", 'Идентификатор объекта');
$sheet->mergeCells('D3:D4');

$sheet->setCellValue("E3", 'Темп. min Подачи');
$sheet->mergeCells('E3:E4');

$sheet->setCellValue("F3", 'Темп. min Обратки');
$sheet->mergeCells('F3:F4');

$sheet->setCellValue("G3", 'Темп. max Подачи');
$sheet->mergeCells('G3:G4');

$sheet->setCellValue("H3", 'Темп. max Обратки');
$sheet->mergeCells('H3:H4');

$sheet->setCellValue("I3", 'Темп. min Нар. Воздуха');
$sheet->mergeCells('I3:I4');

$sheet->setCellValue("J3", 'Темп. max нар. Воздуха');
$sheet->mergeCells('J3:J4');

$sheet->setCellValue("K3", 'Коэф. Подачи');
$sheet->mergeCells('K3:K4');

$sheet->setCellValue("L3", 'Коэф. Обратки');
$sheet->mergeCells('L3:L4');



$sheet->getStyle('A4:L390')->applyFromArray($arrBorderStyle);



for ($a = 65; $a < 76; $a++) {
    /*
     * тут кароче автосайз столбцов которые перебираются циклом 
     * по коду буквы в алфавите
     */
    $sheet->getColumnDimension("" . chr($a) . "")->setAutoSize(True);
}

header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Объекты.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
