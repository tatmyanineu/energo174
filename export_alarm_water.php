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




$sheet->getStyle('A1:N700')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:N700')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);



$sheet->setCellValue("A1", "Аварии: Нет данных по ХВС");
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


$sql_param_water = pg_query('SELECT DISTINCT 
                            "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                            "Tepl"."Places_cnt".plc_id,
                            "Tepl"."Places_cnt"."Name",
                            "Tepl"."ParametrResourse"."Name",
                            "Tepl"."Resourse_cnt"."Name"
                          FROM
                            "Tepl"."ParamResPlc_cnt"
                            INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                            INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                            INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                            INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                            INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
                            INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                          WHERE
                            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' 
                          ORDER BY
                            "Tepl"."Places_cnt".plc_id,
                            "Tepl"."ParamResPlc_cnt"."ParamRes_id"');
$water_param_id = '';
while ($result_water_param = pg_fetch_row($sql_param_water)) {
    if ($result_water_param[0] == 1 or $result_water_param[0] == 308 or $result_water_param[0] == 310
            or $result_water_param[0] == 414 or $result_water_param[0] == 420 or $result_water_param[0] == 436
            or $result_water_param[0] == 787 or $result_water_param[0] == 2 or $result_water_param[0] == 44
            or $result_water_param[0] == 377 or $result_water_param[0] == 442 or $result_water_param[0] == 402
            or $result_water_param[0] == 408 or $result_water_param[0] == 922) {
        $water_param_id[] = $result_water_param[1];
        $water_param_res[] = $result_water_param[0];
        $water_param_name[] = $result_water_param[2];
        $water_param_type[] = $result_water_param[3];
        $water_param_type_name[] = $result_water_param[4];
    }
}



$sql_water = pg_query('SELECT DISTINCT 
                   ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                    "Tepl"."Places_cnt".plc_id,
                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
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
                    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' 
                  ORDER BY
                    "Tepl"."Places_cnt".plc_id,
                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                    "Tepl"."Arhiv_cnt"."DateValue"');
$water_date = '';
$water_id = '';
$water_res = '';
$z = 0;
$n = 0;
while ($result_water = pg_fetch_row($sql_water)) {

    if ($result_water[2] == 1 or $result_water[2] == 308 or $result_water[2] == 310
            or $result_water[2] == 414 or $result_water[2] == 420 or $result_water[2] == 436
            or $result_water[2] == 787 or $result_water[2] == 2 or $result_water[2] == 44
            or $result_water[2] == 377 or $result_water[2] == 442 or $result_water[2] == 402
            or $result_water[2] == 408 or $result_water[2] == 922) {
        $water_date[] = $result_water[0];
        $water_id[] = $result_water[1];
        $water_res[] = $result_water[2];
        $water_name[] = $result_water[3];
    }
}

$n = 0;

for ($j = 0; $j < count($water_param_id); $j++) {
    $resuours = 0;
    for ($i = 0; $i < count($water_id); $i++) {

        if ($water_id[$i] == $water_param_id[$j]) {
            $name = $water_name[$i];
            $res = $water_param_res[$j];
            if ($water_res[$i] == $water_param_res[$j]) {

                if ($water_id[$i] != $water_id[$i + 1] or $water_res[$i] != $water_res[$i + 1] or count($water_res) == $i + 1) {
                    $resuours = 1;
                    if (strtotime($water_date[$i]) != strtotime($date2)) {
                        $n++;
                        $sql_addres = pg_query('SELECT DISTINCT 
                                                                        "Tepl"."PropPlc_cnt"."ValueProp",
                                                                        "PropPlc_cnt1"."ValueProp"
                                                                      FROM
                                                                        "Tepl"."Places_cnt"
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                                      WHERE
                                                                        "Tepl"."Places_cnt".plc_id = ' . $water_id[$i] . ' AND 
                                                                        "PropPlc_cnt1".prop_id = 26 AND 
                                                                        "Tepl"."PropPlc_cnt".prop_id = 27');
                        $res = pg_fetch_row($sql_addres);

                        $sheet->setCellValueByColumnAndRow(0, 5 + $n-1, '' . $n . '');
                        $sheet->setCellValueByColumnAndRow(1, 5 + $n-1, '' . $water_name[$i] . '');
                        $sheet->setCellValueByColumnAndRow(2, 5 + $n-1, '' . $res[0] . ' ' . $res[1] . '');
                        $sheet->setCellValueByColumnAndRow(3, 5 + $n-1, '' . date('d.m.Y', strtotime($water_date[$i])). '');
                        $sheet->setCellValueByColumnAndRow(4, 5 + $n-1, 'Отсутствует:' . $water_param_type_name[$j] . ' ' . $water_param_type[$j].'');


                        $_SESSION['arr_id'][] = $n;
                        $_SESSION['arr_name'][] = $water_name[$i];
                        $_SESSION['arr_addr'][] = $res[0] . ' ' . $res[1];
                        $_SESSION['arr_date'][] = date('d.m.Y', strtotime($water_date[$i]));
                        $_SESSION['arr_stat'][] = 'Отсутствует:' . $water_param_type_name[$j] . ' ' . $water_param_type[$j];
                        $_SESSION['arr_plc_id'][] = $water_id[$i];
                        // echo $water_id[$i] . " " . $water_name[$i] . " " . $water_date[$i] . " " . $water_res[$i] . "<br>";
                    }
                }
            }
        }
    }
    if ($resuours == 0) {
        $n++;
        $sql_addres = pg_query('SELECT DISTINCT 
                                                                        "Tepl"."PropPlc_cnt"."ValueProp",
                                                                        "PropPlc_cnt1"."ValueProp"
                                                                      FROM
                                                                        "Tepl"."Places_cnt"
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                                      WHERE
                                                                        "Tepl"."Places_cnt".plc_id = ' . $water_param_id[$j] . ' AND 
                                                                        "PropPlc_cnt1".prop_id = 26 AND 
                                                                        "Tepl"."PropPlc_cnt".prop_id = 27');
        $res = pg_fetch_row($sql_addres);

        $sheet->setCellValueByColumnAndRow(0, 5 + $n-1, '' . $n . '');
        $sheet->setCellValueByColumnAndRow(1, 5 + $n-1, '' .  $water_param_name[$j] . '');
        $sheet->setCellValueByColumnAndRow(2, 5 + $n-1, '' .$res[0] . ' ' . $res[1]. '');
        $sheet->setCellValueByColumnAndRow(3, 5 + $n-1, 'Нет данных');
        $sheet->setCellValueByColumnAndRow(4, 5 + $n-1, 'Отсутствует:  ' . $water_param_type_name[$j] . ' ' . $water_param_type[$j].'');


        $_SESSION['arr_id'][] = $n;
        $_SESSION['arr_name'][] = $water_param_name[$j];
        $_SESSION['arr_addr'][] = $res[0] . ' ' . $res[1];
        $_SESSION['arr_date'][] = 'Нет данных';
        $_SESSION['arr_stat'][] = 'Отсутствует:  ' . $water_param_type_name[$j] . ' ' . $water_param_type[$j];
        $_SESSION['arr_plc_id'][] = $water_param_id[$j];
        // echo $water_param_id[$j] . " " . $water_param_name[$j] . " нет параметра " .  $water_param_type[$j] . " ".$water_param_type_name[$j]."<br>";
    }
}
$n=$n+4;
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
header("Content-Disposition: attachment; filename=Нет данных по ХВС.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
