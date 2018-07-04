<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL & ~E_NOTICE);


include 'db_config.php';
$date = date('Y-m-d');
session_start();

$date1 = $_GET['data1'];
$date2 = $_GET['data2'];
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
$id = $_GET['id'];

$sql_name_object = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Places_cnt1".plc_id
FROM
  "Tepl"."User_cnt"
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
  INNER JOIN "Tepl"."Places_cnt" "Places_cnt2" ON ("Places_cnt1".place_id = "Places_cnt2".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."User_cnt".usr_id = ' . $id . '
ORDER BY
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."Places_cnt"."Name"');

while ($row_name_object = pg_fetch_row($sql_name_object)) {
    $object_info[] = array(
        'plc_id' => $row_name_object[1],
        'name' => $row_name_object[0],
        'addres' => '' . $row_name_object[2] . ' ' . $row_name_object[3] . '',
        'id_distinct' => $row_name_object[4]
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
    "Tepl"."User_cnt".usr_id = ' . $id . '
ORDER BY
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."Resourse_cnt"."Name"');

while ($row_param = pg_fetch_row($sql_parametr)) {
    $param_info[] = array(
        'prp_id' => $row_param[3],
        'name_res' => $row_param[1],
        'name_param' => $row_param[0],
        'name_sens' => $row_param[2],
        'com_sens' => $row_param[4]
    );
}


$sql_archive1 = pg_query('
SELECT DISTINCT 
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
  "Tepl"."User_cnt".usr_id = ' . $id . ' AND 
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
            'prp_id' => $voda[$i]['prp_id']
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
            'prp_id' => 0
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
  public.ticket.status < 4');

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
                    'ticket' => $ticket[$tick_key]['tick_id']
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
                    'ticket' => ''
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
                    'ticket' => $ticket[$tick_key]['tick_id']
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
                    'ticket' => ''
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
                'ticket' => $ticket[$tick_key]['tick_id']
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
                'ticket' => ''
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


$sql_not_error = pg_query('SELECT DISTINCT 
  public.alarm.plc_id,
  public.alarm.prp_id
FROM
  public.alarm
WHERE
  public.alarm.text_alarm LIKE \'%Импульс%\'');
while ($result = pg_fetch_row($sql_not_error)) {
    if ($result[1] != null) {
        $not_error[] = array(
            'plc_id' => $result[0],
            'prp_id' => $result[1]
        );
    }
}


$sql_korrect = pg_query('SELECT 
  public.korrect.id,
  public.korrect.plc_id,
  public.korrect.prp_id,
  public.korrect.id_tick,
  public.korrect.date_time,
  public.korrect.old_value,
  public.korrect.new_value,
  public.korrect.name_prp
FROM
  public.korrect
WHERE
  public.korrect.date_time >= \'' . $date1 . '\' AND 
  public.korrect.date_time <= \'' . $date2 . '\'');

while ($result = pg_fetch_row($sql_korrect)) {
    if ($result[5] == '') {
        $work = 'Подключение нового счечтика';
        $table_correct[] = array(
            'prp_id' => $result[2],
            'date' => $result[4],
            'old_val' => '',
            'new_val' => 'Нач. показ.: ' . $result[6],
            'work' => $work
        );
    } else {
        $work = 'Коректировка показаний';
        $table_correct[] = array(
            'prp_id' => $result[2],
            'date' => $result[4],
            'old_val' => 'Нач. показ.: ' . $result[5],
            'new_val' => 'Кон. показ.: ' . $result[6],
            'work' => $work
        );
    }
}

for ($i = 0; $i < count($array_all); $i++) {
    $key = array_search($array_all[$i]["prp_id"], array_column($table_correct, "prp_id"));
    if ($key !== false) {

        $ne = array_search($array_all[$i]["prp_id"], array_column($not_error, 'prp_id'));

        if ($ne !== false) {
            $array_all_k[] = array(
                'plc_id' => $array_all[$i]['plc_id'],
                'name' => $array_all[$i]['name'],
                'addres' => $array_all[$i]['addres'],
                'id_dist' => $array_all[$i]['id_dist'],
                'prp_id' => $array_all[$i]['prp_id'],
                'param_id' => $array[$i]['param_id'],
                'date' => $array_all[$i]['date1'] . ' ' . $array[$i]['date2'],
                'value1' => $array_all[$i]['value1'],
                'value2' => $array_all[$i]['value2'],
                'raznost' => $array_all[$i]['raznost'],
                'name_res' => $array_all[$i]['name_res'],
                'name_param' => $array_all[$i]['name_param'],
                'name_sens' => $array_all[$i]['name_sens'],
                'com_sens' => $array_all[$i]['com_sens'],
                'number_sens' => $array_all[$i]['number_sens'],
                'vnr' => $array_all[$i]['vnr'],
                'ticket' => $array_all[$i]['ticket'],
                'korrect' => '' . date("d.m.Y H:00", strtotime($table_correct[$key]["date"])) . ' ' . $table_correct[$key]["work"] . ' ' . $table_correct[$key]["old_val"] . ' ' . $table_correct[$key]["new_val"] . '',
                'not_error' => 'Неисправен импульс'
            );
        } else {
            $array_all_k[] = array(
                'plc_id' => $array_all[$i]['plc_id'],
                'name' => $array_all[$i]['name'],
                'addres' => $array_all[$i]['addres'],
                'id_dist' => $array_all[$i]['id_dist'],
                'prp_id' => $array_all[$i]['prp_id'],
                'param_id' => $array[$i]['param_id'],
                'date' => $array_all[$i]['date1'] . ' ' . $array[$i]['date2'],
                'value1' => $array_all[$i]['value1'],
                'value2' => $array_all[$i]['value2'],
                'raznost' => $array_all[$i]['raznost'],
                'name_res' => $array_all[$i]['name_res'],
                'name_param' => $array_all[$i]['name_param'],
                'name_sens' => $array_all[$i]['name_sens'],
                'com_sens' => $array_all[$i]['com_sens'],
                'number_sens' => $array_all[$i]['number_sens'],
                'vnr' => $array_all[$i]['vnr'],
                'ticket' => $array_all[$i]['ticket'],
                'korrect' => '' . date("d.m.Y H:00", strtotime($table_correct[$key]["date"])) . ' ' . $table_correct[$key]["work"] . ' ' . $table_correct[$key]["old_val"] . ' ' . $table_correct[$key]["new_val"] . '',
                'not_error' => ''
            );
        }
    } else {
        $ne = array_search($array_all[$i]["prp_id"], array_column($not_error, 'prp_id'));
        if ($ne !== false) {
            $array_all_k[] = array(
                'plc_id' => $array_all[$i]['plc_id'],
                'name' => $array_all[$i]['name'],
                'addres' => $array_all[$i]['addres'],
                'id_dist' => $array_all[$i]['id_dist'],
                'prp_id' => $array_all[$i]['prp_id'],
                'param_id' => $array_all[$i]['param_id'],
                'date' => $array_all[$i]['date1'] . ' ' . $array[$i]['date2'],
                'value1' => $array_all[$i]['value1'],
                'value2' => $array_all[$i]['value2'],
                'raznost' => $array_all[$i]['raznost'],
                'name_res' => $array_all[$i]['name_res'],
                'name_param' => $array_all[$i]['name_param'],
                'name_sens' => $array_all[$i]['name_sens'],
                'com_sens' => $array_all[$i]['com_sens'],
                'number_sens' => $array_all[$i]['number_sens'],
                'vnr' => $array_all[$i]['vnr'],
                'ticket' => $array_all[$i]['ticket'],
                'korrect' => '',
                'not_error' => 'Неисправен импульс'
            );
        } else {
            $array_all_k[] = array(
                'plc_id' => $array_all[$i]['plc_id'],
                'name' => $array_all[$i]['name'],
                'addres' => $array_all[$i]['addres'],
                'id_dist' => $array_all[$i]['id_dist'],
                'prp_id' => $array_all[$i]['prp_id'],
                'param_id' => $array_all[$i]['param_id'],
                'date' => $array_all[$i]['date1'] . ' ' . $array[$i]['date2'],
                'value1' => $array_all[$i]['value1'],
                'value2' => $array_all[$i]['value2'],
                'raznost' => $array_all[$i]['raznost'],
                'name_res' => $array_all[$i]['name_res'],
                'name_param' => $array_all[$i]['name_param'],
                'name_sens' => $array_all[$i]['name_sens'],
                'com_sens' => $array_all[$i]['com_sens'],
                'number_sens' => $array_all[$i]['number_sens'],
                'vnr' => $array_all[$i]['vnr'],
                'ticket' => $array_all[$i]['ticket'],
                'korrect' => '',
                'not_error' => ''
            );
        }
    }
}
unset($array_archive1);
unset($array_archive2);
unset($archive1);
unset($archive2);
unset($array);
unset($array_all);
$array_all = $array_all_k;

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
$sheet->setTitle('Отчет МУП ПОВВ');


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

$sheet->getStyle('A1:Q500')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:Q500')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);

$sheet->getStyle('A5:D500')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A5:D500')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);

$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$sheet->getPageSetup()->setFitToPage(true);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);

/*
 *  Стили ячеек
 */

function view_td($j, $res, $name, $array_all, $id) {
    $z = 0;
    $summ = 0;
    for ($i = $j; $i < count($array_all); $i++) {
        if ($array_all[$i]['name'] == $name and $array_all[$i]['plc_id'] == $id) {
            if ($res == $array_all[$i]['name_res']) {
                $summ+=$array_all[$i]['raznost'];
                $z++;
            }
        }
    }
    /* @var $sheet type */
    return array($z, $summ);
}

$sql_distinct = pg_query('SELECT DISTINCT 
  "Places_cnt1"."Name",
  "Places_cnt1".plc_id
FROM
  "Tepl"."PlaceGroupRelations",
  "Tepl"."Places_cnt" "Places_cnt1"
WHERE
  "Places_cnt1".typ_id = 10');
while ($row_dist = pg_fetch_row($sql_distinct)) {
    $dist[] = array(
        'id' => $row_dist[1],
        'name' => $row_dist[0]
    );
}


$sheet->setCellValue("A2", "Отчет за период с " . date("d.m.Y", strtotime("-1 day", strtotime($date1))) . " по " . date("d.m.Y", strtotime("-1 day", strtotime($date2))) . "");
$sheet->getStyle('A2')->applyFromArray($FontStyle14TNR);
$sheet->mergeCells("A2:L2");


$sheet->setCellValue("A3", "№");
$sheet->mergeCells("A3:A4");
$sheet->setCellValue("B3", "Район");
$sheet->mergeCells("B3:B4");
$sheet->setCellValue("C3", "Учереждение");
$sheet->mergeCells("C3:C4");
$sheet->setCellValue("D3", "Адрес");
$sheet->mergeCells("D3:D4");
$sheet->setCellValue("E3", "Зав. № Счетчика");
$sheet->getStyle("E3")->getAlignment()->setWrapText(true);
$sheet->mergeCells("E3:E4");
$sheet->setCellValue("F3", "Тип водоснабжения");
$sheet->getStyle("F3")->getAlignment()->setWrapText(true);
$sheet->mergeCells("F3:F4");
$sheet->setCellValue("G3", "Местоположение");
$sheet->mergeCells("G3:G4");
$sheet->setCellValue("H3", "Показание потребления (м3)");
$sheet->mergeCells("H3:K3");
$sheet->setCellValue("H4", "На начало периода");
$sheet->getStyle("H4")->getAlignment()->setWrapText(true);
$sheet->setCellValue("I4", "На конец периода");
$sheet->getStyle("I4")->getAlignment()->setWrapText(true);
$sheet->setCellValue("J4", "За период");
$sheet->getStyle("J4")->getAlignment()->setWrapText(true);
$sheet->setCellValue("K4", "Общее");
$sheet->setCellValue("L3", "Время наработки (час)");
$sheet->getStyle("L3")->getAlignment()->setWrapText(true);
$sheet->mergeCells("L3:L4");
$sheet->setCellValue("M3", "Корректировки");
$sheet->getStyle("M3")->getAlignment()->setWrapText(true);
$sheet->mergeCells("M3:M4");
$sheet->setCellValue("N3", "Исключения");
$sheet->getStyle("N3")->getAlignment()->setWrapText(true);
$sheet->mergeCells("N3:N4");
$sheet->setCellValue("O3", "Сервисное обслуживание");
$sheet->getStyle("O3")->getAlignment()->setWrapText(true);
$sheet->mergeCells("O3:O4");



$sheet->getStyle('A3:O4')->applyFromArray($arrBorderStyle);
$sheet->getStyle('A3:O4')->applyFromArray($FontStyle11TNR);


$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(24);
$sheet->getColumnDimension('C')->setWidth(65);
$sheet->getColumnDimension('D')->setWidth(41);
$sheet->getColumnDimension('E')->setWidth(23);
$sheet->getColumnDimension('F')->setWidth(10);
$sheet->getColumnDimension('G')->setWidth(25);
$sheet->getColumnDimension('H')->setWidth(12);
$sheet->getColumnDimension('I')->setWidth(12);
$sheet->getColumnDimension('J')->setWidth(12);
$sheet->getColumnDimension('K')->setWidth(12);
$sheet->getColumnDimension('L')->setWidth(12);
$sheet->getColumnDimension('M')->setWidth(26);
$sheet->getColumnDimension('N')->setWidth(28);
$sheet->getColumnDimension('O')->setWidth(28);
$n = 0;
$kol = 0;
$colum_text = 4;
for ($i = 0; $i < count($array_all); $i++) {
    if ($array_all[$i]['plc_id'] == $array_all[$i + 1]['plc_id']) {
        $kol++;
    }
    if ($array_all[$i]['plc_id'] != $array_all[$i + 1]['plc_id']) {
        if ($kol > 0) {
            $n++;
            $key = array_search($array_all[$i]['id_dist'], array_column($dist, 'id'));
            //$kol++;
            $colum_text+=1;
            //$colum_text1=$kol+$colum_text;
            $sheet->setCellValueByColumnAndRow(0, $colum_text, "" . $n . "");
            $sheet->mergeCellsByColumnAndRow(0, $colum_text, 0, $colum_text + $kol);
            $sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $dist[$key]['name'] . "");
            $sheet->mergeCellsByColumnAndRow(1, $colum_text, 1, $colum_text + $kol);
            $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $array_all[$i]['name'] . "");
            $sheet->mergeCellsByColumnAndRow(2, $colum_text, 2, $colum_text + $kol);
            $sheet->setCellValueByColumnAndRow(3, $colum_text, "" . $array_all[$i]['addres'] . "");
            $sheet->mergeCellsByColumnAndRow(3, $colum_text, 3, $colum_text + $kol);


            if ($array_all[$i]['ticket'] != "") {
                $sheet->mergeCellsByColumnAndRow(14, $colum_text, 14, $colum_text + $kol);
                $sheet->setCellValueByColumnAndRow(14, $colum_text, "C.O.");
            }else{
                $sheet->mergeCellsByColumnAndRow(14, $colum_text, 14, $colum_text + $kol);
                $sheet->setCellValueByColumnAndRow(14, $colum_text, "");
            }

            //echo "</tr>";
            $z = 0;
            for ($j = 0; $j < count($array_all); $j++) {

                if ($array_all[$i]['plc_id'] == $array_all[$j]['plc_id']) {
                    if ($z == 0) {
                        $res = $array_all[$j]['name_res'];
                        $name = $array_all[$j]['name'];
                        $id = $array_all[$j]['plc_id'];

                        if ($array_all[$j]['value1'] == "нет данных") {
                            $val_1 = 0;
                        } else {
                            $val_1 = $array_all[$j]['value1'];
                        }
                        if ($array_all[$j]['value2'] == "нет данных") {
                            $val_2 = 0;
                        } else {
                            $val_2 = $array_all[$j]['value2'];
                        }


                        $sheet->setCellValueByColumnAndRow(4, $colum_text + $z, "" . $array_all[$j]['number_sens'] . "");
                        $sheet->setCellValueByColumnAndRow(5, $colum_text + $z, "" . $array_all[$j]['name_res'] . "");
                        $sheet->setCellValueByColumnAndRow(6, $colum_text + $z, "" . $array_all[$j]['com_sens'] . "");
                        $sheet->setCellValueByColumnAndRow(7, $colum_text + $z, "" . number_format($val_1, 2, ',', ' ') . " ");
                        $sheet->setCellValueByColumnAndRow(8, $colum_text + $z, "" . number_format($val_2, 2, ',', ' ') . " ");
                        $sheet->setCellValueByColumnAndRow(9, $colum_text + $z, "" . number_format($array_all[$j]['raznost'], 2, ',', ' ') . "");

                        list($a, $b) = view_td($j, $res, $name, $array_all, $id);
                        $ggg = $a - 1;
                        $hhh = $b;
                        $sheet->setCellValueByColumnAndRow(10, $colum_text + $z, "" . number_format($b, 2, ",", " ") . "");
                        $sheet->mergeCellsByColumnAndRow(10, $colum_text + $z, 10, $colum_text + $ggg);
                        $sheet->setCellValueByColumnAndRow(11, $colum_text + $z, "" . $array_all[$j]['vnr'] . "");
                        if ($array_all[$j]['korrect'] != "") {
                            $sheet->setCellValueByColumnAndRow(12, $colum_text + $z, "" . $array_all[$j]['korrect'] . "");
                            //$sheet->getStyle("E3")->getAlignment()->setWrapText(true);
                            $sheet->getStyleByColumnAndRow(12, $colum_text + $z)->getAlignment()->setWrapText(true);
                        }
                        $sheet->setCellValueByColumnAndRow(13, $colum_text + $z, "" . $array_all[$j]['nor_error'] . "");
                    } elseif ($res != $array_all[$j]['name_res']) {
                        $res = $array_all[$j]['name_res'];
                        $name = $array_all[$j]['name'];
                        $id = $array_all[$j]['plc_id'];


                        if ($array_all[$j]['value1'] == "нет данных") {
                            $val_1 = 0;
                        } else {
                            $val_1 = $array_all[$j]['value1'];
                        }
                        if ($array_all[$j]['value2'] == "нет данных") {
                            $val_2 = 0;
                        } else {
                            $val_2 = $array_all[$j]['value2'];
                        }


                        $sheet->setCellValueByColumnAndRow(4, $colum_text + $z, "" . $array_all[$j]['number_sens'] . "");
                        $sheet->setCellValueByColumnAndRow(5, $colum_text + $z, "" . $array_all[$j]['name_res'] . "");
                        $sheet->setCellValueByColumnAndRow(6, $colum_text + $z, "" . $array_all[$j]['com_sens'] . "");
                        $sheet->setCellValueByColumnAndRow(7, $colum_text + $z, "" . number_format($val_1, 2, ',', ' ') . "");
                        $sheet->setCellValueByColumnAndRow(8, $colum_text + $z, "" . number_format($val_2, 2, ',', ' ') . "");
                        $sheet->setCellValueByColumnAndRow(9, $colum_text + $z, "" . number_format($array_all[$j]['raznost'], 2, ',', ' ') . "");

                        list($a, $b) = view_td($j, $res, $name, $array_all, $id);
                        $ggg = $a - 1;
                        $hhh = $b;
                        $sheet->setCellValueByColumnAndRow(10, $colum_text + $z, "" . number_format($b, 2, ",", " ") . "");
                        $sheet->mergeCellsByColumnAndRow(10, $colum_text + $z, 10, $colum_text + $ggg + $z);
                        $sheet->setCellValueByColumnAndRow(11, $colum_text + $z, "" . $array_all[$j]['vnr'] . "");
                        if ($array_all[$j]['korrect'] != "") {
                            $sheet->setCellValueByColumnAndRow(12, $colum_text + $z, "" . $array_all[$j]['korrect'] . "");
                            //$sheet->getStyle("E3")->getAlignment()->setWrapText(true);
                            $sheet->getStyleByColumnAndRow(12, $colum_text + $z)->getAlignment()->setWrapText(true);
                        }
                        $sheet->setCellValueByColumnAndRow(13, $colum_text + $z, "" . $array_all[$j]['nor_error'] . "");
                    } else {

                        if ($array_all[$j]['value1'] == "нет данных") {
                            $val_1 = 0;
                        } else {
                            $val_1 = $array_all[$j]['value1'];
                        }
                        if ($array_all[$j]['value2'] == "нет данных") {
                            $val_2 = 0;
                        } else {
                            $val_2 = $array_all[$j]['value2'];
                        }

                        $sheet->setCellValueByColumnAndRow(4, $colum_text + $z, "" . $array_all[$j]['number_sens'] . "");
                        $sheet->setCellValueByColumnAndRow(5, $colum_text + $z, "" . $array_all[$j]['name_res'] . "");
                        $sheet->setCellValueByColumnAndRow(6, $colum_text + $z, "" . $array_all[$j]['com_sens'] . "");
                        $sheet->setCellValueByColumnAndRow(7, $colum_text + $z, "" . number_format($val_1, 2, ',', ' ') . "");
                        $sheet->setCellValueByColumnAndRow(8, $colum_text + $z, "" . number_format($val_2, 2, ',', ' ') . "");
                        $sheet->setCellValueByColumnAndRow(9, $colum_text + $z, "" . number_format($array_all[$j]['raznost'], 2, ',', ' ') . "");
                        $sheet->setCellValueByColumnAndRow(11, $colum_text + $z, "" . $array_all[$j]['vnr'] . "");
                        if ($array_all[$j]['korrect'] != "") {
                            $sheet->setCellValueByColumnAndRow(12, $colum_text + $z, "" . $array_all[$j]['korrect'] . "");
                            //$sheet->getStyle("E3")->getAlignment()->setWrapText(true);
                            $sheet->getStyleByColumnAndRow(12, $colum_text + $z)->getAlignment()->setWrapText(true);
                        }
                        //$sheet->setCellValueByColumnAndRow(12, $colum_text + $z, "" . $array_all[$j]['korrect'] . "");
                        $sheet->setCellValueByColumnAndRow(13, $colum_text + $z, "" . $array_all[$j]['nor_error'] . "");
                    }
                    $z++;
                }
            }

            $colum_text+=$kol;
            $kol = 0;
        } else {
            $n++;
            $colum_text+=1;

            if ($array_all[$i]['value1'] == "нет данных") {
                $val_1 = 0;
            } else {
                $val_1 = number_format($array_all[$i]['value1'], 2, ',', ' ');
            }
            if ($array_all[$i]['value2'] == "нет данных") {
                $val_2 = 0;
            } else {
                $val_2 = number_format($array_all[$i]['value2'], 2, ',', ' ');
            }


            $key = array_search($array_all[$i]['id_dist'], array_column($dist, 'id'));
            $sheet->setCellValueByColumnAndRow(0, $colum_text, "" . $n . "");
            $sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $dist[$key]['name'] . "");
            $sheet->setCellValueByColumnAndRow(2, $colum_text, "" . $array_all[$i]['name'] . "");
            $sheet->setCellValueByColumnAndRow(3, $colum_text, "" . $array_all[$i]['addres'] . "");
            $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $array_all[$i]['number_sens'] . "");
            $sheet->setCellValueByColumnAndRow(5, $colum_text, "" . $array_all[$i]['name_res'] . "");
            $sheet->setCellValueByColumnAndRow(6, $colum_text, "" . $array_all[$i]['com_sens'] . "");
            $sheet->setCellValueByColumnAndRow(7, $colum_text, "" . $val_1 . "");
            $sheet->setCellValueByColumnAndRow(8, $colum_text, "" . $val_2 . "");
            $sheet->setCellValueByColumnAndRow(9, $colum_text, "" . number_format($array_all[$i]['raznost'], 2, ',', ' ') . "");
            $sheet->setCellValueByColumnAndRow(10, $colum_text, "" . number_format($array_all[$i]['raznost'], 2, ',', ' ') . "");
            $sheet->setCellValueByColumnAndRow(11, $colum_text, "" . $array_all[$i]['vnr'] . "");
            if ($array_all[$i]['korrect'] != "") {
                $sheet->setCellValueByColumnAndRow(12, $colum_text, "" . $array_all[$i]['korrect'] . "");
                //$sheet->getStyle("E3")->getAlignment()->setWrapText(true);
                $sheet->getStyleByColumnAndRow(12, $colum_text)->getAlignment()->setWrapText(true);
            }
            $sheet->setCellValueByColumnAndRow(13, $colum_text, "" . $array_all[$i]['not_error'] . "");
            if ($array_all[$i]['ticket'] != "") {
                $sheet->setCellValueByColumnAndRow(14, $colum_text, "C.O.");
            }

            $kol = 0;
        }
    }
}

$sheet->getStyle('A5:O' . $colum_text . '')->applyFromArray($arrBorderStyle);
$sheet->getStyle('A5:O' . $colum_text . '')->applyFromArray($FontStyle11TNRtext);



header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename= Отчет МУП ПОВВ_" . date('d.m.Y') . "_" . $n . ".xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
