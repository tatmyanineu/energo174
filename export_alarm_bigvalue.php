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
$kor_value = $_GET['value'];

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



$sheet->setCellValue("A1", "Аварии: Корректировка ХВС");
$sheet->MergeCells("A1:F1");
$sheet->getStyle("A1:F1")->applyFromArray($FontStyle14TNR);

$sheet->setCellValue("A2", "Проверка за период с " . $date1 . " по " . $date2 . "");
$sheet->MergeCells("A2:F2");
$sheet->getStyle("A2:F2")->applyFromArray($FontStyle14TNR);


$sheet->setCellValue("A3", "№");
$sheet->MergeCells("A3:A4");
$sheet->setCellValue("B3", "Учереждение");
$sheet->MergeCells("B3:B4");
$sheet->setCellValue("C3", "Адрес");
$sheet->MergeCells("C3:C4");
$sheet->setCellValue("D3", "Разность данных");
$sheet->MergeCells("D3:F3");

$sheet->setCellValue("D4", "Дата");
$sheet->setCellValue("E4", "Статус");
$sheet->setCellValue("F4", "Параметр");




$sql_archive = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."Arhiv_cnt"."DataValue",
                                    "Tepl"."Places_cnt"."Name",
                                    "Tepl"."ParametrResourse"."Name",
                                    "Tepl"."Resourse_cnt"."Name"
                                  FROM
                                    "Tepl"."ParamResPlc_cnt"
                                    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                    INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
                                    INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                                  WHERE
                                    "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND
                                    "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' AND
                                  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\'AND 
                                    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
                                  ORDER BY
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id"');

