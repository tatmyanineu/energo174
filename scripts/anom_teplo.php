<?php

include '../db_config.php';
session_start();



$date_now = date('Y-m-d H:00:00');
$type_arch = 2;
$sql_error = pg_query('SELECT * FROM fault_cnt WHERE id =0');
while ($row = pg_fetch_row($sql_error)) {
    $data = array(
        'id' => $row[0],
        'pogr' => $row[2],
        'date_time' => $row[3],
        'enable' => $row[5]
    );
}

//while($date_now!=$data['date_time']){
//    //
//    $data['date_time'] = date('Y-m-d H:00:00', strtotime("+1 hour", strtotime($data['date_time'])));
//    $time[] = $data['date_time'];
//}
if ($data['enable'] == 't') {
    $date1 = date('Y-m-d');
    $date2 = date('Y-m-d', strtotime('-1 day'));
    $pogr = $data['pogr'];
    $type_error = 0;

    $array_massa = array();
    $array_temper = array();
    echo '<h3 class="text-center">Проверка1111 за период с ' . date("d.m.Y", strtotime($after_day)) . ' по ' . date("d.m.Y", strtotime($date)) . ' </h3>';
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
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
               "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 19 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
              "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 20 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 21
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

                    if ($array_archive[$i]['param_id'] == 19) {
                        $m1 = $array_archive[$i]['value'];
                    }
                    if ($array_archive[$i]['param_id'] == 20) {
                        $m2 = $array_archive[$i]['value'];
                    }

                    //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
                }
                if ($array_archive[$i]['date'] != $array_archive[$i + 1]['date']) {


                    if ($array_archive[$i]['param_id'] == 19) {
                        $m1 = $array_archive[$i]['value'];
                    }
                    if ($array_archive[$i]['param_id'] == 20) {
                        $m2 = $array_archive[$i]['value'];
                    }


                    if ($m1 > $m2) {
                        $m = (($m1 - $m2) / $m1) * 100;
                        $e = 1;
                    } else {
                        $m = (($m1 - $m2) / $m2) * 100;
                        $e = 2;
                    }



//                $m = (($m2 / $m1) * 100) - 100;
//                $mn = (($m1 / $m2) * 100) - 100;
                    //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
                    if ($m > $pogr) {
                        //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";
                        $array_pogr[] = array(
                            'plc_id' => $array_archive[$i]['plc_id'],
                            'name' => $array_archive[$i]['name'],
                            'date' => $array_archive[$i]['date'],
                            'm1' => $m1,
                            'm2' => $m2,
                            'm3' => '-',
                            'm' => number_format($m, 2, '.', ''),
                            'e' => $e
                        );
                    }
//                elseif ($mn > $pogr) {
//                    $array_pogr[] = array(
//                        'plc_id' => $array_archive[$i]['plc_id'],
//                        'name' => $array_archive[$i]['name'],
//                        'date' => $array_archive[$i]['date'],
//                        'm1' => $m1,
//                        'm2' => $m2,
//                        'm3' => '-',
//                        'm' => number_format($mn, 2, '.', ''),
//                        'e' => $e
//                    );
//                }


                    $m1 = 0;
                    $m2 = 0;
                    $m3 = 0;
                    $m = 0;
                    $mn = 0;
                }
            }
            if ($array_archive[$i]['plc_id'] != $array_archive[$i + 1]['plc_id']) {
                //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
                if ($array_archive[$i]['param_id'] == 19) {
                    $m1 = $array_archive[$i]['value'];
                }
                if ($array_archive[$i]['param_id'] == 20) {
                    $m2 = $array_archive[$i]['value'];
                }


                if ($m1 > $m2) {
                    $m = (($m1 - $m2) / $m1) * 100;
                    $e = 1;
                } else {
                    $m = (($m1 - $m2) / $m2) * 100;
                    $e = 2;
                }


//            $m = (($m2 / $m1) * 100) - 100;
//            $mn = (($m1 / $m2) * 100) - 100;
                //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
                if ($m > $pogr) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";
                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        'm1' => $m1,
                        'm2' => $m2,
                        'm3' => '-',
                        'm' => number_format($m, 2, '.', ''),
                        'e' => $e
                    );
                }
