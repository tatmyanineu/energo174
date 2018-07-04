<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL & ~E_NOTICE);

include 'db_config.php';
session_start();
$type_arch = 2;
$id_object = $_GET['id_object'];
$id = $_GET['id'];
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
$sheet->setTitle('Архив');


/*
 * 
 * Стили ячеек
 *  
 */

$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$sheet->getPageSetup()->setFitToPage(true);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);

$sheet->getStyle('A1:Z7')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A1:Z7')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);


switch (date("m", strtotime($date2))) {
    case "01":$limit_month = 29;
        $month_name = "ЯНВАРЬ";
        break;

    case "02":$limit_month = 30;
        $month_name = "ФЕВРАЛЬ";
        break;

    case "03":$limit_month = 31;
        $month_name = "МАРТ";
        break;

    case "04":$limit_month = 32;
        $month_name = "АПРЕЛЬ";
        break;

    case "05":$limit_month = 33;
        $month_name = "МАЙ";
        break;

    case "06":$limit_month = 34;
        $month_name = "ИЮНЬ";
        break;

    case "07":$limit_month = 35;
        $month_name = "ИЮЛЬ";
        break;

    case "08":$limit_month = 36;
        $month_name = "АВГУСТ";
        break;

    case "09":$limit_month = 37;
        $month_name = "СЕНТЯБРЬ";
        break;

    case "10":$limit_month = 38;
        $month_name = "ОКТЯБРЬ";
        break;

    case "11":$limit_month = 39;
        $month_name = "НОЯБРЬ";
        break;

    case "12":$limit_month = 40;
        $month_name = "ДЕКАБРЬ";
        break;
}



//стиль для ячеек которые будут заголовками
$FontStyle11TNR = array(
    'font' => array(
        'bold' => true,
        'size' => 14,
        'name' => 'Times New Roman'
        ));

