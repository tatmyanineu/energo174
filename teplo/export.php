<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



include '../db_config.php';
session_start();
$type_arch = 2;
$id_object = 298;

$date1 = '2015-11-01';
$date2 = '2015-11-26';

// Подключаем класс для работы с excel
require_once('../PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('../PHPExcel/Writer/Excel5.php');

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


$sheet->getStyle('A8:Z900')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A8:Z900')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);
/*
 *  Стили ячеек
 */


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
$sheet->setCellValue("A7", "Договорная расход(Т/ч):");
$sheet->mergeCells("A7:B7");
$sheet->getStyle("A1:B7")->applyFromArray($FontStyle11TNR);
$sheet->getColumnDimension('A')->setWidth(8.86);
$sheet->getColumnDimension('B')->setWidth(20);

for ($i = 4; $i < 7; $i++) {
    $sheet->getRowDimension($i)->setRowHeight(30);
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

$sheet->setCellValue("C2", "" . $info_obj[0] . "");
$sheet->mergeCells("C2:D2");
$sheet->setCellValue("C3", "ул. " . $info_obj[1] . " д. " . $info_obj[2] . "");
$sheet->mergeCells("C3:D3");
$sheet->getColumnDimension('C')->setWidth(20.18);
$sheet->getColumnDimension('D')->setWidth(18.71);

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


$excel_row = 0;
$excel_Col = 0;
$sheet->setCellValueByColumnAndRow(0, 11, "Ввод 1");
$sheet->getStyleByColumnAndRow(0, 11)->getAlignment()->setWrapText(true);
$excel_row = 11 + $warm + $water_hot;
$sheet->mergeCellsByColumnAndRow(0, 11, 0, $excel_row);
$colum_text = 11;
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


    $sheet->setCellValueByColumnAndRow(5, $colum_text, "-");
    $sheet->setCellValueByColumnAndRow(6, $colum_text, "-");
    $sheet->setCellValueByColumnAndRow(7, $colum_text, "-");
    $colum_text++;
}
$coord = 11 + $warm;
$sheet->getStyle("A11:A" . $coord . "")->applyFromArray($FontStyle11TNR);
$sheet->getStyle("A11:H" . $coord . "")->applyFromArray($arrBorderStyle);
$sheet->getStyle("B11:H" . $coord . "")->applyFromArray($FontStyle11TNRtext);


for ($i = 0; $i < count($sens_id); $i++) {

    if ($sens_resours[$i] == "Тепло") {


        if ($id_object == 39 or $id_object == 40 or$id_object == 54) {
            if ($sens_res_id[$i] == 19) {
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");
            }
            if ($sens_res_id[$i] == 20) {
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");
            }
            if ($sens_res_id[$i] == 21) {
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер обратка");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");
            }
        } else {
            if ($sens_res_id[$i] == 19) {
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер подача");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");
            }
            if ($sens_res_id[$i] == 20) {
                $sheet->setCellValueByColumnAndRow(1, $colum_text, "Расходомер обратка");
                $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $sens_name[$i] . "");

                $form = $colum_text + 1;
                $sheet->setCellValueByColumnAndRow(1, $form, "формула расчета тепловой энергии: M1*(h1-h2)");
                $sheet->mergeCellsByColumnAndRow(1, $form, 7, $form);
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
        $massiv = explode(";", $sens_comm[$i]);
        $sheet->setCellValueByColumnAndRow(5, $colum_text, "" . $massiv[0] . "");
        $sheet->setCellValueByColumnAndRow(6, $colum_text, "" . $massiv[1] . "");
        $sheet->setCellValueByColumnAndRow(7, $colum_text, "" . $massiv[2] . "");
    }

    $colum_text++;
}

$colum_text = $form + 3;

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
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' 
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup"');



$i = 0;
$j = 0;
while ($row_resours = pg_fetch_row($sql_resurse)) {
    $array_res[] = array(
        'param_name' => $row_resours[0],
        'group_name' => $row_resours[1],
        'res_name' => $row_resours[2],
        'param_id' => $row_resours[3]
    );
}


