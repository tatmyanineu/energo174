<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../db_config.php';
include '../include/func_voda.php';
$id_distinct = explode(" ", $_POST['id_dist']);

$_SESSION['rep_id'] = '';
$_SESSION['rep_m'] = '';
$_SESSION['rep_name'] = '';
$_SESSION['rep_addr'] = '';


$_SESSION['rep_date'] = '';
$_SESSION['rep_stat'] = '';

$_SESSION['rep_tep_val'] = '';
$_SESSION['rep_tep_lim'] = '';
$_SESSION['rep_tep_col'] = '';

$_SESSION['rep_vod_val'] = '';
$_SESSION['rep_vod_lim'] = '';
$_SESSION['rep_vod_col'] = '';

$_SESSION['rep_plc_err'] = '';



if (strtotime(date('Y-m-d')) == strtotime(date('' . $_POST['year'] . '-' . $_POST['month'] . '-01'))) {
    //$mon = strtotime("-1 month");
    $month = date('m', strtotime('-1 month'));
    $num = cal_days_in_month(CAL_GREGORIAN, $month, $_POST['year']);
    $date1 = date('' . $_POST['year'] . '-' . $month . '-01');
    $date2 = date('' . $_POST['year'] . '-' . $month . '-' . $num);
    $date_now = $date2;
    //echo $date_now."<br>";
} else {
    $month = $_POST['month'];
    $num = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);
    $date1 = date('' . $_POST['year'] . '-' . $_POST['month'] . '-01');
    $date2 = date('' . $_POST['year'] . '-' . $_POST['month'] . '-' . $num);
    //echo $second_date . "<br>";
    if (date('m') == date($month)) {
        $date_now = date('Y-m-d');
    } else {
        $date_now = $date2;
    }
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


echo '<div class="tab-pane fade in active" id="home">';

echo "<h2 id='center_h1'>Отчет за " . pg_fetch_result($sql_limit_part, 0, 2) . " " . $_POST['year'] . "г.</h2>";



echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b</td>
            <td rowspan=2 data-query='1'><b>Учереждение</b</td>
            <td rowspan=2 data-query='2'><b>Адрес</b</td>
            <td colspan=2><b>Передача данных</b</td>
            <td colspan=3><b>Тепло (ГКал)</b</td>
            <td colspan=3><b>Вода (м<sup>3</sup>)</b</td>
            <td rowspan=2><b>С.О.</b</td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b</td>
                <td data-query='4'><b>Статус</b></td>
                <td ><b>Кол-во записей</b></td>
                <td data-query='5'><b>Данные</b</td>
                <td data-query='6'><b>Лимит</b</td>
                <td ><b>Кол-во записей</b></td>
                <td data-query='7'><b>Данные</b</td>
                <td data-query='8'><b>Лимит</b</td>
            </tr>
        </thead>
        <tbody>";
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
        $col_dis = count($arr_resours) + 12;
        echo "<td class='dist' colspan='" . $col_dis . "'><b>" . pg_fetch_result($sql_pl_dist, 0, 1) . "</b></td>";

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
        $fist_date1 = date('Y-m-d', strtotime('+1 day', strtotime($date1)));
        //echo $fist_date_limit . "<br>";
        $last_date2 = date('Y-m-d', strtotime('+1 day', strtotime($date2)));
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
        while ($result = pg_fetch_row($sql_data)) {
            $arr_plc_id[] = $result[6];
            $arr_data[] = array(
                'plc_id' => $result[6],
                'data' => $result[0],
                'value' => $result[5],
                'res_id' => $result[4]
            );
        }
        $m = 1;



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
            $mass_voda = array();
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

                echo "<tr data-href='object.php?id_object=" . $arr_school[$i]['plc_id'] . "' id='hover' >"
                . "<td>" . $m . "</td>"
                . "<td>" . $arr_school[$i]['name'] . "</td>"
                . "<td>" . $arr_school[$i]['adres'] . "</td>";

                $_SESSION['rep_id'][] = $arr_school[$i]['plc_id'];
                $_SESSION['rep_m'][] = $m;
                $_SESSION['rep_name'][] = $arr_school[$i]['name'];
                $_SESSION['rep_addr'][] = $arr_school[$i]['adres'];

                $sql_device = pg_query('SELECT 
                        "Tepl"."Device_cnt".dev_typ_id
                      FROM
                        "Tepl"."Places_cnt" "Places_cnt1"
                        INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                        INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                        INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                      WHERE
                        "Tepl"."TypeDevices"."Name" NOT LIKE \'%Пуль%\' AND 
                        "Places_cnt1".plc_id =  ' . $arr_school[$i]['plc_id'] . '');
                $dev_id = pg_fetch_result($sql_device, 0, 0);


                array_archive($arr_school[$i]['plc_id'], $dev_id);

                if ($dev_id == 214 or $arr_school[$i]['plc_id'] == 314 or $arr_school[$i]['plc_id'] == 251 or $arr_school[$i]['plc_id'] == 316 or $arr_school[$i]['plc_id'] == 318) {
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

//                $val = 0;
//                $k_voda = 0;
//                for ($l = 0; $l < count($voda); $l++) {
//                    //print_r($voda[$l])."<br>";
//                    $n1 = count($voda[$l]) - 1;
//                    $z = 0;
//                    if ($l == 0) {
//                        $k_voda = count($voda[$l]);
//                    } else {
//                        if ($k_voda > count($voda[$l])) {
//                            $k_voda = count($voda[$l]);
//                        }
//                    }
//
//                    for ($n = 0; $n < count($voda[$l]); $n++) {
//
//                        if ($n == $n1) {
//                            $z = $z;
//                            //echo "n=" .$n." mas = ". $voda[$l][$n]."    z=".$z."  <br>" ;
//                        }
//                        if ($n >= 0 and $n < $n1) {
//                            if ($voda[$l][$n]) {
//                                $z = $z + $voda[$l][$n + 1] - $voda[$l][$n];
//                            }
//                            //echo "n=" .$n." mas = ". $voda[$l][$n]."   mas+1 =  ".$voda[$l][$n+1]. "     z=".$z."  <br>" ;
//                        }
//                    }
//                    $val = $val + $z;
//                    //echo "Z ====".$z."  <br>";
//                }

                $sql_korrect = pg_query('SELECT DISTINCT 
            public.korrect.old_value,
            public.korrect.new_value,
            public.korrect.date_record,
            "Tepl"."ParamResPlc_cnt"."ParamRes_id"
          FROM
            "Tepl"."ParamResPlc_cnt"
            INNER JOIN public.korrect ON ("Tepl"."ParamResPlc_cnt".prp_id = public.korrect.prp_id)
          WHERE
            public.korrect.plc_id = ' . $arr_school[$i]['plc_id'] . ' AND 
            public.korrect.date_time >= \'' . $date1 . '\' AND 
            public.korrect.date_time <= \'' . $date2 . '\'');
                $korrec_arr[] = array();
                while ($result = pg_fetch_row($sql_korrect)) {
                    $korrec_arr[] = array(
                        'date' => $result[2],
                        'id_res' => $result[3]
                    );
                }

                if (pg_num_rows($sql_korrect) > 0) {
                    $val = summ_voda_korrect_for_limit($mass_voda, $arr_school[$i]['plc_id'], $date1, $date2);
                } else {
                    $val = summ_voda_for_limit($mass_voda);
                }


                $days_v = 31;

                echo_data($arr_school[$i]['plc_id'], $max_date, $teplo, $val, $days_v, $days_t);

                //var_dump($keys);
                $g += count($keys);
                echo "</tr>";
                $m++;
                
                unset($korrec_arr);
            }
        }
    }
}

echo '</tbody>'
 . '</table></div></div>';

echo '<div class="tab-pane fade in" id="charts">'
 . '<div id="container" style = "width: 80%"></div>'
 . '<div id="container_voda" style = "width: 80%"></div>';


echo "<script type='text/javascript'>";
echo "$(function () { "
 . " var chart = new Highcharts.Chart({ "
 . "   chart: { "
 . "      renderTo: 'container', "
 . "      type: 'column', "
 . "      options3d: { "
 . "          enabled: true, "
 . "          alpha: 0, "
 . "          beta: 2, "
 . "          depth: 63, "
 . "          viewDistance: 25 "
 . "      } "
 . "  }, "
 . " title: {  "
 . " text: 'График превышения расхода тепла за " . pg_fetch_result($sql_limit_part, 0, 2) . " " . $_POST['year'] . "г.' "
 . " }, colors: ['#ef3038', '#50B432'], "
 . " subtitle: {  "
 . " text: ''  "
 . " }, yAxis: {title: {text: 'Теп. энергия'}}, xAxis: {  "
 . " categories: [ ";