//echo pg_num_rows($sql_archive) . "<br>";
$z = 0;
$kol = 0;
$n = 0;
$d = 0;
while ($resusl_archive = pg_fetch_row($sql_archive)) {
    $arr_id[$z] = $resusl_archive[2];
    $arr_name[$z] = $resusl_archive[4];
    $arr_param[$z] = $resusl_archive[1];
    $arr_value[$z] = $resusl_archive[3];
    $arr_date[$z] = $resusl_archive[0];
    $arr_name_param[$z] = $resusl_archive[5];
    $arr_name_resours[$z] = $resusl_archive[6];
    if ($z != 0) {
        if ($resusl_archive[1] == $arr_param[$z - 1]) {
            if ($resusl_archive[2] == $arr_id[$z - 1]) {
                $summ+=$arr_value[$z];
                $kol++;
                //echo "kol = ". $kol. " name = " . $arr_name[$z] . "  param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
            }
            if ($resusl_archive[2] != $arr_id[$z - 1]) {
                $sred = sqrt($summ / $kol);
                for ($i = 1; $i < $kol; $i++) {
                    if ($arr_param[$z - 1] == 1 or $arr_param[$z - 1] == 308 or $arr_param[$z - 1] == 310 or $arr_param[$z - 1] == 414 or $arr_param[$z - 1] == 420 or $arr_param[$z - 1] == 436 or $arr_param[$z - 1] == 787 or $arr_param[$z - 1] == 2 or $arr_param[$z - 1] == 44 or $arr_param[$z - 1] == 377 or $arr_param[$z - 1] == 442 or $arr_param[$z - 1] == 402 or $arr_param[$z - 1] == 408 or $arr_param[$z - 1] == 922) {
                        $raz = $arr_value[$z - $i] - $arr_value[$z - $i - 1];
                        if ($raz > $kor_value) {
                            $sql_addres = pg_query('SELECT DISTINCT 
                                                                        "Tepl"."PropPlc_cnt"."ValueProp",
                                                                        "PropPlc_cnt1"."ValueProp"
                                                                      FROM
                                                                        "Tepl"."Places_cnt"
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                                      WHERE
                                                                        "Tepl"."Places_cnt".plc_id = ' . $arr_id[$z - 1] . ' AND 
                                                                        "PropPlc_cnt1".prop_id = 26 AND 
                                                                        "Tepl"."PropPlc_cnt".prop_id = 27');
                            $res = pg_fetch_row($sql_addres);

                            $date_arch = explode(" ", $arr_date[$z - $i]);

                            $date_b = date("d.m.Y", strtotime($date_arch[0]));
                            //echo "<tr data-href='object.php?id_object=" . $arr_id[$z - 1] . "' id ='hover'>";
                            //echo "<td>" . $n . "</td><td>" . $arr_name[$z - 1] . "</td><td>" . $res[0] . " " . $res[1] . "</td><td>" . $date_b . "</td><td>" . number_format($raz, 2) . "</td><td>" . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . "</td>";
                            //echo "</tr>";
                            //echo " name = " . $arr_name[$z - 1] . "  param = " . $arr_param[$z - 1];

                            $d++;

                            $sheet->setCellValueByColumnAndRow(0, 5 + $n, '' . $d . '');
                            $sheet->setCellValueByColumnAndRow(1, 5 + $n, '' . $arr_name[$z - 1] . '');
                            $sheet->setCellValueByColumnAndRow(2, 5 + $n, '' . $res[0] . " " . $res[1] . '');
                            $sheet->setCellValueByColumnAndRow(3, 5 + $n, '' . $date_b . '');
                            $sheet->setCellValueByColumnAndRow(4, 5 + $n, '' . number_format($raz, 2) . '');
                            $sheet->setCellValueByColumnAndRow(5, 5 + $n, '' . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . '');


                            $n++;
                            break;
                        }
                    }
                }

                $summ = 0;
                $kol = 0;
                $kol++;
                $summ+=$arr_value[$z];
                //echo " name = " . $arr_name[$z] . "param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
            }
        }
        if ($resusl_archive[1] != $arr_param[$z - 1]) {
            $sred = $summ / $kol;

            for ($i = 1; $i < $kol; $i++) {
                if ($arr_param[$z - 1] == 1 or $arr_param[$z - 1] == 308 or $arr_param[$z - 1] == 310 or $arr_param[$z - 1] == 414 or $arr_param[$z - 1] == 420 or $arr_param[$z - 1] == 436 or $arr_param[$z - 1] == 787 or $arr_param[$z - 1] == 2 or $arr_param[$z - 1] == 44 or $arr_param[$z - 1] == 377 or $arr_param[$z - 1] == 442 or $arr_param[$z - 1] == 402 or $arr_param[$z - 1] == 408 or $arr_param[$z - 1] == 922) {

                    $raz = $arr_value[$z - $i] - $arr_value[$z - $i - 1];
                    if ($raz > $kor_value) {
                        $sql_addres = pg_query('SELECT DISTINCT 
                                                                        "Tepl"."PropPlc_cnt"."ValueProp",
                                                                        "PropPlc_cnt1"."ValueProp"
                                                                      FROM
                                                                        "Tepl"."Places_cnt"
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                                      WHERE
                                                                        "Tepl"."Places_cnt".plc_id = ' . $arr_id[$z - 1] . ' AND 
                                                                        "PropPlc_cnt1".prop_id = 26 AND 
                                                                        "Tepl"."PropPlc_cnt".prop_id = 27');
                        $res = pg_fetch_row($sql_addres);
                        $d++;
                        $date_arch = explode(" ", $arr_date[$z - $i]);
                        $date_b = date("d.m.Y", strtotime($date_arch[0]));

                        $sheet->setCellValueByColumnAndRow(0, 5 + $n, '' . $d . '');
                        $sheet->setCellValueByColumnAndRow(1, 5 + $n, '' . $arr_name[$z - 1] . '');
                        $sheet->setCellValueByColumnAndRow(2, 5 + $n, '' . $res[0] . " " . $res[1] . '');
                        $sheet->setCellValueByColumnAndRow(3, 5 + $n, '' . $date_b . '');
                        $sheet->setCellValueByColumnAndRow(4, 5 + $n, '' . number_format($raz, 2) . '');
                        $sheet->setCellValueByColumnAndRow(5, 5 + $n, '' . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . '');

                        $n++;
                        break;
                    }
                }
            }
            $summ = 0;
            $kol = 0;
            $kol++;
            $summ+=$arr_value[$z];
            //echo "kol = ". $kol. " name = " . $arr_name[$z] . "param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
        }
    } else {
        if ($resusl_archive[1] == $arr_param[$z]) {
            $summ+=$arr_value[$z];
            $kol++;
            //echo "kol = ". $kol. " name = " . $arr_name[$z] . "param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
        }
    }

    $z++;
}

$n=$n+4;
$sheet->getStyle("A3:F" . $n . "")->applyFromArray($arrBorderStyle);



$sheet->getColumnDimension('A')->setWidth(8.57);
$sheet->getColumnDimension('B')->setWidth(60);
$sheet->getColumnDimension('C')->setWidth(40);
$sheet->getColumnDimension('D')->setWidth(16.29);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(30.71);

header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Корректировка ХВС.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
