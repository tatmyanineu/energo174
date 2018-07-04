<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL & ~E_NOTICE);

include 'db_config.php';
session_start();
$id = $_GET['id'];
$user_name = $_GET['user_name'];
$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

$date1 = date('Y-m-d', strtotime('+1 day', strtotime($date1)));
$date2 = date('Y-m-d', strtotime('+1 day', strtotime($date2)));


$sql_not_error = pg_query('SELECT DISTINCT 
  public.alarm.plc_id,
  public.alarm.text_alarm
FROM
  public.alarm
WHERE
  public.alarm.text_alarm LIKE \'%Поверка тепло%\' OR
  public.alarm.text_alarm LIKE \'%Интерф%\' OR
  public.alarm.text_alarm LIKE \'%Наводка%\' OR
  public.alarm.text_alarm LIKE \'%Технические работы%\' OR
  public.alarm.text_alarm LIKE \'%Заблокированная Sim-карта%\'');

while ($result = pg_fetch_row($sql_not_error)) {
    $not_error[] = array(
        'plc_id' => $result[0],
        'text' => $result[1]
    );
}



$sql_archive = pg_query('SELECT DISTINCT 
                        "Tepl"."ParamResPlc_cnt".plc_id,
                        "Tepl"."Arhiv_cnt"."DateValue",
                        "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                      FROM
                        "Tepl"."User_cnt"
                        INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
                        INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
                        INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
                        INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                      WHERE
                        "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                        "Tepl"."User_cnt".usr_id = ' . $id . ' AND
                        "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                        "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\'  
                      ORDER BY
                        "Tepl"."ParamResPlc_cnt".plc_id');




$sql_school = pg_query('SELECT DISTINCT 
                                                "Tepl"."Places_cnt"."Name",
                                                "Tepl"."Places_cnt".plc_id,
                                                "PropPlc_cnt1"."ValueProp",
                                                "Tepl"."PropPlc_cnt"."ValueProp",
                                                "Places_cnt1".plc_id,
                                                "Places_cnt1"."Name"
                                              FROM
                                                "Tepl"."GroupToUserRelations"
                                                INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                                INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                                INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                                INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
                                                INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                              WHERE
                                                "Tepl"."User_cnt".usr_id = ' . $id . ' AND
                                                "Tepl"."Places_cnt".typ_id = 17 AND 
                                                "Tepl"."PropPlc_cnt".prop_id = 26 AND 
                                                "PropPlc_cnt1".prop_id = 27');

while ($row_school = pg_fetch_row($sql_school)) {
    $array_school[] = array(
        'plc_id' => $row_school[1],
        'id_dist' => $row_school[4],
        'name' => $row_school[0],
        'addres' => '' . $row_school[2] . ' ' . $row_school[3] . '',
        'dist' => $row_school[5]
    );
}



while ($row_archive = pg_fetch_row($sql_archive)) {
    $array_archive[] = array(
        'plc_id' => $row_archive[0],
        'param_id' => $row_archive[2],
        'date' => $row_archive[1]
    );
}

for ($i = 0; $i < count($array_archive); $i++) {
    if ($array_archive[$i]['plc_id'] != $array_archive[$i + 1]['plc_id']) {
        $key = array_search($array_archive[$i]['plc_id'], array_column($array_school, 'plc_id'));
        if ($key !== false) {
            $array[] = array(
                'plc_id' => $array_archive[$i]['plc_id'],
                'id_dist' => $array_school[$key]['id_dist'],
                'dist' => $array_school[$key]['dist'],
                'name' => $array_school[$key]['name'],
                'addres' => $array_school[$key]['addres'],
                'date' => $array_archive[$i]['date']
            );
        }
    }
}

for ($i = 0; $i < count($array_school); $i++) {
    $key = array_search($array_school[$i]['plc_id'], array_column($array, 'plc_id'));
    if ($key === false) {
        //echo $array_school[$i]['name']." ".$array_school[$i]['addres']."<br>";
        $array[] = array(
            'plc_id' => $array_school[$i]['plc_id'],
            'id_dist' => $array_school[$i]['id_dist'],
            'dist' => $array_school[$i]['dist'],
            'name' => $array_school[$i]['name'],
            'addres' => $array_school[$i]['addres'],
            'date' => '1970-01-01'
        );
    }
}

$tmp2 = Array();

foreach ($array as &$ma) {
    $tmp2[] = &$ma["name"];
}




array_multisort($tmp2, $array);


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
$sheet->setTitle('Сопроводительная');



//стиль для ячеек которые будут заголовками
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



$sheet->getStyle('A1:F2')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:F2')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);



$sheet->setCellValue("A1", "Сопроводительный лист пользователя: " . $user_name . "");
$sheet->mergeCells("A1:C1");
$sheet->getStyle("A1:F2")->applyFromArray($FontStyle11TNR);


$sheet->getStyle('A3:F300')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A3:F300')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->setCellValue("A2", "№");
$sheet->setCellValue("B2", "Название");
$sheet->setCellValue("C2", "Адрес");
$sheet->setCellValue("D2", "Тех. работы");
$k = 0;
unset($key);

for ($i = 0; $i < count($array); $i++) {
    $k++;
    if (strtotime($array[$i]['date']) == strtotime('1970-01-01')) {
        $sheet->setCellValueByColumnAndRow(0, 3 + $i, "" . $k . "");
        $sheet->setCellValueByColumnAndRow(1, 3 + $i, "" . $array[$i]['name'] . "");
        $sheet->setCellValueByColumnAndRow(2, 3 + $i, "" . $array[$i]['addres'] . "");
        $sheet->setCellValueByColumnAndRow(3, 3 + $i, "Технические работы системы телеметрии");
    } else {
        $key = array_search($array[$i]['plc_id'], array_column($not_error, 'plc_id'));
        if ($key !== false) {
            $sheet->setCellValueByColumnAndRow(0, 3 + $i, "" . $k . "");
            $sheet->setCellValueByColumnAndRow(1, 3 + $i, "" . $array[$i]['name'] . "");
            $sheet->setCellValueByColumnAndRow(2, 3 + $i, "" . $array[$i]['addres'] . "");
            $sheet->setCellValueByColumnAndRow(3, 3 + $i, "" . $not_error[$key]['text'] . "");
        } else {
            $sheet->setCellValueByColumnAndRow(0, 3 + $i, "" . $k . "");
            $sheet->setCellValueByColumnAndRow(1, 3 + $i, "" . $array[$i]['name'] . "");
            $sheet->setCellValueByColumnAndRow(2, 3 + $i, "" . $array[$i]['addres'] . "");
            $sheet->setCellValueByColumnAndRow(3, 3 + $i, "");
        }
    }
}

$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(60.18);
$sheet->getColumnDimension('C')->setWidth(40.71);
$sheet->getColumnDimension('D')->setWidth(40.71);
$symb1 = 65;
$symb2 = 68;
$val = $i + 2;


$sheet->getStyle('' . chr($symb1) . '2:' . chr($symb2) . '' . $val)->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '2:' . chr($symb2) . '' . $val)->applyFromArray($FontStyle11TNRtext);
header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Сопроводительная_".$user_name."_".count($array_school)."_".date('d.m.Y').".xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