//."'Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas'"
$name = '';
for ($i = 0; $i < count($_SESSION['rep_name']); $i++) {
    if ($_SESSION['rep_tep_col'][$i] == 0) {
        $name .= "'" . $_SESSION['rep_name'][$i] . "', ";
    }
}
echo "" . $name . "]  "
 . " }, "
 . " plotOptions: { "
 . "column: { "
 . "depth: 25 "
 . "},"
 . "series: {
                borderWidth: 0,
                dataLabels: {
                    rotation: -90,
                    enabled: true,
                    format: '{point.y:.1f} Гкал'
                }
            } "
 . "}, "
 . " series: [ { name: 'Тепло (ГКал)', "
 . "data: [";
$data1 = '';
$data2 = '';
for ($i = 0; $i < count($_SESSION['rep_tep_val']); $i++) {
    if ($_SESSION['rep_tep_col'][$i] == 0) {
        $data1 .= str_replace(',', '.', $_SESSION['rep_tep_val'][$i]) . ", ";
        $data2 .= str_replace(',', '.', $_SESSION['rep_tep_lim'][$i]) . ", ";
    }
}

echo"" . $data1 . "] "
 . "}, { name: 'Лимит (ГКал)', "
 . "data: [" . $data2 . "] "
 . "}] "
 . "}); "
 . "}); ";


echo "$(function () { "
 . " var chart = new Highcharts.Chart({ "
 . "   chart: { "
 . "      renderTo: 'container_voda', "
 . "      type: 'column', "
 . "      options3d: { "
 . "          enabled: true, "
 . "          alpha: 0, "
 . "          beta: 2, "
 . "          depth: 63, "
 . "          viewDistance: 25 "
 . "      } "
 . "  }, "
 . " title: {  "
 . " text: 'График превышения расхода воды за " . pg_fetch_result($sql_limit_part, 0, 2) . " " . $_POST['year'] . "г.' "
 . " }, colors: ['#337ab7', '#50B432'], "
 . " subtitle: {  "
 . " text: ''  "
 . " }, yAxis: {title: {text: 'Объем'}}, xAxis: {  "
 . " categories: [ ";
//."'Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas'"
$name = '';
for ($i = 0; $i < count($_SESSION['rep_name']); $i++) {
    if ($_SESSION['rep_vod_col'][$i] == 0) {
        $k = array_search($_SESSION['rep_id'][$i], $corect);
        if ($k === false) {
            $name .= "'" . $_SESSION['rep_name'][$i] . "', ";
        }
    }
}
echo "" . $name . "]  "
 . " }, "
 . " plotOptions: { "
 . "column: { "
 . "depth: 25 "
 . "} ,"
 . "series: {
        borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f} м.куб.'
            }
        }"
 . "}, "
 . " series: [ { name: 'Объем (м. куб.)', "
 . "data: [";
$data1 = '';
$data2 = '';
for ($i = 0; $i < count($_SESSION['rep_vod_val']); $i++) {
    if ($_SESSION['rep_vod_col'][$i] == 0) {
        $k = array_search($_SESSION['rep_id'][$i], $corect);
        if ($k === false) {
            $data1 .= str_replace(',', '.', $_SESSION['rep_vod_val'][$i]) . ", ";
            $data2 .= str_replace(',', '.', $_SESSION['rep_vod_lim'][$i]) . ", ";
        }
    }
}

echo"" . $data1 . "] "
 . "}, { name: 'Лимит (м. куб.)', "
 . "data: [" . $data2 . "] "
 . "}] "
 . "}); "
 . "}); ";
echo "</script>";



echo '</div>';