//            elseif ($mn > $pogr) {
//                $array_pogr[] = array(
//                    'plc_id' => $array_archive[$i]['plc_id'],
//                    'name' => $array_archive[$i]['name'],
//                    'date' => $array_archive[$i]['date'],
//                    'm1' => $m1,
//                    'm2' => $m2,
//                    'm3' => '-',
//                    'm' => number_format($mn, 2, '.', ''),
//                    'e' => 2
//                );
//            }


                $m1 = 0;
                $m2 = 0;
                $m3 = 0;
                $m = 0;
                $mn = 0;
            }
        } else {
            if ($array_archive[$i]['plc_id'] == $array_archive[$i + 1]['plc_id']) {
                if ($array_archive[$i]['date'] == $array_archive[$i + 1]['date']) {

                    if ($array_archive[$i]['param_id'] == 19) {
                        $m1 = $array_archive[$i]['value'];
                    }
                    if ($array_archive[$i]['param_id'] == 20) {
                        $m2 = $array_archive[$i]['value'];
                    }
                    if ($array_archive[$i]['param_id'] == 21) {
                        $m3 = $array_archive[$i]['value'];
                    }
                }
                if ($array_archive[$i]['date'] != $array_archive[$i + 1]['date']) {

                    if ($array_archive[$i]['param_id'] == 19) {
                        $m1 = $array_archive[$i]['value'];
                    }
                    if ($array_archive[$i]['param_id'] == 20) {
                        $m2 = $array_archive[$i]['value'];
                    }
                    if ($array_archive[$i]['param_id'] == 21) {
                        $m3 = $array_archive[$i]['value'];
                    }


                    $m_n = $m1 + $m3;
                    if ($m_n > $m2) {
                        $m = (($m_n - $m2) / $m_n) * 100;
                        $e = 1;
                    } else {
                        $m = (($m_n - $m2) / $m2) * 100;
                        $e = 2;
                    }



//                $m = (($m2 / ($m1 + $m3)) * 100) - 100;
//                $mn = ((($m1 + $m3) / $m2 ) * 100) - 100;
                    if ($m > $pogr) {
                        //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";

                        $array_pogr[] = array(
                            'plc_id' => $array_archive[$i]['plc_id'],
                            'name' => $array_archive[$i]['name'],
                            'date' => $array_archive[$i]['date'],
                            'm1' => $m1,
                            'm2' => $m2,
                            'm3' => $m3,
                            'm' => number_format($m, 2, '.', ''),
                            'e' => $e
                        );
                    }
//                if ($mn > $pogr) {
//                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";
//
//                    $array_pogr[] = array(
//                        'plc_id' => $array_archive[$i]['plc_id'],
//                        'name' => $array_archive[$i]['name'],
//                        'date' => $array_archive[$i]['date'],
//                        'm1' => $m1,
//                        'm2' => $m2,
//                        'm3' => $m3,
//                        'm' => number_format($mn, 2, '.', ''),
//                        'e' => 2
//                    );
//                }


                    $m1 = 0;
                    $m2 = 0;
                    $m3 = 0;
                    $m = 0;
                    $mn = 0;
                }
            }
            if ($array_archive[$i]['plc_id'] != $array_archive[$i + 1]['plc_id']) {

                //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
                if ($array_archive[$i]['param_id'] == 19) {
                    $m1 = $array_archive[$i]['value'];
                }
                if ($array_archive[$i]['param_id'] == 20) {
                    $m2 = $array_archive[$i]['value'];
                }
                if ($array_archive[$i]['param_id'] == 21) {
                    $m3 = $array_archive[$i]['value'];
                }

                $m_n = $m1 + $m3;
                if ($m_n > $m2) {
                    $m = (($m_n - $m2) / $m_n) * 100;
                    $e = 1;
                } else {
                    $m = (($m_n - $m2) / $m2) * 100;
                    $e = 2;
                }




//            $m = (($m2 / ($m1 + $m3)) * 100) - 100;
//            $mn = ((($m1 + $m3) / $m2 ) * 100) - 100;
                if ($m > $pogr) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";

                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        'm1' => $m1,
                        'm2' => $m2,
                        'm3' => $m3,
                        'm' => number_format($m, 2, '.', ''),
                        'e' => $e
                    );
                }
