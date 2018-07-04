<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../include/db_config.php';

session_start();


if (($handle = fopen("sens.csv", "r")) !== FALSE) {
    # Set the parent multidimensional array key to 0.
    $nn = 0;
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        # Count the total keys in the row.
        $c = count($data);
        # Populate the multidimensional array.
        $diametrs[] = $data[1];
        $csvarray[] = $data;

        $nn++;
    }
    # Close the File.
    fclose($handle);
}


$sql_sens = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."Places_cnt"."Name",
  "Tepl"."TypeSensor"."Name",
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."Sensor_Property"."Propert_Value",
  "Tepl"."Sensor_Property".id_type_property,
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."ParamResPlc_cnt"."Comment",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  public.fortum_places_cnt.frt_plc
FROM
  "Tepl"."Places_cnt"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."ParamResPlc_cnt".plc_id)
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Sensor_cnt".prp_id)
  INNER JOIN "Tepl"."TypeSensor" ON ("Tepl"."Sensor_cnt".sen_id = "Tepl"."TypeSensor".sen_id)
  LEFT OUTER JOIN "Tepl"."Sensor_Property" ON ("Tepl"."Sensor_cnt".s_id = "Tepl"."Sensor_Property".s_id)
  INNER JOIN public.fortum_places_cnt ON ("Tepl"."Places_cnt".plc_id = public.fortum_places_cnt.plc_id)
WHERE
  "Tepl"."Resourse_cnt"."Name" LIKE \'%Тепло%\' AND 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" != 9 AND 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" != 283 AND 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" != 282 AND 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" != 16 AND 
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" != 17
ORDER BY
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."Sensor_Property".id_type_property');


while ($row = pg_fetch_row($sql_sens)) {
    if ($row[5] != 2) {
        $array_sens[] = array(
            'id' => $row[0],
            'name_plc' => $row[1],
            'name_sens' => $row[2],
            'number' => $row[4],
            'resours' => $row[6],
            'comment' => $row[7],
            'prop_id' => $row[5],
            'f_id' => $row[9]
        );
    }
}

//var_dump($array_sens);
echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td ><b>№</b></td>
            <td ><b>plc_id</b></td>
            <td ><b>fortum_id</b></td>
            <td><b>Учереждение</b></td>
            <td><b>Расходомер</b></td>
            <td><b>Сер. Номер</b></td>
            <td><b>Параметр</b></td>
            <td><b>Диаметр</b></td>
            <td><b>Gmin</b></td>
            <td><b>Gmax</b></td>
        </thead>
        <tbody>";

$n = 1;
for ($i = 0; $i < count($array_sens); $i++) {

    $d = explode(";", $array_sens[$i]['comment']);
    $diametr = $d[0];
    $key = array_keys($diametrs, $diametr);
    for ($s = 0; $s < count($key); $s++) {
        if ($array_sens[$i]['name_sens'] == $csvarray[$key[$s]][0] and $csvarray[$key[$s]][1] == $diametr) {
            //$sheet->setCellValueByColumnAndRow(1, $colum_text, "" . $sens_name[$i] . " ".$csvarray[$s][gmin]. " ".$csvarray[$s][gmax]. "");
            $gmin = $csvarray[$key[$s]][2];
            $gmax = $csvarray[$key[$s]][3];
        }
    }
    echo '<tr>'
    . '<td>' . $n . '</td>'
    . '<td>' . $array_sens[$i][id] . '</td>'
    . '<td>' . $array_sens[$i][f_id] . '</td>'
    . '<td>' . $array_sens[$i][name_plc] . ' </td>'
    . '<td>' . $array_sens[$i][name_sens] . '</td> '
    . '<td>' . $array_sens[$i][number] . '</td> '
    . '<td>' . $array_sens[$i][resours] . '</td> '
    . '<td>' . $diametr . '</td> '
    . '<td>' . $gmin . '</td>'
    . '<td>' . $gmax . '</td></tr>';
    $n++;
}