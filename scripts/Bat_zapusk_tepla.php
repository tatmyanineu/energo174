<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();


$date_now = date('2018-08-16');
$type_arch = 2;
$sql_error = pg_query('SELECT * FROM fault_cnt WHERE id =3');
while ($row = pg_fetch_row($sql_error)) {
    $inc = array(
        'id' => $row[0],
        'pogr' => $row[2],
        'date_time' => $row[3],
        'enable' => $row[5],
        'type' => $row[4]
    );
}


if ($inc['enable'] == 't') {
    $date1 = date('Y-m-d');
    $date2 = date('Y-m-d', strtotime('-1 day'));

//    $date2 = date('2018-09-14');
//    $date1 = date('2018-09-15');

    $pogr = $inc['pogr'];
    $type = $inc['type'];
    $type_error = 3;



    $sql = pg_query('SELECT 
  "Tepl"."ParamResPlc_cnt".plc_id,
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" as param,
  "Tepl"."Arhiv_cnt"."DataValue" as  value,
  "Tepl"."Arhiv_cnt"."DateValue" as date
FROM
  "Tepl"."ParamResPlc_cnt"
  INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
WHERE
  "Tepl"."Arhiv_cnt".typ_arh = ' . $type . ' AND 
  "Tepl"."Arhiv_cnt"."DateValue" > \'' . $date2 . '\' AND 
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\'
ORDER BY
  "Tepl"."ParamResPlc_cnt".plc_id,
  "Tepl"."ParamResPlc_cnt"."ParamRes_id"  
');

    $data = pg_fetch_all($sql);

    for ($i = 0; $i < count($data); $i++) {
        if ($data[$i]['plc_id'] == $data[$i + 1]['plc_id']) {
            if ($data[$i]['param'] != $data[$i + 1]['param']) {
                //echo $data[$i]['plc_id'] . " param ->  " . $data[$i]['param'] . "  value ->" . $data[$i]['value'] . " data-> " . $data[$i]['date'] . " <br> ";
                $array[] = array(
                    'plc_id' => $data[$i]['plc_id'],
                    'param' => $data[$i]['param'],
                    'value' => $data[$i]['value'],
                    'data' => $data[$i]['date']
                );
            }
        } else {
            //echo $data[$i]['plc_id'] . " param ->  " . $data[$i]['param'] . "  value ->" . $data[$i]['value'] . " data-> " . $data[$i]['date'] . " <br> ";
            $array[] = array(
                'plc_id' => $data[$i]['plc_id'],
                'param' => $data[$i]['param'],
                'value' => $data[$i]['value'],
                'data' => $data[$i]['date']
            );
        }
    }
}

$id_list[] = array();
$sql_incident = pg_query('SELECT plc_id FROM fault_inc WHERE numb = 3 ORDER BY plc_id');
while ($row = pg_fetch_row($sql_incident)) {
    $id_list[] = $row[0];
}

for ($i = 0; $i < count($array); $i++) {
    if ($array[$i][plc_id] == 32) {
        $g++;
    }
    if ($array[$i]['plc_id'] == $array[$i + 1]['plc_id']) {
        if ($array[$i]['param'] == 775) {
            $time = $array[$i]['value'];
        }
        if ($array[$i]['param'] == 19) {
            $m1 = $array[$i]['value'];
        }
        if ($array[$i]['param'] == 5) {
            $t1 = $array[$i]['value'];
        }
        if ($array[$i]['param'] == 20) {
            $m2 = $array[$i]['value'];
        }
        if ($array[$i]['param'] == 6) {
            $t2 = $array[$i]['value'];
        }
    } else {
        if ($array[$i]['param'] == 775) {
            $time = $array[$i]['value'];
        }
        if ($array[$i]['param'] == 19) {
            $m1 = $array[$i]['value'];
        }
        if ($array[$i]['param'] == 5) {
            $t1 = $array[$i]['value'];
        }
        if ($array[$i]['param'] == 20) {
            $m2 = $array[$i]['value'];
        }
        if ($array[$i]['param'] == 6) {
            $t2 = $array[$i]['value'];
        }
        if ($m1 == 0 and $t1 > 40) {
            //echo $array[$i]['plc_id'] . " m1 = " . $m1 . "  t1 >" . $t1 . "  time-> " . $time . "<br>";
            $m = "M1 (" . $m1 . ")";
            $t = "t1 (" . sprintf("%.2f", $t1) . ")";
            $error++;
        }
        if (isset($m2)) {
            if ($m2 == 0 and $t2 > 40) {
                //echo $array[$i]['plc_id'] . " m2 = " . $m2 . "  t2 >" . $t2 . "  time-> " . $time . " <br>";
                $m = "M2 (" . $m2 . ")";
                $t = "t2 (" . sprintf("%.2f", $t2) . ")";
                $error++;
            }
        }


        $k = array_search($array[$i]['plc_id'], $id_list);
        if ($k === false) {
            switch ($error) {
                case 1:
                    $text = $m . "=0; " . $t . " > Погр. (" . $inc['pogr'] . ")";
                    $text_mini = $m . " = 0; " . $t . " > Погр." . $inc['pogr'] . "; ВНР = " . $time;
                    echo $text . '<br>';
                    $sql_add = pg_query('INSERT INTO fault_inc(numb, date_time, plc_id, param, view_stat, comments) VALUES (' . $type_error . ', \'' . date("d.m.Y", strtotime($array[$i]['data'])) . '\', ' . $array[$i]['plc_id'] . ', \'' . $text . '\', 0, \'' . $text_mini . '\')');
                    $id_list[] = $array_pogr[$i]['plc_id'];
                    break;

                case 2:
                    $text = "M1 и M2 =0; t1 (" . sprintf("%.2f", $t1) . ") и t2 (" . sprintf("%.2f", $t2) . ") > Погр. (" . $inc['pogr'] . ")";
                    $text_mini = "M1 (" . $m1 . ") = 0 ; t1 (" . sprintf("%.2f", $t1) . ") > " . $inc['pogr'] . " и M2(" . $m2 . ") = 0 ; t2 (" . sprintf("%.2f", $t2) . ") > " . $inc['pogr'] . "; ВНР = " . $time;
                    echo $text_mini . '<br>';
                    $sql_add = pg_query('INSERT INTO fault_inc(numb, date_time, plc_id, param, view_stat, comments) VALUES (' . $type_error . ', \'' . date("d.m.Y", strtotime($array[$i]['data'])) . '\', ' . $array[$i]['plc_id'] . ', \'' . $text . '\', 0, \'' . $text_mini . '\')');
                    $id_list[] = $array_pogr[$i]['plc_id'];
                    break;
            }
        }

        unset($m1);
        unset($m2);
        unset($t1);
        unset($t2);
        unset($time);
        $error = 0;
    }

    $sql = pg_query('UPDATE fault_cnt SET date_time=\'' . date('Y-m-d H:i:00') . '\' WHERE id=3');
}    