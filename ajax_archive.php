<?php

include 'db_config.php';
session_start();
unset($_SESSION['id_object']);
$id_object = $_POST['id_object'];
$_SESSION['id_object'] = $id_object;
$type_arch = $_POST['type_arch'];
//$page = $_POST['page_num'];
//$num=31;
//$start = $page * $num - $num; 

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


if (strtotime(date('Y-m-d')) == strtotime(date('' . $_POST['year'] . '-' . $_POST['month'] . '-01'))) {
    //echo "Первое число месяца<br>";
    $mon = strtotime("-1 month");
    $month = date('m', $mon);
    //echo $month . "<br>";

    $num = cal_days_in_month(CAL_GREGORIAN, $month, $_POST['year']);
    $first_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-01');
    $first_date = date('Y-m-d', strtotime("+1 day", strtotime($first_date)));
    $first_date = date('Y-m-d', strtotime("-1 month", strtotime($first_date)));
    //echo $first_date . "<br>";

    $second_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-' . $num);
    $second_date = date('Y-m-d', strtotime("+1 day", strtotime($second_date)));
    $second_date = date('Y-m-d', strtotime("-1 month", strtotime($second_date)));
    //echo $second_date;

    $date_now = $second_date;

    $sql_date = pg_query('SELECT DISTINCT 
                          ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND
                          "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $first_date . '\' AND 
                          "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $second_date . '\'
                        ORDER BY
                          "Tepl"."Arhiv_cnt"."DateValue"');
} else {
    //echo "Не первое число месяца<br>";
    if ($type_arch == 1) {
        $month = $_POST['month'];
        //echo $month . "<br>";
        $num = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);
        $first_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-01');
        //$first_date = date('Y-m-d', strtotime("+1 day", strtotime($first_date)));

        $second_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-' . $num);
        $second_date = date('Y-m-d', strtotime("+1 day", strtotime($second_date)));

        $date_now = $second_date;
    } else {
        $month = $_POST['month'];
        //echo $month . "<br>";
        $num = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);
        $first_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-01');
        $first_date = date('Y-m-d', strtotime("+1 day", strtotime($first_date)));

        $second_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-' . $num);
        $second_date = date('Y-m-d', strtotime("+1 day", strtotime($second_date)));

        $date_now = $second_date;
    }


    $sql_date = pg_query('SELECT DISTINCT 
                          ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND
                          "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $first_date . '\' AND 
                          "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $second_date . '\'
                        ORDER BY
                          "Tepl"."Arhiv_cnt"."DateValue"');
}



$sql_korrect = pg_query('SELECT DISTINCT 
            public.korrect.old_value,
            public.korrect.new_value,
            public.korrect.date_record,
            "Tepl"."ParamResPlc_cnt"."ParamRes_id"
          FROM
            "Tepl"."ParamResPlc_cnt"
            INNER JOIN public.korrect ON ("Tepl"."ParamResPlc_cnt".prp_id = public.korrect.prp_id)
          WHERE
            public.korrect.plc_id = ' . $id_object . ' AND 
            public.korrect.date_time >= \'' . $first_date . '\' AND 
            public.korrect.date_time <= \'' . $second_date . '\'');
$korrec_arr[] = array();
while ($result = pg_fetch_row($sql_korrect)) {
    $korrec_arr[] = array(
        'date' => $result[2],
        'id_res' => $result[3]
    );
}


//Запрос на вывод параметров на объекте
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
                          "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . '
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
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . '  AND 
  "Tepl"."Sensor_Property".id_type_property = 0
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
$korrections = 0;
$s = 0;
$mass_voda = '';
while ($row_date = pg_fetch_row($sql_date)) {
    echo '<tr id="hover">';
    $s++;
    echo "<td>" . $s . "</td>";

    //echo "дата=  ".$row_date[0] ."  ресурс ";



    if ($type_arch == 1) {
        $date_b = date("d.m.Y H:i", strtotime($row_date[0]));
        echo '<td>' . $date_b . '</td>';
    } elseif ($type_arch == 2) {
        $date_b = date("d.m.Y", strtotime('-1 day', strtotime($row_date[0])));
        echo '<td>' . $date_b . '</td>';
    }

    if (count($korrec_arr) > 0) {
        $flag_korrect = array_search(date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))), array_column($korrec_arr, 'date'));
