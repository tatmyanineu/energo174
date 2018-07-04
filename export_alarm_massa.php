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
$pogr = $_GET['pogr'];

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


$sheet->setCellValue("A1", "Аварии: Аномалии подачи/обратки");
$sheet->MergeCells("A1:I1");
$sheet->getStyle("A1:I1")->applyFromArray($FontStyle14TNR);

$sheet->setCellValue("A2", "Проверка за период с " . $date1 . " по " . $date2 . "");
$sheet->MergeCells("A2:I2");
$sheet->getStyle("A2:I2")->applyFromArray($FontStyle14TNR);


$sheet->setCellValue("A3", "№");
$sheet->MergeCells("A3:A4");
$sheet->setCellValue("B3", "Учереждение");
$sheet->MergeCells("B3:B4");
$sheet->setCellValue("C3", "Дата");
$sheet->MergeCells("C3:C4");

$sheet->setCellValue("D3", "Масса");
$sheet->MergeCells("D3:F3");

$sheet->setCellValue("D4", "Подача (м1)");
$sheet->setCellValue("E4", "Обратка (м2)");
$sheet->setCellValue("F4", "Разность(%)");

$sheet->setCellValue("G3", "Температура");
$sheet->MergeCells("G3:I3");

$sheet->setCellValue("G4", "Подача (т1)");
$sheet->setCellValue("H4", "Обратка (т2)");
$sheet->setCellValue("I4", "Разность(%)");


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
                        "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                        "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' AND 
                        "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                        "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                        "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 5 OR 
                        "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                        "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                        "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' AND 
                        "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                        "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                        "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 6 OR 
                        "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                       "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                        "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' AND 
                        "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                        "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                        "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 12 OR 
                        "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                       "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                        "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' AND 
                        "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                        "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                        "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 19 OR 
                        "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                      "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                        "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' AND 
                        "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                        "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                        "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 20 OR 
                        "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                        "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                        "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' AND 
                        "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                        "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                        "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 21
                      ORDER BY
                        "Tepl"."Places_cnt".plc_id,
                        "Tepl"."Arhiv_cnt"."DateValue",
                        "Tepl"."ParamResPlc_cnt"."ParamRes_id"');

//echo pg_num_rows($sql_archive) . "<br>";
$z = 0;
$kol = 0;
$n = 0;
while ($resusl_archive = pg_fetch_row($sql_archive)) {
    $arr_id[$z] = $resusl_archive[2];
    $arr_name[$z] = $resusl_archive[4];
    $arr_param[$z] = $resusl_archive[1];
    $arr_value[$z] = $resusl_archive[3];
    $arr_date[$z] = $resusl_archive[0];

    if ($resusl_archive[2] == 39 or $resusl_archive[2] == 40 or $resusl_archive[2] == 54) {
        if ($z != 0) {
            $i++;
        }
    } else {

        if ($z != 0) {

            if ($arr_id[$z - 1] == $resusl_archive[2]) {
                if (strtotime($arr_date[$z - 1]) == strtotime($resusl_archive[0])) {
                    if ($arr_param[$z - 1] == 5 and $arr_param[$z] == 6) {
                        $temp = 0;

                        if ($arr_value[$z] > $arr_value[$z - 1]) {

                            $sql_massa = pg_query('SELECT DISTINCT
                                                      "Tepl"."Arhiv_cnt"."DataValue",
                                                      "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                                                      FROM
                                                      "Tepl"."Arhiv_cnt"
                                                      INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."Arhiv_cnt".pr_id = "Tepl"."ParamResPlc_cnt".prp_id)
                                                      WHERE
                                                      "Tepl"."Arhiv_cnt"."DateValue" = \'' . $arr_date[$z] . '\' AND
                                                      "Tepl"."ParamResPlc_cnt".plc_id = ' . $arr_id[$z] . ' AND
                                                      "Tepl"."Arhiv_cnt".typ_arh = 2 AND
                                                      "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 19 OR
                                                      "Tepl"."Arhiv_cnt"."DateValue" = \'' . $arr_date[$z] . '\'AND
                                                      "Tepl"."ParamResPlc_cnt".plc_id = ' . $arr_id[$z] . ' AND
                                                      "Tepl"."Arhiv_cnt".typ_arh = 2 AND
                                                      "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 20
                                                      ORDER BY
                                                      "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                                                      ');

                            if (pg_num_rows($sql_massa) > 1) {
                                $m++;

                                $array_temper[$m] = array(
                                    'id_object' => '' . $arr_id[$z] . '',
                                    'name' => '' . $arr_name[$z] . '',
                                    'date' => '' . $arr_date[$z] . '',
                                    'massa1' => '' . number_format(pg_fetch_result($sql_massa, 0, 0), 2) . '',
                                    'massa2' => '' . number_format(pg_fetch_result($sql_massa, 1, 0), 2) . '',
                                    'temper1' => '' . $arr_value[$z - 1] . '',
                                    'temper2' => '' . $arr_value[$z] . ''
                                );
                            }
                        }
                    }
                    if ($arr_param[$z - 1] == 19 and $arr_param[$z] == 20) {
                        if ($arr_value[$z - 1] == 0) {
                            $raz = ((str_replace(',', '.', $arr_value[$z]) / 0.00001) * 100) - 100;
                        } else {
                            $raz = ((str_replace(',', '.', $arr_value[$z]) / str_replace(',', '.', $arr_value[$z - 1])) * 100) - 100;
                        }
                        if ($raz > $pogr) {
                            $raz_temp = ((str_replace(',', '.', $arr_value[$z - 2]) / str_replace(',', '.', $arr_value[$z - 3])) * 100) - 100;
                            $n++;

                            $array_name_massa[$n] = $arr_id[$z];

                            $array_massa[$n] = array(
                                'id_object' => '' . $arr_id[$z] . '',
                                'name' => '' . $arr_name[$z] . '',
                                'date' => '' . $arr_date[$z] . '',
                                'massa1' => '' . $arr_value[$z - 1] . '',
                                'massa2' => '' . $arr_value[$z] . '',
                                'temper1' => '' . $arr_value[$z - 3] . '',
                                'temper2' => '' . $arr_value[$z - 2] . ''
                            );
                        }
                    }
                }
            }
        }
        $z++;
    }
}



