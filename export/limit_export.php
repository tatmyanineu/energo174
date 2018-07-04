<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../db_config.php';
session_start();


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
$sheet->setTitle('Лимиты');



//стиль для ячеек которые будут заголовками
$arHeadStyle = array(
    'font' => array(
        'bold' => true,
        'size' => 14,
        'name' => 'Times New Roman'
        ));
//стиль для ячеек с простым текстом
$arTextStyle = array(
    'font' => array(
        'size' => 14,
        'name' => 'Times New Roman'
        ));

$sheet->getStyle('A1:K4')->applyFromArray($arHeadStyle);

$sheet->getStyle('A1:K4')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:K4')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);


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

if ($_GET['month'] > 100) {
    switch ($_GET['month']) {
        case 101:
            $month = array(1, 2, 3);
            $num = cal_days_in_month(CAL_GREGORIAN, 3, $_GET['year']);
            $date1 = date('' . $_GET['year'] . '-01-01');
            $date2 = date('' . $_GET['year'] . '-03-' . $num);
            $date_now = $date2;
            break;
        case 102:
            $month = array(4, 5, 6);
            $num = cal_days_in_month(CAL_GREGORIAN, 6, $_GET['year']);
            $date1 = date('' . $_GET['year'] . '-04-01');
            $date2 = date('' . $_GET['year'] . '-06-' . $num);
            $date_now = $date2;
            break;
        case 103:
            $month = array(7, 8, 9);
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
} else {
    if (strtotime(date('Y-m-d')) == strtotime(date('' . $_GET['year'] . '-' . $_GET['month'] . '-01'))) {
        //$mon = strtotime("-1 month");
        $month = date('m', strtotime('-1 month'));
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $_GET['year']);
        $date1 = date('' . $_GET['year'] . '-' . $month . '-01');
        $date2 = date('' . $_GET['year'] . '-' . $month . '-' . $num);
        $date_now = $date2;
        //echo $date_now."<br>";
    } else {
        $month = $_GET['month'];
        $num = cal_days_in_month(CAL_GREGORIAN, $_GET['month'], $_GET['year']);
        $date1 = date('' . $_GET['year'] . '-' . $_GET['month'] . '-01');
        $date2 = date('' . $_GET['year'] . '-' . $_GET['month'] . '-' . $num);
        //echo $second_date . "<br>";
        if (date('m') == date($month)) {
            $date_now = date('Y-m-d');
        } else {
            $date_now = $date2;
        }
    }
}

$sql_not_alarm = pg_query('SELECT 
  public.alarm.plc_id
FROM
  public.alarm
WHERE
  public.alarm.text_alarm LIKE \'%Импульс%\'');
while ($result = pg_fetch_row($sql_not_alarm)) {
    $not_alarm[] = $result[0];
}


$sql_not_alarm = pg_query('SELECT 
  public.alarm.plc_id
FROM
  public.alarm
WHERE
  public.alarm.text_alarm LIKE \'%Интерфейс тепло%\'');
while ($result = pg_fetch_row($sql_not_alarm)) {
    $not_alarm_teplo[] = $result[0];
}


$sql_corection = pg_query('SELECT DISTINCT
  public.korrect.plc_id
FROM
  public.korrect
WHERE
  public.korrect.date_record >= \'' . $date1 . '\' AND 
  public.korrect.date_record <= \'' . $date2 . '\'');

while ($result = pg_fetch_row($sql_corection)) {
    $corect[] = $result[0];
}


$sql_ticket = pg_query('SELECT DISTINCT 
  public.ticket.id,
  public.ticket.date_ticket
FROM
  public.ticket
WHERE
  public.ticket.status < 4 AND 
  public.ticket.date_ticket >= \'' . $date1 . '\'AND 
  public.ticket.date_ticket <= \'' . $date2 . '\'');