//            if ($mn > $pogr) {
//                //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";
//
//                $array_pogr[] = array(
//                    'plc_id' => $array_archive[$i]['plc_id'],
//                    'name' => $array_archive[$i]['name'],
//                    'date' => $array_archive[$i]['date'],
//                    'm1' => $m1,
//                    'm2' => $m2,
//                    'm3' => $m3,
//                    'm' => number_format($mn, 2, '.', ''),
//                    'e' => 2
//                );
//            }


                $m1 = 0;
                $m2 = 0;
                $m3 = 0;
                $m = 0;
                $mn = 0;
            }
        }
    }

//var_dump($array_pogr);

    echo "<table id='main_table' class='table table-bordered'>"
    . "<thead id='thead'>"
    . "<tr id='warning'>"
    . "<td rowspan=2><b>№</b></td>"
    . "<td rowspan=2><b>Название</b></td>"
    . "<td rowspan=2><b>Дата</b></td>"
    . "<td colspan=4><b>Масса</b></td>"
    . "</tr>"
    . "<tr id='warning'>"
    . "<td><b>Подача (м1)</b></td>"
    . "<td><b>Обратка (м2)</b></td>"
    . "<td><b>Подача (м3)</b></td>"
    . "<td><b>Разность (%)</b></td>"
    . "</tr>"
    . "</thead>"
    . "<tbody>";
    $n = 0;
    $k = 0;
    $id_list[] = array();
    $sql_incident = pg_query('SELECT plc_id FROM fault_inc WHERE numb = 0 ORDER BY plc_id');
    while ($row = pg_fetch_row($sql_incident)) {
        $id_list[] = $row[0];
    }

    for ($i = 0; $i < count($array_pogr); $i++) {

        if ($type_arch == 1) {
            $date_b = date('d.m.Y H:i', strtotime($array_pogr[$i]['date']));
        } elseif ($type_arch == 2) {
            $date_b = date('d.m.Y', strtotime($array_pogr[$i]['date']));
        }
        $k = array_search($array_pogr[$i]['plc_id'], $id_list);
        if ($k === false) {

            if ($array_pogr[$i]['e'] == 1) { // $m = (($m_n - $m2) / $m_n) * 100;
                if ($m3 != '-') {
                    $text_error = 'Погрешность => (((Подача (m1: ' . number_format($array_pogr[$i]['m1'], 2, '.', '') . ') + Подача (m3: ' . number_format($array_pogr[$i]['m3'], 2, '.', '') . ')) - Обратка (m2: ' . number_format($array_pogr[$i]['m2'], 2, '.', '') . '))/ Подача (m1: ' . number_format($array_pogr[$i]['m1'], 2, '.', '') . ') + Подача (m3: ' . number_format($array_pogr[$i]['m3'], 2, '.', '') . ' ))*100 = ' . number_format($array_pogr[$i]['m'], 2, '.', '') . '; <br>Погреш. (' . number_format($array_pogr[$i]['m'], 2, '.', '') . ') > Допуст. Погр.(' . $pogr . ')';
                    $mini_error = 'Погреш. (' . number_format($array_pogr[$i]['m'], 2, '.', '') . ') > Допуст. Погр.(' . $pogr . ')';
                } else {
                    $text_error = 'Погрешность => ((Подача (m1: ' . number_format($array_pogr[$i]['m1'], 2, '.', '') . ') - Обратка (m2: ' . number_format($array_pogr[$i]['m2'], 2, '.', '') . '))/ Обратка (m1: ' . number_format($array_pogr[$i]['m1'], 2, '.', '') . '))*100 = ' . number_format($array_pogr[$i]['m'], 2, '.', '') . ';<br> Погреш. (' . number_format($array_pogr[$i]['m'], 2, '.', '') . ') > Допуст. Погр.(' . $pogr . ')';
                    $mini_error = 'Погреш. (' . number_format($array_pogr[$i]['m'], 2, '.', '') . ') > Допуст. Погр.(' . $pogr . ')';
                }
            } else {
                if ($m3 != '-') { // $m = (($m_n - $m2) / $m2) * 100;
                    $text_error = 'Погрешность => (((Подача (m1: ' . number_format($array_pogr[$i]['m1'], 2, '.', '') . ') + Подача (m3: ' . number_format($array_pogr[$i]['m3'], 2, '.', '') . ')) - Обратка (m2: ' . number_format($array_pogr[$i]['m2'], 2, '.', '') . '))/ Обратка (m2: ' . number_format($array_pogr[$i]['m2'], 2, '.', '') . ' ))*100 = ' . number_format($array_pogr[$i]['m'], 2, '.', '') . '; <br>Погреш. (' . number_format($array_pogr[$i]['m'], 2, '.', '') . ') > Допуст. Погр.(' . $pogr . ')';
                    $mini_error = 'Погреш. (' . number_format($array_pogr[$i]['m'], 2, '.', '') . ') > Допуст. Погр.(' . $pogr . ')';
                } else {
                    $text_error = 'Погрешность => ((Подача (m1: ' . number_format($array_pogr[$i]['m1'], 2, '.', '') . ')  - Обратка (m2: ' . number_format($array_pogr[$i]['m2'], 2, '.', '') . '))/ Обратка (m2: ' . number_format($array_pogr[$i]['m2'], 2, '.', '') . '))*100 = ' . number_format($array_pogr[$i]['m'], 2, '.', '') . '; <br> Погреш. (' . number_format($array_pogr[$i]['m'], 2, '.', '') . ') > Допуст. Погр.(' . $pogr . ')';
                    $mini_error = 'Погреш. (' . number_format($array_pogr[$i]['m'], 2, '.', '') . ') > Допуст. Погр.(' . $pogr . ')';
                }
            }

            $sql_add = pg_query('INSERT INTO fault_inc(numb, date_time, plc_id, param, view_stat, comments) VALUES (' . $type_error . ', \'' . $date_b . '\', ' . $array_pogr[$i]['plc_id'] . ', \'' . $mini_error . '\', 0, \'' . $text_error . '\')');
            $id_list[] = $array_pogr[$i]['plc_id'];
        }
        echo '<tr data-href="object.php?id_object=' . $array_pogr[$i]['plc_id'] . '">'
        . '<td>' . $k . '</td>'
        . '<td>' . $array_pogr[$i]['name'] . '</td>'
        . '<td>' . $date_b . '</td>'
        . '<td>' . number_format($array_pogr[$i]['m1'], 2, '.', '') . '</td>'
        . '<td>' . number_format($array_pogr[$i]['m2'], 2, '.', '') . '</td>'
        . '<td>' . number_format($array_pogr[$i]['m3'], 2, '.', '') . '</td>'
        . '<td>' . $array_pogr[$i]['m'] . '</td>'
        . '</tr>';
        $n = 0;
    }

    echo "</tbody></table>";
    $sql = pg_query('UPDATE fault_cnt SET date_time=\'' . date('Y-m-d H:i:00') . '\' WHERE id=0');
}
?>