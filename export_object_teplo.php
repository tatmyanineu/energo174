<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL & ~E_NOTICE);

include 'db_config.php';
session_start();
$id_object = $_SESSION['id_object'];
$type_arch = 2;

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

$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$sheet->getPageSetup()->setFitToPage(true);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);

$sheet->getColumnDimension('A')->setWidth(16);
$sheet->getColumnDimension('B')->setWidth(14);




/*
 * 
 * Стили ячеек
 *  
 */

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


/*
 *  Стили ячеек
 */






if (strtotime(date('Y-m-d')) == strtotime(date('' . $_GET['year'] . '-' . $_GET['month'] . '-01'))) {
    //echo "Первое число месяца<br>";
    $mon = strtotime("-1 month");
    $month = date('m', $mon);
    //echo $month."<br>";

    $num = cal_days_in_month(CAL_GREGORIAN, $month, $_GET['year']);
    $first_date = date('' . $_GET['year'] . '-' . $_GET['month'] . '-01');
    $first_date = date('Y-m-d', strtotime("-1 month", strtotime($first_date)));
    //echo $first_date."<br>";

    $second_date = date('' . $_GET['year'] . '-' . $_GET['month'] . '-' . $num);
    $second_date = date('Y-m-d', strtotime("-1 month", strtotime($second_date)));
    //echo $second_date;
} else {
    //echo "Не первое число месяца<br>";
    $month = $_GET['month'];
    //echo $month."<br>";
    $num = cal_days_in_month(CAL_GREGORIAN, $_GET['month'], $_GET['year']);
    $first_date = date('' . $_GET['year'] . '-' . $_GET['month'] . '-01');

    $second_date = date('' . $_GET['year'] . '-' . $_GET['month'] . '-' . $num);
}