function echo_data($id, $max_date, $teplo, $val, $days_v, $days_t) {
    global $arr_all_limit, $date_now, $limit_teplo_part, $limit_voda_part, $corect, $not_alarm, $num, $gr_id, $gr_counter, $gr_row, $not_alarm_teplo;
    $kol_day = (strtotime($date_now) - strtotime($max_date)) / (60 * 60 * 24);
    if ($kol_day > 3) {
        echo "<td class='danger'> - </td>";
        echo "<td class='danger'>Нет связи </td>";


        $_SESSION['rep_date'][] = " - ";
        $_SESSION['rep_stat'][] = "Нет связи";
        $_SESSION['rep_plc_err'][] = 0;
    } else {
        echo "<td>" . date('d.m.Y', strtotime($max_date)) . "</td>";
        echo "<td>OK</td>";


        $_SESSION['rep_date'][] = date('d.m.Y', strtotime($max_date));
        $_SESSION['rep_stat'][] = "OK";
        $_SESSION['rep_plc_err'][] = 1;
    }


    $c = array_search($id, $corect);
    if ($c !== false) {
        $td = "<td><span class='glyphicon glyphicon-wrench'></span></td>";
    } else {
        $td = "<td> </td>";
    }


    if ($gr_id != NULL) {
        $key_limit = array_search($gr_id, array_column($arr_all_limit, 'plc_id'));
    } else {
        $key_limit = array_search($id, array_column($arr_all_limit, 'plc_id'));
    }


    if ($key_limit !== false) {


        if ($days_t == $num) {
            echo "<td>" . $days_t . "</td>";
        } else {
            echo "<td class='warning'>" . $days_t . "</td>";
        }



        $lim1 = ((float) $arr_all_limit[$key_limit]['teplo'] / 100 ) * (float) $limit_teplo_part;
        $lim2 = ((float) $arr_all_limit[$key_limit]['voda'] / 100) * (float) $limit_voda_part;


        $t = array_search($id, $not_alarm);
        if ($t != FALSE) {
            echo "<td>Неисправен интерф. порт</td>";

            if ($gr_id != NULL) {
                if ($gr_counter == 1) {
                    echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim2) . " </td>";
                }
            } else {
                echo "<td> " . str_replace('.', ',', $lim2) . " </td>";
            }


            $_SESSION['rep_vod_val'][] = "Неисправен импульс";
            $_SESSION['rep_vod_lim'][] = $lim2;
            $_SESSION['rep_vod_col'][] = 3;
        } else {

            if ($teplo == 0) {

                echo "<td>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                    }
                } else {
                    echo "<td> " . str_replace('.', ',', $lim1) . " </td>";
                }

                $_SESSION['rep_tep_val'][] = substr($teplo, 0, 6);
                $_SESSION['rep_tep_lim'][] = $lim1;
                $_SESSION['rep_tep_col'][] = 3;
            } elseif ($teplo > $lim1 * 0.9 and $teplo < $lim1) {
                echo "<td  class='warning'>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        echo "<td class='warning' rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                    }
                } else {
                    echo "<td class='warning'> " . str_replace('.', ',', $lim1) . " </td>";
                }

                $_SESSION['rep_tep_val'][] = substr($teplo, 0, 6);
                $_SESSION['rep_tep_lim'][] = $lim1;
                $_SESSION['rep_tep_col'][] = 1;
            } elseif ($teplo < $lim1 * 0.9) {
                echo "<td>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim1) . " </td>";
                    }
                } else {
                    echo "<td> " . str_replace('.', ',', $lim1) . " </td>";
                }


                $_SESSION['rep_tep_val'][] = substr($teplo, 0, 6);
                $_SESSION['rep_tep_lim'][] = $lim1;
                $_SESSION['rep_tep_col'][] = 2;
            } elseif ($teplo > $lim1) {
                echo "<td class='danger'>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        echo "<td  rowspan=" . $gr_row . "  class='danger'> " . str_replace('.', ',', $lim1) . " </td>";
                    }
                } else {
                    echo "<td  class='danger'> " . str_replace('.', ',', $lim1) . " </td>";
                }


                $_SESSION['rep_tep_val'][] = substr($teplo, 0, 6);
                $_SESSION['rep_tep_lim'][] = $lim1;
                $_SESSION['rep_tep_col'][] = 0;
            }
        }
        if ($days_v == $num) {
            echo "<td>" . $days_v . "</td>";
        } else {
            echo "<td class='warning'>" . $days_v . "</td>";
        }

        $v = array_search($id, $not_alarm);
        if ($v != FALSE) {
            echo "<td>Неисправен импульс</td>";

            if ($gr_id != NULL) {
                if ($gr_counter == 1) {
                    echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim2) . " </td>";
                }
            } else {
                echo "<td> " . str_replace('.', ',', $lim2) . " </td>";
            }

            echo $td;

            $_SESSION['rep_vod_val'][] = "Неисправен импульс";
            $_SESSION['rep_vod_lim'][] = $lim2;
            $_SESSION['rep_vod_col'][] = 3;
        } else {

            if ($val == 0) {
                echo "<td>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim2) . " </td>";
                    }
                } else {
                    echo "<td> " . str_replace('.', ',', $lim2) . " </td>";
                }

                echo $td;

                $_SESSION['rep_vod_val'][] = substr($val, 0, 6);
                $_SESSION['rep_vod_lim'][] = $lim2;
                $_SESSION['rep_vod_col'][] = 3;
            } elseif ($val > $lim2 * 0.9 and $val < $lim2) {
                echo "<td  class='warning'>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        echo "<td class='warning' rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim2) . " </td>";
                    }
                } else {
                    echo "<td class='warning'> " . str_replace('.', ',', $lim2) . " </td>";
                }

                echo $td;

                $_SESSION['rep_vod_val'][] = substr($val, 0, 6);
                $_SESSION['rep_vod_lim'][] = $lim2;
                $_SESSION['rep_vod_col'][] = 1;
            } elseif ($val < $lim2 * 0.9) {
                echo "<td>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        echo "<td  rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim2) . " </td>";
                    }
                } else {
                    echo "<td> " . str_replace('.', ',', $lim2) . " </td>";
                }

                echo $td;

                $_SESSION['rep_vod_val'][] = substr($val, 0, 6);
                $_SESSION['rep_vod_lim'][] = $lim2;
                $_SESSION['rep_vod_col'][] = 2;
            } elseif ($val > $lim2) {
                echo "<td class='danger'>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";

                if ($gr_id != NULL) {
                    if ($gr_counter == 1) {
                        echo "<td class='danger' rowspan=" . $gr_row . "> " . str_replace('.', ',', $lim2) . " </td>";
                    }
                } else {
                    echo "<td class='danger'> " . str_replace('.', ',', $lim2) . " </td>";
                }

                echo $td;

                $_SESSION['rep_vod_val'][] = substr($val, 0, 6);
                $_SESSION['rep_vod_lim'][] = $lim2;
                $_SESSION['rep_vod_col'][] = 0;
            }
        }
    } else {
        echo "<td>" . $days_t . "</td>";
        echo "<td>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
        echo "<td> - </td>";
        echo "<td>" . $days_v . "</td>";
        echo "<td>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";
        echo "<td> - </td>";
        echo $td;

        $_SESSION['rep_tep_val'][] = substr($teplo, 0, 6);
        $_SESSION['rep_tep_lim'][] = $lim1;
        $_SESSION['rep_tep_col'][] = 3;
        $_SESSION['rep_vod_val'][] = substr($val, 0, 6);
        $_SESSION['rep_vod_lim'][] = $lim2;
        $_SESSION['rep_vod_col'][] = 3;
    }
}

