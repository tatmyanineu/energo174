<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */


include '../db_config.php';
session_start();

$kv = $_GET['kvartal'] - 100;

$sql_name_object = pg_query('SELECT 
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp"
FROM
  "Tepl"."PropPlc_cnt"
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PropPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."Places_cnt".plc_id = ' . $_GET['id_object'] . '');

$name = pg_fetch_result($sql_name_object, 0, 1) . ' ' . pg_fetch_result($sql_name_object, 0, 2) . ' ' . pg_fetch_result($sql_name_object, 0, 3);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="График лимитов ' . pg_fetch_result($sql_name_object, 0, 1) . '.xlsx"');
header('Cache-Control: max-age=0');

include_once '../Classes/PHPExcel.php';
$phpexcel = new PHPExcel();

$phpexcel->setActiveSheetIndex(0);
$sheet = $phpexcel->getActiveSheet();
$sheet->setTitle('Worksheet');

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


$styleArray11 = array(
    'borders' => array(
        'inside' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'argb' => '000'
            )
        ),
        'font' => array(
            'size' => 12,
            'name' => 'Times New Roman'
        ),
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'argb' => '000'
            )
        )
    )
);


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

$sheet->getStyle('A1:D4')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:D4')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);



$sheet->getStyle('A3:D8')->applyFromArray($styleArray11);
$sheet->getStyle('A3:D4')->applyFromArray($arHeadStyle);

$sheet->getStyle('A5:C8')->applyFromArray($arTextStyle);

$sheet->getStyle('D5:D8')->applyFromArray($arHeadStyle);

$sheet->getStyle('A8')->applyFromArray($arHeadStyle);

$sheet->getStyle('A1:D2')->applyFromArray($arHeadStyle);
$sheet->getStyle('A10')->applyFromArray($arHeadStyle);
$sheet->getStyle('A29')->applyFromArray($arHeadStyle);

$sheet->getStyle('A3:D8')->applyFromArray($arrBorderStyle);
$sheet->getStyle('D5:D7')->applyFromArray($arrBorderStyle);
$sheet->getStyle('A8:D8')->applyFromArray($arrBorderStyle);
$sheet->getStyle('A3:D4')->applyFromArray($arrBorderStyle);
$sheet->getStyle('D4')->applyFromArray($arrBorderStyle);
$sheet->getStyle('D8')->applyFromArray($arrBorderStyle);

switch ($_GET['kvartal']) {
    case 101:
        $month = array(01, 02, 03);
        $num = cal_days_in_month(CAL_GREGORIAN, 3, $_GET['year']);
        $date1 = date('' . $_GET['year'] . '-01-01');
        $date2 = date('' . $_GET['year'] . '-03-' . $num);
        $date_now = $date2;
        break;
    case 102:
        $month = array(04, 05, 06);
        $num = cal_days_in_month(CAL_GREGORIAN, 6, $_GET['year']);
        $date1 = date('' . $_GET['year'] . '-04-01');
        $date2 = date('' . $_GET['year'] . '-06-' . $num);
        $date_now = $date2;
        break;
    case 103:
        $month = array(07, 08, 09);
        $num = cal_days_in_month(CAL_GREGORIAN, 9, $_GET['year']);
        $date1 = date('' . $_GET['year'] . '-07-01');
        $date2 = date('' . $_GET['year'] . '-09-' . $num);
        $date_now = $date2;
        break;
    case 104:
        $month = array(10, 11, 12);
        $num = cal_days_in_month(CAL_GREGORIAN, 12, $_GET['year']);
        $date1 = date('' . $_GET['year'] . '-10-01');
        $date2 = date('' . $_GET['year'] . '-12-' . $num);
        $date_now = $date2;
        break;
}