//стиль для ячеек с простым текстом
$FontStyle11TNRtext = array(
    'font' => array(
        'size' => 14,
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


$sheet->getStyle('A8:Z70')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A8:Z70')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);
/*
 *  Стили ячеек
 */
$sql_kol_vvod = pg_query('SELECT DISTINCT 
  "Tepl"."ParamResPlc_cnt"."NameGroup"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
  "Tepl"."ParamResPlc_cnt"."NameGroup" LIKE \'%Тр%\'');


$sql_resurse = pg_query('SELECT DISTINCT 
  ("Tepl"."ParametrResourse"."Name") AS "FIELD_1",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
 "Tepl"."User_cnt".usr_id = ' . $id . '
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup"');

$name_row = array("name", "d", "gmin", "gmax");
if (($handle = fopen("ajax/sens.csv", "r")) !== FALSE) {
    # Set the parent multidimensional array key to 0.
    $nn = 0;
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        # Count the total keys in the row.
        $c = count($data);
        # Populate the multidimensional array.
        for ($x = 0; $x < $c; $x++) {
            $csvarray[$nn][$name_row[$x]] = $data[$x];
        }
        $nn++;
    }
    # Close the File.
    fclose($handle);
}



$sheet->setCellValue("A1", "Договор ТСН:");
$sheet->mergeCells("A1:B1");
$sheet->setCellValue("A2", "Обьект:");
$sheet->mergeCells("A2:B2");
$sheet->setCellValue("A3", "Адрес:");
$sheet->mergeCells("A3:B3");
$sheet->setCellValue("A4", "Договорная нагрузка Отопление (ГКал/ч):");
$sheet->getStyle("A4")->getAlignment()->setWrapText(true);
$sheet->mergeCells("A4:B4");
$sheet->setCellValue("A5", "Договорная нагрузка ГВС (ГКал/ч):");
$sheet->getStyle("A5")->getAlignment()->setWrapText(true);
$sheet->mergeCells("A5:B5");
$sheet->setCellValue("A6", "Договорная нагрузка Вентиляция (ГКал/ч):");
$sheet->getStyle("A6")->getAlignment()->setWrapText(true);
$sheet->mergeCells("A6:B6");
$sheet->setCellValue("A7", "Договорной расход(м3/ч):");
$sheet->mergeCells("A7:B7");
$sheet->getStyle("A1:B7")->applyFromArray($FontStyle11TNR);
$sheet->getColumnDimension('A')->setWidth(25);
$sheet->getColumnDimension('B')->setWidth(20);

for ($i = 4; $i < 7; $i++) {
    $sheet->getRowDimension($i)->setRowHeight(35);
}

$sql_objinfo = pg_query('SELECT 
  "Tepl"."Places_cnt"."Name",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."Places_cnt"
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 26 AND 
  "PropPlc_cnt1".prop_id = 27 AND 
  "Tepl"."Places_cnt".plc_id = ' . $id_object . '');

$info_obj = pg_fetch_row($sql_objinfo);


$sql_prop_42 = pg_query('SELECT DISTINCT 
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."Places_cnt"
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
WHERE
  "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND
   "Tepl"."PropPlc_cnt".prop_id = 42');
$prop_42 = pg_fetch_row($sql_prop_42);
if ($prop_42) {
    $sheet->setCellValue("C4", "" . $prop_42[0] . "");
}
$sql_prop_43 = pg_query('SELECT DISTINCT 
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."Places_cnt"
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
WHERE
  "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND
   "Tepl"."PropPlc_cnt".prop_id = 43');
$prop_43 = pg_fetch_row($sql_prop_43);
if ($prop_43) {
    $sheet->setCellValue("C1", "" . $prop_43[0] . "");
}
$sql_prop_44 = pg_query('SELECT DISTINCT 
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."Places_cnt"
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
WHERE
  "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND
   "Tepl"."PropPlc_cnt".prop_id = 44');
$prop_44 = pg_fetch_row($sql_prop_44);
if ($prop_44) {
    $sheet->setCellValue("C7", "" . $prop_44[0] . "");
}
$sql_prop_45 = pg_query('SELECT DISTINCT 
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."Places_cnt"
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
WHERE
  "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND
   "Tepl"."PropPlc_cnt".prop_id = 45');
$prop_45 = pg_fetch_row($sql_prop_45);
if ($prop_45) {
    $sheet->setCellValue("C6", "" . $prop_45[0] . "");
}

$sql_prop_46 = pg_query('SELECT DISTINCT 
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."Places_cnt"
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
WHERE
  "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND
   "Tepl"."PropPlc_cnt".prop_id = 46');
$prop_46 = pg_fetch_row($sql_prop_46);
if ($prop_46) {
    $sheet->setCellValue("C5", "" . $prop_46[0] . "");
}


$sheet->setCellValue("C2", "" . $info_obj[0] . "");
$sheet->mergeCells("C2:D2");
$sheet->setCellValue("C3", "ул. " . $info_obj[1] . " д. " . $info_obj[2] . "");
$sheet->mergeCells("C3:D3");
$sheet->getColumnDimension('C')->setWidth(20.18);
$sheet->getColumnDimension('D')->setWidth(18.71);

$sheet->getStyle("C1:C7")->applyFromArray($FontStyle11TNRtext);



$sheet->setCellValue("A9", "Приборы учета тепловой энергии");
$sheet->mergeCells("A9:H9");
$sheet->getStyle("A9:H9")->applyFromArray($FontStyle14TNR);


//$sheet->setCellValue("A10", "Ресурс");
$sheet->setCellValue("A10", "Тип прибора");
$sheet->setCellValue("B10", "Наименование");
$sheet->setCellValue("C10", "Зав. №");
$sheet->setCellValue("D10", "Дата ближайшей поверки");
$sheet->getStyle("D10")->getAlignment()->setWrapText(true);
$sheet->setCellValue("E10", "Ду (мм)");
$sheet->setCellValue("F10", "Gmin (м.куб./ч)");
$sheet->setCellValue("G10", "Gmax (м.куб./ч)");
$sheet->getStyle("A10:G10")->applyFromArray($FontStyle11TNR);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(20);
$sheet->getColumnDimension('H')->setWidth(16.86);
$sheet->getStyle('A10:G10')->applyFromArray($arrBorderStyle);

$sheet->getColumnDimension('I')->setWidth(16);
$sheet->getColumnDimension('J')->setWidth(16);
$sheet->getColumnDimension('K')->setWidth(16);


$water_cold = 0;
$water_hot = 0;
$warm = 0;

$sql_device = pg_query('SELECT 
  "Tepl"."TypeDevices"."Name",
  "Tepl"."Device_cnt".dev_typ_id,
  "Tepl"."Device_cnt"."Comment"
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
  INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
WHERE
  "Places_cnt1".plc_id = ' . $id_object . '
ORDER BY
  "Tepl"."TypeDevices"."Name"');
while ($result_devise = pg_fetch_row($sql_device)) {
    if ($result_devise[1] == 175 or $result_devise[1] == 229 or $result_devise[1] == 217 or $result_devise[1] == 116 or $result_devise[1] == 214) {
        $warm++;
        $dev_name[] = $result_devise[0];
        $dev_typ_id[] = $result_devise[1];
        $dev_comment[] = $result_devise[2];
    }
}

if ($dev_typ_id[0] != 175) {
    $sql_sens = pg_query('SELECT 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."TypeSensor"."Name",
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."ParamResPlc_cnt"."Comment",
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Resourse_cnt"."Name"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  LEFT OUTER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Sensor_cnt".prp_id)
  LEFT OUTER JOIN "Tepl"."TypeSensor" ON ("Tepl"."Sensor_cnt".sen_id = "Tepl"."TypeSensor".sen_id)
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
WHERE
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" =19 AND 
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' OR
    "Tepl"."ParamResPlc_cnt"."ParamRes_id" =20 AND 
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . 'OR
    "Tepl"."ParamResPlc_cnt"."ParamRes_id" =21 AND 
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . '
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."ParametrResourse"."Name"');
    while ($result_sens = pg_fetch_row($sql_sens)) {
        if ($result_sens[5] == "Тепло") {
            $warm++;
        }
        $sens_res_id[] = $result_sens[0];
        $sens_name[] = $result_sens[1];
        $sens_res_name[] = $result_sens[2];
        $sens_resours[] = $result_sens[5];
        $sens_id[] = $result_sens[4];
        $sens_comm[] = $result_sens[3];
    }
} else {
    $sql_sens = pg_query('SELECT 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."TypeSensor"."Name",
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."ParamResPlc_cnt"."Comment",
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Resourse_cnt"."Name"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  LEFT OUTER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Sensor_cnt".prp_id)
  LEFT OUTER JOIN "Tepl"."TypeSensor" ON ("Tepl"."Sensor_cnt".sen_id = "Tepl"."TypeSensor".sen_id)
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 3 OR 
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 4
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."ParametrResourse"."Name"');
    while ($result_sens = pg_fetch_row($sql_sens)) {
        if ($result_sens[5] == "Тепло") {
            $warm++;
        }
        $sens_res_id[] = $result_sens[0];
        $sens_name[] = $result_sens[1];
        $sens_res_name[] = $result_sens[2];
        $sens_resours[] = $result_sens[5];
        $sens_id[] = $result_sens[4];
        $sens_comm[] = $result_sens[3];
    }
}

$excel_row = 0;
$excel_Col = 0;

$colum_text = 11;
for ($i = 0; $i < count($dev_typ_id); $i++) {

    $sheet->setCellValueByColumnAndRow(0, $colum_text, "Тепловычислитель");
    $sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $dev_name[$i] . "");
    $dev_type_id = $dev_typ_id[$i];
    $sql_dev_prop = pg_query('SELECT
        "Tepl"."Device_Property"."Propert_Value"
        FROM
        "Tepl"."Places_cnt" "Places_cnt1"
        INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
        INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
        INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
        INNER JOIN "Tepl"."Device_Property" ON ("Tepl"."Device_cnt".dev_id = "Tepl"."Device_Property".dev_id)
        WHERE
        "Places_cnt1".plc_id = ' . $id_object . ' AND
        "Tepl"."Device_Property".id_type_property = 0 AND
        "Tepl"."Device_cnt".dev_typ_id = ' . $dev_typ_id[$i] . '
        ORDER BY
        "Tepl"."TypeDevices"."Name",
        "Tepl"."Device_Property".id_type_property');

    if (pg_num_rows($sql_dev_prop) != 0) {
        $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . pg_fetch_result($sql_dev_prop, 0, 0) . "");
    } else {
        $sheet->setCellValueByColumnAndRow(2, $colum_text, "-");
    }

    $sql_dev_prop = pg_query('SELECT
        "Tepl"."Device_Property"."Propert_Value"
        FROM
        "Tepl"."Places_cnt" "Places_cnt1"
        INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
        INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
        INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
        INNER JOIN "Tepl"."Device_Property" ON ("Tepl"."Device_cnt".dev_id = "Tepl"."Device_Property".dev_id)
        WHERE
        "Places_cnt1".plc_id = ' . $id_object . ' AND
        "Tepl"."Device_Property".id_type_property = 2 AND
        "Tepl"."Device_cnt".dev_typ_id = ' . $dev_typ_id[$i] . '
        ORDER BY
        "Tepl"."TypeDevices"."Name",
        "Tepl"."Device_Property".id_type_property');


    if (pg_num_rows($sql_dev_prop) != 0) {
        if (pg_fetch_result($sql_dev_prop, 0, 0) != '01/01/0001 00:00:00') {
            $sheet->setCellValueByColumnAndRow(3, $colum_text, "" . date("d.m.Y", strtotime(pg_fetch_result($sql_dev_prop, 0, 0))) . "");
        } else {
            $sheet->setCellValueByColumnAndRow(3, $colum_text, "-");
        }
    } else {
        $sheet->setCellValueByColumnAndRow(3, $colum_text, "-");
    }


    $sheet->setCellValueByColumnAndRow(4, $colum_text, "-");
    $sheet->setCellValueByColumnAndRow(5, $colum_text, "-");
    $sheet->setCellValueByColumnAndRow(6, $colum_text, "-");
    $sxema = $dev_comment[$i];
    $colum_text++;
}
$coord = 11 + $warm;
$sheet->getStyle("A11:G" . $coord . "")->applyFromArray($arrBorderStyle);
$sheet->getStyle("A11:G" . $coord . "")->applyFromArray($FontStyle11TNRtext);

$form = 0;
for ($i = 0; $i < count($sens_id); $i++) {
    if ($sens_resours[$i] == "Тепло") {


        if (pg_num_rows($sql_kol_vvod) >= 3) {
            $diametr = "";
            $gmin = "";
            $gmax = "";
            if ($sens_res_id[$i] == 19) {
                $form = $colum_text;
                $sheet->setCellValueByColumnAndRow(0, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . "");

                for ($s = 0; $s < count($csvarray); $s++) {
                    $massiv = explode(";", $sens_comm[$i]);
                    $diametr = $massiv[0];
                    //          $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $massiv[0] . "");
                    if ($sens_name[$i] == $csvarray[$s][name] and $csvarray[$s][d] == $diametr) {
                        //$sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . " ".$csvarray[$s][gmin]. " ".$csvarray[$s][gmax]. "");
                        $gmin = $csvarray[$s][gmin];
                        $gmax = $csvarray[$s][gmax];
                    }
                }
            }
            if ($sens_res_id[$i] == 20) {
                $form = $colum_text;
                $sheet->setCellValueByColumnAndRow(0, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . "");

                for ($s = 0; $s < count($csvarray); $s++) {
                    $massiv = explode(";", $sens_comm[$i]);
                    $diametr = $massiv[0];
                    //          $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $massiv[0] . "");
                    if ($sens_name[$i] == $csvarray[$s][name] and $csvarray[$s][d] == $diametr) {
                        //$sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . " ".$csvarray[$s][gmin]. " ".$csvarray[$s][gmax]. "");
                        $gmin = $csvarray[$s][gmin];
                        $gmax = $csvarray[$s][gmax];
                    }
                }
            }
            if ($sens_res_id[$i] == 21) {
                $sheet->setCellValueByColumnAndRow(0, $colum_text, "Расходомер обратка");
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . "");
                $form = $colum_text + 1;
                $sheet->setCellValueByColumnAndRow(0, $form, "Формула расчета тепловой энергии: M1*(h1-h2) " . $sxema);
                $sheet->mergeCellsByColumnAndRow(0, $form, 6, $form);

                for ($s = 0; $s < count($csvarray); $s++) {
                    $massiv = explode(";", $sens_comm[$i]);
                    $diametr = $massiv[0];
                    //          $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $massiv[0] . "");
                    if ($sens_name[$i] == $csvarray[$s][name] and $csvarray[$s][d] == $diametr) {
                        //$sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . " ".$csvarray[$s][gmin]. " ".$csvarray[$s][gmax]. "");
                        $gmin = $csvarray[$s][gmin];
                        $gmax = $csvarray[$s][gmax];
                    }
                }
            }
        } elseif (pg_num_rows($sql_kol_vvod) == 2) {
            $diametr = "";
            $gmin = "";
            $gmax = "";
            if ($sens_res_id[$i] == 19 or $sens_res_id[$i] == 3) {
                $form = $colum_text;
                $sheet->setCellValueByColumnAndRow(0, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . "");

                for ($s = 0; $s < count($csvarray); $s++) {
                    $massiv = explode(";", $sens_comm[$i]);
                    $diametr = $massiv[0];
                    //          $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $massiv[0] . "");
                    if ($sens_name[$i] == $csvarray[$s][name] and $csvarray[$s][d] == $diametr) {
                        //$sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . " ".$csvarray[$s][gmin]. " ".$csvarray[$s][gmax]. "");
                        $gmin = $csvarray[$s][gmin];
                        $gmax = $csvarray[$s][gmax];
                    }
                }
            }
            if ($sens_res_id[$i] == 20 or $sens_res_id[$i] == 4) {
                $sheet->setCellValueByColumnAndRow(0, $colum_text, "Расходомер обратка");
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . "");

                for ($s = 0; $s < count($csvarray); $s++) {
                    $massiv = explode(";", $sens_comm[$i]);
                    $diametr = $massiv[0];
                    //          $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $massiv[0] . "");
                    if ($sens_name[$i] == $csvarray[$s][name] and $csvarray[$s][d] == $diametr) {
                        //$sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . " ".$csvarray[$s][gmin]. " ".$csvarray[$s][gmax]. "");
                        $gmin = $csvarray[$s][gmin];
                        $gmax = $csvarray[$s][gmax];
                    }
                }

                $form = $colum_text + 1;
                $sheet->setCellValueByColumnAndRow(0, $form, "Формула расчета тепловой энергии: M1*(h1-h2) " . $sxema);
                $sheet->mergeCellsByColumnAndRow(0, $form, 6, $form);
            }
        } elseif (pg_num_rows($sql_kol_vvod) == 1) {
            if ($sens_res_id[$i] == 19 or $sens_res_id[$i] == 3) {
                $sheet->setCellValueByColumnAndRow(0, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . "");

                for ($s = 0; $s < count($csvarray); $s++) {
                    $massiv = explode(";", $sens_comm[$i]);
                    $diametr = $massiv[0];
                    //          $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $massiv[0] . "");
                    if ($sens_name[$i] == $csvarray[$s][name] and $csvarray[$s][d] == $diametr) {
                        //$sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . " ".$csvarray[$s][gmin]. " ".$csvarray[$s][gmax]. "");
                        $gmin = $csvarray[$s][gmin];
                        $gmax = $csvarray[$s][gmax];
                    }
                }

                $form = $colum_text + 1;
                $sheet->setCellValueByColumnAndRow(0, $form, "Формула расчета тепловой энергии: M1*(h1-h2) " . $sxema);
                $sheet->mergeCellsByColumnAndRow(0, $form, 6, $form);
            }
        }

        $sql_sens_prop = pg_query('SELECT 
                "Tepl"."Sensor_Property"."Propert_Value"
              FROM
                "Tepl"."Sensor_Property"
                INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."Sensor_Property".s_id = "Tepl"."Sensor_cnt".s_id)
              WHERE
                "Tepl"."Sensor_cnt".prp_id = ' . $sens_id[$i] . ' AND 
                "Tepl"."Sensor_Property".id_type_property = 0
              ORDER BY
                "Tepl"."Sensor_Property".id_type_property');

        if (pg_num_rows($sql_sens_prop) != 0) {
            $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . pg_fetch_result($sql_sens_prop, 0, 0) . "");
        } else {
            $sheet->setCellValueByColumnAndRow(2, $colum_text, " - ");
        }


        $sql_sens_prop = pg_query('SELECT 
                "Tepl"."Sensor_Property"."Propert_Value"
              FROM
                "Tepl"."Sensor_Property"
                INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."Sensor_Property".s_id = "Tepl"."Sensor_cnt".s_id)
              WHERE
                "Tepl"."Sensor_cnt".prp_id = ' . $sens_id[$i] . ' AND 
                "Tepl"."Sensor_Property".id_type_property = 2
              ORDER BY
                "Tepl"."Sensor_Property".id_type_property');

        if (pg_num_rows($sql_sens_prop) != 0) {
            $sheet->setCellValueByColumnAndRow(3, $colum_text, "" . date("d.m.Y", strtotime(pg_fetch_result($sql_sens_prop, 0, 0))) . "");
        } else {
            $sheet->setCellValueByColumnAndRow(3, $colum_text, " - ");
        }
        $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $diametr . "");
        $sheet->setCellValueByColumnAndRow(5, $colum_text, "" . $gmin . "");
        $sheet->setCellValueByColumnAndRow(6, $colum_text, "" . $gmax . "");

        /*
          $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $massiv[0] . "");
          $sheet->setCellValueByColumnAndRow(5, $colum_text, "" . $massiv[1] . "");
          $sheet->setCellValueByColumnAndRow(6, $colum_text, "" . $massiv[2] . "");

         */
    }

    $colum_text++;
}
if (pg_num_rows($sql_kol_vvod) == 3) {
    $colum_text+=2;
} else {
    $colum_text = $form + 3;
}

$sql_device = pg_query('SELECT 
                MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
              FROM
                "Tepl"."Places_cnt" "Places_cnt1"
                INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
              WHERE
                "Places_cnt1".plc_id = ' . $id_object . '');
$row_device = pg_fetch_row($sql_device);

$g = 0;
//шапка для 2х трубной системы

if (pg_num_rows($sql_kol_vvod) >= 3) {


    $array_resourse [] = array(
        'name_res' => 'Время<br> исправной<br> работы',
        'name_res_row' => 'ВНР',
        'name_group' => 'Время<br> исправной<br> работы',
        'name_gr_row' => 'ВНР',
        'name_param' => 'Время<br> исправной<br> работы',
        'name_param_row' => 'ВНР',
        'id_param' => 775,
        'ed_izmer' => 'ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'V1',
        'name_param_row' => 'V1 Объемный расход',
        'id_param' => 3,
        'ed_izmer' => 'м3/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'М1',
        'name_param_row' => 'М1 Масса1',
        'id_param' => 19,
        'ed_izmer' => 'т/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'Т1',
        'name_param_row' => 'Т1 Температура1',
        'id_param' => 5,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба3',
        'name_param' => 'V3',
        'name_param_row' => 'V3 Объемный расход',
        'id_param' => 4,
        'ed_izmer' => 'м3/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба3',
        'name_param' => 'М3',
        'name_param_row' => 'М3 Масса3',
        'id_param' => 20,
        'ed_izmer' => 'т/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба3',
        'name_param' => 'Т3',
        'name_param_row' => 'Т3 Температура3',
        'id_param' => 6,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Vгвс',
        'name_param_row' => 'Vгвс Объемный расход2',
        'id_param' => 10,
        'ed_izmer' => 'м3/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Мгвс',
        'name_param_row' => 'Мгвс Масса2',
        'id_param' => 21,
        'ed_izmer' => 'т/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Тгвс',
        'name_param_row' => 'Тгвс Температура1',
        'id_param' => 12,
        'ed_izmer' => '°С'
    );


    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС1',
        'name_group' => 'Обратный<br> трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Тгвс',
        'name_param_row' => 'Тгвс Температура2',
        'id_param' => 13,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'dt',
        'name_gr_row' => 'delt',
        'name_param' => 'dt',
        'name_param_row' => 'delt',
        'id_param' => 285,
        'ed_izmer' => '°С'
    );
    if ($dev_type_id == 214 or $dev_type_id == 217 or $dev_type_id == 175) {
        $array_resourse [] = array(
            'name_res' => 'Тепло',
            'name_res_row' => 'Тепло2',
            'name_group' => 'Q',
            'name_gr_row' => 'Q1',
            'name_param' => 'Q',
            'name_param_row' => 'Q1',
            'id_param' => 9,
            'ed_izmer' => 'ГКал'
        );
    } else {
        $array_resourse [] = array(
            'name_res' => 'Тепло',
            'name_res_row' => 'Тепло2',
            'name_group' => 'Q',
            'name_gr_row' => 'Q1',
            'name_param' => 'Q',
            'name_param_row' => 'Q1',
            'id_param' => 282,
            'ed_izmer' => 'ГКал сумм'
        );
    }
    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС2',
        'name_group' => 'dt',
        'name_gr_row' => 'delt',
        'name_param' => 'dt',
        'name_param_row' => 'delt',
        'id_param' => 286,
        'ed_izmer' => '°С'
    );
    if ($dev_type_id == 214 or $dev_type_id == 217 or $dev_type_id == 175) {
        $array_resourse [] = array(
            'name_res' => 'ГВС',
            'name_res_row' => 'ГВС2',
            'name_group' => 'Qгвс',
            'name_gr_row' => 'Q2',
            'name_param' => 'Qгвс',
            'name_param_row' => 'Q2',
            'id_param' => 16,
            'ed_izmer' => 'ГКал сумм'
        );
    } elseif ($dev_type_id == 116) {
        $array_resourse [] = array(
            'name_res' => 'ГВС',
            'name_res_row' => 'ГВС2',
            'name_group' => 'Qгвс',
            'name_gr_row' => 'Q2',
            'name_param' => 'Qгвс',
            'name_param_row' => 'Q2',
            'id_param' => 16,
            'ed_izmer' => 'ГКал'
        );
    } else {
        $array_resourse [] = array(
            'name_res' => 'ГВС',
            'name_res_row' => 'ГВС2',
            'name_group' => 'Qгвс',
            'name_gr_row' => 'Q2',
            'name_param' => 'Qгвс',
            'name_param_row' => 'Q2',
            'id_param' => 283,
            'ed_izmer' => 'ГКал сумм'
        );
    }

    $sheet->setCellValueByColumnAndRow(0, $colum_text, "Потребление тепловой энергии и горячего водоснабжения за " . $month_name . " " . date("Y") . "г.");
    $sheet->getStyleByColumnAndRow(0, $colum_text)->applyFromArray($FontStyle14TNR);
    $sheet->mergeCellsByColumnAndRow(0, $colum_text, 1 + count($array_resourse), $colum_text);

    $colum_text++;

    $sheet->setCellValueByColumnAndRow(0, $colum_text, "№");
    $sheet->mergeCellsByColumnAndRow(0, $colum_text, 0, $colum_text + 3);
    $sheet->setCellValueByColumnAndRow(1, $colum_text, "Дата");
    $sheet->mergeCellsByColumnAndRow(1, $colum_text, 1, $colum_text + 3);

    $sheet->setCellValueByColumnAndRow(2, $colum_text, "Тепло");
    $sheet->mergeCellsByColumnAndRow(2, $colum_text, 8, $colum_text);
    $sheet->setCellValueByColumnAndRow(9, $colum_text, "ГВС");
    $sheet->mergeCellsByColumnAndRow(9, $colum_text, 12, $colum_text);
    $sheet->setCellValueByColumnAndRow(13, $colum_text, "Тепло");
    $sheet->mergeCellsByColumnAndRow(13, $colum_text, 14, $colum_text);

    $sheet->setCellValueByColumnAndRow(15, $colum_text, "ГВС");
    $sheet->mergeCellsByColumnAndRow(15, $colum_text, 16, $colum_text);
    $colum_text++;

    $sheet->setCellValueByColumnAndRow(2, $colum_text, "h");
    $sheet->mergeCellsByColumnAndRow(2, $colum_text, 2, $colum_text + 1);

    $sheet->setCellValueByColumnAndRow(3, $colum_text, "Подающий трубопровод");
    $sheet->mergeCellsByColumnAndRow(3, $colum_text, 5, $colum_text);

    $sheet->setCellValueByColumnAndRow(6, $colum_text, "Обратный трубопровод");
    $sheet->mergeCellsByColumnAndRow(6, $colum_text, 8, $colum_text);

    $sheet->setCellValueByColumnAndRow(9, $colum_text, "Подающий трубопровод");
    $sheet->mergeCellsByColumnAndRow(9, $colum_text, 11, $colum_text);
    $sheet->setCellValueByColumnAndRow(12, $colum_text, "Обратный трубопровод");
    $sheet->getStyle("M19")->getAlignment()->setWrapText(true);
    $sheet->getColumnDimension('M')->setWidth(14);
    $sheet->getRowDimension(19)->setRowHeight(28.5);

    $sheet->setCellValueByColumnAndRow(13, $colum_text, "dt");
    $sheet->mergeCellsByColumnAndRow(13, $colum_text, 13, $colum_text + 1);
    $sheet->setCellValueByColumnAndRow(14, $colum_text, "Тепло");
    $sheet->mergeCellsByColumnAndRow(14, $colum_text, 14, $colum_text + 1);
    $sheet->setCellValueByColumnAndRow(15, $colum_text, "dt");
    $sheet->mergeCellsByColumnAndRow(15, $colum_text, 15, $colum_text + 1);
    $sheet->setCellValueByColumnAndRow(16, $colum_text, "ГВС");
    $sheet->mergeCellsByColumnAndRow(16, $colum_text, 16, $colum_text + 1);

    $colum_text++;
    $sheet->setCellValueByColumnAndRow(3, $colum_text, "V1");
    $sheet->setCellValueByColumnAndRow(4, $colum_text, "M1");
    $sheet->setCellValueByColumnAndRow(5, $colum_text, "T1");

    $sheet->setCellValueByColumnAndRow(6, $colum_text, "V2");
    $sheet->setCellValueByColumnAndRow(7, $colum_text, "M2");
    $sheet->setCellValueByColumnAndRow(8, $colum_text, "T2");

    $sheet->setCellValueByColumnAndRow(9, $colum_text, "Vгвс");
    $sheet->setCellValueByColumnAndRow(10, $colum_text, "Mгвс");
    $sheet->setCellValueByColumnAndRow(11, $colum_text, "Тгвс1");
    $sheet->setCellValueByColumnAndRow(12, $colum_text, "Тгвс2");

    $sheet->getColumnDimension('O')->setWidth(12);
    $sheet->getColumnDimension('Q')->setWidth(12);

    $colum_text++;
    for ($i = 0; $i < count($array_resourse); $i++) {

        //echo "<td>" . $array_resourse[$i]['ed_izmer'] . "</td>";
        $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text, "" . $array_resourse[$i]['ed_izmer'] . "");
    }



    $symb1 = 65;
    $symb2 = 65 + 16;
    $value1 = $colum_text - 3;
    $value2 = $colum_text;
    $sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
    $sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNR);
} else {

    $array_resourse [] = array(
        'name_res' => 'h',
        'name_res_row' => 'ВНР',
        'name_group' => 'h',
        'name_gr_row' => 'ВНР',
        'name_param' => 'h',
        'name_param_row' => 'ВНР',
        'id_param' => 775,
        'ed_izmer' => 'ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'V1',
        'name_param_row' => 'V1 Объемный расход',
        'id_param' => 3,
        'ed_izmer' => 'м3'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'М1',
        'name_param_row' => 'М1 Масса1',
        'id_param' => 19,
        'ed_izmer' => 'т'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'Т1',
        'name_param_row' => 'Т1 Температура1',
        'id_param' => 5,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'V2',
        'name_param_row' => 'V2 Объемный расход2',
        'id_param' => 4,
        'ed_izmer' => 'м3'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'М2',
        'name_param_row' => 'М2 Масса2',
        'id_param' => 20,
        'ed_izmer' => 'т'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Т2',
        'name_param_row' => 'Т2 Температура1',
        'id_param' => 6,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'dt',
        'name_gr_row' => 'delt',
        'name_param' => 'dt',
        'name_param_row' => 'delt',
        'id_param' => 285,
        'ed_izmer' => '°С'
    );
    if ($dev_type_id == 214 or $dev_type_id == 217 or $dev_type_id == 175) {
        $array_resourse [] = array(
            'name_res' => 'Тепло',
            'name_res_row' => 'Тепло1',
            'name_group' => 'Q',
            'name_gr_row' => 'Q1',
            'name_param' => 'Q',
            'name_param_row' => 'Q1',
            'id_param' => 9,
            'ed_izmer' => 'ГКал'
        );
    } else {
        $array_resourse [] = array(
            'name_res' => 'Тепло',
            'name_res_row' => 'Тепло1',
            'name_group' => 'Q',
            'name_gr_row' => 'Q1',
            'name_param' => 'Q',
            'name_param_row' => 'Q1',
            'id_param' => 282,
            'ed_izmer' => 'ГКал сумм'
        );
    }
    $sheet->setCellValueByColumnAndRow(0, $colum_text, "Потребление тепловой энергии за " . $month_name . " " . date("Y") . "г.");
    $sheet->getStyleByColumnAndRow(0, $colum_text)->applyFromArray($FontStyle14TNR);
    $sheet->mergeCellsByColumnAndRow(0, $colum_text, 1 + count($array_resourse), $colum_text);

    $colum_text++;


    $sheet->setCellValueByColumnAndRow(0, $colum_text, "№");
    $sheet->mergeCellsByColumnAndRow(0, $colum_text, 0, $colum_text + 3);
    $sheet->setCellValueByColumnAndRow(1, $colum_text, "Дата");
    $sheet->mergeCellsByColumnAndRow(1, $colum_text, 1, $colum_text + 3);
    $sheet->setCellValueByColumnAndRow(2, $colum_text, "Тепло");
    $sheet->mergeCellsByColumnAndRow(2, $colum_text, 2 + 8, $colum_text);
    $colum_text++;
    $sheet->setCellValueByColumnAndRow(2, $colum_text, "h");
    $sheet->mergeCellsByColumnAndRow(2, $colum_text, 2, $colum_text + 1);

    $sheet->setCellValueByColumnAndRow(3, $colum_text, "Подающий трубопровод");
    $sheet->mergeCellsByColumnAndRow(3, $colum_text, 5, $colum_text);
    $sheet->setCellValueByColumnAndRow(6, $colum_text, "Обратный трубопровод");
    $sheet->mergeCellsByColumnAndRow(6, $colum_text, 8, $colum_text);

    $sheet->setCellValueByColumnAndRow(9, $colum_text, "dt");
    $sheet->mergeCellsByColumnAndRow(9, $colum_text, 9, $colum_text + 1);
    $sheet->setCellValueByColumnAndRow(10, $colum_text, "Q");
    $sheet->mergeCellsByColumnAndRow(10, $colum_text, 10, $colum_text + 1);
    $colum_text++;
    $sheet->setCellValueByColumnAndRow(3, $colum_text, "V1");
    $sheet->setCellValueByColumnAndRow(4, $colum_text, "M1");
    $sheet->setCellValueByColumnAndRow(5, $colum_text, "T1");

    $sheet->setCellValueByColumnAndRow(6, $colum_text, "V2");
    $sheet->setCellValueByColumnAndRow(7, $colum_text, "M2");
    $sheet->setCellValueByColumnAndRow(8, $colum_text, "T2");

    $colum_text++;

    for ($i = 0; $i < count($array_resourse); $i++) {

        //echo "<td>" . $array_resourse[$i]['ed_izmer'] . "</td>";
        $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text, "" . $array_resourse[$i]['ed_izmer'] . "");
    }

    $symb1 = 65;
    $symb2 = 65 + 10;
    $value1 = $colum_text - 3;
    $value2 = $colum_text;
    $sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
    $sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNR);
}