function array_archive($plc_id, $dev_id) {

    global $arr_plc_id, $arr_data;
    global $teplo, $voda, $mass_voda, $max_date, $vte, $days_t, $days_v;
    $k_tep1 = null;
    $k_tep2 = null;
    $keys = array_keys($arr_plc_id, $plc_id);
    for ($j = 0; $j < count($keys); $j++) {
        if ($plc_id == 161) {
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
            //$voda[0][] = $arr_data[$keys[$j]]['value'];
            $days_v++;
            $mass_voda[0][] = array(
                'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($arr_data[$keys[$j]]['data']))),
                'value' => $arr_data[$keys[$j]]['value'],
                'res_id' => $arr_data[$keys[$j]]['res_id']
            );
        }
        if ($arr_data[$keys[$j]]['res_id'] == 308) {
            //$voda[1][] = $arr_data[$keys[$j]]['value'];
            $days_v++;
            $mass_voda[1][] = array(
                'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($arr_data[$keys[$j]]['data']))),
                'value' => $arr_data[$keys[$j]]['value'],
                'res_id' => $arr_data[$keys[$j]]['res_id']
            );
        }
        if ($arr_data[$keys[$j]]['res_id'] == 310) {
            //$voda[2][] = $arr_data[$keys[$j]]['value'];
            $days_v++;
            $mass_voda[2][] = array(
                'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($arr_data[$keys[$j]]['data']))),
                'value' => $arr_data[$keys[$j]]['value'],
                'res_id' => $arr_data[$keys[$j]]['res_id']
            );
        }
        if ($arr_data[$keys[$j]]['res_id'] == 414) {
            //$voda[3][] = $arr_data[$keys[$j]]['value'];
            $days_v++;
            $mass_voda[3][] = array(
                'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($arr_data[$keys[$j]]['data']))),
                'value' => $arr_data[$keys[$j]]['value'],
                'res_id' => $arr_data[$keys[$j]]['res_id']
            );
        }
        if ($arr_data[$keys[$j]]['res_id'] == 420) {
            //$voda[4][] = $arr_data[$keys[$j]]['value'];
            $days_v++;
            $mass_voda[4][] = array(
                'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($arr_data[$keys[$j]]['data']))),
                'value' => $arr_data[$keys[$j]]['value'],
                'res_id' => $arr_data[$keys[$j]]['res_id']
            );
        }
    }


    //$month = date('m', strtotime('-1 month'));
    $kol_days = cal_days_in_month(CAL_GREGORIAN, date("m", strtotime('-1 month', strtotime($max_date))), date("Y", strtotime($max_date))); //кол-во дней месяца
    if ($k_tep1 > $kol_days) { //проверяем если кол0во дней больше чем есть(повторная запись в архиве)
        $k_tep1 = $kol_days;
        echo " В базе присутствуют дублирующие значения на обьекте " . $plc_id;
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
    //echo "<h1>".$days."</h1>";
}

function view_archive_group($gr_id) {
    $summ_teplo = 0;
    $summ_voda = 0;

    global $days, $teplo, $voda, $max_date, $vte, $days_t, $days_v;


    $max_date = '';
    $sql_plc_group = pg_query('SELECT 
                    public.group_plc.plc_id
                  FROM
                    public.group_plc
                  WHERE
                    public.group_plc.group_id = ' . $gr_id);
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
    //обнуляем кол-во дней тут нужна доп проверка 
    $days = "-";

    $days_t = "-";
    $days_v = "-";

    echo_data($gr_id, $max_date, $summ_teplo, $summ_voda, $days_v, $days_t);
}

function view_group_archive($gr_id) {
    global $arr_school, $m, $date1, $date2, $mass_voda;
    global $teplo, $voda, $max_date, $vte, $days_t, $days_v, $gr_counter, $gr_row;

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
        $mass_voda=array();

        $key = array_search($result[0], array_column($arr_school, 'plc_id'));
        $m++;
        echo "<tr id='hover'>"
        . "<td>" . $m . "</td>"
        . "<td>" . $arr_school[$key]['name'] . "</td>"
        . "<td> " . $arr_school[$key]['adres'] . " </td>";

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


        // проверка была ли коррекция в данном периоде

        $sql_korrect = pg_query('SELECT DISTINCT 
            public.korrect.old_value,
            public.korrect.new_value,
            public.korrect.date_record,
            "Tepl"."ParamResPlc_cnt"."ParamRes_id"
          FROM
            "Tepl"."ParamResPlc_cnt"
            INNER JOIN public.korrect ON ("Tepl"."ParamResPlc_cnt".prp_id = public.korrect.prp_id)
          WHERE
            public.korrect.plc_id = ' . $arr_school[$key]['plc_id'] . ' AND 
            public.korrect.date_time >= \'' . $date1 . '\' AND 
            public.korrect.date_time <= \'' . $date2 . '\'');
        $korrec_arr[] = array();
        while ($result = pg_fetch_row($sql_korrect)) {
            $korrec_arr[] = array(
                'date' => $result[2],
                'id_res' => $result[3]
            );
        }

        if (pg_num_rows($sql_korrect) > 0) {
            $value = summ_voda_korrect_for_limit($mass_voda, $arr_school[$key]['plc_id'], $date1, $date2);
        } else {
            $value = summ_voda_for_limit($mass_voda);
        }
        //$value = summ_voda($voda);

        echo_data($result[0], $max_date, $teplo, $value, $days_v, $days_t);
        unset($korrec_arr);
        echo "</tr>";
    }
}

function summa_voda($voda) {
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
