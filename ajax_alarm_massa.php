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
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
               "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 19 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
              "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 20 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
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

                $m = (($m2 / $m1) * 100) - 100;
                $mn = (($m1 / $m2) * 100) - 100;
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
                        'm' => '->' . number_format($m, 2, '.', '') . ''
                    );
                } elseif ($mn > $pogr) {
                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        'm1' => $m1,
                        'm2' => $m2,
                        'm3' => '-',
                        'm' => '<-' . number_format($mn, 2, '.', '') . ''
                    );
                }


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

            $m = (($m2 / $m1) * 100) - 100;
            $mn = (($m1 / $m2) * 100) - 100;
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
                    'm' => '->' . number_format($m, 2, '.', '') . ''
                );
            } elseif ($mn > $pogr) {
                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    'm1' => $m1,
                    'm2' => $m2,
                    'm3' => '-',
                    'm' => '<-' . number_format($mn, 2, '.', '') . ''
                );
            }


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

                $m = (($m2 / ($m1 + $m3)) * 100) - 100;
                $mn = ((($m1 + $m3) / $m2 ) * 100) - 100;
                if ($m > $pogr) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";

                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        'm1' => $m1,
                        'm2' => $m2,
                        'm3' => $m3,
                        'm' => '->' . number_format($m, 2, '.', '')
                    );
                }
                if ($mn > $pogr) {
                    //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";

                    $array_pogr[] = array(
                        'plc_id' => $array_archive[$i]['plc_id'],
                        'name' => $array_archive[$i]['name'],
                        'date' => $array_archive[$i]['date'],
                        'm1' => $m1,
                        'm2' => $m2,
                        'm3' => $m3,
                        'm' => '<-' . number_format($mn, 2, '.', '')
                    );
                }


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

            $m = (($m2 / ($m1 + $m3)) * 100) - 100;
            $mn = ((($m1 + $m3) / $m2 ) * 100) - 100;
            if ($m > $pogr) {
                //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";

                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    'm1' => $m1,
                    'm2' => $m2,
                    'm3' => $m3,
                    'm' => '->' . number_format($m, 2, '.', '')
                );
            }
            if ($mn > $pogr) {
                //echo $array_archive[$i]['plc_id'] . " m1=" . $m1 . " m2=" . $m2 . " m3=" . $m3 . " m=" . $m . " t1=" . $t1 . " t2=" . $t2 . " t3= " . $t3 . "  th=" . $th . " tw=" . $tw . "<br>";

                $array_pogr[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'name' => $array_archive[$i]['name'],
                    'date' => $array_archive[$i]['date'],
                    'm1' => $m1,
                    'm2' => $m2,
                    'm3' => $m3,
                    'm' => '<-' . number_format($mn, 2, '.', '')
                );
            }


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
                    . '<td>' . number_format($array_pogr[$i]['m1'], 2, '.', '') . '</td>'
                    . '<td>' . number_format($array_pogr[$i]['m2'], 2, '.', '') . '</td>'
                    . '<td>' . number_format($array_pogr[$i]['m3'], 2, '.', '') . '</td>'
                    . '<td>' . $array_pogr[$i]['m'] . '</td>'
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
            . '<td>' . number_format($array_pogr[$i]['m1'], 2, '.', '') . '</td>'
            . '<td>' . number_format($array_pogr[$i]['m2'], 2, '.', '') . '</td>'
            . '<td>' . number_format($array_pogr[$i]['m3'], 2, '.', '') . '</td>'
            . '<td>' . $array_pogr[$i]['m'] . '</td>'
            . '</tr>';
            $n = 0;
        }
    }
}

echo "</tbody></table>";
?>