while ($row = pg_fetch_row($sql_ticket)) {
    $ticket[] = $row[0];
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

if ($_GET['month'] > 100) {
    for ($i = 0; $i < count($month); $i++) {
        $sql_limit_part = pg_query('SELECT 
        public."LimitMonth_cnt".teplo,
        public."LimitMonth_cnt".voda,
        public."LimitMonth_cnt".name
      FROM
        public."LimitMonth_cnt"
      WHERE
        public."LimitMonth_cnt".id = ' . $month[$i] . '');

        $limit_teplo_part[] = pg_fetch_result($sql_limit_part, 0, 0);
        $limit_voda_part[] = pg_fetch_result($sql_limit_part, 0, 1);
    }
} else {
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
}



if ($_GET['month'] > 100) {
    switch ($_GET['month']) {
        case 101:
            break;
        case 102:
            break;
        case 103:
            break;
        case 104:
            break;
    }
} else {
    
}


if ($_GET['month'] > 100) {
    switch ($_GET['month']) {
        case 101:$sheet->setCellValue("A1", 'Отчет за I квартал');
            break;
        case 102:$sheet->setCellValue("A1", 'Отчет за II квартал');
            break;
        case 103:$sheet->setCellValue("A1", 'Отчет за III квартал');
            break;
        case 104:$sheet->setCellValue("A1", 'Отчет за IV квартал');
            break;
    }
} else {
    $sheet->setCellValue("A1", 'Отчет за ' . pg_fetch_result($sql_limit_part, 0, 2) . '');
}

$sheet->mergeCells('A1:K1');



$sheet->setCellValue("A3", '№');
$sheet->mergeCells('A3:A4');


$sheet->setCellValue("B3", 'Район');
$sheet->mergeCells('B3:B4');

$sheet->setCellValue("C3", 'Учереждение');
$sheet->mergeCells('C3:C4');

$sheet->setCellValue("D3", 'Адрес');
$sheet->mergeCells('D3:D4');

$sheet->setCellValue("E3", 'Передача данных');
$sheet->mergeCells('E3:F3');

$sheet->setCellValue("E4", 'Дата обновления');
$sheet->setCellValue("F4", 'Статус');

$sheet->setCellValue("G3", 'Тепло (Г.кал.)');
$sheet->mergeCells('G3:I3');

$sheet->setCellValue("G4", 'Кол-во записей');
$sheet->setCellValue("H4", 'Данные');
$sheet->setCellValue("I4", 'Лимит');

$sheet->setCellValue("J3", 'Вода (Куб.м.)');
$sheet->mergeCells('J3:L3');

$sheet->setCellValue("J4", 'Кол-во записей');
$sheet->setCellValue("K4", 'Данные');
$sheet->setCellValue("L4", 'Лимит');

$sheet->setCellValue("M3", 'C.O.');
$sheet->mergeCells('M3:M4');


$sheet->getStyle('A1:M4')->applyFromArray($arHeadStyle);

$sheet->getStyle('A1:M4')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:M4')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);

$sheet->getStyle('A3:M4')->applyFromArray($styleArray11);

$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$sheet->getPageSetup()->setFitToPage(true);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);


$sql_user_plc = pg_query('SELECT DISTINCT 
                          "Tepl"."User_cnt"."SurName",
                          "Tepl"."User_cnt"."PatronName",
                          "Tepl"."User_cnt".usr_id,
                          "Tepl"."Places_cnt"."Name",
                          "Tepl"."User_cnt"."Login",
                          "Tepl"."User_cnt"."Password",
                          "Tepl"."Places_cnt".plc_id
                        FROM
                          "Tepl"."GroupToUserRelations"
                          INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                          INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                          INNER JOIN "Tepl"."PlaceTyp_cnt" ON ("Tepl"."Places_cnt".typ_id = "Tepl"."PlaceTyp_cnt".typ_id)
                        WHERE
                          "Tepl"."PlaceTyp_cnt".typ_id = 10 AND 
                          "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                          "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
                        ORDER BY
                          "Tepl"."Places_cnt"."Name"');

$id_distinct = '';
if (pg_num_rows($sql_user_plc) != 0) {
    while ($row_user_plc = pg_fetch_row($sql_user_plc)) {

        $id_distinct[] = $row_user_plc[6];

        //echo "<input value='".$row_user_plc[3]."' type='submit' id='rectangular-button' class='".$row_user_plc[6]."'>";
    }
} else {
    $id_distinct[] = 0;
}

$fist_date1 = date('Y-m-d', strtotime('+1 day', strtotime($date1)));
//echo $fist_date_limit . "<br>";
$last_date2 = date('Y-m-d', strtotime('+1 day', strtotime($date2)));

$m = 0;
$numb = 1;
$arr_gr_limit[] = array();
for ($d = 0; $d < count($id_distinct); $d++) {
    $sql_pl_dist = pg_query('
        SELECT DISTINCT 
          "Tepl"."Places_cnt".plc_id,
          "Tepl"."Places_cnt"."Name"
        FROM
          "Tepl"."GroupToUserRelations"
          INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
          INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
          INNER JOIN "Tepl"."PlaceTyp_cnt" ON ("Tepl"."Places_cnt".typ_id = "Tepl"."PlaceTyp_cnt".typ_id)
        WHERE
          "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
          "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
          "Places_cnt".plc_id= ' . $id_distinct[$d] . '') or die("error: " . pg_errormessage());

    if (pg_num_rows($sql_pl_dist) != 0) {
        $col_dis = count($arr_resours) + 9;

        unset($arr_school);
        $sql_school_info = pg_query('SELECT 
                "Places_cnt1"."Name",
                "Tepl"."PropPlc_cnt"."ValueProp",
                "PropPlc_cnt1"."ValueProp",
                "Places_cnt1".plc_id
              FROM
                "Tepl"."Places_cnt" "Places_cnt1"
                INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
              WHERE
                "Places_cnt1".place_id = ' . pg_fetch_result($sql_pl_dist, 0, 0) . ' AND 
                "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                "PropPlc_cnt1".prop_id = 26
              ORDER BY
                "Tepl"."Places_cnt".plc_id');
        while ($result = pg_fetch_row($sql_school_info)) {
            $arr_school[] = array(
                'plc_id' => $result[3],
                'name' => $result[0],
                'adres' => '' . $result[1] . ' ' . $result[2] . ''
            );
        }

        $sql_data = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                                    "Places_cnt1"."Name",
                                    "Tepl"."PropPlc_cnt"."ValueProp",
                                    "PropPlc_cnt1"."ValueProp",
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                    "Tepl"."Arhiv_cnt"."DataValue",
                                    "Places_cnt1".plc_id
                                  FROM
                                    "Tepl"."Places_cnt"
                                    INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".plc_id = "Places_cnt1".place_id)
                                    INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
                                    INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                    INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."ParamResPlc_cnt".plc_id)
                                    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                    INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
                                    INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
                                  WHERE
                                    "Tepl"."Places_cnt".plc_id = ' . pg_fetch_result($sql_pl_dist, 0, 0) . ' AND 
                                    "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                                    "PropPlc_cnt1".prop_id = 26 AND 
                                    "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $fist_date1 . '\' AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $last_date2 . '\' AND 
                                    "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\'  AND 
                                    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
                                  ORDER BY
                                    "Places_cnt1"."Name",
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id"');
        unset($arr_plc_id);
        unset($arr_data);
        while ($result = pg_fetch_row($sql_data)) {
            $arr_plc_id[] = $result[6];
            $arr_data[] = array(
                'plc_id' => $result[6],
                'data' => $result[0],
                'value' => $result[5],
                'res_id' => $result[4]
            );
        }

        for ($i = 0; $i < count($arr_school); $i++) {
            $days = 0;

            $days_t = 0;
            $days_v = 0;

            unset($vte);
            unset($voda);

            $teplo = 0;
            $max_date = '';
            $vte = array();
            $voda = array();
            $gr_id = NULL;
            $gr_counter = 0;
            $gr_row = 0;


            $sql_limit_group = pg_query('SELECT DISTINCT 
                            public.group_plc.group_id
                          FROM
                            public.group_plc
                          WHERE
                            public.group_plc.plc_id = ' . $arr_school[$i]['plc_id']);
            $gr_id = pg_fetch_result($sql_limit_group, 0, 0);
            if (pg_num_rows($sql_limit_group) != 0) {
                //echo "<h3>Попался обьект из группы " . pg_fetch_result($sql_limit_group, 0, 0) . "</h3>";
                $key_gr = array_search(pg_fetch_result($sql_limit_group, 0, 0), $arr_gr_limit);
                if ($key_gr === false) {
                    $arr_gr_limit[] = pg_fetch_result($sql_limit_group, 0, 0);
                    /*
                      echo "<tr id='hover'>"
                      . "<td>" . $m . "</td>"
                      . "<td>" . pg_fetch_result($sql_limit_group, 0, 0) . "</td>"
                      . "<td> " . pg_fetch_result($sql_limit_group, 0, 4) . " </td>";

                      $_SESSION['rep_id'][] = pg_fetch_result($sql_limit_group, 0, 1);
                      $_SESSION['rep_m'][] = $m;
                      $_SESSION['rep_name'][] = pg_fetch_result($sql_limit_group, 0, 0);
                      $_SESSION['rep_addr'][] = pg_fetch_result($sql_limit_group, 0, 4);

                      view_archive_group(pg_fetch_result($sql_limit_group, 0, 1));
                      echo "</tr>";

                     * 
                     */


                    view_group_archive($gr_id);
                }
            } else {
                $sheet->setCellValueByColumnAndRow(0, 5 + $m, '' . $numb . '');
                $sheet->getStyleByColumnAndRow(0, 5 + $m)->applyFromArray($styleArray11);

                $sheet->setCellValueByColumnAndRow(1, 5 + $m, '' . pg_fetch_result($sql_pl_dist, 0, 1) . '');
                $sheet->getStyleByColumnAndRow(1, 5 + $m)->applyFromArray($styleArray11);
                $sheet->setCellValueByColumnAndRow(2, 5 + $m, '' . $arr_school[$i]['name'] . '');
                $sheet->getStyleByColumnAndRow(2, 5 + $m)->applyFromArray($styleArray11);
                $sheet->setCellValueByColumnAndRow(3, 5 + $m, '' . $arr_school[$i]['adres'] . '');
                $sheet->getStyleByColumnAndRow(3, 5 + $m)->applyFromArray($styleArray11);

                $keys = array_keys($arr_plc_id, $arr_school[$i]['plc_id']);
                $sql_device = pg_query('SELECT
                                    MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
                                    FROM
                                    "Tepl"."Places_cnt" "Places_cnt1"
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                    WHERE
                                    "Places_cnt1".plc_id = ' . $arr_school[$i]['plc_id'] . '');
                $dev_id = pg_fetch_result($sql_device, 0, 0);

                unset($vte);
                unset($voda);

                $teplo = 0;
                $max_date = '';
                $vte = array();
                $voda = array();

                array_archive($arr_school[$i]['plc_id'], $dev_id);

                if ($row_device[0] == 214 or $id_plc[$a] == 314 or $id_plc[$a] == 251 or $id_plc[$a] == 316 or $id_plc[$a] == 318) {
                    $z = 0;
                    $o = 0;
                    $p = 0;
                    //print_r($voda)."<br>";
                    //echo "count mass_voda = ".count($voda)."<br>";
                    $val = 0;
                    for ($l = count($vte) - 1; $l >= 0; $l--) {
                        //echo  "id == " .$id_plc[$a] .    "     l ==   " . $l . "  val ==  " . $vte[$l];
                        if ($l - 1 >= 0) {
                            $p = $vte[$l] - $vte[$l - 1];
                        }
                        $o = $o + $p;

                        //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                        $p = 0;
                    }
                    $teplo = $o;
                }


                $val = 0;
                $k_voda = 0;
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

                echo_data($arr_school[$i]['plc_id'], $max_date, $teplo, $val, $days_v, $days_t);

                $g += count($keys);
                //var_dump($keys);
                //$g+=count($keys);
            }
        }
    }
}

$sheet->getStyle('A5:O500')->applyFromArray($arTextStyle);


$sheet->getStyle('A5:M500')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A5:M500')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);



for ($a = 65; $a < 80; $a++) {
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
header("Content-Disposition: attachment; filename=Выгрузка по лимитам.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');

function echo_data($id, $max_date, $teplo, $val, $days_v, $days_t) {
    global $styleArray11, $sheet, $m, $arr_all_limit, $date_now, $limit_teplo_part, $ticket, $limit_voda_part, $month, $corect, $not_alarm, $num, $gr_id, $gr_counter, $gr_row, $not_alarm_teplo, $numb;


    $kol_day = (strtotime($date_now) - strtotime($max_date)) / (60 * 60 * 24);

    if ($kol_day > 3) {
        $sheet->setCellValueByColumnAndRow(4, 5 + $m, ' - ');
        $sheet->getStyleByColumnAndRow(4, 5 + $m)->applyFromArray($styleArray11);
        $sheet->setCellValueByColumnAndRow(5, 5 + $m, 'Нет связи');
        $sheet->getStyleByColumnAndRow(5, 5 + $m)->applyFromArray($styleArray11);
        $sheet->getStyleByColumnAndRow(4, 5 + $m)->getFill()->
                getStartColor()->applyFromArray(array('rgb' => 'ff2400'));
        $sheet->getStyleByColumnAndRow(5, 5 + $m)->getFill()->
                getStartColor()->applyFromArray(array('rgb' => 'ffff00'));
    } else {
        $sheet->setCellValueByColumnAndRow(4, 5 + $m, '' . date('d.m.Y', strtotime($max_date)) . '');
        $sheet->getStyleByColumnAndRow(4, 5 + $m)->applyFromArray($styleArray11);
        $sheet->setCellValueByColumnAndRow(5, 5 + $m, 'OK');
        $sheet->getStyleByColumnAndRow(5, 5 + $m)->applyFromArray($styleArray11);
    }




    $sum_teplo = 0;
    $arch_teplo = 0;

    if ($gr_id != NULL) {
        $key_limit = array_search($gr_id, array_column($arr_all_limit, 'plc_id'));

        $sum_teplo = summ_group_teplo($gr_id);
        $arch_teplo = $teplo;
        $teplo = $sum_teplo;


        $summ_voda = summ_group_voda($gr_id);
        $arch_voda = $val;
        $val = $summ_voda;
    } else {
        $key_limit = array_search($id, array_column($arr_all_limit, 'plc_id'));
    }


    if ($key_limit !== false) {
        if ($_GET['month'] > 100) {
            $num = 0;
            for ($l = 0; $l < count($month); $l++) {
                $lim1 += ((float) $arr_all_limit[$key_limit]['teplo'] / 100 ) * (float) $limit_teplo_part[$l];
                $lim2 += ((float) $arr_all_limit[$key_limit]['voda'] / 100) * (float) $limit_voda_part[$l];
                $num += cal_days_in_month(CAL_GREGORIAN, $month[$l], $_GET['year']);
            }
        } else {

            $lim1 = ($arr_all_limit[$key_limit]['teplo'] * $limit_teplo_part) / 100;
            $lim2 = ($arr_all_limit[$key_limit]['voda'] * $limit_voda_part) / 100;
        }

        if ($days_t == $num) {
            $sheet->setCellValueByColumnAndRow(6, 5 + $m, '' . $days_t . '');
            $sheet->getStyleByColumnAndRow(6, 5 + $m)->applyFromArray($styleArray11);
        } else {
            $sheet->setCellValueByColumnAndRow(6, 5 + $m, '' . $days_t . '');
            $sheet->getStyleByColumnAndRow(6, 5 + $m)->getFill()->
                    setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet->getStyleByColumnAndRow(6, 5 + $m)->getFill()->
                    getStartColor()->applyFromArray(array('rgb' => 'ff9a89'));
            $sheet->getStyleByColumnAndRow(6, 5 + $m)->applyFromArray($styleArray11);
        }



        $t = array_search($id, $not_alarm_teplo);
        if ($t != FALSE) {
            $sheet->setCellValueByColumnAndRow(7, 5 + $m, ' неисправен интерф. порт ');
            $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);

            if ($gr_id != NULL) {
                if ($gr_counter == 1) {
                    $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
                    $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
                    $sheet->mergeCellsByColumnAndRow(8, 5 + $m, 8, 5 + $m + $gr_row - 1);
                }
            } else {
                $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
            }
        } else {


            if ($teplo == 0) {
                //echo "<td>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
                //echo "<td> " . str_replace('.', ',', $lim1) . " </td>";
                $sheet->setCellValueByColumnAndRow(7, 5 + $m, '' . substr(str_replace('.', ',', $teplo), 0, 6) . '');
                $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        //echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                        $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
                        $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
                        $sheet->mergeCellsByColumnAndRow(8, 5 + $m, 8, 5 + $m + $gr_row - 1);
                    }
                } else {
                    $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
                    $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
                }
            } elseif ($teplo > $lim1 * 0.9 and $teplo < $lim1) {
                //echo "<td  class='warning'>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
                //echo "<td  class='warning'> " . str_replace('.', ',', $lim1) . " </td>";
                if ($gr_id != NULL) {
                    $sheet->setCellValueByColumnAndRow(7, 5 + $m, '' . substr(str_replace('.', ',', $arch_teplo), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);
                } else {
                    $sheet->setCellValueByColumnAndRow(7, 5 + $m, '' . substr(str_replace('.', ',', $teplo), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);
                }
                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        //echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                        $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
                        $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
                        $sheet->mergeCellsByColumnAndRow(8, 5 + $m, 8, 5 + $m + $gr_row - 1);
                    }
                } else {
                    $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
                    $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
                }

                $sheet->getStyleByColumnAndRow(7, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $sheet->getStyleByColumnAndRow(7, 5 + $m)->getFill()->
                        getStartColor()->applyFromArray(array('rgb' => 'ffff00'));
                $sheet->getStyleByColumnAndRow(8, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $sheet->getStyleByColumnAndRow(8, 5 + $m)->getFill()->
                        getStartColor()->applyFromArray(array('rgb' => 'ffff00'));
            } elseif ($teplo < $lim1 * 0.9) {
                //echo "<td  class='success'>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
                //echo "<td  class='success'> " . str_replace('.', ',', $lim1) . " </td>";

                if ($gr_id != NULL) {
                    $sheet->setCellValueByColumnAndRow(7, 5 + $m, '' . substr(str_replace('.', ',', $arch_teplo), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);
                } else {
                    $sheet->setCellValueByColumnAndRow(7, 5 + $m, '' . substr(str_replace('.', ',', $teplo), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);
                }

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        //echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                        $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
                        $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
                        $sheet->mergeCellsByColumnAndRow(8, 5 + $m, 8, 5 + $m + $gr_row - 1);
                    }
                } else {
                    $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
                    $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
                }

//            $sheet->setCellValueByColumnAndRow(7, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
//            $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);
                $sheet->getStyleByColumnAndRow(7, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);

                $sheet->getStyleByColumnAndRow(8, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            } elseif ($teplo > $lim1) {
                //echo "<td class='danger'>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
                //echo "<td  class='danger'> " . str_replace('.', ',', $lim1) . " </td>";

                if ($gr_id != NULL) {
                    $sheet->setCellValueByColumnAndRow(7, 5 + $m, '' . substr(str_replace('.', ',', $arch_teplo), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);
                } else {
                    $sheet->setCellValueByColumnAndRow(7, 5 + $m, '' . substr(str_replace('.', ',', $teplo), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);
                }

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        //echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                        $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
                        $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
                        $sheet->mergeCellsByColumnAndRow(8, 5 + $m, 8, 5 + $m + $gr_row - 1);
                    }
                } else {
                    $sheet->setCellValueByColumnAndRow(8, 5 + $m, '' . str_replace('.', ',', $lim1) . '');
                    $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);
                }

                $sheet->getStyleByColumnAndRow(7, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $sheet->getStyleByColumnAndRow(7, 5 + $m)->getFill()->
                        getStartColor()->applyFromArray(array('rgb' => 'ff2400'));
                $sheet->getStyleByColumnAndRow(8, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $sheet->getStyleByColumnAndRow(8, 5 + $m)->getFill()->
                        getStartColor()->applyFromArray(array('rgb' => 'ff2400'));
            }
        }

        if ($days_v == $num) {
            $sheet->setCellValueByColumnAndRow(9, 5 + $m, '' . $days_v . '');
            $sheet->getStyleByColumnAndRow(9, 5 + $m)->applyFromArray($styleArray11);
        } else {
            $sheet->setCellValueByColumnAndRow(9, 5 + $m, '' . $days_v . '');
            $sheet->getStyleByColumnAndRow(9, 5 + $m)->applyFromArray($styleArray11);
            $sheet->getStyleByColumnAndRow(9, 5 + $m)->getFill()->
                    setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet->getStyleByColumnAndRow(9, 5 + $m)->getFill()->
                    getStartColor()->applyFromArray(array('rgb' => 'ff9a89'));
        }


        $v = array_search($id, $not_alarm);
        if ($v != FALSE) {
            $sheet->setCellValueByColumnAndRow(10, 5 + $m, ' неисправен импульс ');
            $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);


            if ($gr_id != NULL) {
                if ($gr_counter == 1) {
                    $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                    $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
                    $sheet->mergeCellsByColumnAndRow(11, 5 + $m, 11, 5 + $m + $gr_row - 1);
                }
            } else {
                $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
            }
        } else {
            if ($val == 0) {
                //echo "<td>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";
                //echo "<td> " . str_replace('.', ',', $lim2) . " </td>";

                if ($gr_id != NULL) {
                    $sheet->setCellValueByColumnAndRow(10, 5 + $m, '' . substr(str_replace('.', ',', $arch_voda), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);
                } else {
                    $sheet->setCellValueByColumnAndRow(10, 5 + $m, '' . substr(str_replace('.', ',', $val), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);
                }




                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        //echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                        $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                        $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
                        $sheet->mergeCellsByColumnAndRow(11, 5 + $m, 11, 5 + $m + $gr_row - 1);
                    }
                } else {
                    $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                    $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
                }
            } elseif ($val > $lim2 * 0.9 and $val < $lim2) {
                //echo "<td  class='warning'>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";
                //echo "<td  class='warning'> " . str_replace('.', ',', $lim2) . " </td>";


                if ($gr_id != NULL) {
                    $sheet->setCellValueByColumnAndRow(10, 5 + $m, '' . substr(str_replace('.', ',', $arch_voda), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);
                } else {
                    $sheet->setCellValueByColumnAndRow(10, 5 + $m, '' . substr(str_replace('.', ',', $val), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);
                }
                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        //echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                        $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                        $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
                        $sheet->mergeCellsByColumnAndRow(11, 5 + $m, 11, 5 + $m + $gr_row - 1);
                    }
                } else {
                    $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                    $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
                }

                $sheet->getStyleByColumnAndRow(10, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $sheet->getStyleByColumnAndRow(10, 5 + $m)->getFill()->
                        getStartColor()->applyFromArray(array('rgb' => 'ffff00'));
                $sheet->getStyleByColumnAndRow(11, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $sheet->getStyleByColumnAndRow(11, 5 + $m)->getFill()->
                        getStartColor()->applyFromArray(array('rgb' => 'ffff00'));
            } elseif ($val < $lim2 * 0.9) {
                //echo "<td  class='success'>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";
                //echo "<td  class='success'> " . str_replace('.', ',', $lim2) . " </td>";


                if ($gr_id != NULL) {
                    $sheet->setCellValueByColumnAndRow(10, 5 + $m, '' . substr(str_replace('.', ',', $arch_voda), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);
                } else {
                    $sheet->setCellValueByColumnAndRow(10, 5 + $m, '' . substr(str_replace('.', ',', $val), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);
                }

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        //echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                        $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                        $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
                        $sheet->mergeCellsByColumnAndRow(11, 5 + $m, 11, 5 + $m + $gr_row - 1);
                    }
                } else {
                    $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                    $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
                }
                $sheet->getStyleByColumnAndRow(10, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $sheet->getStyleByColumnAndRow(11, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            } elseif ($val > $lim2) {
                //echo "<td class='danger'>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";
                //echo "<td  class='danger'> " . str_replace('.', ',', $lim2) . " </td>";


                if ($gr_id != NULL) {
                    $sheet->setCellValueByColumnAndRow(10, 5 + $m, '' . substr(str_replace('.', ',', $arch_voda), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);
                } else {
                    $sheet->setCellValueByColumnAndRow(10, 5 + $m, '' . substr(str_replace('.', ',', $val), 0, 6) . '');
                    $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);
                }

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        //echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                        $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                        $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
                        $sheet->mergeCellsByColumnAndRow(11, 5 + $m, 11, 5 + $m + $gr_row - 1);
                    }
                } else {
                    $sheet->setCellValueByColumnAndRow(11, 5 + $m, '' . str_replace('.', ',', $lim2) . '');
                    $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);
                }

                $sheet->getStyleByColumnAndRow(10, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $sheet->getStyleByColumnAndRow(10, 5 + $m)->getFill()->
                        getStartColor()->applyFromArray(array('rgb' => 'ff2400'));
                $sheet->getStyleByColumnAndRow(11, 5 + $m)->getFill()->
                        setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $sheet->getStyleByColumnAndRow(11, 5 + $m)->getFill()->
                        getStartColor()->applyFromArray(array('rgb' => 'ff2400'));
            }
        }




        $c = array_search($id, $corect);
        if ($c !== false) {
            $tick = array_search($id, $ticket);
            if ($tick !== false) {
                $sheet->setCellValueByColumnAndRow(12, 5 + $m, ' С.О.');
            } else {
                $sheet->setCellValueByColumnAndRow(12, 5 + $m, ' коррекция');
            }
        } else {
            $tick = array_search($id, $ticket);
            if ($tick !== false) {
                $sheet->setCellValueByColumnAndRow(12, 5 + $m, ' С.О.');
            } else {
                $sheet->setCellValueByColumnAndRow(12, 5 + $m, ' - ');
            }
        }
        $sheet->getStyleByColumnAndRow(12, 5 + $m)->applyFromArray($styleArray11);
    } else {

        $sheet->setCellValueByColumnAndRow(6, 5 + $m, '' . $days_t . '');
        $sheet->getStyleByColumnAndRow(6, 5 + $m)->applyFromArray($styleArray11);
        $sheet->setCellValueByColumnAndRow(7, 5 + $m, '' . substr(str_replace('.', ',', $teplo), 0, 6) . '');
        $sheet->getStyleByColumnAndRow(7, 5 + $m)->applyFromArray($styleArray11);
        $sheet->setCellValueByColumnAndRow(8, 5 + $m, ' - ');
        $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);



        $sheet->setCellValueByColumnAndRow(9, 5 + $m, '' . $days_v . '');
        $sheet->getStyleByColumnAndRow(9, 5 + $m)->applyFromArray($styleArray11);
        $sheet->setCellValueByColumnAndRow(10, 5 + $m, '' . substr(str_replace('.', ',', $val), 0, 6) . '');
        $sheet->getStyleByColumnAndRow(10, 5 + $m)->applyFromArray($styleArray11);
        $sheet->setCellValueByColumnAndRow(11, 5 + $m, ' - ');
        $sheet->getStyleByColumnAndRow(11, 5 + $m)->applyFromArray($styleArray11);


        if ($c !== false) {
            $sheet->setCellValueByColumnAndRow(12, 5 + $m, ' коррекция');
        } else {
            $sheet->setCellValueByColumnAndRow(12, 5 + $m, ' - ');
        }
        $sheet->getStyleByColumnAndRow(12, 5 + $m)->applyFromArray($styleArray11);

        /*
          $sheet->setCellValueByColumnAndRow(8, 5 + $m, '-');
          $sheet->getStyleByColumnAndRow(8, 5 + $m)->applyFromArray($styleArray11);

         * }
         */
    }
    $m++;
    $numb++;
}

function array_archive($plc_id, $dev_id) {

    global $arr_plc_id, $arr_data;
    global $teplo, $voda, $max_date, $vte, $days_t, $days_v;
    $k_tep1 = null;
    $k_tep2 = null;

    $keys = array_keys($arr_plc_id, $plc_id);
    for ($j = 0; $j < count($keys); $j++) {
        if ($plc_id == 375) {
            //echo $arr_data[$keys[$j]]['res_id'] . " " . $arr_data[$keys[$j]]['value'] . " " . $arr_data[$keys[$j]]['data'] . " <br>";
        }
        if (strtotime($max_date) < strtotime($arr_data[$keys[$j]]['data'])) {
            $max_date = $arr_data[$keys[$j]]['data'];
        }

        if ($arr_data[$keys[$j]]['res_id'] == 9) {
            if ($dev_id == 214 or $plc_id == 314 or $plc_id == 251 or $plc_id == 316 or $plc_id == 318) {
                $vte[] = $data_val[$b];
                $k_tep1++;
            } else {
                $teplo += $arr_data[$keys[$j]]['value'];
                $k_tep1++;
            }
        } elseif ($arr_data[$keys[$j]]['res_id'] == 16) {
            $teplo += $arr_data[$keys[$j]]['value'];
            $k_tep2++;
        }



        if ($arr_data[$keys[$j]]['res_id'] == 1) {
            $voda[0][] = $arr_data[$keys[$j]]['value'];
        }
        if ($arr_data[$keys[$j]]['res_id'] == 308) {
            $voda[1][] = $arr_data[$keys[$j]]['value'];
        }
        if ($arr_data[$keys[$j]]['res_id'] == 310) {
            $voda[2][] = $arr_data[$keys[$j]]['value'];
        }
        if ($arr_data[$keys[$j]]['res_id'] == 414) {
            $voda[3][] = $arr_data[$keys[$j]]['value'];
        }
        if ($arr_data[$keys[$j]]['res_id'] == 420) {
            $voda[4][] = $arr_data[$keys[$j]]['value'];
        }
    }

    if ($k_tep1 != null AND $k_tep2 != null) {
        if ($k_tep1 == $k_tep2) {
            $days_t = $k_tep1;
        } elseif ($k_tep1 > $k_tep2) {
            $days_t = $k_tep2;
        } elseif ($k_tep1 < $k_tep2) {
            $days_t = $k_tep1;
        }
    } else {
        if ($k_tep1 == null) {
            $days_t = 0;
        } else {
            $days_t = $k_tep1;
        }
    }
}

function view_archive_group($gr_id) {
    $summ_teplo = 0;
    $summ_voda = 0;

    global $teplo, $voda, $max_date, $vte;


    $max_date = '';
    $sql_plc_group = pg_query('SELECT 
                    public.group_plc.plc_id
                  FROM
                    public.group_plc
                  WHERE
                    public.group_plc.group_id = = ' . $gr_id);
    while ($resutl = pg_fetch_row($sql_plc_group)) {
        $teplo = 0;

        $vte = array();
        $voda = array();

        $sql_device = pg_query('SELECT
                                    MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
                                    FROM
                                    "Tepl"."Places_cnt" "Places_cnt1"
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                    WHERE
                                    "Places_cnt1".plc_id = ' . $resutl[0] . '');
        $dev_id = pg_fetch_result($sql_device, 0, 0);
        array_archive($resutl[0], $dev_id);


        if ($dev_id == 214 or $resutl[0] == 314 or $resutl[0] == 251 or$resutl[0] == 316 or $resutl[0] == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            //print_r($voda)."<br>";
            //echo "count mass_voda = ".count($voda)."<br>";
            $val = 0;
            for ($l = count($vte) - 1; $l >= 0; $l--) {
                //echo  "id == " .$id_plc[$a] .    "     l ==   " . $l . "  val ==  " . $vte[$l];
                if ($l - 1 >= 0) {
                    $p = $vte[$l] - $vte[$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        }
        $summ_teplo += $teplo;

        $val = 0;
        for ($l = 0; $l < count($voda); $l++) {
            //print_r($voda[$l])."<br>";
            $n1 = count($voda[$l]) - 1;
            $z = 0;
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
        $summ_voda += $val;
    }

    echo_data($gr_id, $max_date, $summ_teplo, $summ_voda);
}

function view_group_archive($gr_id) {
    global $arr_school, $m, $sheet, $styleArray11;
    global $teplo, $voda, $max_date, $vte, $days_t, $days_v, $gr_counter, $gr_row, $sql_pl_dist, $numb;

    $sql_plc_group = pg_query('SELECT DISTINCT 
                            public.group_plc.plc_id
                          FROM
                            public.group_plc
                          WHERE
                            public.group_plc.group_id = ' . $gr_id . '');

    $gr_row = pg_num_rows($sql_plc_group);
    if ($gr_row == 0) {
        $gr_row = 1;
    }

    while ($result = pg_fetch_row($sql_plc_group)) {

        $days_t = 0;
        $days_v = 0;
        $gr_counter++;
        $teplo = 0;
        $max_date = '';
        $vte = array();
        $voda = array();

        $key = array_search($result[0], array_column($arr_school, 'plc_id'));
        // $m++;

        $sheet->setCellValueByColumnAndRow(0, 5 + $m, '' . $numb . '');
        $sheet->getStyleByColumnAndRow(0, 5 + $m)->applyFromArray($styleArray11);

        $sheet->setCellValueByColumnAndRow(1, 5 + $m, '' . pg_fetch_result($sql_pl_dist, 0, 1) . '');
        $sheet->getStyleByColumnAndRow(1, 5 + $m)->applyFromArray($styleArray11);
        $sheet->setCellValueByColumnAndRow(2, 5 + $m, '' . $arr_school[$key]['name'] . '');
        $sheet->getStyleByColumnAndRow(2, 5 + $m)->applyFromArray($styleArray11);
        $sheet->setCellValueByColumnAndRow(3, 5 + $m, '' . $arr_school[$key]['adres'] . '');
        $sheet->getStyleByColumnAndRow(3, 5 + $m)->applyFromArray($styleArray11);


        /* echo "<tr id='hover'>"
          . "<td>" . $m . "</td>"
          . "<td>" . $arr_school[$key]['name'] . "</td>"
          . "<td> " . $arr_school[$key]['adres'] . " </td>";
         */

        //echo "<p>" . $result[0] . " " . $arr_school[$key]['name'] . " " . $arr_school[$key]['adres'] . " " . $gr_id . " " . $gr_counter . " " . $gr_row . "</p>";

        $sql_device = pg_query('SELECT
                                    MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
                                    FROM
                                    "Tepl"."Places_cnt" "Places_cnt1"
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                    WHERE
                                    "Places_cnt1".plc_id = ' . $result[0] . '');
        $dev_id = pg_fetch_result($sql_device, 0, 0);

        array_archive($result[0], $dev_id);

        if ($dev_id == 214 or $result[0] == 314 or $result[0] == 251 or $result[0] == 316 or $result[0] == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            //print_r($voda)."<br>";
            //echo "count mass_voda = ".count($voda)."<br>";
            $val = 0;
            for ($l = count($vte) - 1; $l >= 0; $l--) {
                //echo  "id == " .$id_plc[$a] .    "     l ==   " . $l . "  val ==  " . $vte[$l];
                if ($l - 1 >= 0) {
                    $p = $vte[$l] - $vte[$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        }



        $value = summ_voda($voda);

        echo_data($result[0], $max_date, $teplo, $value, $days_v, $days_t);

        //echo "</tr>";
    }
}

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

function summ_group_teplo($gr_id) {
    global $arr_plc_id, $arr_data;

    $sql_plc_group = pg_query('SELECT DISTINCT 
                            public.group_plc.plc_id
                          FROM
                            public.group_plc
                          WHERE
                            public.group_plc.group_id = ' . $gr_id . '');
    $arr_teplo[] = array();
    while ($result = pg_fetch_row($sql_plc_group)) {

        $sql_device = pg_query('SELECT
                                    MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
                                    FROM
                                    "Tepl"."Places_cnt" "Places_cnt1"
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                    WHERE
                                    "Places_cnt1".plc_id = ' . $result[0] . '');
        $dev_id = pg_fetch_result($sql_device, 0, 0);

        $k_tep1 = null;
        $k_tep2 = null;
        $teplo = 0;
        $keys = array_keys($arr_plc_id, $result[0]);
        for ($j = 0; $j < count($keys); $j++) {
            if ($arr_data[$keys[$j]]['res_id'] == 9) {
                if ($dev_id == 214 or $result[0] == 314 or $result[0] == 251 or $result[0] == 316 or $result[0] == 318) {
                    $vte[] = $data_val[$b];
                } else {
                    $teplo += $arr_data[$keys[$j]]['value'];
                }
            } elseif ($arr_data[$keys[$j]]['res_id'] == 16) {
                $teplo += $arr_data[$keys[$j]]['value'];
            }
        }
        if ($dev_id == 214 or $result[0] == 314 or $result[0] == 251 or $result[0] == 316 or $result[0] == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            //print_r($voda)."<br>";
            //echo "count mass_voda = ".count($voda)."<br>";
            $val = 0;
            for ($l = count($vte) - 1; $l >= 0; $l--) {
                //echo  "id == " .$id_plc[$a] .    "     l ==   " . $l . "  val ==  " . $vte[$l];
                if ($l - 1 >= 0) {
                    $p = $vte[$l] - $vte[$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        }

        $arr_teplo[] = $teplo;
    }
    return array_sum($arr_teplo);
}

function summ_group_voda($gr_id) {
    global $arr_plc_id, $arr_data;

    $sql_plc_group = pg_query('SELECT DISTINCT 
                            public.group_plc.plc_id
                          FROM
                            public.group_plc
                          WHERE
                            public.group_plc.group_id = ' . $gr_id . '');
    $arr_teplo[] = array();
    while ($result = pg_fetch_row($sql_plc_group)) {
        $keys = array_keys($arr_plc_id, $plc_id);
        for ($j = 0; $j < count($keys); $j++) {
            if ($arr_data[$keys[$j]]['res_id'] == 1) {
                $voda[0][] = $arr_data[$keys[$j]]['value'];
            }
            if ($arr_data[$keys[$j]]['res_id'] == 308) {
                $voda[1][] = $arr_data[$keys[$j]]['value'];
            }
            if ($arr_data[$keys[$j]]['res_id'] == 310) {
                $voda[2][] = $arr_data[$keys[$j]]['value'];
            }
            if ($arr_data[$keys[$j]]['res_id'] == 414) {
                $voda[3][] = $arr_data[$keys[$j]]['value'];
            }
            if ($arr_data[$keys[$j]]['res_id'] == 420) {
                $voda[4][] = $arr_data[$keys[$j]]['value'];
            }
        }

        $val = 0;
        for ($l = 0; $l < count($voda); $l++) {
            //print_r($voda[$l])."<br>";
            $n1 = count($voda[$l]) - 1;
            $z = 0;
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
        $summ_voda += $val;
    }
    return $summ_voda;
}