//конец формирования шапки для 2х трубной системы
//вывод архива для 2х трубной системы




$sql_date = pg_query('SELECT DISTINCT 
                          ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = 2  AND
                          "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                          "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\'
                        ORDER BY
                          "Tepl"."Arhiv_cnt"."DateValue"');
$s = 0;
unset($array_summ);
unset($mass_arch);
$array_summ = '';
$mass_arch = '';
while ($row_date = pg_fetch_row($sql_date)) {
    //echo '<tr id="hover">';
    $s++;
    $sheet->setCellValueByColumnAndRow(0, $colum_text + $s, "" . $s . "");
    $sheet->setCellValueByColumnAndRow(1, $colum_text + $s, "" . date("d.m.Y", strtotime("-1 day", strtotime($row_date[0]))) . "");
    //echo "<td>" . $s . "</td>";
    //echo '<td>' . date("d.m.Y", strtotime($row_date[0])) . '</td>';
    $sql_archive = pg_query('SELECT 
                                  "Tepl"."Arhiv_cnt"."DataValue",
                                  "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                                FROM
                                  "Tepl"."ParametrResourse"
                                  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
                                  INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                                WHERE
                                  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
                                  "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                  "Tepl"."Arhiv_cnt"."DateValue" = \'' . $row_date[0] . '\'
                                ORDER BY
                                  "Tepl"."Arhiv_cnt"."DateValue",
                                  "Tepl"."Resourse_cnt"."Name",
                                  "Tepl"."ParamResPlc_cnt"."NameGroup",
                                  "Tepl"."ParametrResourse"."Name"
                                ');
    unset($archive);
    while ($row_archive = pg_fetch_row($sql_archive)) {
        $archive[] = array(
            'value' => $row_archive[0],
            'id_param' => $row_archive[1]
        );
    }

    $t1 = 0;
    $t2 = 0;
    $t3 = 0;
    $t = 0;

    for ($i = 0; $i < count($array_resourse); $i++) {
        $key = array_search($array_resourse[$i]['id_param'], array_column($archive, 'id_param'));
        if ($key !== false) {
            if ($archive[$key]['value'] == "NaN") {
                $g = "0";
            } else {
                $g = $archive[$key]['value'];
            }



            if ($array_resourse[$i]['id_param'] == 775) {
                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "" . number_format($g, 0, ',', ' ') . "");
            } else {
                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "" . number_format($g, 2, ',', ' ') . "");
            }

            if (pg_num_rows($sql_kol_vvod) == 2) {
                if ($array_resourse[$i]['id_param'] == 5) {
                    $t1 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 6) {
                    $t2 = $archive[$key]['value'];
                }
            } else {
                if ($array_resourse[$i]['id_param'] == 5) {
                    $t1 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 6) {
                    $t2 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 12) {
                    $t3 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 13) {
                    $t4 = $archive[$key]['value'];
                }
            }
            if ($s != 1) {
                // echo "<td>" . number_format($archive[$key]['value'], 2, ',', '') . "</td>";
                //$array_summ[$i] = $array_summ[$i] + $g;

                if ($array_resourse[$i]['id_param'] == 9 or $array_resourse[$i]['id_param'] == 16) {
                    if ($row_device[0] == 214) {
                        $mass_arch[$i][] = $archive[$key]['value'];
                    } else {
                        $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                    }
                } elseif ($array_resourse[$i]['id_param'] == 282 or $array_resourse[$i]['id_param'] == 283) {
                    $mass_arch[$i][] = $archive[$key]['value'];
                } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
                    if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
                        $mass_arch[$i][] = $archive[$key]['value'];
                    } else {
                        $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                    }
                } elseif ($array_resourse[$i]['id_param'] == 775 or $array_resourse[$i]['id_param'] == 3 or $array_resourse[$i]['id_param'] == 4 or $array_resourse[$i]['id_param'] == 10) {
                    $mass_arch[$i][] = $archive[$key]['value'];
                } else {
                    $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                }
            }
        }
        if ($key === false) {
            if ($array_resourse[$i]['id_param'] == 285) {

                $t = $t1 - $t2;
                if ($s != 1) {
                    $mass_arch[$i] = $mass_arch[$i] + $t;
                }

                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "" . number_format($t, 2, ",", " ") . "");
            } elseif ($array_resourse[$i]['id_param'] == 286) {

                $t = $t3 - $t4;
                if ($s != 1) {
                    $mass_arch[$i] = $mass_arch[$i] + $t;
                }
                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "" . number_format($t, 2, ",", " ") . "");
            } else {
                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "-");
            }


            //$sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "-");
            //echo "<td> - </td>";
        }
    }
}

