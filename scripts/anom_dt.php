<?php

include '../db_config.php';
session_start();



$type_arch = 2;
$sql_error = pg_query('SELECT * FROM fault_cnt WHERE id =2');
while ($row = pg_fetch_row($sql_error)) {
    $data = array(
        'id' => $row[0],
        'pogr' => $row[2],
        'date_time' => $row[3]
    );
}

//$date1 = date('Y-m-d');
//$date2 = date('Y-m-d', strtotime('-1 day'));
$date1 = '2018-05-02';
$date2 = '2018-05-01';
$pogr = $data['pogr'];
$type_error = 2;

$array_massa = array();
$array_temper = array();
echo '<h3 class="text-center">Проверка за период с ' . date("d.m.Y", strtotime($after_day)) . ' по ' . date("d.m.Y", strtotime($date)) . ' </h3>';
$sql_archive = pg_query('SELECT DISTINCT 
                ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                "Tepl"."Places_cnt".plc_id,
                "Tepl"."Arhiv_cnt"."DataValue",
                "Tepl"."Places_cnt"."Name"
              FROM
                "Tepl"."ParamResPlc_cnt"
                INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
              WHERE
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND 
                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND  
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 5 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND 
                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 6 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND 
               "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 12 
              ORDER BY
                "Tepl"."Places_cnt".plc_id,
                "Tepl"."Arhiv_cnt"."DateValue",
                "Tepl"."ParamResPlc_cnt"."ParamRes_id"');

//echo pg_num_rows($sql_archive) . "<br>";
$z = 0;
$kol = 0;
$n = 0;


$arr_object_list = array(264, 293, 339, 336, 274, 40, 39, 54, 275, 189, 338);

while ($result = pg_fetch_row($sql_archive)) {
    $array_archive[] = array(
        'plc_id' => $result[2],
        'name' => $result[4],
        'param_id' => $result[1],
        'date' => $result[0],
        'value' => $result[3]
    );
}


for ($i = 0; $i < count($array_archive); $i++) {
    $k_obj = array_search($array_archive[$i]['plc_id'], $arr_object_list);
    if ($k_obj === false) {
        if ($array_archive[$i]['plc_id'] == $array_archive[$i + 1]['plc_id']) {
            if ($array_archive[$i]['date'] == $array_archive[$i + 1]['date']) {

                if ($array_archive[$i]['param_id'] == 5) {
                    $t1 = $array_archive[$i]['value'];
                }
                if ($array_archive[$i]['param_id'] == 6) {
                    $t2 = $array_archive[$i]['value'];
                }

                //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
            }
            if ($array_archive[$i]['date'] != $array_archive[$i + 1]['date']) {

                if ($array_archive[$i]['param_id'] == 5) {
                    $t1 = $array_archive[$i]['value'];
                }
                if ($array_archive[$i]['param_id'] == 6) {
                    $t2 = $array_archive[$i]['value'];
                }


                $t = $t1 - $t2;
                //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
                if ($t1 == 0 or $t2 == 0) {


                    $array_pogr[] = array(
                        //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";

                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => $t,
                        'error' => 1
                    );
                } elseif ($t1 < $t2) {
                    echo "ОШИБКА 0 =>" . $array_archive[$i]['plc_id'] . " t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> " . $tt . "<br>";

                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => $t,
                        'error' => 0
                    );
                } elseif ($t1 > 120 or $t2 > 120) {
                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => $t,
                        'error' => 2
                    );
                } elseif ($t < $pogr) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";
                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => $t,
                        'error' => 3
                    );
                }

                $t1 = 0;
                $t2 = 0;
                $t3 = 0;
                $t = 0;
            }
        }
        if ($array_archive[$i]['plc_id'] != $array_archive[$i + 1]['plc_id']) {
            //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";


            if ($array_archive[$i]['param_id'] == 5) {
                $t1 = $array_archive[$i]['value'];
            }
            if ($array_archive[$i]['param_id'] == 6) {
                $t2 = $array_archive[$i]['value'];
            }

            $t = $t1 - $t2;
            //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
            if ($t1 == 0 or $t2 == 0) {


                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $t,
                    'error' => 1
                );
            } elseif ($t1 < $t2) {
                echo "ОШИБКА 0 =>" . $array_archive[$i]['plc_id'] . " t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> " . $tt . "<br>";

                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $t,
                    'error' => 0
                );
            } elseif ($t1 > 120 or $t2 > 120) {
                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $t,
                    'error' => 2
                );
            } elseif ($t < $pogr) {
                //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";
                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $t,
                    'error' => 3
                );
            }

            $t1 = 0;
            $t2 = 0;
            $t3 = 0;
            $t = 0;
        }
    } else {
        if ($array_archive[$i]['plc_id'] == $array_archive[$i + 1]['plc_id']) {
            if ($array_archive[$i]['date'] == $array_archive[$i + 1]['date']) {


                if ($array_archive[$i]['param_id'] == 5) {
                    $t1 = $array_archive[$i]['value'];
                }
                if ($array_archive[$i]['param_id'] == 6) {
                    $t2 = $array_archive[$i]['value'];
                }
                if ($array_archive[$i]['param_id'] == 12) {
                    $t3 = $array_archive[$i]['value'];
                }
            }
            if ($array_archive[$i]['date'] != $array_archive[$i + 1]['date']) {


                if ($array_archive[$i]['param_id'] == 5) {
                    $t1 = $array_archive[$i]['value'];
                }
                if ($array_archive[$i]['param_id'] == 6) {
                    $t2 = $array_archive[$i]['value'];
                }
                if ($array_archive[$i]['param_id'] == 12) {
                    $t3 = $array_archive[$i]['value'];
                }

                $t = $t1 - $t2;
                if ($t1 == 0 or $t2 == 0) {

                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => $t,
                        'error' => 1
                    );
                } elseif ($t1 < $t2) {
                    echo "ОШИБКА 0 =>" . $array_archive[$i]['plc_id'] . " t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> " . $tt . "<br>";

                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => $t,
                        'error' => 0
                    );
                } elseif ($t1 > 120 or $t2 > 120) {
                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => $t,
                        'error' => 2
                    );
                } elseif ($t < $pogr) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";
                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => $t,
                        'error' => 3
                    );
                }


                $t1 = 0;
                $t2 = 0;
                $t3 = 0;
                $t = 0;
            }
        }
        if ($array_archive[$i]['plc_id'] != $array_archive[$i + 1]['plc_id']) {

            //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";


            if ($array_archive[$i]['param_id'] == 5) {
                $t1 = $array_archive[$i]['value'];
            }
            if ($array_archive[$i]['param_id'] == 6) {
                $t2 = $array_archive[$i]['value'];
            }
            if ($array_archive[$i]['param_id'] == 12) {
                $t3 = $array_archive[$i]['value'];
            }
            $t = $t1 - $t2;
            if ($t1 == 0 or $t2 == 0) {


                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $t,
                    'error' => 1
                );
            } elseif ($t1 < $t2) {
                    echo "ОШИБКА 0 =>" . $array_archive[$i]['plc_id'] . " t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> " . $tt . "<br>";

                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $t,
                    'error' => 0
                );
            } elseif ($t1 > 120 or $t2 > 120) {
                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $t,
                    'error' => 2
                );
            } elseif ($t < $pogr) {
                //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";
                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $t,
                    'error' => 3
                );
            }

            $t1 = 0;
            $t2 = 0;
            $t3 = 0;
            $t = 0;
        }
    }
}