$sql_all_limit = pg_query('SELECT DISTINCT 
  public."LimitPlaces_cnt".plc_id,
  public."LimitPlaces_cnt".teplo,
  public."LimitPlaces_cnt".voda
FROM
  public."LimitPlaces_cnt"');

while ($result = pg_fetch_row($sql_all_limit)) {
    $arr_all_limit[] = array(
        'plc_id' => $result[0],
        'teplo' => $result[1],
        'voda' => $result[2]
    );
}


$month = (int) $month;
$sql_limit_part = pg_query('SELECT 
  public."LimitMonth_cnt".teplo,
  public."LimitMonth_cnt".voda,
  public."LimitMonth_cnt".name
FROM
  public."LimitMonth_cnt"
WHERE
  public."LimitMonth_cnt".id = ' . $month . '');

$limit_teplo_part = pg_fetch_result($sql_limit_part, 0, 0);
$limit_voda_part = pg_fetch_result($sql_limit_part, 0, 1);
$month_name = pg_fetch_result($sql_limit_part, 0, 2);


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


$sheet->getStyle('A1:Z7')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A1:Z7')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);


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
$sheet->getColumnDimension('A')->setWidth(20);
$sheet->getColumnDimension('B')->setWidth(24);

for ($i = 4; $i < 7; $i++) {
    $sheet->getRowDimension($i)->setRowHeight(38);
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



$sheet->getStyle('A8:Z70')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A8:Z70')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);

$sheet->setCellValue("C2", "" . $info_obj[0] . "");
$sheet->mergeCells("C2:D2");
$sheet->setCellValue("C3", "ул. " . $info_obj[1] . " д. " . $info_obj[2] . "");
$sheet->mergeCells("C3:D3");
$sheet->getColumnDimension('C')->setWidth(20.18);
$sheet->getColumnDimension('D')->setWidth(18.71);

$sheet->getStyle("C1:C7")->applyFromArray($FontStyle11TNRtext);

$sheet->setCellValue("A9", "Приборы учета тепловой энергии и холодного водоснабжения");
$sheet->mergeCells("A9:H9");
$sheet->getStyle("A9:H9")->applyFromArray($FontStyle14TNR);




$sheet->setCellValue("A10", "Ресурс");
$sheet->setCellValue("B10", "Тип прибора");
$sheet->setCellValue("C10", "Наименование");
$sheet->setCellValue("D10", "Зав. №");
$sheet->setCellValue("E10", "Дата ближайшей поверки");
$sheet->getStyle("E10")->getAlignment()->setWrapText(true);
$sheet->setCellValue("F10", "Ду (мм)");
$sheet->setCellValue("G10", "Gmin (м.куб./ч)");
$sheet->setCellValue("H10", "Gmax (м.куб./ч)");
$sheet->getStyle("A10:H10")->applyFromArray($FontStyle11TNR);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(16.86);
$sheet->getColumnDimension('G')->setWidth(20.57);
$sheet->getColumnDimension('H')->setWidth(16.86);
$sheet->getStyle('A10:H10')->applyFromArray($arrBorderStyle);

/*
 * 
 * считаем сколько приборов учавствуют в ГВС

 *  */

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


$sql_sens_teplo = pg_query('SELECT 
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
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 19 AND 
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . '  OR 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 20 AND 
   "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . '  OR
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 21 AND 
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' 
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."ParametrResourse"."Name"');


while ($result_sens = pg_fetch_row($sql_sens_teplo)) {
    if ($result_sens[5] == "Тепло") {
        $warm++;
    } elseif ($result_sens[5] == "ХВС") {
        $water_cold++;
    } elseif ($result_sens[5] == "ГВС") {
        $water_hot++;
    }
    $sens_res_id[] = $result_sens[0];
    $sens_name[] = $result_sens[1];
    $sens_res_name[] = $result_sens[2];
    $sens_resours[] = $result_sens[5];
    $sens_id[] = $result_sens[4];
    $sens_comm[] = $result_sens[3];
}


$sql_sens_xvs = pg_query('SELECT 
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
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . '  AND 
  "Tepl"."Resourse_cnt"."Name" = \'ХВС\'
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."ParametrResourse"."Name"');
while ($result_sens = pg_fetch_row($sql_sens_xvs)) {
    if ($result_sens[5] == "Тепло") {
        $warm++;
    } elseif ($result_sens[5] == "ХВС") {
        $water_cold++;
    } elseif ($result_sens[5] == "ГВС") {
        $water_hot++;
    }
    $sens_res_id[] = $result_sens[0];
    $sens_name[] = $result_sens[1];
    $sens_res_name[] = $result_sens[2];
    $sens_resours[] = $result_sens[5];
    $sens_id[] = $result_sens[4];
    $sens_comm[] = $result_sens[3];
}
$sql_sens_gvs = pg_query('SELECT 
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
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . '  AND 
  "Tepl"."Resourse_cnt"."Name" = \'ГВС\'
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."ParametrResourse"."Name"');
while ($result_sens = pg_fetch_row($sql_sens_gvs)) {
    if ($result_sens[5] == "Тепло") {
        $warm++;
    } elseif ($result_sens[5] == "ХВС") {
        $water_cold++;
    } elseif ($result_sens[5] == "ГВС") {
        $water_hot++;
    }
    $sens_res_id[] = $result_sens[0];
    $sens_name[] = $result_sens[1];
    $sens_res_name[] = $result_sens[2];
    $sens_resours[] = $result_sens[5];
    $sens_id[] = $result_sens[4];
    $sens_comm[] = $result_sens[3];
}
/*
 * считаем сколько приборов учавствуют в ГВС
 */

$excel_row = 0;
$excel_Col = 0;
$sheet->setCellValueByColumnAndRow(0, 11, "Тепло, ГВС");
$sheet->getStyleByColumnAndRow(0, 11)->getAlignment()->setWrapText(true);
$excel_row = 11 - 1 + $warm + $water_hot;
$sheet->mergeCellsByColumnAndRow(0, 11, 0, $excel_row);






$colum_text = 11;
if ($water_cold != 0) {
    $sheet->setCellValueByColumnAndRow(0, $excel_row + 1, "ХВС");
    $sheet->mergeCellsByColumnAndRow(0, $excel_row + 1, 0, $excel_row + $water_cold);
}
for ($i = 0; $i < count($dev_typ_id); $i++) {

    $sheet->setCellValueByColumnAndRow(1, $colum_text, "Тепловычислитель");
    $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $dev_name[$i] . "");
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
        $sheet->setCellValueByColumnAndRow(3, $colum_text, "" . pg_fetch_result($sql_dev_prop, 0, 0) . "");
    } else {
        $sheet->setCellValueByColumnAndRow(3, $colum_text, "-");
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
            $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . date("d.m.Y", strtotime(pg_fetch_result($sql_dev_prop, 0, 0))) . "");
        } else {
            $sheet->setCellValueByColumnAndRow(4, $colum_text, "-");
        }
    } else {
        $sheet->setCellValueByColumnAndRow(4, $colum_text, "-");
    }


    $sheet->setCellValueByColumnAndRow(5, $colum_text, "" . $dev_comment[$i] . "");
    $sheet->mergeCellsByColumnAndRow(5, $colum_text, 7, $colum_text);
    $colum_text++;
}
$coord = 11 - 1 + $warm + $water_hot;
$sheet->getStyle("A10:A" . $coord . "")->applyFromArray($FontStyle11TNR);
$sheet->getStyle("A10:H" . $coord . "")->applyFromArray($arrBorderStyle);
$sheet->getStyle("B10:H" . $coord . "")->applyFromArray($FontStyle11TNRtext);
if ($water_cold != 0) {
    $coord1 = $coord + 1;
    $coord2 = $coord + $water_cold;
    $sheet->getStyle("A" . $coord1 . ":H" . $coord2 . "")->applyFromArray($arrBorderStyle);
    $sheet->getStyle("A" . $coord1 . ":A" . $coord2 . "")->applyFromArray($FontStyle11TNR);
    $sheet->getStyle("B" . $coord1 . ":H" . $coord2 . "")->applyFromArray($FontStyle11TNRtext);
}
for ($i = 0; $i < count($sens_id); $i++) {

    if ($sens_resours[$i] == "Тепло") {


        if (pg_num_rows($sql_kol_vvod) >= 3) {
            $diametr = "";
            $gmin = "";
            $gmax = "";
            if ($sens_res_id[$i] == 19) {
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");
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
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");
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
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер обратка");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");
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
        } else {
            $diametr = "";
            $gmin = "";
            $gmax = "";
            if ($sens_res_id[$i] == 19) {
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");
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
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер обратка");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");
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
            $sheet->setCellValueByColumnAndRow(3, $colum_text, "" . pg_fetch_result($sql_sens_prop, 0, 0) . "");
        } else {
            $sheet->setCellValueByColumnAndRow(3, $colum_text, " - ");
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
            $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . date("d.m.Y", strtotime(pg_fetch_result($sql_sens_prop, 0, 0))) . "");
        } else {
            $sheet->setCellValueByColumnAndRow(4, $colum_text, " - ");
        }

        $sheet->setCellValueByColumnAndRow(5, $colum_text, "" . $diametr . "");
        $sheet->setCellValueByColumnAndRow(6, $colum_text, "" . $gmin . "");
        $sheet->setCellValueByColumnAndRow(7, $colum_text, "" . $gmax . "");
    }
    if ($sens_resours[$i] == "ХВС" or $sens_resours[$i] == "ГВС") {

        $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер");
        $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");

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
            $sheet->setCellValueByColumnAndRow(3, $colum_text, "" . pg_fetch_result($sql_sens_prop, 0, 0) . "");
        } else {
            $sheet->setCellValueByColumnAndRow(3, $colum_text, " - ");
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
            $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . date("d.m.Y", strtotime(pg_fetch_result($sql_sens_prop, 0, 0))) . "");
        } else {
            $sheet->setCellValueByColumnAndRow(4, $colum_text, " - ");
        }

        $massiv = explode(";", $sens_comm[$i]);
        $sheet->setCellValueByColumnAndRow(5, $colum_text, "" . $massiv[0] . "");
        $sheet->setCellValueByColumnAndRow(6, $colum_text, "" . $massiv[1] . "");
        $sheet->setCellValueByColumnAndRow(7, $colum_text, "" . $massiv[2] . "");
    }
    $colum_text++;
}