$array_sort = array('775', '3', '19', '5', '4', '20', '6', '285', '9');

$g = 0;
if ($id_object == 39 or $id_object == 40 or $id_object == 54) {
    for ($i = 0; $i < count($array_sort); $i++) {
        $key = array_search($array_sort[$i], array_column($array_res, 'param_id'));
        if ($key !== false) {

            switch ($array_sort[$i]) {
                case 23:

                    $array_resourse [] = array(
                        'name_res' => 'Время исправной работы',
                        'name_res_row' => 'ВНР',
                        'name_group' => 'Время исправной работы',
                        'name_gr_row' => 'ВНР',
                        'name_param' => 'Время исправной работы',
                        'name_param_row' => 'ВНР',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'ч'
                    );
                    break;
                case 320:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Подающий трубопровод',
                        'name_gr_row' => 'Труба1',
                        'name_param' => 'V1',
                        'name_param_row' => 'V1 Объемный расход',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'м3/ч'
                    );
                    break;
                case 19:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Подающий трубопровод',
                        'name_gr_row' => 'Труба1',
                        'name_param' => 'М1',
                        'name_param_row' => 'М1 Масса1',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'т/ч'
                    );
                    break;
                case 5:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Подающий трубопровод',
                        'name_gr_row' => 'Труба1',
                        'name_param' => 'Т1',
                        'name_param_row' => 'Т1 Температура1',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => '°С'
                    );
                    break;
                case 328:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Обратный трубопровод',
                        'name_gr_row' => 'Труба3',
                        'name_param' => 'V3',
                        'name_param_row' => 'V3 Объемный расход',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'м3/ч'
                    );
                    break;
                case 21:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Обратный трубопровод',
                        'name_gr_row' => 'Труба3',
                        'name_param' => 'М3',
                        'name_param_row' => 'М3 Масса3',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'т/ч'
                    );
                    break;
                case 12:
                    if ($g == 0) {
                        $array_resourse [] = array(
                            'name_res' => 'Тепло',
                            'name_res_row' => 'Тепло1',
                            'name_group' => 'Обратный трубопровод',
                            'name_gr_row' => 'Труба3',
                            'name_param' => 'Т3',
                            'name_param_row' => 'Т3 Температура3',
                            'id_param' => $array_res[$key]['param_id'],
                            'ed_izmer' => '°С'
                        );
                    } elseif ($g == 1) {
                        $array_resourse [] = array(
                            'name_res' => 'ГВС',
                            'name_res_row' => 'ГВС1',
                            'name_group' => 'Обратный трубопровод',
                            'name_gr_row' => 'Труба2',
                            'name_param' => 'Тгвс',
                            'name_param_row' => 'Тгвс Температура2',
                            'id_param' => $array_res[$key]['param_id'],
                            'ed_izmer' => '°С'
                        );
                    }
                    $g++;
                    break;
                case 324:
                    $array_resourse [] = array(
                        'name_res' => 'ГВС',
                        'name_res_row' => 'ГВС1',
                        'name_group' => 'Подающий трубопровод',
                        'name_gr_row' => 'Труба2',
                        'name_param' => 'Vгвс',
                        'name_param_row' => 'Vгвс Объемный расход2',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'м3/ч'
                    );
                    break;
                case 20:
                    $array_resourse [] = array(
                        'name_res' => 'ГВС',
                        'name_res_row' => 'ГВС1',
                        'name_group' => 'Подающий трубопровод',
                        'name_gr_row' => 'Труба2',
                        'name_param' => 'Мгвс',
                        'name_param_row' => 'Мгвс Масса2',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'т/ч'
                    );
                    break;
                case 6:
                    $array_resourse [] = array(
                        'name_res' => 'ГВС',
                        'name_res_row' => 'ГВС1',
                        'name_group' => 'Подающий трубопровод',
                        'name_gr_row' => 'Труба2',
                        'name_param' => 'Тгвс',
                        'name_param_row' => 'Тгвс Температура1',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => '°С'
                    );
                    break;
                case 9:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло2',
                        'name_group' => 'Q',
                        'name_gr_row' => 'Q1',
                        'name_param' => 'Q',
                        'name_param_row' => 'Q1',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'ГКал'
                    );
                    break;
                case 16:
                    $array_resourse [] = array(
                        'name_res' => 'ГВС',
                        'name_res_row' => 'ГВС2',
                        'name_group' => 'Qгвс',
                        'name_gr_row' => 'Q2',
                        'name_param' => 'Qгвс',
                        'name_param_row' => 'Q2',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'ГКал'
                    );
                    break;
            }
        }
    }
} else {
    for ($i = 0; $i < count($array_sort); $i++) {
        $key = array_search($array_sort[$i], array_column($array_res, 'param_id'));
        if ($key !== false) {

            switch ($array_sort[$i]) {
                case 775:

                    $array_resourse [] = array(
                        'name_res' => 'h',
                        'name_res_row' => 'ВНР',
                        'name_group' => 'h',
                        'name_gr_row' => 'ВНР',
                        'name_param' => 'h',
                        'name_param_row' => 'ВНР',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'ч'
                    );
                    break;
                case 3:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Подающий трубопровод',
                        'name_gr_row' => 'Труба1',
                        'name_param' => 'V1',
                        'name_param_row' => 'V1 Объемный расход',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'м3'
                    );
                    break;
                case 19:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Подающий трубопровод',
                        'name_gr_row' => 'Труба1',
                        'name_param' => 'М1',
                        'name_param_row' => 'М1 Масса1',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'т'
                    );
                    break;
                case 5:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Подающий трубопровод',
                        'name_gr_row' => 'Труба1',
                        'name_param' => 'Т1',
                        'name_param_row' => 'Т1 Температура1',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => '°С'
                    );
                    break;
                case 4:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Обратный трубопровод',
                        'name_gr_row' => 'Труба2',
                        'name_param' => 'V2',
                        'name_param_row' => 'V2 Объемный расход2',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'м3'
                    );
                    break;
                case 20:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Обратный трубопровод',
                        'name_gr_row' => 'Труба2',
                        'name_param' => 'М2',
                        'name_param_row' => 'М2 Масса2',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'т'
                    );
                    break;
                case 6:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Обратный трубопровод',
                        'name_gr_row' => 'Труба2',
                        'name_param' => 'Т2',
                        'name_param_row' => 'Т2 Температура1',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => '°С'
                    );
                    break;
                case 285:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'dt',
                        'name_gr_row' => 'delt',
                        'name_param' => 'dt',
                        'name_param_row' => 'delt',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => '°С'
                    );
                    break;
                case 9:
                    $array_resourse [] = array(
                        'name_res' => 'Тепло',
                        'name_res_row' => 'Тепло1',
                        'name_group' => 'Q',
                        'name_gr_row' => 'Q1',
                        'name_param' => 'Q',
                        'name_param_row' => 'Q1',
                        'id_param' => $array_res[$key]['param_id'],
                        'ed_izmer' => 'ГКал'
                    );
                    break;
            }
        }
    }
}