var_dump($array_pogr);

echo "<table id='main_table' class='table table-bordered'>"
 . "<thead id='thead'>"
 . "<tr id='warning'>"
 . "<td rowspan=2><b>№</b></td>"
 . "<td rowspan=2><b>Название</b></td>"
 . "<td rowspan=2><b>Дата</b></td>"
 . "<td colspan=3><b>Температура</b></td>"
 . "</tr>"
 . "<tr id='warning'>"
 . "<td><b>Подача (т1)</b></td>"
 . "<td><b>Обратка (т2)</b></td>"
 . "<td><b>Разность</b></td>"
 . "</tr>"
 . "</thead>"
 . "<tbody>";

$id_list[] = array();
$sql_incident = pg_query('SELECT plc_id FROM fault_inc WHERE numb = 2 ORDER BY plc_id');
while ($row = pg_fetch_row($sql_incident)) {
    $id_list[] = $row[0];
}


$n = 0;
$k = 0;
for ($i = 0; $i < count($array_pogr); $i++) {

    if ($type_arch == 1) {
        $date_b = date('d.m.Y H:i', strtotime($array_pogr[$i]['date']));
    } elseif ($type_arch == 2) {
        $date_b = date('d.m.Y', strtotime($array_pogr[$i]['date']));
    }

    $k = array_search($array_pogr[$i]['plc_id'], $id_list);
    if ($k === false) {

        switch ($array_pogr[$i]['error']) {
            case 0:
                $text_error = 't2 (' . number_format($array_pogr[$i]['t2'], 2, '.', '') . ') > t1 (' . number_format($array_pogr[$i]['t1'], 2, '.', '') . ')';
                $mini_error = 't2 (' . number_format($array_pogr[$i]['t2'], 2, '.', '') . ') > t1 (' . number_format($array_pogr[$i]['t1'], 2, '.', '') . ')';

                break;
            case 1:

                if ($array_pogr[$i]['t1'] == 0) {
                    $text_error = 't1 (' . number_format($array_pogr[$i]['t1'], 2, '.', '') . ') = 0';
                    $mini_error = 't1 (' . number_format($array_pogr[$i]['t1'], 2, '.', '') . ') = 0';
                } else {
                    $text_error = 't2 (' . number_format($array_pogr[$i]['t2'], 2, '.', '') . ') = 0';
                    $mini_error = 't2 (' . number_format($array_pogr[$i]['t2'], 2, '.', '') . ') = 0';
                }

                break;
            case 2:
                if ($array_pogr[$i]['t1'] > 120) {
                    $text_error = 't1 (' . number_format($array_pogr[$i]['t1'], 2, '.', '') . ') > 120';
                    $mini_error = 't1 (' . number_format($array_pogr[$i]['t1'], 2, '.', '') . ') > 120';
                } else {
                    $text_error = 't2 (' . number_format($array_pogr[$i]['t2'], 2, '.', '') . ') > 120';
                    $mini_error = 't2 (' . number_format($array_pogr[$i]['t2'], 2, '.', '') . ') > 120';
                }
                break;
            case 3:
                $text_error = 'dt => Подача (t1: ' . number_format($array_pogr[$i]['t1'], 2, '.', '') . ') -  Обратка (t2: ' . number_format($array_pogr[$i]['t2'], 2, '.', '') . ') = ' . number_format($array_pogr[$i]['t'], 2, '.', '') . ';<br> dt (' . number_format($array_pogr[$i]['t'], 2, '.', '') . ') < Допуст. Погр.(' . $pogr . ')';
                $mini_error = 'dt (' . number_format($array_pogr[$i]['t'], 2, '.', '') . ') < Допуст. Погр.(' . $pogr . ')';

                break;

        }




        $sql_add = pg_query('INSERT INTO fault_inc(numb, date_time, plc_id, param, view_stat, comments) VALUES (' . $type_error . ', \'' . $date_b . '\', ' . $array_pogr[$i]['plc_id'] . ', \'' . $mini_error . '\', 0, \'' . $text_error . '\')');
        $id_list[] = $array_pogr[$i]['plc_id'];
    }


    echo '<tr data-href="object.php?id_object=' . $array_pogr[$i]['plc_id'] . '">'
    . '<td>' . $k . '</td>'
    . '<td>' . $array_pogr[$i]['name'] . '</td>'
    . '<td>' . $date_b . '</td>'
    . '<td>' . number_format($array_pogr[$i]['t1'], 2, '.', '') . '</td>'
    . '<td>' . number_format($array_pogr[$i]['t2'], 2, '.', '') . '</td>'
    . '<td>' . number_format($array_pogr[$i]['t'], 2, '.', '') . '</td>'
    . '</tr>';
    $n = 0;
}

echo "</tbody></table>";


$sql = pg_query('UPDATE fault_cnt SET date_time=\'' . date('Y-m-d H:i:00') . '\' WHERE id=2');
$i = 0;
?>