$symb1 = 65;
$symb2 = 65 + 1 + $i;
$value1 = $colum_text + 1;
$value2 = $colum_text + $s;
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNRtext);




$sheet->setCellValueByColumnAndRow(0, $colum_text + $s + 1, 'СРЕДНЕЕ');
$sheet->getRowDimension($colum_text + $s + 1)->setRowHeight(22);
$sheet->mergeCellsByColumnAndRow(0, $colum_text + $s + 1, 1, $colum_text + $s + 1);
$m = 0;
$h = 0;
for ($i = 0; $i < count($array_resourse); $i++) {
    $temp_s = 0;
    $teplo = 0;
    if ($array_resourse[$i]['id_param'] == 282 or $array_resourse[$i]['id_param'] == 283) {
        $z = 0;
        $o = 0;
        $p = 0;
        for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
            //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
            if ($l - 1 >= 0) {
                $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
            }
            $o = $o + $p;

            //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
            $p = 0;
        }
        $teplo = $o / ($s - 1);


        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($teplo, 2, ',', ' ') . '');
    } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
        if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
                //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
                if ($l - 1 >= 0) {
                    $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o / ($s - 1);
        } else {
            $teplo = $mass_arch[$i] / ($s - 1);
        }


        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($teplo, 2, ',', ' ') . '');
    } elseif ($array_resourse[$i]['id_param'] == 5 or $array_resourse[$i]['id_param'] == 6 or $array_resourse[$i]['id_param'] == 12) {
        $temp_s = $mass_arch[$i] / ($s - 1);
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($temp_s, 2, ',', ' ') . '');
        // echo '<td></td>';
    } elseif ($array_resourse[$i]['id_param'] == 282) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($array_resourse[$i]['id_param'] == 283) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($array_resourse[$i]['id_param'] == 775) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($array_resourse[$i]['id_param'] == 3) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($array_resourse[$i]['id_param'] == 4) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($array_resourse[$i]['id_param'] == 10) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($array_resourse[$i]['id_param'] == 9) {

        if ($row_device[0] == 214) {
            $z = 0;
            $o = 0;
            $p = 0;
            for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
                //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
                if ($l - 1 >= 0) {
                    $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o / ($s - 1);
        } else {
            $teplo = $mass_arch[$i] / ($s - 1);
        }
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($teplo, 2, ',', ' ') . '');
    } else {
        $temp_s = $mass_arch[$i] / ($s - 1);
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($temp_s, 2, ',', ' ') . '');
    }
}