$sql_all_limit = pg_query('SELECT DISTINCT
  public."LimitPlaces_cnt".plc_id,
  public."LimitPlaces_cnt".teplo,
  public."LimitPlaces_cnt".voda
  FROM
  public."LimitPlaces_cnt"
  WHERE
  public."LimitPlaces_cnt".plc_id = ' . $_GET['id_object'] . '');

while ($result = pg_fetch_row($sql_all_limit)) {
    $arr_all_limit[] = array(
        'plc_id' => $result[0],
        'teplo' => $result[1],
        'voda' => $result[2]
    );
}

for ($i = 0; $i < count($month); $i++) {
    $sql_limit_part = pg_query('SELECT
  public."LimitMonth_cnt".teplo,
  public."LimitMonth_cnt".voda,
  public."LimitMonth_cnt".name
  FROM
  public."LimitMonth_cnt"
  WHERE
  public."LimitMonth_cnt".id = ' . $month[$i] . '');

    $limits[] = array(
        'teplo' => pg_fetch_result($sql_limit_part, 0, 0),
        'voda' => pg_fetch_result($sql_limit_part, 0, 1),
        'name' => pg_fetch_result($sql_limit_part, 0, 2)
    );

    $limit_teplo_part[] = pg_fetch_result($sql_limit_part, 0, 0);
    $limit_voda_part[] = pg_fetch_result($sql_limit_part, 0, 1);
    $month_name[] = pg_fetch_result($sql_limit_part, 0, 2);
}

$sheet->setCellValue("A1", 'Потребление воды в ' . $kv . ' квартале  ' . $_GET['year'] . ' г.');
$sheet->mergeCells('A1:D1');
$sheet->setCellValue("A2", '' . $name . '');
$sheet->mergeCells('A2:D2');


$sheet->setCellValue("A3", 'Месяц');
$sheet->mergeCells('A3:A4');

$sheet->setCellValue("B3", 'Потребление воды');
$sheet->mergeCells('B3:D3');

$sheet->setCellValue("B4", 'Фактическое (куб.м.)');
$sheet->setCellValue("C4", 'Лимит (куб.м.)');
$sheet->setCellValue("D4", 'Перерасход (куб.м.)');


$sheet->getColumnDimension('A')->setWidth(18);
$sheet->getColumnDimension('B')->setWidth(22);
$sheet->getColumnDimension('C')->setWidth(19);
$sheet->getColumnDimension('D')->setWidth(22);

$row = 0;

for ($i = 0; $i < count($month); $i++) {
    $num = cal_days_in_month(CAL_GREGORIAN, $month[$i], $_GET['year']);
    $date1 = date('' . $_GET['year'] . '-' . $month[$i] . '-01');
    $date2 = date('' . $_GET['year'] . '-' . $month[$i] . '-' . $num);

    $fist_date1 = date('Y-m-d', strtotime('+1 day', strtotime($date1)));
    $last_date2 = date('Y-m-d', strtotime('+1 day', strtotime($date2)));
    $teplo = 0;
    $limit = 0;
    $raz = 0;
    $sql_text = 'SELECT DISTINCT
                ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                "Tepl"."Arhiv_cnt"."DataValue"
                FROM
                "Tepl"."Places_cnt" "Places_cnt1"
                INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."ParamResPlc_cnt".plc_id)
                INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
                INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
                WHERE
                "Tepl"."Arhiv_cnt".typ_arh = 2 AND
                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $fist_date1 . '\' AND
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $last_date2 . '\' AND
                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\'  AND
                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
                "Places_cnt1".plc_id =  ' . $_GET['id_object'] . '
                ORDER BY
                "Tepl"."ParamResPlc_cnt"."ParamRes_id"';
    $sql_data = pg_query($conn, $sql_text);
    $voda = array();
    while ($result = pg_fetch_row($sql_data)) {

        if ($result[1] == 1) {
            $voda[0][] = $result[2];
        }
        if ($result[1] == 308) {
            $voda[1][] = $result[2];
        }
        if ($result[1] == 310) {
            $voda[2][] = $result[2];
        }
        if ($result[1] == 414) {
            $voda[3][] = $result[2];
        }
        if ($result[1] == 420) {
            $voda[4][] = $result[2];
        }
    }

    $value = summ_voda($voda);


    $value = number_format($value, 2, '.', ' ');

    $limit = ((float) $arr_all_limit[0]['voda'] / 100 ) * (float) $limits[$i]['voda'];
    $raz = number_format($value - $limit, 2, '.', ' ');

    $sheet->setCellValueByColumnAndRow(0, 5 + $row, '' . $limits[$i]['name'] . '');
    $sheet->setCellValueByColumnAndRow(1, 5 + $row, '' . $value . '');
    $sheet->setCellValueByColumnAndRow(2, 5 + $row, '' . $limit . '');
    $sheet->setCellValueByColumnAndRow(3, 5 + $row, '' . $raz . '');
    $row++;

    $arr_limit[] = $limit;
    $arr_teplo[] = $value;
    $arr_raznost[] = $raz;
}


$sheet->setCellValueByColumnAndRow(0, 5 + $row, 'ИТОГО');
$sheet->setCellValueByColumnAndRow(1, 5 + $row, '' . array_sum($arr_teplo) . '');
$sheet->setCellValueByColumnAndRow(2, 5 + $row, '' . array_sum($arr_limit) . '');
$sheet->setCellValueByColumnAndRow(3, 5 + $row, '' . array_sum($arr_raznost) . '');

$sheet->setCellValue("A10", '- Помесячно:');
$sheet->setCellValue("A29", '- За ' . $kv . ' квартал ' . $_GET['year'] . ' г.:');



if (array_sum($arr_limit) > array_sum($arr_teplo)) {
    $max_X = array_sum($arr_limit) + 100;
} else {
    $max_X = array_sum($arr_teplo) + 100;
}


$dsl = array(
    new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$4', NULL, 1),
    new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$4', NULL, 1),
);

$values = array(
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$5:$B$7'),
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$5:$C$7'),);

$categories = new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$5:$A$7', NULL,3);

$series = new PHPExcel_Chart_DataSeries(
        PHPExcel_Chart_DataSeries::TYPE_BARCHART, // plotType
        PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED, // plotGrouping
        range(0, count($values) - 1), // plotOrder
        $dsl, // plotLabel
        array($categories), // plotCategory
        $values                              // plotValues
);

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COLUMN);

$layout2 = new PHPExcel_Chart_Layout();
$layout2->setShowVal(true);
$layout2->setHeight(1000);
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);