//        echo $flag_korrect;
    } else {
        $flag_korrect = false;
    }

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
                                  "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
                                  "Tepl"."Arhiv_cnt"."DateValue" = \'' . $row_date[0] . '\'
                                ORDER BY
                                  "Tepl"."Arhiv_cnt"."DateValue",
                                  "Tepl"."Resourse_cnt"."Name",
                                  "Tepl"."ParamResPlc_cnt"."NameGroup",
                                  "Tepl"."ParametrResourse"."Name"
                                ');

    $kol = pg_num_rows($sql_archive);


    while ($resul = pg_fetch_row($sql_archive)) {
        $arr_arch[] = array(
            'res_id' => $resul[1],
            'value' => $resul[0]
        );
    }


    for ($i = 0; $i < count($id_resours); $i++) {
        $key = array_search($id_resours[$i], array_column($arr_arch, 'res_id'));
        if ($key !== false) {

            if ($flag_korrect !== false) {
                $korrections++;
                $key_kor_id = array_search($id_resours[$i], array_column($korrec_arr, 'id_res'));

                if ($arr_arch[$key]['value'] != 'NaN') {
                    if ($key_kor_id !== false) {
                        echo "<td class='warning'>" . number_format($arr_arch[$key]['value'], 3, ",", "") . "</td>";
                        $a = $arr_arch[$key]['value'];
                    } else {
                        echo "<td>" . number_format($arr_arch[$key]['value'], 3, ",", "") . "</td>";
                        $a = $arr_arch[$key]['value'];
                    }
                } else {
                    echo "<td>NaN</td>";
                    $a = 0;
                }
            } else {
                if ($arr_arch[$key]['value'] != 'NaN') {
                    echo "<td>" . number_format($arr_arch[$key]['value'], 3, ",", "") . "</td>";
                    $a = $arr_arch[$key]['value'];
                } else {
                    echo "<td>NaN</td>";
                    $a = 0;
                }
            }

            //Для подсчета коррекций показаний использовать массив вида $mass_voda=array{date, value}; вместо старого $mass_voda[0][]

            if ($id_resours[$i] == 1) {
                //$mass_voda[0][] = $a;
                $mass_voda[0][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 308) {
//                $mass_voda[1][] = $a;
                $mass_voda[1][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 310) {
//                $mass_voda[2][] = $a;
                $mass_voda[2][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 414) {
//                $mass_voda[3][] = $a;
                $mass_voda[3][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 420) {
//                $mass_voda[4][] = $a;
                $mass_voda[4][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 436) {
//                $mass_voda[5][] = $a;
                $mass_voda[5][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 787) {
//                $mass_voda[6][] = $a;
                $mass_voda[6][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 2) {
                $mass_voda2[0][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 44) {
                $mass_voda2[1][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 377) {
                $mass_voda2[2][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 442) {
                $mass_voda2[3][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 402) {
                $mass_voda2[4][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 408) {
                $mass_voda2[5][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 922) {
                $mass_voda2[6][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } else {
                //or $id_object == 316 or $id_object == 314 or $id_object == 251     or $id_object == 318
                if ($row_device[0] == 214) {
                    if ($id_resours[$i] == 9 or $id_resours[$i] == 16) {
                        $mass_arch[$i][] = $a;
                    } elseif ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {
                        $mass_arch[$i][] = $a;
                    } else {
                        $mass_arch[$i] = $mass_arch[$i] + $a;
                    }
                } else {
                    if ($id_object == 316 or $id_object == 314 or $id_object == 251 or $id_object == 318) {
                        if ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {
                            $mass_arch[$i][] = $a;
                        } elseif ($id_resours[$i] == 9 or $id_resours[$i] == 16) {
                            $mass_arch[$i] = $mass_arch[$i] + $a;
                        } else {
                            $mass_arch[$i] = $mass_arch[$i] + $a;
                        }
                    } else {
                        $mass_arch[$i] = $mass_arch[$i] + $a;
                    }
                }
            }
        } else {
            if ($id_resours[$i] == 1) {
                //$mass_voda[0][] = $a;
                $mass_voda[0][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 308) {
//                $mass_voda[1][] = $a;
                $mass_voda[1][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 310) {
//                $mass_voda[2][] = $a;
                $mass_voda[2][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 414) {
//                $mass_voda[3][] = $a;
                $mass_voda[3][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 420) {
//                $mass_voda[4][] = $a;
                $mass_voda[4][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 436) {
//                $mass_voda[5][] = $a;
                $mass_voda[5][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 787) {
//                $mass_voda[6][] = $a;
                $mass_voda[6][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 2) {
                $mass_voda2[0][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 44) {
                $mass_voda2[1][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 377) {
                $mass_voda2[2][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 442) {
                $mass_voda2[3][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 402) {
                $mass_voda2[4][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 408) {
                $mass_voda2[5][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } elseif ($id_resours[$i] == 922) {
                $mass_voda2[6][] = array(
                    'date' => date("Y-m-d H:i:00", strtotime('-1 day', strtotime($row_date[0]))),
                    'value' => $a,
                    'res_id' => $id_resours[$i]
                );
            } else {
                //or $id_object == 316 or $id_object == 314 or $id_object == 251     or $id_object == 318
                if ($row_device[0] == 214) {
                    if ($id_resours[$i] == 9 or $id_resours[$i] == 16) {
                        $mass_arch[$i][] = 0;
                    } elseif ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {
                        $mass_arch[$i][] = 0;
                    } else {
                        $mass_arch[$i] = $mass_arch[$i] + 0;
                    }
                } else {
                    if ($id_object == 316 or $id_object == 314 or $id_object == 251 or $id_object == 318) {
                        if ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {
                            $mass_arch[$i][] = 0;
                        } elseif ($id_resours[$i] == 9 or $id_resours[$i] == 16) {
                            $mass_arch[$i] = $mass_arch[$i] + 0;
                        } else {
                            $mass_arch[$i] = $mass_arch[$i] + 0;
                        }
                    } else {
                        $mass_arch[$i] = $mass_arch[$i] + 0;
                    }
                }
            }
            echo "<td>—</td>";
        }
    }



    echo '</tr>';
    unset($arr_arch);
    unset($key);
}

//print_r($arrayryb);
//print_r($mass_voda)."<br>";
//echo "count mass_voda = ".count($mass_voda)."<br>";

include '/include/func_voda.php';
if ($korrections != 0) {
    $val = summ_voda_korrect($mass_voda, $id_object, $first_date, $second_date);
//    echo "<h1>ababab</h1>";
//    echo print_r($val);
    $val2 = summ_voda_korrect($mass_voda2, $id_object, $first_date, $second_date);
} else {
    $val = summ_voda($mass_voda);
    $val2 = summ_voda($mass_voda2);
}


echo '<tr id = "warning">';
echo '<td colspan=2><b>Итого:</b></td>';
$m = 0;
$h = 0;
for ($i = 0; $i < count($id_resours); $i++) {
    //echo $id_resours[$i]."<br>"; or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318
    if ($id_resours[$i] == 1 or $id_resours[$i] == 308 or $id_resours[$i] == 310 or $id_resours[$i] == 414 or $id_resours[$i] == 420 or $id_resours[$i] == 436 or $id_resours[$i] == 787) {
        echo "<td><b>" . substr(str_replace('.', ',', $val[$m]), 0, 6) . "</b></td>";
        $summ_xv = $summ_xv + $val[$m];
        $m++;
    } elseif ($id_resours[$i] == 2 or $id_resours[$i] == 44 or $id_resours[$i] == 377 or $id_resours[$i] == 442 or $id_resours[$i] == 402 or $id_resours[$i] == 408 or $id_resours[$i] == 922) {
        echo "<td><b>" . substr(str_replace('.', ',', $val2[$h]), 0, 6) . "</b></td>";
        $summ_gv = $summ_gv + $val2[$h];
        $h++;
    } elseif ($id_resours[$i] == 9 or $id_resours[$i] == 16) {
        if ($row_device[0] == 214) {
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

        $limit = substr(str_replace('.', ',', $teplo), 0, 6);
        echo '<td><b>' . $limit . '</td>';
    } elseif ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {
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

        $limit = substr(str_replace('.', ',', $teplo), 0, 6);
        // суммарное показание массы заменено 14.10.2016 echo '<td><b>' . $limit . '</td>';
        echo '<td><b>—</td>';
    } elseif ($id_resours[$i] == 5 or $id_resours[$i] == 6 or $id_resours[$i] == 12) {
        $temp_s = $mass_arch[$i] / $s;
        //echo '<td><b>' . substr(str_replace('.', ',', $temp_s), 0, 4) . '</b></td>';
        echo '<td><b> — </b></td>';
    } else {
        echo '<td><b> — </b></td>';
    }
}
echo '</tr>';
$xv = 0;
$gv = 0;
$t = 0;
for ($c = 0; $c < count($arr_name); $c++) {
    if ($arr_name[$c] == "ГВС" and $par[$c] > 1 or $arr_name[$c] == "ХВС" and $par[$c] > 1) {
        $t++;
        if ($t == 1) {
            echo ' <tr id = "warning"><td  colspan =2><b>Всего:</b></td>';
        }
    }
}
if ($t > 0) {
    for ($c = 0; $c < count($arr_name); $c++) {
        if ($arr_name[$c] == "Тепло") {
            echo '<td colspan =' . $par[$c] . '>—</td>';
            $tep = $par[$c];
        }

        if ($arr_name[$c] == "ГВС") {
            echo '<td colspan = ' . $par[$c] . '><b>' . $summ_gv . '</b></td>';
            $gv = $par[$c];
        }
        if ($arr_name[$c] == "ХВС") {
            echo '<td colspan = ' . $par[$c] . '><b>' . $summ_xv . '</b></td>';
            $xv = $par[$c];
        }
    }
}
echo '<tr id = "warning">';
echo '<td colspan=2><b>Лимит:</b></td>';


$sql_limit_group = pg_query('SELECT 
                        public.group_limit.group_name,
                        public.group_limit.group_id,
                        public."LimitPlaces_cnt".teplo,
                        public."LimitPlaces_cnt".voda,
                        public.group_limit.group_adres
                      FROM
                        public.group_limit
                        INNER JOIN public."LimitPlaces_cnt" ON (public.group_limit.group_id = public."LimitPlaces_cnt".plc_id)
                        INNER JOIN public.group_plc ON (public.group_limit.group_id = public.group_plc.group_id)
                      WHERE
                        public.group_plc.plc_id =' . $id_object);

if (pg_num_rows($sql_limit_group) != 0) {
    $sql_all_limit = pg_query('SELECT DISTINCT 
            public."LimitPlaces_cnt".plc_id,
            public."LimitPlaces_cnt".teplo,
            public."LimitPlaces_cnt".voda
          FROM
            public."LimitPlaces_cnt"
          WHERE
            public."LimitPlaces_cnt".plc_id=' . pg_fetch_result($sql_limit_group, 0, 1) . '');
} else {
    $sql_all_limit = pg_query('SELECT DISTINCT 
            public."LimitPlaces_cnt".plc_id,
            public."LimitPlaces_cnt".teplo,
            public."LimitPlaces_cnt".voda
          FROM
            public."LimitPlaces_cnt"
          WHERE
            public."LimitPlaces_cnt".plc_id=' . $id_object . '');
}

while ($result = pg_fetch_row($sql_all_limit)) {
    $arr_all_limit = array(
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
$month_name = pg_fetch_result($sql_limit_part, 0, 2);

$limit_teplo = number_format(($arr_all_limit['teplo'] / 100) * $limit_teplo_part, 2, ',', '');
$limit_voda = number_format((($arr_all_limit['voda'] / 100) * $limit_voda_part), 2, ',', '');
for ($i = 0; $i < count($id_resours); $i++) {

    if ($id_resours[$i] == 308 or $id_resours[$i] == 310 or $id_resours[$i] == 414
            or $id_resours[$i] == 420 or $id_resours[$i] == 436 or $id_resours[$i] == 787
            or $id_resours[$i] == 44 or $id_resours[$i] == 377 or $id_resours[$i] == 442
            or $id_resours[$i] == 402 or $id_resours[$i] == 408 or $id_resours[$i] == 922) {
        $y++;
    } elseif ($id_resours[$i] == 9) {
        if ($limit_teplo == '') {
            echo '<td><b> — </b></td>';
        } else {
            echo '<td><b>' . $limit_teplo . '</b></td>';
        }
    } elseif ($id_resours[$i] == 1) {
        if ($xv == 0) {
            if ($limit_voda == '') {
                echo '<td><b> — </b></td>';
            } else {
                echo '<td><b>' . $limit_voda . '</b></td>';
            }
        } else {
            if ($limit_voda == '') {
                echo '<td colspan=' . $xv . '><b> — </b></td>';
            } else {
                echo '<td colspan=' . $xv . '><b>' . $limit_voda . '</b></td>';
            }
        }
    } elseif ($id_resours[$i] == 2) {
        if ($gv == 0) {
            if ($limit_teplo == '') {
                echo '<td><b> — </b></td>';
            } else {
                echo '<td><b>' . $limit_teplo . '</b></td>';
            }
        } else {
            if ($limit_voda == '') {
                echo '<td colspan=' . $gv . '><b> — </b></td>';
            } else {
                echo '<td colspan=' . $gv . '><b>' . $limit_voda . '</b></td>';
            }
        }
    } else {
        echo '<td><b> — </b></td>';
    }
}

//echo $result_limit[0];
echo '</tr>';

echo '</tabel>';
?>