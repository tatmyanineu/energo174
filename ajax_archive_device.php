<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//запрос на выборку дат поверок и т.п. для расходомеров
include 'db_config.php';
$date = date('Y-m-d');
session_start();
$id_object = $_POST['id_object'];

$name_row = array("name", "d", "gmin", "gmax");
if (($handle = fopen("ajax/sens.csv", "r")) !== FALSE) {
    # Set the parent multidimensional array key to 0.
    $nn = 0;
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        # Count the total keys in the row.
        $c = count($data);
        # Populate the multidimensional array.
        for ($x = 0; $x < $c; $x++) {
            $csvarray[$nn][$name_row[$x]] = $data[$x];
        }
        $nn++;
    }
    # Close the File.
    fclose($handle);
}

//запрос для расходомеров на тепло

$sql_sens_warm = pg_query('SELECT 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."TypeSensor"."Name",
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."ParamResPlc_cnt"."Comment",
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Resourse_cnt"."Name"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Sensor_cnt".prp_id)
  INNER JOIN "Tepl"."TypeSensor" ON ("Tepl"."Sensor_cnt".sen_id = "Tepl"."TypeSensor".sen_id)
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
WHERE
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" != 9 AND 
  "Tepl"."ParamResPlc_cnt".plc_id =  ' . $id_object . ' AND
   "Tepl"."Resourse_cnt"."Name" = \'Тепло\'
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."ParametrResourse"."Name"');
while ($res_sens_warm = pg_fetch_row($sql_sens_warm)) {
    $sens_warm[] = array(
        'sens_param_id' => $res_sens_warm[0],
        'sens_name' => $res_sens_warm[1],
        'sens_param_name' => $res_sens_warm[2],
        'sens_comment' => $res_sens_warm[3],
        'sens_id' => $res_sens_warm[4],
    );
}
//конец запроса для расходомеров на тепло
//запрос для расходомеров на воду

$sql_sens_water = pg_query('SELECT DISTINCT 
  ("Tepl"."ParamResPlc_cnt"."ParamRes_id") AS "FIELD_1",
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."Device_cnt"."Numbe",
  "Tepl"."TypeSensor"."Name",
  "Tepl"."Sensor_cnt"."Comment",
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."ParamResPlc_cnt"."Comment"
FROM
  "Tepl"."ParamResPlc_cnt"
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."PointRead" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."PointRead".prp_id)
  INNER JOIN "Tepl"."Device_cnt" ON ("Tepl"."PointRead".dev_id = "Tepl"."Device_cnt".dev_id)
  INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."PointRead".prp_id = "Tepl"."Sensor_cnt".prp_id)
  INNER JOIN "Tepl"."TypeSensor" ON ("Tepl"."Sensor_cnt".sen_id = "Tepl"."TypeSensor".sen_id)
  INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
WHERE
  "Tepl"."Places_cnt".plc_id =  ' . $id_object . ' AND 
  "Tepl"."Resourse_cnt"."Name" = \'ХВС\'  OR 
  "Tepl"."Places_cnt".plc_id =  ' . $id_object . ' AND 
  "Tepl"."Resourse_cnt"."Name" = \'ГВС\'
ORDER BY
  "Tepl"."ParametrResourse"."Name"');


while ($res_sens_water = pg_fetch_row($sql_sens_water)) {
    $sens_water[] = array(
        'sens_param_id' => $res_sens_water[0],
        'sens_name' => $res_sens_water[4],
        'sens_param_name' => $res_sens_water[1],
        'sens_comment' => $res_sens_water[7],
        'sens_id' => $res_sens_water[6],
        'num_dev' => $res_sens_water[3],
        'place' => $res_sens_water[5],
        'name_res' => $res_sens_water[2]
    );
}

