<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



session_start();
include '../../db_config.php';

$y = date('Y', strtotime($_POST['date']));
$m = date('m', strtotime($_POST['date']));
$num = cal_days_in_month(CAL_GREGORIAN, $m, $y);

$d1 = date('Y-m-01', strtotime($_POST['date']));
$d2 = date('Y-m-' . $num, strtotime($_POST['date']));

$sql_inc = pg_query('SELECT 
  public.fault_inc.date_time,
  public.fault_inc.view_stat,
  public.fault_inc.comments,
  public.fault_cnt.name,
  public.fault_cnt.id,
  public.fault_cnt.type_arch
FROM
  public.fault_cnt
  INNER JOIN public.fault_inc ON (public.fault_cnt.id = public.fault_inc.numb)
WHERE
  public.fault_inc.id = ' . $_POST['id']);

$inc = pg_fetch_all($sql_inc);

echo "<br>";

$sql_date = pg_query('SELECT DISTINCT 
                          ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = ' . $_POST['plc'] . ' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = ' . $inc[0][type_arch] . '  AND
                          "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $d1 . '\' AND 
                          "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $d2 . '\'
                        ORDER BY
                          "Tepl"."Arhiv_cnt"."DateValue"');
$d = pg_fetch_all($sql_date);

$sql_resurse = pg_query('SELECT DISTINCT 
                          ("Tepl"."ParametrResourse"."Name") AS "FIELD_1",
                          "Tepl"."ParamResPlc_cnt"."NameGroup",
                          "Tepl"."Resourse_cnt"."Name",
                          "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                        FROM
                          "Tepl"."ParametrResourse"
                          INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
                          INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                        WHERE
                          "Tepl"."ParamResPlc_cnt".plc_id = ' . $_POST['plc'] . '
                        ORDER BY
                          "Tepl"."Resourse_cnt"."Name",
                          "Tepl"."ParamResPlc_cnt"."NameGroup"');


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

while ($row_date = pg_fetch_row($sql_date)) {
    echo '<tr id="hover">';
    $s++;
    echo "<td>" . $s . "</td>";

//echo "дата=  ".$row_date[0] ."  ресурс ";



    if ($inc[0][type_arch] == 1) {
        $date_b = date("d.m.Y H:i", strtotime($row_date[0]));
        echo '<td>' . $date_b . '</td>';
    } elseif ($inc[0][type_arch] == 2) {
        $date_b = date("d.m.Y", strtotime('-1 day', strtotime($row_date[0])));
        echo '<td>' . $date_b . '</td>';
    }

    $sql_archive = pg_query('SELECT 
                                  "Tepl"."Arhiv_cnt"."DataValue",
                                  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                  "Tepl"."Arhiv_cnt"."DataValue"
                                FROM
                                  "Tepl"."ParametrResourse"
                                  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
                                  INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                                WHERE
                                  "Tepl"."ParamResPlc_cnt".plc_id = ' . $_POST['plc'] . ' AND 
                                  "Tepl"."Arhiv_cnt".typ_arh = ' . $inc[0][type_arch] . ' AND 
                                  "Tepl"."Arhiv_cnt"."DateValue" = \'' . $row_date[0] . '\'
                                ORDER BY
                                  "Tepl"."Arhiv_cnt"."DateValue",
                                  "Tepl"."Resourse_cnt"."Name",
                                  "Tepl"."ParamResPlc_cnt"."NameGroup",
                                  "Tepl"."ParametrResourse"."Name"
                                ');

    $kol = pg_num_rows($sql_archive);
    unset($arr_arch);

    while ($resul = pg_fetch_row($sql_archive)) {
        $arr_arch[] = array(
            'res_id' => $resul[1],
            'value' => $resul[0]
        );
    }


    for ($i = 0; $i < count($id_resours); $i++) {
        $key = array_search($id_resours[$i], array_column($arr_arch, 'res_id'));
        switch ($inc[0][id]) {
            case 0: $p = [19, 20, 21];
                break;
            case 1: $p = [5, 6];
                break;
            case 2: $p = [5, 6];
                break;
            case 3: $p = [19, 5, 20, 6, 775];
                break;
        }
        if ($key !== false) {
            if (strtotime($row_date[0]) == strtotime($_POST['date'])) {
                $pkey = array_search($arr_arch[$key]['res_id'], $p);
                if ($pkey !== false) {
                    if ($arr_arch[$key]['value'] != 'NaN') {
                        echo "<td class='danger'>" . number_format($arr_arch[$key]['value'], 3, ",", "") . "</td>";
                    } else {
                        echo "<td class='danger'>NaN</td>";
                    }
                } else {

                    if ($arr_arch[$key]['value'] != 'NaN') {
                        echo "<td>" . number_format($arr_arch[$key]['value'], 3, ",", "") . "</td>";
                    } else {
                        echo "<td>NaN</td>";
                    }
                }
            } else {
                if ($arr_arch[$key]['value'] != 'NaN') {
                    echo "<td>" . number_format($arr_arch[$key]['value'], 3, ",", "") . "</td>";
                } else {
                    echo "<td>NaN</td>";
                }
            }
        } else {
            echo "<td>—</td>";
        }
    }
}    