$colum_text = $colum_text + 1;

$sql_resurse = pg_query('SELECT DISTINCT 
                          ("Tepl"."ParametrResourse"."Name") AS "FIELD_1",
                          "Tepl"."ParamResPlc_cnt"."NameGroup",
                          "Tepl"."Resourse_cnt"."Name",
                          "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                        FROM
                          "Tepl"."ParametrResourse"
                          INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
                          INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                        WHERE
                          "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . '
                        ORDER BY
                          "Tepl"."Resourse_cnt"."Name",
                          "Tepl"."ParamResPlc_cnt"."NameGroup"');

$i = 0;
$j = 0;

$sheet->setCellValueByColumnAndRow(0, $colum_text, "Потребление тепловой энергии и холодного водоснабжения за " . $month_name . " " . $_GET['year'] . "г.");
$sheet->getStyleByColumnAndRow(0, $colum_text)->applyFromArray($FontStyle14TNR);
$sheet->mergeCellsByColumnAndRow(0, $colum_text, 1 + pg_num_rows($sql_resurse), $colum_text);
$h1_2 = $colum_text;



while ($row_resours = pg_fetch_row($sql_resurse)) {
    $id_resours[] = $row_resours[3];
    $arr_resours[] = $row_resours[0];
    if ($arr_name[$i] == "")
        $arr_name[$i] = $row_resours[2];
    if ($arr_group[$j] == "")
        $arr_group[$j] = $row_resours[1];

    if ($arr_name[$i] == $row_resours[2]) {
        $par[$i] ++;
        // echo " arr_name = ".$arr_name[$i]."   par=".$par[$i]."   i=".$i."<br>";
    } else {
        $i++;
        $arr_name[$i] = $row_resours[2];
        $par[$i] ++;
        $j++;
        $arr_group[$j] = $row_resours[1];
    };

    if ($arr_group[$j] == $row_resours[1]) {

        $grou[$j] ++;
        //echo  "arr_grou = ". $arr_group[$j]."   grou=".$grou[$j]."  j=".$j." <br>";
    } else {
        //echo "j++<br>";
        $j++;
        $arr_group[$j] = $row_resours[1];
        $grou[$j] ++;
        //echo  "arr_grou = ". $arr_group[$j]."   grou=".$grou[$j]."  j=".$j." <br>";
    };
}


$colum_text = $colum_text + 1;
$sheet->setCellValueByColumnAndRow(0, $colum_text, "№");
$sheet->mergeCellsByColumnAndRow(0, $colum_text, 0, $colum_text + 1);

$sheet->setCellValueByColumnAndRow(1, $colum_text, "Дата");
$sheet->mergeCellsByColumnAndRow(1, $colum_text, 1, $colum_text + 1);


$k = 0;
for ($c = 0; $c < count($arr_name); $c++) {
    /*
     * в этом месте код делает вставку ресурса по столбцу 3(D) и строке(7)
     * после чего обьеденяет ячейки для группы параметров для данного ресурса
     * $k используйется для перехода на след столбец
     * $par[$c] исп. как количество ресурсов для данного параметра 
     */
    $sheet->setCellValueByColumnAndRow(2 + $k, $colum_text, "" . $arr_name[$c] . "");
    $sheet->mergeCellsByColumnAndRow(2 + $k, $colum_text, 2 + $k + $par[$c] - 1, $colum_text);
    $k = $par[$c];
}

for ($b = 0; $b < count($arr_resours); $b++) {
    /*
     * тут мы выводим параметры ресурсов
     */
    $sheet->setCellValueByColumnAndRow(2 + $b, $colum_text + 1, '' . $arr_resours[$b] . '');
    $sheet->getStyleByColumnAndRow(2 + $b, $colum_text + 1)->getAlignment()->setWrapText(true);
}
$symb1 = 65;
$symb2 = 65 + 1 + $b;
$value1 = $colum_text;
$value2 = $colum_text + 1;
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNR);