//шапка для 2х трубной системы

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

$sheet->getStyle("A17:K20")->applyFromArray($FontStyle11TNR);
$sheet->getStyle("A17:K20")->applyFromArray($arrBorderStyle);
$colum_text++;
for ($i = 0; $i < count($array_resourse); $i++) {

    //echo "<td>" . $array_resourse[$i]['ed_izmer'] . "</td>";
    $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text, "" . $array_resourse[$i]['ed_izmer'] . "");
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
while ($row_date = pg_fetch_row($sql_date)) {
    //echo '<tr id="hover">';
    $s++;
    $sheet->setCellValueByColumnAndRow(0, $colum_text + $s, "" . $s . "");
    $sheet->setCellValueByColumnAndRow(1, $colum_text + $s, "" . date("d.m.Y", strtotime($row_date[0])) . "");
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


    for ($i = 0; $i < count($array_resourse); $i++) {
        $key = array_search($array_resourse[$i]['id_param'], array_column($archive, 'id_param'));
        if ($key !== false) {
            $sheet->setCellValueByColumnAndRow(2 + $i, $colum_text + $s, "" . number_format($archive[$key]['value'], 2, '.', '') . "");
            // echo "<td>" . number_format($archive[$key]['value'], 2, ',', '') . "</td>";
            $array_summ[$i] += $archive[$key]['value'];
            if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
                if ($array_resourse[$i]['id_param'] == 9 or $array_resourse[$i]['id_param'] == 16) {
                    $mass_arch[$i][] = $archive[$key]['value'];
                } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
                    $mass_arch[$i][] = $archive[$key]['value'];
                } else {
                    $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                }
            } else {
                $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
            }
        }
        if ($key === false) {
            //echo "<td> - </td>";
        }
    }
}

