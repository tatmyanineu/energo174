<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../db_config.php';
session_start();
$id_object = $_POST['id_object'];

$date1 = $_POST['date1'];
$date2 = $_POST['date2'];

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

$sql_sensor = pg_query('SELECT 
  "Tepl"."Sensor_cnt"."Comment",
  "Tepl"."Sensor_Property"."Propert_Value",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Sensor_cnt".prp_id)
  INNER JOIN "Tepl"."TypeSensor" ON ("Tepl"."Sensor_cnt".sen_id = "Tepl"."TypeSensor".sen_id)
  INNER JOIN "Tepl"."Sensor_Property" ON ("Tepl"."Sensor_cnt".s_id = "Tepl"."Sensor_Property".s_id)
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."ParamResGroupRelations".prp_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."ParamResGroupRelations".grp_id = "Tepl"."GroupToUserRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
  "Tepl"."Sensor_Property".id_type_property = 0 AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' 
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."Sensor_Property".id_type_property
  ');
while ($row_sensor = pg_fetch_row($sql_sensor)) {
    $arr_comments[] = $row_sensor[0];
    $arr_number[] = $row_sensor[1];
    $arr_sens_id_resours[] = $row_sensor[2];
}

echo '<table id="main_table" class="table table-responsive table-bordered">
    <thead  id="thead">
        <tr id = "warning">
            <td rowspan = 2><b>№</b></td>
            <td rowspan = 2><b>Дата</b></td>';

//while ($row_resours = pg_fetch_row($sql_resurse)){
//        echo '<td>'.$row_resours[0].'</td>';
//        $id_resours[]=$row_resours[3];
//}
$i = 0;
$j = 0;
while ($row_resours = pg_fetch_row($sql_resurse)) {
    $id_resours[] = $row_resours[3];
    $arr_resours[] = $row_resours[0];
    if ($arr_name[$i] == "")
        $arr_name[$i] = $row_resours[2];
    if ($arr_group[$j] == "")
        $arr_group[$j] = $row_resours[1];

    if ($arr_name[$i] == $row_resours[2]) {
        $par[$i] ++;
        // echo " arr_name = ".$arr_name[$i]."   par=".$par[$i]."   i=".$i."<br>";
    } else {
        $i++;
        $arr_name[$i] = $row_resours[2];
        $par[$i] ++;
        $j++;
        $arr_group[$j] = $row_resours[1];
    };

    if ($arr_group[$j] == $row_resours[1]) {

        $grou[$j] ++;
        //echo  "arr_grou = ". $arr_group[$j]."   grou=".$grou[$j]."  j=".$j." <br>";
    } else {
        //echo "j++<br>";
        $j++;
        $arr_group[$j] = $row_resours[1];
        $grou[$j] ++;
        //echo  "arr_grou = ". $arr_group[$j]."   grou=".$grou[$j]."  j=".$j." <br>";
    };
}
for ($c = 0; $c < count($arr_name); $c++) {
    echo "<td colspan=" . $par[$c] . "><b>" . $arr_name[$c] . "</b></td>";
}
echo '<tr id = "warning" >';

for ($b = 0; $b < count($arr_resours); $b++) {
    if ($arr_resours[$b] == 'Масса1(т)') {
        echo "<td><b>Масса<sub>1</sub> (Т)</b></td>";
    } elseif ($arr_resours[$b] == 'Масса2(т)') {
        echo "<td><b>Масса<sub>2</sub> (Т)</b></td>";
    } elseif ($arr_resours[$b] == 'Масса3(т)') {
        echo "<td><b>Масса<sub>3</sub> (Т)</b></td>";
    } elseif ($arr_resours[$b] == 'Температура1(гр. С)') {
        echo "<td><b>Температура<sub>1</sub> (<sup>o</sup>C)</b></td>";
    } elseif ($arr_resours[$b] == 'Температура2(гр. С)') {
        echo "<td><b>Температура<sub>2</sub> (<sup>o</sup>C)</b></td>";
    } elseif ($arr_resours[$b] == 'Температура3(гр. С)') {
        echo "<td><b>Температура<sub>3</sub> (<sup>o</sup>C)</b></td>";
    } elseif ($arr_resours[$b] == 'Тепл. энерг.1 (ГКал)') {
        echo "<td><b>Тепл. энергия<sub>1</sub> (ГКал)</b></td>";
    } elseif ($arr_resours[$b] == 'Тепл. энерг.2 (ГКал)') {
        echo "<td><b>Тепл. энергия<sub>2</sub> (ГКал)</b></td>";
    } elseif ($arr_resours[$b] == 'Время работы1(ч)') {
        echo "<td><b>Время работы<sub>1</sub> (ч)</b></td>";
    } elseif ($arr_resours[$b] == 'Объёмный расход1(м3/ч)') {
        echo "<td><b>Объёмный расход<sub>1</sub><br> (м<sup>3</sup>/ч)</b></td>";
    } elseif ($arr_resours[$b] == 'Объёмный расход2(м3/ч)') {
        echo "<td><b>Объёмный расход<sub>2</sub><br> (м<sup>3</sup>/ч)</b></td>";
    } elseif ($arr_resours[$b] == 'Объёмный расход3(м3/ч)') {
        echo "<td><b>Объёмный расход<sub>3</sub><br> (м<sup>3</sup>/ч)</b></td>";
    } elseif ($arr_resours[$b] == 'Объём1(м3)') {
        if ($arr_sens_id_resours[$b] == $id_resours[$b]) {
            echo "<td><b> Объём<sub>1</sub> (м<sup>3</sup>)</b><br> <i>" . $arr_number[$b] . "</i><br> <i>" . $arr_comments[$b] . "</i></td>";
        } else {
            echo "<td><b>Объём<sub>1</sub> (м<sup>3</sup>)</b></td>";
        }
    } elseif ($arr_resours[$b] == 'Объём2(м3)') {
        if ($arr_sens_id_resours[$b] == $id_resours[$b]) {
            echo "<td><b> Объём<sub>2</sub> (м<sup>3</sup>)</b><br> <i>" . $arr_number[$b] . "</i><br> <i>" . $arr_comments[$b] . "</i></td>";
        } else {
            echo "<td><b>Объём<sub>2</sub> (м<sup>3</sup>)</b></td>";
        }
    } elseif ($arr_resours[$b] == 'Объём3(м3)') {
        if ($arr_sens_id_resours[$b] == $id_resours[$b]) {
            echo "<td> <b>Объём<sub>3</sub> (м<sup>3</sup>)</b><br> <i>" . $arr_number[$b] . "</i><br> <i>" . $arr_comments[$b] . "</i></td>";
        } else {
            echo "<td><b>Объём<sub>3</sub> (м<sup>3</sup></b>)</td>";
        }
    } elseif ($arr_resours[$b] == 'Объём4(м3)') {
        if ($arr_sens_id_resours[$b] == $id_resours[$b]) {
            echo "<td><b> Объём<sub>4</sub> (м<sup>3</sup>)</b><br> <i>" . $arr_number[$b] . "</i><br> <i>" . $arr_comments[$b] . "</i></td>";
        } else {
            echo "<td><b>Объём<sub>4</sub> (м<sup>3</sup>)</b></td>";
        }
    } elseif ($arr_resours[$b] == 'Объём5(м3)') {
        if ($arr_sens_id_resours[$b] == $id_resours[$b]) {
            echo "<td><b> Объём<sub>5</sub> (м<sup>3</sup>)</b><br> <i>" . $arr_number[$b] . "</i><br> <i>" . $arr_comments[$b] . "</i></td>";
        } else {
            echo "<td><b>Объём<sub>5</sub> (м<sup>3</sup>)</b></td>";
        }
    } elseif ($arr_resours[$b] == 'Объём6(м3)') {
        if ($arr_sens_id_resours[$b] == $id_resours[$b]) {
            echo "<td><b> Объём<sub>6</sub> (м<sup>3</sup>)</b><br> <i>" . $arr_number[$b] . "</i><br> <i>" . $arr_comments[$b] . "</i></td>";
        } else {
            echo "<td><b>Объём<sub>6</sub> (м<sup>3</sup>)</b></td>";
        }
    } elseif ($arr_resours[$b] == 'Объём7(м3)') {
        if ($arr_sens_id_resours[$b] == $id_resours[$b]) {
            echo "<td><b> Объём<sub>7</sub> (м<sup>3</sup>)</b><br> <i>" . $arr_number[$b] . "</i><br> <i>" . $arr_comments[$b] . "</i></td>";
        } else {
            echo "<td><b>Объём<sub>7</sub> (м<sup>3</sup>)</b></td>";
        }
    } else {
        echo "<td><b>" . $arr_resours[$b] . "</b></td>";
    }
}

echo '</tr></thead>';


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
    echo '<tr id="hover">';
    $s++;
    echo "<td>" . $s . "</td>";
    echo '<td>' . date("d.m.Y", strtotime($row_date[0])) . '</td>';
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

    for ($i = 0; $i < count($id_resours); $i++) {
        $key = array_search($id_resours[$i], array_column($archive, 'id_param'));
        if ($key !== false) {
            echo "<td>" . number_format($archive[$key]['value'], 2, ',', '') . "</td>";
            if ($id_resours[$i] == 1) {
                $mass_voda[0][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 308) {
                $mass_voda[1][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 310) {
                $mass_voda[2][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 414) {
                $mass_voda[3][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 420) {
                $mass_voda[4][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 436) {
                $mass_voda[5][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 787) {
                $mass_voda[6][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 2) {
                $mass_voda2[0][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 44) {
                $mass_voda2[1][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 377) {
                $mass_voda2[2][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 442) {
                $mass_voda2[3][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 402) {
                $mass_voda2[4][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 408) {
                $mass_voda2[5][] = $archive[$key]['value'];
            } elseif ($id_resours[$i] == 922) {
                $mass_voda2[6][] = $archive[$key]['value'];
            }
        }
        if ($key === false) {
            echo "<td> - </td>";
        }
    }
    echo "</tr>";
}

for ($l = 0; $l < count($mass_voda); $l++) {
    $n1 = count($mass_voda[$l]) - 1;
    $z = 0;
    for ($n = 0; $n < count($mass_voda[$l]); $n++) {

        if ($n == $n1) {
            $z = $z;
            // echo "n=" .$n." mas = ". $mass_voda[$l][$n]."    z=".$z."  <br>" ;
        }
        if ($n >= 0 and $n < $n1) {
            if ($mass_voda[$l][$n])
                $z = $z + $mass_voda[$l][$n + 1] - $mass_voda[$l][$n];
            //echo "n=" .$n." mas = ". $mass_voda[$l][$n]."   mas+1 =  ".$mass_voda[$l][$n+1]. "     z=".$z."  <br>" ;
        }
    }
    $val[$l] = $z;
    //echo "Z ====" . $val[$l] . "  <br>";
}

//print_r($mass_voda2)."<br>";
//echo "count mass_voda = ".count($mass_voda2)."<br>";
for ($l = 0; $l < count($mass_voda2); $l++) {
    $n1 = count($mass_voda2[$l]) - 1;
    $z = 0;
    for ($n = 0; $n < count($mass_voda2[$l]); $n++) {

        if ($n == $n1) {
            $z = $z;
            //echo "n=" .$n." mas = ". $mass_voda2[$l][$n]."    z=".$z."  <br>" ;
        }
        if ($n >= 0 and $n < $n1) {
            if ($mass_voda[$l][$n])
                $z = $z + $mass_voda2[$l][$n + 1] - $mass_voda2[$l][$n];
            //echo "n=" .$n." mas = ". $mass_voda2[$l][$n]."   mas+1 =  ".$mass_voda2[$l][$n+1]. "     z=".$z."  <br>" ;
        }
    }
    $val2[$l] = $z;
    //echo "Z ====".$val2[$l]."  <br>";
}

echo '<tr id = "warning">';
echo '<td colspan=2><b>Итого:</b></td>';
$m = 0;
$h = 0;
for ($i = 0; $i < count($id_resours); $i++) {
    if ($id_resours[$i] == 1 or $id_resours[$i] == 308 or $id_resours[$i] == 310 or $id_resours[$i] == 414 or $id_resours[$i] == 420 or $id_resours[$i] == 436 or $id_resours[$i] == 787) {
        echo "<td><b>" . substr(str_replace('.', ',', $val[$m]), 0, 6) . "</b></td>";
        $summ_xv = $summ_xv + $val[$m];
        $m++;
    } elseif ($id_resours[$i] == 2 or $id_resours[$i] == 44 or $id_resours[$i] == 377 or $id_resours[$i] == 442 or $id_resours[$i] == 402 or $id_resours[$i] == 408 or $id_resours[$i] == 922) {
        echo "<td><b>" . substr(str_replace('.', ',', $val2[$h]), 0, 6) . "</b></td>";
        $summ_gv = $summ_gv + $val2[$h];
        $h++;
    }
}
/*

//var_dump($array_resourse);
echo '<table id="main_table" style="text-align: center;" class="table table-responsive table-bordered">
    <thead  id="thead">
        <tr id = "warning">
            <td rowspan = 3><b>№</b></td>
            <td rowspan = 3><b>Дата</b></td>';
$col = 0;
$row = 1;

for ($i = 0; $i < count($array_res); $i++) {
    if ($array_res[$i]['res_name'] == $array_res[$i + 1]['res_name']) {
        $col++;
    }
    if ($array_res[$i]['res_name'] != $array_res[$i + 1]['res_name']) {
        $col++;
        echo "<td colspan='" . $col . "'><b>" . $array_res[$i]['res_name'] . "</b></td>";
        $col = 0;
        $row = 1;
    }
}
echo "</tr>";
echo "<tr id = 'warning'>";
$col = 0;
for ($i = 0; $i < count($array_res); $i++) {

    echo "<td><b>" . $array_res[$i]['param_name'] . "</b></td>";
}
echo "</tr>";


for ($i = 0; $i < count($array_resourse); $i++) {

    if ($array_resourse[$i]['name_group'] == $array_resourse[$i + 1]['name_group']) {
        $col++;
    }

    if ($array_resourse[$i]['name_group'] != $array_resourse[$i + 1]['name_group']) {
        if ($array_resourse[$i]['name_res_row'] != $array_resourse[$i]['name_gr_row']) {
            if ($array_resourse[$i]['name_res_row'] != $array_resourse[$i]['name_gr_row'] and $array_resourse[$i]['name_gr_row'] == $array_resourse[$i]['name_param_row']) {
                $row++;
            }
            $col++;
            echo "<td colspan='" . $col . "' rowspan='" . $row . "'><b>" . $array_resourse[$i]['name_group'] . "</b></td>";
            //echo $row . " " . $col . " " . $array_resourse[$i]['name_group'] . "<br>";
            $col = 0;
            $row = 1;
        }
    }
}
echo "</tr>";

echo "<tr id = 'warning'>";
$col = 0;
$row = 1;
for ($i = 0; $i < count($array_resourse); $i++) {
    if ($array_resourse[$i]['name_gr_row'] != $array_resourse[$i]['name_param_row']) {
        $col++;
        echo "<td colspan='" . $col . "' rowspan='" . $row . "'><b>" . $array_resourse[$i]['name_param'] . "</b></td>";
        //echo $row . " " . $col . " " . $array_resourse[$i]['name_param'] . "<br>";
        $col = 0;
        $row = 1;
    }
}
echo "</tr>";

echo "<tr id = 'warning'>";
$col = 0;
$row = 1;
for ($i = 0; $i < count($array_resourse); $i++) {

    echo "<td><b>" . $array_resourse[$i]['ed_izmer'] . "</b></td>";
}
echo "</tr>";

echo "</thead><tbody>";




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
    echo '<tr id="hover">';
    $s++;
    echo "<td>" . $s . "</td>";
    echo '<td>' . date("d.m.Y", strtotime($row_date[0])) . '</td>';
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

    $t1 = 0;
    $t2 = 0;
    $t3 = 0;
    $t = 0;
    for ($i = 0; $i < count($array_resourse); $i++) {
        $key = array_search($array_resourse[$i]['id_param'], array_column($archive, 'id_param'));
        if ($key !== false) {
            echo "<td>" . number_format($archive[$key]['value'], 2, ',', '') . "</td>";
            $array_summ[$i] += $archive[$key]['value'];

            if (pg_num_rows($sql_kol_vvod) == 2) {
                if ($array_resourse[$i]['id_param'] == 5) {
                    $t1 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 6) {
                    $t2 = $archive[$key]['value'];
                }
            } else {
                if ($array_resourse[$i]['id_param'] == 5) {
                    $t1 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 6) {
                    $t2 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 12) {
                    $t3 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 13) {
                    $t4 = $archive[$key]['value'];
                }
            }

            if ($array_resourse[$i]['id_param'] == 9 or $array_resourse[$i]['id_param'] == 16) {
                if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
                    $mass_arch[$i][] = $archive[$key]['value'];
                } else {
                    $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                }
            } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
                if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
                    if ($archive[$key]['value'] != "NaN") {
                        $mass_arch[$i][] = $archive[$key]['value'];
                    }
                } else {
                    $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                }
            } elseif ($array_resourse[$i]['id_param'] == 775 or $array_resourse[$i]['id_param'] == 3 or $array_resourse[$i]['id_param'] == 4 or $array_resourse[$i]['id_param'] == 10) {
                $mass_arch[$i][] = $archive[$key]['value'];
            } else {
                $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
            }
        }
        if ($key === false) {
            if ($array_resourse[$i]['id_param'] == 285) {
                    $t = $t1 - $t2;
                    $mass_arch[$i] = $mass_arch[$i] + $t;

                echo "<td> " . number_format($t, 2) . " </td>";
            } if ($array_resourse[$i]['id_param'] == 286) {
                    $t = $t1 - $t2;
                    $mass_arch[$i] = $mass_arch[$i] + $t;
                echo "<td> " . number_format($t, 2) . " </td>";
            }else {
                echo "<td> - </td>";
            }
        }
    }
    echo "</tr>";
}



echo '<tr id = "warning">';
echo '<td colspan=2><b>Среднее:</b></td>';
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

        echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
        if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
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


        echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 5 or $array_resourse[$i]['id_param'] == 6 or $array_resourse[$i]['id_param'] == 12) {
        $temp_s = $mass_arch[$i] / $s;
        echo '<td><b>' . number_format($temp_s, 2, ',', '') . '</b></td>';
        // echo '<td></td>';
    } elseif ($array_resourse[$i]['id_param'] == 775) {
        echo "<td></td>";
    } elseif ($array_resourse[$i]['id_param'] == 3) {
        echo "<td></td>";
    } elseif ($array_resourse[$i]['id_param'] == 4) {
        echo "<td></td>";
    } elseif ($array_resourse[$i]['id_param'] == 10) {
        echo "<td></td>";
    } else {
        $temp_s = $mass_arch[$i] / $s;
        echo '<td><b>' . number_format($temp_s, 2, ',', '') . '</b></td>';
    }
}
echo '</tr>';



echo '<tr id = "warning">';
echo '<td colspan=2><b>Итого:</b></td>';
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
            $teplo = $o;
        } else {
            $teplo = $mass_arch[$i];
        }

        echo '<td><b>' . number_format($teplo, 2, '.', '') . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
        if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
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


        echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 5 or $array_resourse[$i]['id_param'] == 6 or $array_resourse[$i]['id_param'] == 12) {
        $temp_s = $mass_arch[$i] / $s;
        //echo '<td><b>' .number_format($temp_s, 2, '.', ''). '</b></td>';
        echo '<td></td>';
    } elseif ($array_resourse[$i]['id_param'] == 775 or $array_resourse[$i]['id_param'] == 3 or $array_resourse[$i]['id_param'] == 4 or $array_resourse[$i]['id_param'] == 10) {
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

        echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } else {
        $temp_s = $mass_arch[$i];
        echo "<td></td>";
        //echo '<td><b>' . number_format($temp_s, 2, '.', '') . '</b></td>';
    }
}
echo '</tr>';
|*/