$value1 = $colum_text + $s + 1;
$value2 = $colum_text + $s + 2;
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNR);


$sheet->setCellValueByColumnAndRow(0, $colum_text + $s + 2, 'ИТОГО');
$sheet->getRowDimension($colum_text + $s + 2)->setRowHeight(22);
$sheet->mergeCellsByColumnAndRow(0, $colum_text + $s + 2, 1, $colum_text + $s + 2);
$m = 0;
$h = 0;




for ($i = 0; $i < count($array_resourse); $i++) {
    //echo $id_resours[$i]."<br>";
    if ($array_resourse[$i]['id_param'] == 282 or $array_resourse[$i]['id_param'] == 283) {


        $z = 0;
        $o = 0;
        $p = 0;
        for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
            //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
            if ($l - 1 >= 0) {
                $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
            }
            $o = $o + $p;

            //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
            $p = 0;
        }
        $teplo = $o;




        $limit = number_format($teplo, 2, ",", " ");
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . $limit . '');
        //echo '<td><b>' . $limit . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {

        if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
                //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
                if ($l - 1 >= 0) {
                    $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        } else {
            $teplo = $mass_arch[$i];
        }



        //' . number_format($teplo,3,".","") . '
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($teplo, 2, ',', ' ') . '');
        //echo '<td><b>' . substr(str_replace('.', ',', $mass_arch[$i]), 0, 6) . '</b></td>';
    } elseif ($array_resourse[$i]['id_param'] == 3 or $array_resourse[$i]['id_param'] == 4) {

        $teplo = array_sum($mass_arch[$i]);

        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($teplo, 2, ',', ' ') . '');
    } elseif ($array_resourse[$i]['id_param'] == 9 or $array_resourse[$i]['id_param'] == 16) {

        if ($row_device[0] == 214) {
            $z = 0;
            $o = 0;
            $p = 0;
            for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
                //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
                if ($l - 1 >= 0) {
                    $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        } else {
            $teplo = $mass_arch[$i];
        }



        //' . number_format($teplo,3,".","") . '
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($teplo, 2, ',', ' ') . '');
        //echo '<td><b>' . substr(str_replace('.', ',', $mass_arch[$i]), 0, 6) . '</b></td>';
    } elseif ($array_resourse[$i]['id_param'] == 5 or $array_resourse[$i]['id_param'] == 6 or $array_resourse[$i]['id_param'] == 12) {
        $temp_s = $mass_arch[$i] / $s;
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '   ');
        //echo '<td><b>' . substr(str_replace('.', ',', $temp_s), 0, 4) . '</b></td>';
    } elseif ($array_resourse[$i]['id_param'] == 775) {
        if ($row_device[0] != 217 AND $row_device[0] != 175) {
            $z = 0;
            $o = 0;
            $p = 0;
            for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
                //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
                if ($l - 1 >= 0) {
                    $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        } else {
            $teplo = array_sum($mass_arch[$i]);
        }
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($teplo, 0, ',', ' ') . '');
        //echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 3 or $array_resourse[$i]['id_param'] == 4 or $array_resourse[$i]['id_param'] == 10) {
        $z = 0;
        $o = 0;
        $p = 0;
        for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
            //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
            if ($l - 1 >= 0) {
                $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
            }
            $o = $o + $p;

            //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
            $p = 0;
        }
        $teplo = $o;
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($teplo, 2, ',', ' ') . '');
        //echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } else {
        $temp_s = $mass_arch[$i];
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '   ');
        //echo "<td></td>";
        //
        //echo '<td><b>' . number_format($temp_s, 2, '.', '') . '</b></td>';
    }
}



header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $info_obj[0] . "_" . date("d.m.Y") . ".xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');