$array_massa = array_merge($array_massa, $array_temper);

$tmp1 = Array();
foreach ($array_massa as &$ma) {
    $tmp1[] = &$ma["name"];
}
array_multisort($tmp1, $array_massa);

$n=0;
for ($i = 0; $i < count($array_massa); $i++) {
    if ($array_massa[$i]['name'] == $array_massa[$i + 1]['name'] and strtotime($array_massa[$i]['date']) != strtotime($array_massa[$i + 1]['date'])) {
        $kol++;
    }

    if ($array_massa[$i]['name'] != $array_massa[$i + 1]['name']) {
        if ($kol > 1) {
            $f++;
            $kol++;

            for ($j = 0; $j < count($array_massa); $j++) {
                if ($array_massa[$i]['name'] == $array_massa[$j]['name'] and strtotime($array_massa[$j]['date']) != strtotime($array_massa[$j + 1]['date'])) {
                    //echo "<tr><td></td> <td>" . $array_massa[$j]['name'] . "</td><td>" . date("d.m.Y", strtotime($array_massa[$j]['date'])) . "</td><td>" . $array_massa[$j]['massa1'] . " </td> <td>" . $array_massa[$i]['massa2'] . "</td</tr>";
                    $raznost_mass = 0;
                    $raznost_temp = 0;
                    $raz_massa = 0;
                    $raznost_temp = ((str_replace(',', '.', $array_massa[$j]['temper2']) / str_replace(',', '.', $array_massa[$j]['temper1'])) * 100) - 100;
                    if ($array_massa[$j]['temper1'] < $array_massa[$j]['temper2']) {
                        $raznost_temp = "" . number_format($raznost_temp, 3) . "";
                    } else {
                        $raznost_temp = " - ";
                    }


                    $raznost_mass = ((str_replace(',', '.', $array_massa[$j]['massa2']) / str_replace(',', '.', $array_massa[$j]['massa1'])) * 100) - 100;

                    if ($array_massa[$j]['massa1'] != 0) {
                        if ($raznost_mass > $pogr) {
                            $raz_massa = "" . number_format($raznost_mass, 3) . "";
                        } else {
                            $raz_massa = " - ";
                        }
                    } else {
                        if ($array_massa[$j]['massa2'] != 0) {
                            $raznost_mass = $array_massa[$j]['massa2'] * 100;
                            $raz_massa = "" . number_format($raznost_mass, 3) . "";
                        } elseif ($array_massa[$j]['massa2'] == 0 and $array_massa[$j]['massa1'] == 0) {
                            $raz_massa = " - ";
                        }
                    }

                    $sheet->setCellValueByColumnAndRow(0, 5 + $n, '' . $f . '');
                    $sheet->setCellValueByColumnAndRow(1, 5 + $n, '' . $array_massa[$j]['name'] . '');
                    $sheet->setCellValueByColumnAndRow(2, 5 + $n, '' . date("d.m.Y", strtotime("-1 day", strtotime($array_massa[$j]['date']))) . '');
                    $sheet->setCellValueByColumnAndRow(3, 5 + $n, '' . number_format($array_massa[$j]['massa1'], 3) . '');
                    $sheet->setCellValueByColumnAndRow(4, 5 + $n, '' . number_format($array_massa[$j]['massa2'], 3) . '');
                    $sheet->setCellValueByColumnAndRow(5, 5 + $n, '' . $raz_massa . '');
                    $sheet->setCellValueByColumnAndRow(6, 5 + $n, '' . number_format($array_massa[$j]['temper1'], 3) . '');
                    $sheet->setCellValueByColumnAndRow(7, 5 + $n, '' . number_format($array_massa[$j]['temper2'], 3) . '');
                    $sheet->setCellValueByColumnAndRow(8, 5 + $n, '' . $raznost_temp . '');
                    $n++;
                }
            }
        } else {
            $f++;
            $raznost_mass = 0;
            $raznost_temp = 0;
            $raz_massa = 0;
            $raznost_temp = ((str_replace(',', '.', $array_massa[$i]['temper2']) / str_replace(',', '.', $array_massa[$i]['temper1'])) * 100) - 100;
            if ($array_massa[$i]['temper1'] < $array_massa[$i]['temper2']) {
                $raznost_temp = "" . number_format($raznost_temp, 3) . "";
            } else {
                $raznost_temp = " - ";
            }


            $raznost_mass = ((str_replace(',', '.', $array_massa[$i]['massa2']) / str_replace(',', '.', $array_massa[$i]['massa1'])) * 100) - 100;

            if ($array_massa[$i]['massa1'] != 0) {
                if ($raznost_mass > $pogr) {
                    $raz_massa = "" . number_format($raznost_mass, 3) . "";
                } else {
                    $raz_massa = " - ";
                }
            } else {
                if ($array_massa[$i]['massa2'] != 0) {
                    $raznost_mass = $array_massa[$i]['massa2'] * 100;
                    $raz_massa = "" . number_format($raznost_mass, 3) . "";
                } elseif ($array_massa[$i]['massa2'] == 0 and $array_massa[$i]['massa1'] == 0) {
                    $raz_massa = " - ";
                }
            }
            $sheet->setCellValueByColumnAndRow(0, 5 + $n, '' . $f . '');
            $sheet->setCellValueByColumnAndRow(1, 5 + $n, '' . $array_massa[$i]['name'] . '');
            $sheet->setCellValueByColumnAndRow(2, 5 + $n, '' . date("d.m.Y", strtotime("-1 day", strtotime($array_massa[$ji]['date']))) . '');
            $sheet->setCellValueByColumnAndRow(3, 5 + $n, '' . number_format($array_massa[$i]['massa1'], 3) . '');
            $sheet->setCellValueByColumnAndRow(4, 5 + $n, '' . number_format($array_massa[$i]['massa2'], 3) . '');
            $sheet->setCellValueByColumnAndRow(5, 5 + $n, '' . $raz_massa . '');
            $sheet->setCellValueByColumnAndRow(6, 5 + $n, '' . number_format($array_massa[$i]['temper1'], 3) . '');
            $sheet->setCellValueByColumnAndRow(7, 5 + $n, '' . number_format($array_massa[$i]['temper2'], 3) . '');
            $sheet->setCellValueByColumnAndRow(8, 5 + $n, '' . $raznost_temp . '');
            $n++;
            
        }
        $kol = 0;
    }
}

$n=$n+4;
$sheet->getStyle("A3:I" . $n . "")->applyFromArray($arrBorderStyle);
$sheet->getStyle("A3:I" . $n . "")->applyFromArray($FontStyle11TNRtext);


$sheet->getColumnDimension('A')->setWidth(8.57);
$sheet->getColumnDimension('B')->setWidth(60);
$sheet->getColumnDimension('C')->setWidth(12);
$sheet->getColumnDimension('D')->setWidth(13);
$sheet->getColumnDimension('E')->setWidth(13);
$sheet->getColumnDimension('F')->setWidth(13);
$sheet->getColumnDimension('G')->setWidth(13);
$sheet->getColumnDimension('H')->setWidth(13);
$sheet->getColumnDimension('I')->setWidth(13);
header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Аномалии теплоносителя.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