$plotarea = new PHPExcel_Chart_PlotArea($layout2, array($series));
$xTitle = new PHPExcel_Chart_Title('');
$yTitle = new PHPExcel_Chart_Title('');
$chart = new PHPExcel_Chart('sample', null, $legend, $plotarea, true, 0, $xTitle, $yTitle);

$chart->setTopLeftPosition('A12');
$chart->setBottomRightPosition('E28');

$sheet->addChart($chart);

$values2 = array(
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$8'),
    new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$8'),);
$categories2 = new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$8',NULL,3);

$series2 = new PHPExcel_Chart_DataSeries(
        PHPExcel_Chart_DataSeries::TYPE_BARCHART, // plotType
        PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED, // plotGrouping
        range(0, count($values) - 1), // plotOrder
        $dsl, // plotLabel
        array($categories2), // plotCategory
        $values2                              // plotValues
);
$series2->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COLUMN);
$plotarea2 = new PHPExcel_Chart_PlotArea($layout2, array($series2));

$axis = new PHPExcel_Chart_Axis();
$axis->setAxisOptionsProperties('nextTo', null, null, null, null, null, 0, $max_X);
$chart2 = new PHPExcel_Chart('sample', null, $legend, $plotarea2, true, 0, $xTitle, $yTitle, $axis);
//$chart = new PHPExcel_Chart('chart' . $locTL, $title, $legend, $pa, true, 0, NULL, NULL, $axis);

$chart2->setTopLeftPosition('A31');
$chart2->setBottomRightPosition('E45');

$sheet->addChart($chart2);



$writer = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
$writer->setIncludeCharts(TRUE);
$writer->save('php://output');

function summ_voda($voda) {
    global $days_v;
    $k_voda = 0;
    $val = 0;
    for ($l = 0; $l < count($voda); $l++) {
        //print_r($voda[$l])."<br>";
        $n1 = count($voda[$l]) - 1;
        $z = 0;
        if ($l == 0) {
            $k_voda = count($voda[$l]);
        } else {
            if ($k_voda > count($voda[$l])) {
                $k_voda = count($voda[$l]);
            }
        }

        for ($n = 0; $n < count($voda[$l]); $n++) {

            if ($n == $n1) {
                $z = $z;
                //echo "n=" .$n." mas = ". $voda[$l][$n]."    z=".$z."  <br>" ;
            }
            if ($n >= 0 and $n < $n1) {
                if ($voda[$l][$n]) {
                    $z = $z + $voda[$l][$n + 1] - $voda[$l][$n];
                }
                //echo "n=" .$n." mas = ". $voda[$l][$n]."   mas+1 =  ".$voda[$l][$n+1]. "     z=".$z."  <br>" ;
            }
        }
        $val = $val + $z;
        //echo "Z ====".$z."  <br>";
    }
    $days_v = $k_voda;
    return $val;
}
