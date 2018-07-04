<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
$date = date('Y-m-d');
session_start();
unset($_SESSION['file_name']);


$date1 = $_POST['date1'];
$date2 = $_POST['date2'];
$date_now = date("d", strtotime($date2));

if ($date_now == 20) {
    $date1 = date("Y-m-d", strtotime("+1 day", strtotime($date1)));
//$date2 = $_GET['data2'];
    $date2 = date("Y-m-d", strtotime("+1 day", strtotime($date2)));
} else {
    //$date1 = $_GET['data1'];
    $date1 = date("Y-m-d", strtotime("+1 day", strtotime($date1)));
//$date2 = $_GET['data2'];
    $date2 = date("Y-m-d", strtotime("+0 day", strtotime($date2)));
}

$id_dist = $_POST['id_distinct'];


echo "<h2 class='text-center'>Вывод данных с " . date("d.m.Y", strtotime($date2)) . " по " . date("d.m.Y", strtotime($date1)) . "</h2>";



$sql_name_object = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Places_cnt1".plc_id,
  public.fortum_places_cnt.frt_plc
FROM
  "Tepl"."User_cnt"
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
  INNER JOIN "Tepl"."Places_cnt" "Places_cnt2" ON ("Places_cnt1".place_id = "Places_cnt2".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN public.fortum_places_cnt ON ("Tepl"."Places_cnt".plc_id = public.fortum_places_cnt.plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
ORDER BY
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."Places_cnt"."Name"

');

while ($row_name_object = pg_fetch_row($sql_name_object)) {
    $object_info[] = array(
        'plc_id' => $row_name_object[1],
        'name' => $row_name_object[0],
        'addres' => '' . $row_name_object[2] . ' ' . $row_name_object[3] . '',
        'id_distinct' => $row_name_object[4],
        'street' => $row_name_object[2],
        'house' => $row_name_object[3],
        'house_id' => $row_name_object[5]
    );
}

$sql_number_sens = pg_query('SELECT DISTINCT 
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Sensor_Property"."Propert_Value",
  "Tepl"."ParamResPlc_cnt".plc_id
FROM
  "Tepl"."ParamResPlc_cnt"
  LEFT OUTER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Sensor_cnt".prp_id)
  LEFT OUTER JOIN "Tepl"."Sensor_Property" ON ("Tepl"."Sensor_cnt".s_id = "Tepl"."Sensor_Property".s_id)
WHERE
  "Tepl"."Sensor_Property".id_type_property = 0');

while ($row_number = pg_fetch_row($sql_number_sens)) {
    $number_sens[] = array(
        'prp_id' => $row_number[0],
        'number' => $row_number[1]
    );
}


$sql_parametr = pg_query('SELECT DISTINCT 
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."TypeSensor"."Name",
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Sensor_cnt"."Comment"
FROM
  "Tepl"."ParamResPlc_cnt"
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."ParamResGroupRelations".prp_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."ParamResGroupRelations".grp_id = "Tepl"."GroupToUserRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  LEFT OUTER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Sensor_cnt".prp_id)
  FULL OUTER JOIN "Tepl"."TypeSensor" ON ("Tepl"."Sensor_cnt".sen_id = "Tepl"."TypeSensor".sen_id)
WHERE
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
ORDER BY
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."Resourse_cnt"."Name"

');

while ($row_param = pg_fetch_row($sql_parametr)) {
    $param_info[] = array(
        'prp_id' => $row_param[3],
        'name_res' => $row_param[1],
        'name_param' => $row_param[0],
        'name_sens' => $row_param[2],
        'com_sens' => $row_param[4]
    );
}


$sql_archive1 = pg_query('SELECT DISTINCT 
  "Tepl"."Arhiv_cnt"."DateValue",
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."Arhiv_cnt"."DataValue",
  "Tepl"."ParamResPlc_cnt".prp_id
FROM
  "Tepl"."GroupToUserRelations"
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
WHERE
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
  "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
  "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\'  AND 
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' 
ORDER BY
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."ParamResPlc_cnt".prp_id

');
$z = 0;
$g = 0;
while ($result = pg_fetch_row($sql_archive1)) {
    if ($result[3] == 'NaN') {
        $result[3] = 0;
    }
    $archive[$z] = array(
        'plc_id' => $result[1],
        'param_id' => $result[2],
        'date' => $result[0],
        'value' => $result[3],
        'prp_id' => $result[4]
    );
    if ($z != 0) {
        if ($archive[$z]['prp_id'] != $archive[$z - 1]['prp_id']) {



            $vnr = (strtotime($archive[$z - 1]['date']) - strtotime($d1)) / (60 * 60);
            $raz = $archive[$z - 1]['value'] - $v1;
            if ($raz == $archive[$z - 1]['value']) {
                $raz = 0;
            }
            $voda[$g] = array(
                'plc_id' => $archive[$z - 1]['plc_id'],
                'param_id' => $archive[$z - 1]['param_id'],
                'prp_id' => $archive[$z - 1]['prp_id'],
                'date1' => $d1,
                'value1' => $v1,
                'date2' => $archive[$z - 1]['date'],
                'value2' => $archive[$z - 1]['value'],
                'raz' => $raz,
                'vnr' => $vnr,
            );
            //echo $g . ' ' . $vnr . ' ' . $raz . '  ' . $d1 . ' ' . $archive[$z - 1]['date'] . ' ' . $v1 . ' ' . $archive[$z - 1]['value'] . ' ' . $archive[$z - 1]['prp_id'] . ' ' . $archive[$z - 1]['plc_id'] . '<br>';
            $g++;
            $v1 = $archive[$z]['value'];
            $d1 = $archive[$z]['date'];
            $vnr = 0;
            $raz = 0;
            //echo $g . ' ' . $archive[$z]['date'] . ' ' . $archive[$z]['value'] . ' ' . $archive[$z]['prp_id'] . ' ' . $archive[$z]['plc_id'] . '<br>';
        }
    }
    if ($z == 0) {
        $v1 = $archive[$z]['value'];
        $d1 = $archive[$z]['date'];
        //echo $g . ' ' . $archive[$z]['date'] . ' ' . $archive[$z]['value'] . ' ' . $archive[$z]['prp_id'] . ' ' . $archive[$z]['plc_id'] . '<br>';
    }
    if ($z == pg_num_rows($sql_archive1) - 1) {
        $vnr = (strtotime($archive[$z - 1]['date']) - strtotime($d1)) / (60 * 60);
        $raz = $archive[$z - 1]['value'] - $v1;
        $voda[$g] = array(
            'plc_id' => $archive[$z - 1]['plc_id'],
            'param_id' => $archive[$z - 1]['param_id'],
            'prp_id' => $archive[$z - 1]['prp_id'],
            'date1' => $d1,
            'value1' => $v1,
            'date2' => $archive[$z - 1]['date'],
            'value2' => $archive[$z - 1]['value'],
            'raz' => $raz,
            'vnr' => $vnr,
        );
        //echo $g . ' ' . $vnr . ' ' . $raz . '  ' . $d1 . ' ' . $archive[$z - 1]['date'] . ' ' . $v1 . ' ' . $archive[$z - 1]['value'] . ' ' . $archive[$z - 1]['prp_id'] . ' ' . $archive[$z - 1]['plc_id'] . '<br>';
    }
    $z++;
}


for ($i = 0; $i < count($voda); $i++) {
    $key = array_search($voda[$i]['plc_id'], array_column($object_info, 'plc_id'));
    if ($key !== false) {
        $array[] = array(
            'plc_id' => $object_info[$key]['plc_id'],
            'name' => $object_info[$key]['name'],
            'id_dist' => $object_info[$key]['id_distinct'],
            'addres' => $object_info[$key]['addres'],
            'param_id' => $voda[$i]['param_id'],
            'date1' => $voda[$i]['date1'],
            'value1' => $voda[$i]['value1'],
            'date2' => $voda[$i]['date2'],
            'value2' => $voda[$i]['value2'],
            'vnr' => $voda[$i]['vnr'],
            'raz' => $voda[$i]['raz'],
            'prp_id' => $voda[$i]['prp_id'],
            'house' => $object_info[$key]['house'],
            'street' => $object_info[$key]['street'],
            'house_id' => $object_info[$key]['house_id']
        );
    }
}

for ($i = 0; $i < count($object_info); $i++) {
    $key = array_search($object_info[$i]['plc_id'], array_column($voda, 'plc_id'));
    if (false === $key) {
        $array[] = array(
            'plc_id' => $object_info[$i]['plc_id'],
            'name' => $object_info[$i]['name'],
            'id_dist' => $object_info[$i]['id_distinct'],
            'addres' => $object_info[$i]['addres'],
            'param_id' => 1,
            'date1' => '',
            'value1' => 0,
            'date2' => '',
            'value2' => 0,
            'vnr' => '-',
            'raz' => '-',
            'prp_id' => 0,
            'house' => $object_info[$i]['house'],
            'street' => $object_info[$i]['street'],
            'house_id' => $object_info[$i]['house_id']
        );
    }
}
unset($voda);
unset($archive);
$sql_tickets_reports = pg_query('SELECT 
  public.ticket.id,
  public.ticket.plc_id,
  public.ticket.date_ticket,
  public.ticket.text_ticket,
  public.ticket.status,
  public.ticket.close_date,
  public.ticket.close_text
FROM
  public.ticket
WHERE
  public.ticket.date_ticket >= \'' . $date1 . '\' AND 
  public.ticket.date_ticket <= \'' . $date2 . '\' AND 
  public.ticket.status > 2');

$ticket = array();
while ($result = pg_fetch_row($sql_tickets_reports)) {
    $ticket[] = array(
        'plc_id' => $result[1],
        'tick_id' => $result[0]
    );
}

for ($i = 0; $i < count($array); $i++) {
    $key = array_search($array[$i]['prp_id'], array_column($param_info, 'prp_id'));
    if ($key !== false) {

        $key_number = array_search($array[$i]['prp_id'], array_column($number_sens, 'prp_id'));
        if ($key_number !== false) {

            $tick_key = array_search($array[$i]['plc_id'], array_column($ticket, 'plc_id'));
            if ($tick_key !== false) {
                $array_all[] = array(
                    'plc_id' => $array[$i]['plc_id'],
                    'name' => $array[$i]['name'],
                    'addres' => $array[$i]['addres'],
                    'id_dist' => $array[$i]['id_dist'],
                    'prp_id' => $array[$i]['prp_id'],
                    'param_id' => $array[$i]['param_id'],
                    'date' => $array[$i]['date1'] . ' ' . $array[$i]['date2'],
                    'value1' => $array[$i]['value1'],
                    'value2' => $array[$i]['value2'],
                    'raznost' => $array[$i]['raz'],
                    'name_res' => $param_info[$key]['name_res'],
                    'name_param' => $param_info[$key]['name_param'],
                    'name_sens' => $param_info[$key]['name_sens'],
                    'com_sens' => $param_info[$key]['com_sens'],
                    'number_sens' => $number_sens[$key_number]['number'],
                    'vnr' => $array[$i]['vnr'],
                    'ticket' => $ticket[$tick_key]['tick_id'],
                    'house' => $array[$i]['house'],
                    'street' => $array[$i]['street'],
                    'house_id' => $array[$i]['house_id'],
                    'date1' => $array[$i]['date1'],
                    'date2' => $array[$i]['date2']
                );
            } else {
                $array_all[] = array(
                    'plc_id' => $array[$i]['plc_id'],
                    'name' => $array[$i]['name'],
                    'addres' => $array[$i]['addres'],
                    'id_dist' => $array[$i]['id_dist'],
                    'prp_id' => $array[$i]['prp_id'],
                    'param_id' => $array[$i]['param_id'],
                    'date' => $array[$i]['date1'] . ' ' . $array[$i]['date2'],
                    'value1' => $array[$i]['value1'],
                    'value2' => $array[$i]['value2'],
                    'raznost' => $array[$i]['raz'],
                    'name_res' => $param_info[$key]['name_res'],
                    'name_param' => $param_info[$key]['name_param'],
                    'name_sens' => $param_info[$key]['name_sens'],
                    'com_sens' => $param_info[$key]['com_sens'],
                    'number_sens' => $number_sens[$key_number]['number'],
                    'vnr' => $array[$i]['vnr'],
                    'ticket' => '',
                    'house' => $array[$i]['house'],
                    'street' => $array[$i]['street'],
                    'house_id' => $array[$i]['house_id'],
                    'date1' => $array[$i]['date1'],
                    'date2' => $array[$i]['date2']
                );
            }
        }
        if ($key_number === false) {

            $tick_key = array_search($array[$i]['plc_id'], array_column($ticket, 'plc_id'));
            if ($tick_key !== false) {
                $array_all[] = array(
                    'plc_id' => $array[$i]['plc_id'],
                    'name' => $array[$i]['name'],
                    'addres' => $array[$i]['addres'],
                    'id_dist' => $array[$i]['id_dist'],
                    'prp_id' => $array[$i]['prp_id'],
                    'param_id' => $array[$i]['param_id'],
                    'date' => $array[$i]['date1'] . ' ' . $array[$i]['date2'],
                    'value1' => $array[$i]['value1'],
                    'value2' => $array[$i]['value2'],
                    'raznost' => $array[$i]['raz'],
                    'name_res' => $param_info[$key]['name_res'],
                    'name_param' => $param_info[$key]['name_param'],
                    'name_sens' => $param_info[$key]['name_sens'],
                    'com_sens' => $param_info[$key]['com_sens'],
                    'number_sens' => '-',
                    'vnr' => $array[$i]['vnr'],
                    'ticket' => $ticket[$tick_key]['tick_id'],
                    'house' => $array[$i]['house'],
                    'street' => $array[$i]['street'],
                    'house_id' => $array[$i]['house_id'],
                    'date1' => $array[$i]['date1'],
                    'date2' => $array[$i]['date2']
                );
            } else {
                $array_all[] = array(
                    'plc_id' => $array[$i]['plc_id'],
                    'name' => $array[$i]['name'],
                    'addres' => $array[$i]['addres'],
                    'id_dist' => $array[$i]['id_dist'],
                    'prp_id' => $array[$i]['prp_id'],
                    'param_id' => $array[$i]['param_id'],
                    'date' => $array[$i]['date1'] . ' ' . $array[$i]['date2'],
                    'value1' => $array[$i]['value1'],
                    'value2' => $array[$i]['value2'],
                    'raznost' => $array[$i]['raz'],
                    'name_res' => $param_info[$key]['name_res'],
                    'name_param' => $param_info[$key]['name_param'],
                    'name_sens' => $param_info[$key]['name_sens'],
                    'com_sens' => $param_info[$key]['com_sens'],
                    'number_sens' => '-',
                    'vnr' => $array[$i]['vnr'],
                    'ticket' => '',
                    'house' => $array[$i]['house'],
                    'street' => $array[$i]['street'],
                    'house_id' => $array[$i]['house_id'],
                    'date1' => $array[$i]['date1'],
                    'date2' => $array[$i]['date2']
                );
            }
        }
    }
    if ($key === false) {

        $tick_key = array_search($array[$i]['plc_id'], array_column($ticket, 'plc_id'));
        if ($tick_key !== false) {
            $array_all[] = array(
                'plc_id' => $array[$i]['plc_id'],
                'name' => $array[$i]['name'],
                'addres' => $array[$i]['addres'],
                'id_dist' => $array[$i]['id_dist'],
                'prp_id' => $array[$i]['prp_id'],
                'param_id' => $array[$i]['param_id'],
                'date' => $array[$i]['date1'] . ' ' . $array[$i]['date2'],
                'value1' => $array[$i]['value1'],
                'value2' => $array[$i]['value2'],
                'raznost' => $array[$i]['raz'],
                'name_res' => '',
                'name_param' => '',
                'name_sens' => '',
                'com_sens' => '',
                'number_sens' => '-',
                'vnr' => $array[$i]['vnr'],
                'ticket' => $ticket[$tick_key]['tick_id'],
                'house' => $array[$i]['house'],
                'street' => $array[$i]['street'],
                'house_id' => $array[$i]['house_id'],
                'date1' => $array[$i]['date1'],
                'date2' => $array[$i]['date2']
            );
        } else {
            $array_all[] = array(
                'plc_id' => $array[$i]['plc_id'],
                'name' => $array[$i]['name'],
                'addres' => $array[$i]['addres'],
                'id_dist' => $array[$i]['id_dist'],
                'prp_id' => $array[$i]['prp_id'],
                'param_id' => $array[$i]['param_id'],
                'date' => $array[$i]['date1'] . ' ' . $array[$i]['date2'],
                'value1' => $array[$i]['value1'],
                'value2' => $array[$i]['value2'],
                'raznost' => $array[$i]['raz'],
                'name_res' => '',
                'name_param' => '',
                'name_sens' => '',
                'com_sens' => '',
                'number_sens' => '-',
                'vnr' => $array[$i]['vnr'],
                'ticket' => '',
                'house' => $array[$i]['house'],
                'street' => $array[$i]['street'],
                'house_id' => $array[$i]['house_id'],
                'date1' => $array[$i]['date1'],
                'date2' => $array[$i]['date2']
            );
        }
    }
}

$tmp1 = Array();
foreach ($array_all as &$ma) {
    $tmp1[] = &$ma["id_dist"];
}
$tmp2 = Array();

foreach ($array_all as &$ma) {
    $tmp2[] = &$ma["name"];
}
$tmp3 = Array();

foreach ($array_all as &$ma) {
    $tmp3[] = &$ma["addres"];
}
$tmp4 = Array();
foreach ($array_all as &$ma) {
    $tmp4[] = &$ma["name_res"];
}


array_multisort($tmp1, $tmp2, $tmp3, $tmp4, $array_all);


//var_dump($array_all);

echo '<table id="main_table" class="table table table-bordered"> '
 . '<thead id="thead"><tr id="warning">'
 . '<td rowspan=2><b>№</b></td>'
 . '<td rowspan=2><b>Учереждение</b></td>'
 . '<td rowspan=2><b>Адрес</b></td>'
 . '<td rowspan=2><b>Зав. № Счетчика</b></td>'
 . '<td rowspan=2><b>Тип водоснабжения</b></td>'
 . '<td rowspan=2><b>Местоположение</b></td>'
 . '<td colspan=4><b>Показание потребления (м<sup>3</sup>)</b></td>'
 . '<td rowspan=2><b>Время наработки (час) </b></td>'
 . '<td rowspan=2><b>С.О.</b></td>'
 . '</tr>'
 . '<tr id="warning">'
 . '<td><b>На начало периода</b></td>'
 . '<td><b>На конец периода</b></td>'
 . '<td><b>За период</b></td>'
 . '<td><b>Общее</b></td>'
 . '</tr>'
 . '</thead><tbody>';

function view_td($j, $res, $name, $array_all, $id) {
    $z = 0;
    $summ = 0;
    for ($i = $j; $i < count($array_all); $i++) {
        if ($array_all[$i]['name'] == $name and $array_all[$i]['plc_id'] == $id) {
            if ($res == $array_all[$i]['name_res']) {
                $summ += $array_all[$i]['raznost'];
                $z++;
            }
        }
    }
    echo "<td rowspan = '" . $z . "'>" . number_format($summ, 2, ',', ' ') . "</td>";
}

$kol = 1;
$n = 0;

if ($id_dist == 0) {

    for ($i = 0; $i < count($array_all); $i++) {
        if ($array_all[$i]['plc_id'] == $array_all[$i + 1]['plc_id']) {
            $kol++;
        }
        if ($array_all[$i]['plc_id'] != $array_all[$i + 1]['plc_id']) {
            if ($kol > 1) {
                $n++;
                //$kol++;
                echo "<tr>"
                . "<td rowspan='" . $kol . "'>" . $n . "</td>"
                . "<td rowspan='" . $kol . "'><a  href='#' class='go_object' id='" . $array_all[$i]['plc_id'] . "'>" . $array_all[$i]['name'] . "</a></td>"
                . "<td rowspan='" . $kol . "'>" . $array_all[$i]['addres'] . "</td>";
                $kol2 = 1;




                $z = 0;
                for ($j = 0; $j < count($array_all); $j++) {

                    if ($array_all[$i]['plc_id'] == $array_all[$j]['plc_id']) {

                        if ($array_all[$j]['ticket'] > 0) {
                            $tickets = "<a href='#' class='go_ticket' id='" . $array_all[$i]['plc_id'] . "'><span class='glyphicon glyphicon-wrench'></span></a>";
                        } else {
                            $tickets = "";
                        }

                        if ($z == 0) {
                            $res = $array_all[$j]['name_res'];
                            $name = $array_all[$j]['name'];
                            $id = $array_all[$j]['plc_id'];
                            echo "<td>" . $array_all[$j]['number_sens'] . "</td>"
                            . "<td>" . $array_all[$j]['name_res'] . "</td>"
                            . "<td>" . $array_all[$j]['com_sens'] . "</td>"
                            . "<td>" . number_format($array_all[$j]['value1'], 2, ',', ' ') . "</td>"
                            . "<td>" . number_format($array_all[$j]['value2'], 2, ',', ' ') . "</td>"
                            . "<td>" . number_format($array_all[$j]['raznost'], 2, ',', ' ') . "</td>";

                            view_td($j, $res, $name, $array_all, $id);
                            echo "<td>" . $array_all[$j]['vnr'] . "</td>"
                            . "<td rowspan='" . $kol . "'>" . $tickets . "</td>";
                            echo "</tr>";
                        } elseif ($res != $array_all[$j]['name_res']) {
                            $res = $array_all[$j]['name_res'];
                            $name = $array_all[$j]['name'];
                            $id = $array_all[$j]['plc_id'];

                            echo "<td>" . $array_all[$j]['number_sens'] . "</td>"
                            . "<td>" . $array_all[$j]['name_res'] . "</td>"
                            . "<td>" . $array_all[$j]['com_sens'] . "</td>"
                            . "<td>" . number_format($array_all[$j]['value1'], 2, ',', ' ') . "</td>"
                            . "<td>" . number_format($array_all[$j]['value2'], 2, ',', ' ') . "</td>"
                            . "<td>" . number_format($array_all[$j]['raznost'], 2, ',', ' ') . "</td>";

                            view_td($j, $res, $name, $array_all, $id);
                            echo "<td>" . $array_all[$j]['vnr'] . "</td>";
                            echo "</tr>";
                        } else {
                            echo "<td>" . $array_all[$j]['number_sens'] . "</td>"
                            . "<td>" . $array_all[$j]['name_res'] . "</td>"
                            . "<td>" . $array_all[$j]['com_sens'] . "</td>"
                            . "<td>" . number_format($array_all[$j]['value1'], 2, ',', ' ') . "</td>"
                            . "<td>" . number_format($array_all[$j]['value2'], 2, ',', ' ') . "</td>"
                            . "<td>" . number_format($array_all[$j]['raznost'], 2, ',', ' ') . "</td>";
                            echo "<td>" . $array_all[$j]['vnr'] . "</td>";
                            echo "</tr>";
                        }
                        $z++;
                    }
                }

                //echo "</tr>";
                $kol = 1;
            } else {

                if ($array_all[$i]['ticket'] > 0) {
                    $tickets = "<a href='#' class='go_ticket' id='" . $array_all[$i]['plc_id'] . "'><span class='glyphicon glyphicon-wrench'></span></a>";
                } else {
                    $tickets = "";
                }
                $n++;
                echo "<tr>"
                . "<td rowspan='" . $kol . "'>" . $n . "</td>"
                . "<td rowspan='" . $kol . "'><a  href='#' class='go_object' id='" . $array_all[$i]['plc_id'] . "'>" . $array_all[$i]['name'] . "</a></td>"
                . "<td rowspan='" . $kol . "'>" . $array_all[$i]['addres'] . "</td>"
                . "<td>" . $array_all[$i]['number_sens'] . "</td>"
                . "<td>" . $array_all[$i]['name_res'] . "</td>"
                . "<td>" . $array_all[$i]['com_sens'] . "</td>"
                . "<td>" . number_format($array_all[$i]['value1'], 2, ',', ' ') . "</td>"
                . "<td>" . number_format($array_all[$i]['value2'], 2, ',', ' ') . "</td>"
                . "<td>" . number_format($array_all[$i]['raznost'], 2, ',', ' ') . "</td>"
                . "<td>" . number_format($array_all[$i]['raznost'], 2, ',', ' ') . "</td>"
                . "<td>" . $array_all[$i]['vnr'] . "</td>"
                . "<td>" . $tickets . "</td>"
                . "</tr>";
            }
        }
    }
} else {
    for ($i = 0; $i < count($array_all); $i++) {

        if ($array_all[$i]['id_dist'] == $id_dist) {
            if ($array_all[$i]['plc_id'] == $array_all[$i + 1]['plc_id']) {
                $kol++;
            }
            if ($array_all[$i]['plc_id'] != $array_all[$i + 1]['plc_id']) {
                if ($kol > 1) {
                    $n++;
                    //$kol++;
                    echo "<tr>"
                    . "<td rowspan='" . $kol . "'>" . $n . "</td>"
                    . "<td rowspan='" . $kol . "'><a  href='#' class='go_object' id='" . $array_all[$i]['plc_id'] . "'>" . $array_all[$i]['name'] . "</a></td>"
                    . "<td rowspan='" . $kol . "'>" . $array_all[$i]['addres'] . "</td>";
                    $kol2 = 1;




                    $z = 0;
                    for ($j = 0; $j < count($array_all); $j++) {

                        if ($array_all[$i]['plc_id'] == $array_all[$j]['plc_id']) {

                            if ($array_all[$j]['ticket'] > 0) {
                                $tickets = "<a href='#' class='go_ticket' id='" . $array_all[$i]['plc_id'] . "'><span class='glyphicon glyphicon-wrench'></span></a>";
                            } else {
                                $tickets = "";
                            }

                            if ($z == 0) {
                                $res = $array_all[$j]['name_res'];
                                $name = $array_all[$j]['name'];
                                $id = $array_all[$j]['plc_id'];
                                echo "<td>" . $array_all[$j]['number_sens'] . "</td>"
                                . "<td>" . $array_all[$j]['name_res'] . "</td>"
                                . "<td>" . $array_all[$j]['com_sens'] . "</td>"
                                . "<td>" . number_format($array_all[$j]['value1'], 2, ',', ' ') . "</td>"
                                . "<td>" . number_format($array_all[$j]['value2'], 2, ',', ' ') . "</td>"
                                . "<td>" . number_format($array_all[$j]['raznost'], 2, ',', ' ') . "</td>";

                                view_td($j, $res, $name, $array_all, $id);
                                echo "<td>" . $array_all[$j]['vnr'] . "</td>"
                                . "<td rowspan='" . $kol . "'>" . $tickets . "</td>";
                                echo "</tr>";
                            } elseif ($res != $array_all[$j]['name_res']) {
                                $res = $array_all[$j]['name_res'];
                                $name = $array_all[$j]['name'];
                                $id = $array_all[$j]['plc_id'];

                                echo "<td>" . $array_all[$j]['number_sens'] . "</td>"
                                . "<td>" . $array_all[$j]['name_res'] . "</td>"
                                . "<td>" . $array_all[$j]['com_sens'] . "</td>"
                                . "<td>" . number_format($array_all[$j]['value1'], 2, ',', ' ') . "</td>"
                                . "<td>" . number_format($array_all[$j]['value2'], 2, ',', ' ') . "</td>"
                                . "<td>" . number_format($array_all[$j]['raznost'], 2, ',', ' ') . "</td>";

                                view_td($j, $res, $name, $array_all, $id);
                                echo "<td>" . $array_all[$j]['vnr'] . "</td>";
                                echo "</tr>";
                            } else {
                                echo "<td>" . $array_all[$j]['number_sens'] . "</td>"
                                . "<td>" . $array_all[$j]['name_res'] . "</td>"
                                . "<td>" . $array_all[$j]['com_sens'] . "</td>"
                                . "<td>" . number_format($array_all[$j]['value1'], 2, ',', ' ') . "</td>"
                                . "<td>" . number_format($array_all[$j]['value2'], 2, ',', ' ') . "</td>"
                                . "<td>" . number_format($array_all[$j]['raznost'], 2, ',', ' ') . "</td>";
                                echo "<td>" . $array_all[$j]['vnr'] . "</td>";
                                echo "</tr>";
                            }
                            $z++;
                        }
                    }

                    //echo "</tr>";
                    $kol = 1;
                } else {

                    if ($array_all[$i]['ticket'] > 0) {
                        $tickets = "<a href='#' class='go_ticket' id='" . $array_all[$i]['plc_id'] . "'><span class='glyphicon glyphicon-wrench'></span></a>";
                    } else {
                        $tickets = "";
                    }
                    $n++;
                    echo "<tr>"
                    . "<td rowspan='" . $kol . "'>" . $n . "</td>"
                    . "<td rowspan='" . $kol . "'><a  href='#' class='go_object' id='" . $array_all[$i]['plc_id'] . "'>" . $array_all[$i]['name'] . "</a></td>"
                    . "<td rowspan='" . $kol . "'>" . $array_all[$i]['addres'] . "</td>"
                    . "<td>" . $array_all[$i]['number_sens'] . "</td>"
                    . "<td>" . $array_all[$i]['name_res'] . "</td>"
                    . "<td>" . $array_all[$i]['com_sens'] . "</td>"
                    . "<td>" . number_format($array_all[$i]['value1'], 2, ',', ' ') . "</td>"
                    . "<td>" . number_format($array_all[$i]['value2'], 2, ',', ' ') . "</td>"
                    . "<td>" . number_format($array_all[$i]['raznost'], 2, ',', ' ') . "</td>"
                    . "<td>" . number_format($array_all[$i]['raznost'], 2, ',', ' ') . "</td>"
                    . "<td>" . $array_all[$i]['vnr'] . "</td>"
                    . "<td>" . $tickets . "</td>"
                    . "</tr>";
                }
            }
        }
    }
}
echo '</tbody></table>';

//var_dump($array_all);

$sql_fias = pg_query('SELECT * FROM fias_cnt');
while ($row = pg_fetch_row($sql_fias)) {
    $f[] = array(
        'plc_id' => $row[2],
        'fias' => $row[1],
        'cdog' => $row[3]
    );
}


$sql_prop = pg_query('SELECT * FROM prop_connect');
while ($row = pg_fetch_row($sql_prop)) {
    $p[] = array(
        'prp' => $row[1],
        'id_con' => $row[2],
        'numb' => $row[4]
    );
}


$def = array(
    array("HOUSE_ID", "C", 16),
    array("HOUSE_FIAS", "C", 36),
    array("STNAME", "C", 128),
    array("H_NOMER", "C", 32),
    array("DEVICE", "C", 16),
    array("D_NOMER", "C", 32),
    array("PREV_VOL", "N", 10, 0),
    array("PREV_DATA", "D"),
    array("CURR_VOL", "N", 10, 0),
    array("CURR_DATA", "D"),
    array("RASHOD", "N", 10, 0),
    array("VOLUME1", "N", 15, 4),
    array("VOLUME0", "N", 15, 4),
    array("ER_CODE", "N", 10, 0),
    array("ER_NAME", "C", 128),
    array("ER_INFO", "C", 128),
    array("CONTRACTID", "C", 11),
    array("KV", "C", 32),
    array("PLACE", "C", 32)
);


$dbf_name = "export/report_" . $date2 . "_" . $date1 . ".dbf";
$_SESSION['file_name'] = $dbf_name;
unlink($dbf_name);
if (!dbase_create($dbf_name, $def)) {
    die("Error, can't create the database");
}

$dbf = dbase_open($dbf_name, 2);

if ($id_dist == 0) {
    for ($i = 0; $i < count($array_all); $i++) {

        $k = array_search($array_all[$i]['plc_id'], array_column($f, 'plc_id'));
        if ($k !== false) {
            $fias_code = $f[$k]['fias'];
            $dog = $f[$k]['cdog'];
        } else {
            $fias_code = "";
            $dog = "";
        }

        $e = array_search($array_all[$i]['prp_id'], array_column($p, 'prp'));
        if ($e !== false) {
            $con = $p[$e]['id_con'];
            // $numb =;
        } else {
            $con = "";
            //$numb =;
        }

        dbase_add_record($dbf, array(
            iconv('UTF-8', 'CP866', $array_all[$i]['house_id']), //Идентификатор
            iconv('UTF-8', 'CP866', $fias_code), //ФИАС код
            iconv('UTF-8', 'CP866', $array_all[$i]['street']), // Улица
            iconv('UTF-8', 'CP866', $array_all[$i]['house']), //Дом
            iconv('UTF-8', 'CP866', $con), //ID счетчика в системе поставщика
            iconv('UTF-8', 'CP866', $array_all[$i]['number_sens']), // заводской номер счечтика
            iconv('UTF-8', 'CP866', $array_all[$i]['value1']), // Предыдущие показания
            iconv('UTF-8', 'CP866', date('Ymd', strtotime($array_all[$i]['date1']))), // Дата снятия предыдущего показания
            iconv('UTF-8', 'CP866', $array_all[$i]['value2']), // Текущие покзаания
            iconv('UTF-8', 'CP866', date('Ymd', strtotime($array_all[$i]['date2']))), // Дата снятия текущий покзааний
            iconv('UTF-8', 'CP866', $array_all[$i]['raznost']), // Расход
            iconv('UTF-8', 'CP866', $array_all[$i]['value2']), // объем потребления по ИПУ
            iconv('UTF-8', 'CP866', 0), // Объем потребления по нормативу
            iconv('UTF-8', 'CP866', ''), // Код несправности
            iconv('UTF-8', 'CP866', ''), // Описание неисправности
            iconv('UTF-8', 'CP866', $array_all[$i]['ticket']), // Доп инфомрация об ошибке
            iconv('UTF-8', 'CP866', $dog), //код договора
            iconv('UTF-8', 'CP866', ''), // Диапазон квартир
            iconv('UTF-8', 'CP866', $array_all[$i]['com_sens']) // место установки ПУ
                )
        );
    }
} else {
    for ($i = 0; $i < count($array_all); $i++) {
        if ($id_dist == $array_all[$i]['id_dist']) {
            $k = array_search($array_all[$i]['plc_id'], array_column($f, 'plc_id'));
            if ($k !== false) {
                $fias_code = $f[$k]['fias'];
                $dog = $f[$k]['cdog'];
            } else {
                $fias_code = "";
                $dog = "";
            }

            $e = array_search($array_all[$i]['prp_id'], array_column($p, 'prp'));
            if ($e !== false) {
                $con = $p[$e]['id_con'];
                // $numb =;
            } else {
                $con = "";
                //$numb =;
            }

            dbase_add_record($dbf, array(
                iconv('UTF-8', 'CP866', $array_all[$i]['house_id']), //Идентификатор
                iconv('UTF-8', 'CP866', $fias_code), //ФИАС код
                iconv('UTF-8', 'CP866', $array_all[$i]['street']), // Улица
                iconv('UTF-8', 'CP866', $array_all[$i]['house']), //Дом
                iconv('UTF-8', 'CP866', $con), //ID счетчика в системе поставщика
                iconv('UTF-8', 'CP866', $array_all[$i]['number_sens']), // заводской номер счечтика
                iconv('UTF-8', 'CP866', $array_all[$i]['value1']), // Предыдущие показания
                iconv('UTF-8', 'CP866', date('Ymd', strtotime($array_all[$i]['date1']))), // Дата снятия предыдущего показания
                iconv('UTF-8', 'CP866', $array_all[$i]['value2']), // Текущие покзаания
                iconv('UTF-8', 'CP866', date('Ymd', strtotime($array_all[$i]['date2']))), // Дата снятия текущий покзааний
                iconv('UTF-8', 'CP866', $array_all[$i]['raznost']), // Расход
                iconv('UTF-8', 'CP866', $array_all[$i]['value2']), // объем потребления по ИПУ
                iconv('UTF-8', 'CP866', 0), // Объем потребления по нормативу
                iconv('UTF-8', 'CP866', ''), // Код несправности
                iconv('UTF-8', 'CP866', ''), // Описание неисправности
                iconv('UTF-8', 'CP866', $array_all[$i]['ticket']), // Доп инфомрация об ошибке
                iconv('UTF-8', 'CP866', $dog), //код договора
                iconv('UTF-8', 'CP866', ''), // Диапазон квартир
                iconv('UTF-8', 'CP866', $array_all[$i]['com_sens']) // место установки ПУ
                    )
            );
        }
    }
}