$fist_date_limit = date('Y-m-d', strtotime('+1 day', strtotime($first_date)));
//echo $fist_date_limit . "<br>";
$last_date_limit = date('Y-m-d', strtotime('+1 day', strtotime($second_date)));
//echo $last_date_limit . "<br>";
//$sheet->setCellValue("C9", ''.$fist_date_limit.'');
//$sheet->setCellValue("C10", ''.$last_date_limit.'');
//Запром на вывод архива для конкретного объекта
$sql_date = pg_query('SELECT DISTINCT 
                          ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND
                          "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $fist_date_limit . '\' AND 
                          "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $last_date_limit . '\'
                        ORDER BY
                          "Tepl"."Arhiv_cnt"."DateValue"');


$colum_text = $colum_text + 1;
$s = 0;
while ($row_date = pg_fetch_row($sql_date)) {
    $s++;
    $sheet->setCellValueByColumnAndRow(0, $colum_text + $s, '' . $s . '');
    $date_b = date("d.m.Y H:i", strtotime($row_date[0]));
    $date_arch = explode(' ', $date_b);
    $sheet->setCellValueByColumnAndRow(1, $colum_text + $s, '' . date("d.m.Y", strtotime('-1 day', strtotime($date_arch[0]))) . '');
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
                                  "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
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
    for ($i = 0; $i < count($id_resours); $i++) {
        $key = array_search($id_resours[$i], array_column($archive, 'id_param'));
        if ($key !== false) {
            if ($archive[$key]['value'] == "NaN") {
                $g = "0";
            } else {
                $g = $archive[$key]['value'];
            }
            if ($id_resours[$i] == 775) {
                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "" . number_format($g, 0, ',', ' ') . "");
            } else {
                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "" . number_format($g, 2, ',', ' ') . "");
            }
            if ($id_resours[$i] == 1) {
                $mass_voda[0][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 308) {
                $mass_voda[1][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 310) {
                $mass_voda[2][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 414) {
                $mass_voda[3][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 420) {
                $mass_voda[4][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 436) {
                $mass_voda[5][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 787) {
                $mass_voda[6][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 2) {
                $mass_voda2[0][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 44) {
                $mass_voda2[1][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 377) {
                $mass_voda2[2][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 442) {
                $mass_voda2[3][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 402) {
                $mass_voda2[4][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 408) {
                $mass_voda2[5][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 922) {
                $mass_voda2[6][] = $archive[$key]['value'];
            } else {
                if ($id_resours[$i] == 9 or $id_resours[$i] == 16) {
                    if ($row_device[0] == 214) {
                        $mass_arch[$i][] = $archive[$key]['value'];
                    } else {
                        $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                    }
                } elseif ($id_resours[$i] == 282 or $id_resours[$i] == 283) {
                    $mass_arch[$i][] = $archive[$key]['value'];
                } elseif ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {
                    if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
                        $mass_arch[$i][] = $archive[$key]['value'];
                    } else {
                        $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                    }
                } elseif ($id_resours[$i] == 775 or $id_resours[$i] == 3 or $id_resours[$i] == 4 or $id_resours[$i] == 10) {
                    $mass_arch[$i][] = $archive[$key]['value'];
                } else {
                    $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                }
            }
        }
        if ($key === false) {
            if ($id_resours[$i] == 285) {
                $key_t1 = array_search(5, array_column($archive, 'id_param'));
                $key_t2 = array_search(6, array_column($archive, 'id_param'));
                if ($key_t1 !== false and $key_t2 !== false) {
                    $t = $archive[$key_t1]['value'] - $archive[$key_t2]['value'];
                }
                $mass_arch[$i] = $mass_arch[$i] + $t;
                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "" . number_format($t, 2, ",", " ") . "");
            } elseif ($id_resours[$i] == 286) {
                $key_t3 = array_search(12, array_column($archive, 'id_param'));
                $key_t4 = array_search(13, array_column($archive, 'id_param'));
                if ($key_t3 !== false and $key_t4 !== false) {
                    $t = $archive[$key_t3]['value'] - $archive[$key_t4]['value'];
                }
                $mass_arch[$i] = $mass_arch[$i] + $t;
                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "" . number_format($t, 2, ",", " ") . "");
            } else {
                $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "-");
            }
        }
    }
    /*
      for ($i = 0; $i < count($id_resours); $i++) {
      $prov = 0;
      for ($j = 0; $j < count($arr_ResId); $j++) {
      if ($id_resours[$i] == $arr_ResId[$j]) {
      $prov = 1;
      $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, '' . number_format($pokaz[$j], 3, ".", "") . '');
      if ($id_resours[$i] == 1) {
      $mass_voda[0][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 308) {
      $mass_voda[1][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 310) {
      $mass_voda[2][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 414) {
      $mass_voda[3][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 420) {
      $mass_voda[4][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 436) {
      $mass_voda[5][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 787) {
      $mass_voda[6][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 2) {
      $mass_voda2[0][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 44) {
      $mass_voda2[1][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 377) {
      $mass_voda2[2][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 442) {
      $mass_voda2[3][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 402) {
      $mass_voda2[4][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 408) {
      $mass_voda2[5][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 922) {
      $mass_voda2[6][] = $pokaz[$j];
      } else {

      if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
      if ($id_resours[$i] == 9 or $id_resours[$i] == 16) {
      $mass_arch[$i][] = $pokaz[$j];
      } elseif ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {
      $mass_arch[$i][] = $pokaz[$j];
      } else {
      $mass_arch[$i] = $mass_arch[$i] + $pokaz[$j];
      }
      } else {
      $mass_arch[$i] = $mass_arch[$i] + $pokaz[$j];
      }
      }
      }
      }
      if ($prov == 0) {
      //echo "<td>-</td>";
      $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, '—');
      }
      }
      //print_r($mass_arch);
      //echo '</tr>';
      }
     */
}
$symb1 = 65;
$symb2 = 65 + 1 + $b;
$value1 = $colum_text + 1;
$value2 = $colum_text + $s;
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNRtext);
$sheet->getStyle('' . chr(69) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);



for ($l = 0; $l < count($mass_voda); $l++) {
    $n1 = count($mass_voda[$l]) - 1;
    $z = 0;
    for ($n = 0; $n < count($mass_voda[$l]); $n++) {

        if ($n == $n1) {
            $z = $z;
            // echo "n=" .$n." mas = ". $mass_voda[$l][$n]."    z=".$z."  <br>" ;
        }
        if ($n >= 0 and $n < $n1) {
            if ($mass_voda[$l][$n]) {
                $z = $z + $mass_voda[$l][$n + 1] - $mass_voda[$l][$n];
            }
            //echo "n=" .$n." mas = ". $mass_voda[$l][$n]."   mas+1 =  ".$mass_voda[$l][$n+1]. "     z=".$z."  <br>" ;
        }
    }
    $val[$l] = $z;
    //echo "Z ====".$val[$l]."  <br>";
}

//print_r($mass_voda2)."<br>";
//echo "count mass_voda = ".count($mass_voda2)."<br>";
for ($l = 0; $l < count($mass_voda2); $l++) {
    $n1 = count($mass_voda2[$l]) - 1;
    $z = 0;
    for ($n = 0; $n < count($mass_voda2[$l]); $n++) {

        if ($n == $n1) {
            $z = $z;
            //echo "n=" .$n." mas = ". $mass_voda2[$l][$n]."    z=".$z."  <br>" ;
        }
        if ($n >= 0 and $n < $n1) {
            if ($mass_voda2[$l][$n]) {
                $z = $z + $mass_voda2[$l][$n + 1] - $mass_voda2[$l][$n];
            }
            //echo "n=" .$n." mas = ". $mass_voda2[$l][$n]."   mas+1 =  ".$mass_voda2[$l][$n+1]. "     z=".$z."  <br>" ;
        }
    }
    $val2[$l] = $z;
    //echo "Z ====".$val2[$l]."  <br>";
}
$sheet->setCellValueByColumnAndRow(0, $colum_text + $s + 1, 'СРЕДНЕЕ');
$sheet->getRowDimension($colum_text + $s + 1)->setRowHeight(26);
$sheet->mergeCellsByColumnAndRow(0, $colum_text + $s + 1, 1, $colum_text + $s + 1);
$m = 0;
$h = 0;

for ($i = 0; $i < count($id_resours); $i++) {
    $temp_s = 0;
    $teplo = 0;
    if ($id_resours[$i] == 282 or $id_resours[$i] == 283) {
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
    } elseif ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {
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
    } elseif ($id_resours[$i] == 5 or $id_resours[$i] == 6 or $id_resours[$i] == 12) {
        $temp_s = $mass_arch[$i] / ($s - 1);
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($temp_s, 2, ',', ' ') . '');
        // echo '<td></td>';
    } elseif ($id_resours[$i] == 282) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($id_resours[$i] == 283) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($id_resours[$i] == 775) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($id_resours[$i] == 3) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($id_resours[$i] == 4) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($id_resours[$i] == 10) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($id_resours[$i] == 9) {

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
    } elseif ($id_resours[$i] == 1 or $id_resours[$i] == 308 or $id_resours[$i] == 310 or $id_resours[$i] == 414 or $id_resours[$i] == 420 or $id_resours[$i] == 436 or $id_resours[$i] == 787) {
        // echo "<td><b>" . substr(str_replace('.', ',', $val[$m]), 0, 6) . "</b></td>";
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, ' ');
    } elseif ($id_resours[$i] == 2 or $id_resours[$i] == 44 or $id_resours[$i] == 377 or $id_resours[$i] == 442 or $id_resours[$i] == 402 or $id_resours[$i] == 408 or $id_resours[$i] == 922) {
        //echo "<td><b>" . substr(str_replace('.', ',', $val2[$h]), 0, 6) . "</b></td>";
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, ' ');
    } else {
        $temp_s = $mass_arch[$i] / ($s - 1);
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($temp_s, 2, ',', ' ') . '');
    }
}




$sheet->setCellValueByColumnAndRow(0, $colum_text + $s + 2, 'ИТОГО');
$sheet->getRowDimension($colum_text + $s + 2)->setRowHeight(26);
$sheet->mergeCellsByColumnAndRow(0, $colum_text + $s + 2, 1, $colum_text + $s + 2);





$symb1 = 65;
$symb2 = 65 + 1 + $b;
$value1 = $colum_text + $s + 2;
$value2 = $colum_text + $s + 2;
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNRtext);


for ($i = 0; $i < count($id_resours); $i++) {
    //echo $id_resours[$i]."<br>";
    if ($id_resours[$i] == 1 or $id_resours[$i] == 308 or $id_resours[$i] == 310 or $id_resours[$i] == 414 or $id_resours[$i] == 420 or $id_resours[$i] == 436 or $id_resours[$i] == 787) {
        // echo "<td><b>" . substr(str_replace('.', ',', $val[$m]), 0, 6) . "</b></td>";
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($val[$m], 3, ".", "") . '');
        $m++;
    } elseif ($id_resours[$i] == 2 or $id_resours[$i] == 44 or $id_resours[$i] == 377 or $id_resours[$i] == 442 or $id_resours[$i] == 402 or $id_resours[$i] == 408 or $id_resours[$i] == 922) {
        //echo "<td><b>" . substr(str_replace('.', ',', $val2[$h]), 0, 6) . "</b></td>";
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($val2[$h], 3, ".", "") . '');
        $h++;
    } elseif ($id_resours[$i] == 282 or $id_resours[$i] == 283) {


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
    } elseif ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {

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


        //$sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($teplo, 2, ',', ' ') . '');
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '');
    } elseif ($id_resours[$i] == 3 or $id_resours[$i] == 4) {

        $teplo = array_sum($mass_arch[$i]);

        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($teplo, 2, ',', ' ') . '');
    } elseif ($id_resours[$i] == 9 or $id_resours[$i] == 16) {

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
    } elseif ($id_resours[$i] == 5 or $id_resours[$i] == 6 or $id_resours[$i] == 12) {
        $temp_s = $mass_arch[$i] / $s;
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '   ');
        //echo '<td><b>' . substr(str_replace('.', ',', $temp_s), 0, 4) . '</b></td>';
    } elseif ($id_resours[$i] == 775) {
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
    } elseif ($id_resours[$i] == 3 or $id_resours[$i] == 4 or $id_resours[$i] == 10) {
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

$symb1 = 65;
$symb2 = 65 + 1 + $b;
$value1 = $colum_text + $s + 1;
$value2 = $colum_text + $s + 1;
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNRtext);




for ($i = 68; $i <= $symb2; $i++) {
    //$sheet->getColumnDimension("" . chr($i) . "")->setAutoSize(True)
    $sheet->getColumnDimension("" . chr($i) . "")->setWidth(20.12);
    ;
}



/*
 * 
 * высота строк для документа
 * 
 */

$colum_text = $colum_text + 2 + $s;

for ($i = 11; $i < $colum_text - 1; $i++) {
    $sheet->getRowDimension($i)->setRowHeight(18);
}



$sheet->getRowDimension(9)->setRowHeight(27.53);
$sheet->getRowDimension(10)->setRowHeight(51.53);
$sheet->getRowDimension($h1_2)->setRowHeight(27.53);
$sheet->getRowDimension($h1_2 + 1)->setRowHeight(20.53);
$sheet->getRowDimension($h1_2 + 2)->setRowHeight(35.53);

header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename= " . $info_obj[0] . "_" . $month_name . "_" . $_GET['year'] . ".xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