$symb1 = 65;
$symb2 = 65 + 1 + 9;
$value1 = $colum_text+1;
$value2 = $colum_text +$s;
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNRtext);




$sheet->setCellValueByColumnAndRow(0, $colum_text + $s + 1, 'СРЕДНЕЕ');
$sheet->getRowDimension($colum_text + $s + 1)->setRowHeight(22);
$sheet->mergeCellsByColumnAndRow(0, $colum_text + $s + 1, 1, $colum_text + $s + 1);
$m = 0;
$h = 0;
for ($i = 0; $i < count($array_resourse); $i++) {
    if ($array_resourse[$i]['id_param'] == 9 or $array_resourse[$i]['id_param'] == 16) {
        if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
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
            $teplo = $o / $s;
        } else {
            $teplo = $mass_arch[$i] / $s;
        }

        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($teplo, 2, '.', '') . '');
    } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
        if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
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
            $teplo = $o / $s;
        } else {
            $teplo = $mass_arch[$i] / $s;
        }


        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($teplo, 2, '.', '') . '');
    } elseif ($array_resourse[$i]['id_param'] == 5 or $array_resourse[$i]['id_param'] == 6 or $array_resourse[$i]['id_param'] == 12) {
        $temp_s = $mass_arch[$i] / $s;
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($temp_s, 2, '.', '') . '');
        // echo '<td></td>';
    } elseif ($array_resourse[$i]['id_param'] == 775) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($array_resourse[$i]['id_param'] == 3) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } elseif ($array_resourse[$i]['id_param'] == 4) {
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '');
    } else {
        $temp_s = $mass_arch[$i] / $s;
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 1, '' . number_format($temp_s, 2, '.', '') . '');
    }
}

$symb1 = 65;
$symb2 = 65 + 1 + 9;
$value1 = $colum_text+$s+1;
$value2 = $colum_text +$s+2;
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '' . $value1 . ':' . chr($symb2) . '' . $value2 . '')->applyFromArray($FontStyle11TNRtext);


$sheet->setCellValueByColumnAndRow(0, $colum_text + $s + 2, 'ИТОГО');
$sheet->getRowDimension($colum_text + $s + 2)->setRowHeight(22);
$sheet->mergeCellsByColumnAndRow(0, $colum_text + $s + 2, 1, $colum_text + $s + 2);
$m = 0;
$h = 0;


for ($i = 0; $i < count($array_resourse); $i++) {
    //echo $id_resours[$i]."<br>";
    if ($array_resourse[$i]['id_param'] == 9 or $array_resourse[$i]['id_param'] == 16) {

        if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
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



        $limit = number_format($teplo, 3, ".", "");
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . $limit . '');
        //echo '<td><b>' . $limit . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {

        if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
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
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '' . number_format($teplo,2,'.','' ). '');
        //echo '<td><b>' . substr(str_replace('.', ',', $mass_arch[$i]), 0, 6) . '</b></td>';
    } elseif ($array_resourse[$i]['id_param'] == 5 or $array_resourse[$i]['id_param'] == 6 or $array_resourse[$i]['id_param'] == 12) {
        $temp_s = $mass_arch[$i] / $s;
        $sheet->setCellValueByColumnAndRow($i + 2, $colum_text + $s + 2, '   ');
        //echo '<td><b>' . substr(str_replace('.', ',', $temp_s), 0, 4) . '</b></td>';
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
header("Content-Disposition: attachment; filename= отчет.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
