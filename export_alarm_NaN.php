<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_config.php';
session_start();

$time = strtotime("-10 day");

$date1 = date("Y-m-d", $time);
$date2 = date('Y-m-d');

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



$sheet->setCellValue("A1", "Аварии: NaN значения");
$sheet->MergeCells("A1:E1");
$sheet->getStyle("A1:E1")->applyFromArray($FontStyle14TNR);

$sheet->setCellValue("A2", "Проверка за период с " . date("d.m.Y", strtotime($date1)) . " по " . date("d.m.Y", strtotime($date2)) . "");
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




$sql_date_archive = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                                  FROM
                                    "Tepl"."ParamResPlc_cnt"
                                    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                  WHERE
                                    "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\'
                                  ORDER BY
                                    "Tepl"."Arhiv_cnt"."DateValue" DESC');
//echo pg_num_rows($sql_date_archive);
$n = 0;
while ($result_date = pg_fetch_row($sql_date_archive)) {

    $massiv = '';
    $pokaz = '';
    $date_arch = explode(" ", $result_date[0]);
    $time = strtotime("-1 day");
    $date_b = date("d.m.Y", strtotime("-1 day", strtotime($date_arch[0])));

    $sheet->setCellValueByColumnAndRow(0, 5 + $n, '' . $date_b . '');
    $sheet->mergeCellsByColumnAndRow(0, 5 + $n, 4, 5 + $n);
    $sheet->getStyleByColumnAndRow(0, 5 + $n)->applyFromArray($FontStyle11TNR);
    $n = $n + 1;
    // echo "<tr><td class='dist text-center' colspan='5'><b>" . $date_b . "</b></td></tr>";
    //echo $result_date[0] . "<br>";
    //echo "ДЕНЬ ПОOOOOOOOOOOOOOOOOOOOOOOOOOOOOШЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛ!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br>";
    $sql_archive = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."Arhiv_cnt"."DataValue",
                                    "Tepl"."Places_cnt"."Name"
                                  FROM
                                    "Tepl"."ParamResPlc_cnt"
                                    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                  WHERE
                                    "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" = \'' . $result_date[0] . '\' AND 
                                    "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\'AND 
                                    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
                                  ORDER BY
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id"');
    $q = 1;
    //echo pg_num_rows($sql_archive) . "<br>";
    while ($resusl_archive = pg_fetch_row($sql_archive)) {
        $arr_id[$z] = $resusl_archive[2];
        $aar_name[$z] = $resusl_archive[4];
        //$arr_val[$v] = 
        //$arr_param[$v] =
        //
                                //echo "Z== " . $z . " id = " . $arr_id[$z] . "  name = " . $aar_name[$z] . " res = " . $resusl_archive[1]. "<br>";

        if ($z != 0) {
            if ($resusl_archive[2] == $arr_id[$z - 1]) {
                //$kol_res++;
                $arr_param[$v][] = $resusl_archive[1];
                $arr_val[$v][] = $resusl_archive[3];
            }
            if ($resusl_archive[2] != $arr_id[$z - 1]) {
                $arr_param[$v + 1][] = $resusl_archive[1];
                $arr_val[$v + 1][] = $resusl_archive[3];
                $plc = $aar_name[$z - 1];



                //print_r($arr_param[$v]);echo " <br>";
                //print_r($arr_val[$v]);echo " <br>";

                for ($i = 0; $i < count($arr_param[$v]); $i++) {
                    if ($arr_val[$v][$i] == 'NaN') {
                        //echo "id= " . $arr_id[$z - 1] . " " . $plc . "  kol. res = " . $kol_res . " <br>";
                        $massiv[] = $arr_id[$z - 1];
                    }
                }

                $v++;
                //$kol_res = 0;
                //$kol_res ++;
            }
        } else {
            if ($resusl_archive[2] == $arr_id[$z]) {
                //$kol_res++;
                $arr_param[$v][] = $resusl_archive[1];
                $arr_val[$v][] = $resusl_archive[3];
            }
        }
        $z++;
    }

    $arr_distinct = array_unique($massiv);
    //print_r($arr_distinct);
    foreach ($arr_distinct as $key => $value) {
        $pokaz[] = $arr_distinct[$key];
    }
    //print_r($pokaz);



    for ($j = 0; $j < count($pokaz); $j++) {

        $sql_info = pg_query('SELECT DISTINCT 
                                                    "Tepl"."Places_cnt".plc_id,
                                                    "Tepl"."Places_cnt"."Name",
                                                    "Places_cnt1"."Name",
                                                    "Tepl"."PropPlc_cnt"."ValueProp",
                                                    "PropPlc_cnt1"."ValueProp"
                                                  FROM
                                                    "Tepl"."Places_cnt"
                                                    INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                                    INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                                    INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
                                                    INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                    INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                  WHERE
                                                    "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                                    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                                                    "Tepl"."Places_cnt".plc_id = ' . $pokaz[$j] . 'AND 
                                                    "Tepl"."PropPlc_cnt".prop_id = 26 AND 
                                                    "PropPlc_cnt1".prop_id = 27
                                                  ORDER BY
                                                    "Tepl"."Places_cnt".plc_id');
        $result = pg_fetch_row($sql_info);


        $sheet->setCellValueByColumnAndRow(0, 5 + $n, '' . $q . '');
        $sheet->setCellValueByColumnAndRow(1, 5 + $n, '' . $result[1] . '');
        $sheet->setCellValueByColumnAndRow(2, 5 + $n, '' . $result[3] . " " . $result[4] . '');
        $sheet->setCellValueByColumnAndRow(3, 5 + $n, '' . $date_b . '');
        $sheet->setCellValueByColumnAndRow(4, 5 + $n, 'NaN');
        $n = $n + 1;
        $q++;
        //echo "<tr data-href='object.php?id_object=" . $result[0] . "' id ='hover'><td>" . $q++ . "</td><td>" . $result[1] . "</td><td>" . $result[3] . " " . $result[4] . "</td><td>" . $date_b . "</td><td>NaN</td></tr>";
    }
}


$n=$n+4;
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
header("Content-Disposition: attachment; filename=NaN значения.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