//конец запроса для расходомеров на воду
//запрос на выборку тепловычислителя
$sql_dev_warm = pg_query('SELECT 
  "Places_cnt1".plc_id,
  "Tepl"."TypeDevices"."Name",
  "Tepl"."Device_cnt".dev_typ_id,
  "Tepl"."Device_cnt"."Numbe",
  "Tepl"."Device_cnt"."Comment"
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
  INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
WHERE
  "Places_cnt1".plc_id = ' . $id_object . ' AND 
  "Tepl"."TypeDevices"."Name" NOT LIKE \'%Пульс%\'
ORDER BY
  "Tepl"."TypeDevices"."Name"');


while ($res_dev_warm = pg_fetch_row($sql_dev_warm)) {
    $dev_warm[] = array(
        'dev_plc_id' => $res_dev_warm[0],
        'dev_name' => $res_dev_warm[1],
        'dev_id' => $res_dev_warm[2],
        'dev_number' => $res_dev_warm[3],
        'dev_comment' => $res_dev_warm[4]
    );
}
//var_dump($dev_warm);
//конец запроса на выборку тепловычислителя
//запрос на выборку регистратора

$sql_dev_water = pg_query('SELECT 
  "Places_cnt1".plc_id,
  "Tepl"."TypeDevices"."Name",
  "Tepl"."Device_cnt".dev_typ_id,
  "Tepl"."Device_cnt"."Numbe",
  "Tepl"."Device_cnt"."Comment"
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
  INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
WHERE
  "Places_cnt1".plc_id = ' . $id_object . ' AND 
  "Tepl"."TypeDevices"."Name" LIKE \'%Пульс%\'
ORDER BY
  "Tepl"."TypeDevices"."Name"');

while ($res_dev_water = pg_fetch_row($sql_dev_water)) {
    $dev_water[] = array(
        'dev_plc_id' => $res_dev_water[0],
        'dev_name' => $res_dev_water[1],
        'dev_id' => $res_dev_water[2],
        'dev_number' => $res_dev_water[3],
        'dev_comment' => $res_dev_water[4]
    );
}

$sql_port=  pg_query('SELECT 
  "Tepl"."ProperConnect_cnt"."ValueProp",
  "Tepl"."TypePropertyConnect"."Name",
  "Tepl"."Connect_cnt".plc_id
FROM
  "Tepl"."Connect_cnt"
  INNER JOIN "Tepl"."Connect_cnt_Config" ON ("Tepl"."Connect_cnt".con_id = "Tepl"."Connect_cnt_Config".con_id)
  INNER JOIN "Tepl"."ProperConnect_cnt" ON ("Tepl"."Connect_cnt_Config"."Conf_id" = "Tepl"."ProperConnect_cnt"."Conf_id")
  INNER JOIN "Tepl"."TypePropertyConnect" ON ("Tepl"."ProperConnect_cnt".typ_id = "Tepl"."TypePropertyConnect".typ_id)
WHERE
  "Tepl"."ProperConnect_cnt".typ_id = 6 AND 
  "Tepl"."Connect_cnt".plc_id = ' . $id_object . '');


//конец запроса на выборку регистратора
if ($_SESSION['privelege'] > 1) {
    echo '<div class="col-lg-1 col-md-1 col-xs-6"><h4>Порт:</h4></div>'
    . '<div class="col-lg-2 col-md-2 col-xs-6"><h4>'.pg_fetch_result($sql_port, 0, 0).'</h4></div>';
}

if (pg_num_rows($sql_dev_warm) != 0 or pg_num_rows($sql_sens_warm) != 0) {
    if ($_SESSION['privelege'] > 1) {
        echo '<div class="text-right"><button id="edit_teplo" class="btn btn-default btn-primary"><span  class="glyphicon glyphicon-pencil"></span>  <b>Редактировать узел</b></button></div>';
    }
    echo '<h2>Приборы учета тепловой энергии</h2>';
    echo '<table class="table table-responsive table-bordered" >'
    . '<thead id="thead">'
    . '<tr id = "warning">'
    . '<td><b>Тип прибора</b></td>'
    . '<td><b>Наименование</b></td>'
    . '<td><b>Зав. №</b></td>'
    . '<td><b>Дата ближайшей поверки</b></td>'
    . '<td><b>Диаметр (мм)</b></td>'
    . '<td><b>Gmin (м3/ч)</b></td>'
    . '<td><b>Gmax (м3/ч)</b></td>'
    . '</tr>'
    . '</thead>'
    . '<tbody>';
    if (pg_num_rows($sql_dev_warm) != 0) {
        for ($i = 0; $i < count($dev_warm); $i++) {
            echo '<tr>'
            . '<td>Тепловычислитель</td>'
            . '<td>' . $dev_warm[$i]['dev_name'] . ': '.$dev_warm[$i]['dev_number'].'</td>';

            $sql_dev_prop = pg_query('SELECT
                    "Tepl"."Device_Property"."Propert_Value"
                    FROM
                    "Tepl"."Places_cnt" "Places_cnt1"
                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                    INNER JOIN "Tepl"."Device_Property" ON ("Tepl"."Device_cnt".dev_id = "Tepl"."Device_Property".dev_id)
                    WHERE
                    "Places_cnt1".plc_id = ' . $id_object . ' AND
                    "Tepl"."Device_Property".id_type_property = 0 AND
                    "Tepl"."Device_cnt".dev_typ_id = ' . $dev_warm[$i]['dev_id'] . '
                    ORDER BY
                    "Tepl"."TypeDevices"."Name",
                    "Tepl"."Device_Property".id_type_property');
            if (pg_num_rows($sql_dev_prop) != 0) {
                echo "<td>" . pg_fetch_result($sql_dev_prop, 0, 0) . "</td>";
            } else {
                echo "<td></td>";
            }

            $sql_dev_prop = pg_query('SELECT
                    "Tepl"."Device_Property"."Propert_Value",
                    "Tepl"."Device_cnt"."Numbe"
                    FROM
                    "Tepl"."Places_cnt" "Places_cnt1"
                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                    INNER JOIN "Tepl"."Device_Property" ON ("Tepl"."Device_cnt".dev_id = "Tepl"."Device_Property".dev_id)
                    WHERE
                    "Places_cnt1".plc_id = ' . $id_object . ' AND
                    "Tepl"."Device_Property".id_type_property = 2 AND
                    "Tepl"."Device_cnt".dev_typ_id = ' . $dev_warm[$i]['dev_id'] . '
                    ORDER BY
                    "Tepl"."TypeDevices"."Name",
                    "Tepl"."Device_Property".id_type_property');
            if (pg_num_rows($sql_dev_prop) != 0) {
                echo "<td>" . date("d.m.Y", strtotime(pg_fetch_result($sql_dev_prop, 0, 0))) . "</td>";
            } else {
                echo "<td></td>";
            }
            echo '<td colspan=3>' . $dev_warm[$i]['dev_comment'] . '</td></tr>';
        }
    }
    if (pg_num_rows($sql_sens_warm) != 0) {
        for ($i = 0; $i < count($sens_warm); $i++) {

            echo '<tr>'
            . '<td>Расходомер</td>'
            . '<td>' . $sens_warm[$i]['sens_name'] . '</td>';

            $sql_sens_prop = pg_query('SELECT
                    "Tepl"."Sensor_Property"."Propert_Value"
                    FROM
                    "Tepl"."Sensor_Property"
                    INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."Sensor_Property".s_id = "Tepl"."Sensor_cnt".s_id)
                    WHERE
                    "Tepl"."Sensor_cnt".prp_id = ' . $sens_warm[$i]['sens_id'] . ' AND
                    "Tepl"."Sensor_Property".id_type_property = 0
                    ORDER BY
                    "Tepl"."Sensor_Property".id_type_property');

            if (pg_num_rows($sql_sens_prop) != 0) {
                echo "<td>" . pg_fetch_result($sql_sens_prop, 0, 0) . "</td>";
            } else {
                echo "<td></td>";
            }
            //дата поверки расходомера
            $sql_sens_prop = pg_query('SELECT
                    "Tepl"."Sensor_Property"."Propert_Value"
                    FROM
                    "Tepl"."Sensor_Property"
                    INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."Sensor_Property".s_id = "Tepl"."Sensor_cnt".s_id)
                    WHERE
                    "Tepl"."Sensor_cnt".prp_id = ' . $sens_warm[$i]['sens_id'] . ' AND
                    "Tepl"."Sensor_Property".id_type_property = 2
                    ORDER BY
                    "Tepl"."Sensor_Property".id_type_property');
            //дата поверки расходомера
            if (pg_num_rows($sql_sens_prop) != 0) {
                echo "<td>" . date("d.m.Y", strtotime(pg_fetch_result($sql_sens_prop, 0, 0))) . "</td>";
            } else {
                echo "<td></td>";
            }


            $massiv = explode(";", $sens_warm[$i]['sens_comment']);
            //print_r($massiv);
            echo "<td> " . $massiv[0] . "</td>";


            for ($s = 0; $s < count($csvarray); $s++) {
                $diametr = $massiv[0];
                //          $sheet->setCellValueByColumnAndRow(4, $colum_text, "" . $massiv[0] . "");
                if ($sens_warm[$i]['sens_name'] == $csvarray[$s][name] and $csvarray[$s][d] == $diametr) {
                    //$sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . " ".$csvarray[$s][gmin]. " ".$csvarray[$s][gmax]. "");
                    $gmin = $csvarray[$s][gmin];
                    $gmax = $csvarray[$s][gmax];
                }
            }
            echo "<td> " . $gmin . "</td>";
            echo "<td> " . $gmax . "</td>";
            echo '</tr>';
        }
    }
    echo "</tbody></table>";
}


if (pg_num_rows($sql_dev_water) != 0 or pg_num_rows($sql_sens_water) != 0) {

    if ($_SESSION['privelege'] > 1) {
        echo '<br><br><div class="text-right"><button id="edit_voda" class="btn btn-default btn-primary"><span  class="glyphicon glyphicon-pencil"></span>  <b>Редактировать узел</b></button></div>';
    }
    echo '<h2>Приборы учета водоснабжения</h2>';
    echo '<table class="table table-responsive table-bordered" >'
    . '<thead id="thead">'
    . '<tr id = "warning">'
    . '<td><b>Тип прибора</b></td>'
    . '<td><b>Наименование</b></td>'
    . '<td><b>Зав. №</b></td>'
    . '<td><b>Ресурс</b></td>'
    . '<td><b>Местоположение</b></td>'
    . '<td><b>Дата ближайшей поверки</b></td>'
    . '<td><b>Диаметр (мм)</b></td>'
    . '</tr>'
    . '</thead>'
    . '<tbody>';
    if (pg_num_rows($sql_dev_water) != 0) {
        for ($i = 0; $i < count($dev_water); $i++) {
            echo '<tr>'
            . '<td>Счетчик-регистратор</td>'
            . '<td>' . $dev_water[$i]['dev_name'] . '</td>'
            . '<td>' . $dev_water[$i]['dev_number'] . '</td>'
            . '<td></td>';



            $sql_dev_prop = pg_query('SELECT
                    "Tepl"."Device_Property"."Propert_Value"
                    FROM
                    "Tepl"."Places_cnt" "Places_cnt1"
                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                    INNER JOIN "Tepl"."Device_Property" ON ("Tepl"."Device_cnt".dev_id = "Tepl"."Device_Property".dev_id)
                    WHERE
                    "Places_cnt1".plc_id = ' . $id_object . ' AND
                    "Tepl"."Device_Property".id_type_property = 2 AND
                    "Tepl"."Device_cnt".dev_typ_id = ' . $dev_water[$i]['dev_id'] . '
                    ORDER BY
                    "Tepl"."TypeDevices"."Name",
                    "Tepl"."Device_Property".id_type_property');
            if (pg_num_rows($sql_dev_prop) != 0) {
                if (pg_fetch_result($sql_sens_prop, 0, 0) != '01/01/0001 00:00:00') {
                    echo "<td>" . date("d.m.Y", strtotime(pg_fetch_result($sql_dev_prop, 0, 0))) . "</td>";
                } else {
                    echo "<td> - </td>";
                }
            } else {
                echo "<td></td>";
            }

            echo '<td></td>'
            . '<td>' . $dev_water[$i]['dev_comment'] . '</td></tr>';
            for ($j = 0; $j < count($sens_water); $j++) {
                if ($dev_water[$i][dev_number] == $sens_water[$j][num_dev]) {
                    echo '<tr>'
                    . '<td>Расходомер</td>'
                    . '<td>' . $sens_water[$j]['sens_name'] . ' (Рег.: '.$dev_water[$i]['dev_number'].')</td>';

                    $sql_sens_prop = pg_query('SELECT
                    "Tepl"."Sensor_Property"."Propert_Value"
                    FROM
                    "Tepl"."Sensor_Property"
                    INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."Sensor_Property".s_id = "Tepl"."Sensor_cnt".s_id)
                    WHERE
                    "Tepl"."Sensor_cnt".prp_id = ' . $sens_water[$j]['sens_id'] . ' AND
                    "Tepl"."Sensor_Property".id_type_property = 0
                    ORDER BY
                    "Tepl"."Sensor_Property".id_type_property');

                    if (pg_num_rows($sql_sens_prop) != 0) {
                        echo "<td>" . pg_fetch_result($sql_sens_prop, 0, 0) . "</td>";
                    } else {
                        echo "<td></td>";
                    }

                    echo '<td>' . $sens_water[$j]['name_res'] . '</td>'
                    . '<td>' . $sens_water[$j]['place'] . '</td>';
                    $sql_sens_prop = pg_query('SELECT
                    "Tepl"."Sensor_Property"."Propert_Value"
                    FROM
                    "Tepl"."Sensor_Property"
                    INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."Sensor_Property".s_id = "Tepl"."Sensor_cnt".s_id)
                    WHERE
                    "Tepl"."Sensor_cnt".prp_id = ' . $sens_water[$j]['sens_id'] . ' AND
                    "Tepl"."Sensor_Property".id_type_property = 2
                    ORDER BY
                    "Tepl"."Sensor_Property".id_type_property');
                    if (pg_num_rows($sql_sens_prop) != 0) {

                        echo "<td>" . date("d.m.Y", strtotime(pg_fetch_result($sql_sens_prop, 0, 0))) . "</td>";
                    } else {
                        echo "<td></td>";
                    }


                    $massiv = explode(";", $sens_water[$j]['sens_comment']);
                    //print_r($massiv);
                    echo "<td> " . $massiv[0] . "</td>";
                    echo '</tr>';
                }
            }
        }
    }
}