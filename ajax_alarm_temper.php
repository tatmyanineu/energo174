<?php

include 'db_config.php';
session_start();
$date = $_POST['date_afte'];
$time = strtotime("-10 day");
$after_day = $_POST['date_now'];
$pogr = $_POST['pogr'];
$type_arch = $_POST['type_arch'];
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
                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 5 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND 
                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 6 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND 
               "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
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

                $t = (($t2 / $t1) * 100) - 100;
                $tr = $t1 - $t2;
                //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
                if ($t > $pogr) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";
                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't3' => '-',
                        't' => $t
                    );
                }
                if ($tr < 0) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";
                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => $tr
                    );
                }

                $t1 = 0;
                $t2 = 0;
                $t3 = 0;
                $t = 0;
                $tr = 0;
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

            $t = (($t2 / $t1) * 100) - 100;
            $tr = $t1 - $t2;
            //echo $array_archive[$i]['plc_id'] . " " . $array_archive[$i]['date'] . " " . $array_archive[$i]['param_id'] . " " . $array_archive[$i]['value'] . "<br>";
            if ($t > $pogr) {
                //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";

                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $t
                );
            }

            if ($tr < 0) {
                //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m=<b>" . $m . "</b> t1=" . $t1 . " t2=" . $t2 . " t=<b>" . $t . "</b> <br>";
                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $tr
                );
            }

            $t1 = 0;
            $t2 = 0;
            $t3 = 0;
            $t = 0;
            $tr = 0;
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

               $t = (($t2 / $t1) * 100) - 100;

                $tr = ($t1 + $t3) - $t2;
                if ($t > $pogr) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";

                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't' => '' . $t . ''
                    );
                }

                if ($tr < 0) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";

                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        't1' => $t1,
                        't2' => $t2,
                        't3' => $t3,
                        't' => $tr
                    );
                }
                $t1 = 0;
                $t2 = 0;
                $t3 = 0;
                $th = 0;
                $tw = 0;
                $tr = 0;
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
            $t = (($t2 / $t1) * 100) - 100;
            
            $tr = ($t1 + $t3) - $t2;
            if ($t > $pogr) {
                //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";
                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => '' . $t. ''
                );
            }
            if ($tr < 0) {
                //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";

                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    't1' => $t1,
                    't2' => $t2,
                    't' => $tr
                );
            }
            $t1 = 0;
            $t2 = 0;
            $t3 = 0;
            $th = 0;
            $tw = 0;
            $tr = 0;
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
 . "<td colspan=4><b>Температура</b></td>"
 . "</tr>"
 . "<tr id='warning'>"
 . "<td><b>Подача (т1)</b></td>"
 . "<td><b>Обратка (т2)</b></td>"
 . "<td><b>Разность (%)</b></td>"
 . "</tr>"
 . "</thead>"
 . "<tbody>";
$n = 0;
$k = 0;
for ($i = 0; $i < count($array_pogr); $i++) {
    if ($array_pogr[$i][plc_id] == $array_pogr[$i + 1][plc_id]) {
        $n++;
    }
    if ($array_pogr[$i][plc_id] != $array_pogr[$i + 1][plc_id]) {
        if ($n > 0) {
            $n++;
            $k++;
            echo "<tr class='for_click warning' id='" . $array_pogr[$i]['plc_id'] . "'>"
            . "<td>" . $k . "</td>"
            . "<td><b>" . $array_pogr[$i]['name'] . "</b></td>"
            . "<td colspan=9><b>" . $n . "</b></td>"
            . "</tr>";
            for ($j = 0; $j < count($array_pogr); $j++) {
                if ($array_pogr[$j][plc_id] == $array_pogr[$i][plc_id]) {

                    if ($type_arch == 1) {
                        $date_b = date('d.m.Y H:i', strtotime($array_pogr[$j]['date']));
                    } elseif ($type_arch == 2) {
                        $date_b = date('d.m.Y', strtotime($array_pogr[$j]['date']));
                    }

                    echo '<tr data-href="object.php?id_object=' . $array_pogr[$j]['plc_id'] . '" id="hide_' . $array_pogr[$j]['plc_id'] . '" style= "display: none;">'
                    . '<td>' . $k . '</td>'
                    . '<td>' . $array_pogr[$j]['name'] . '</td>'
                    . '<td>' . $date_b . '</td>'
                    . '<td>' . number_format($array_pogr[$i]['t1'], 2, '.', '') . '</td>'
                    . '<td>' . number_format($array_pogr[$i]['t2'], 2, '.', '') . '</td>'
                    . '<td>' . number_format($array_pogr[$i]['t'], 2, '.', '') . '</td>'
                    . '</tr>';
                }
            }
            $n = 0;
        } else {
            $k++;

            if ($type_arch == 1) {
                $date_b = date('d.m.Y H:i', strtotime($array_pogr[$i]['date']));
            } elseif ($type_arch == 2) {
                $date_b = date('d.m.Y', strtotime($array_pogr[$i]['date']));
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
    }
}

echo "</tbody></table>";
?>