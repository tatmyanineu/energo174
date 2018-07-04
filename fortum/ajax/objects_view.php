<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../include/db_config.php';

$sql_object = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."TypeDevices"."Name",
  "Tepl"."Device_cnt".dev_id,
  "Tepl"."Device_Property"."Propert_Value",
  "Tepl"."Device_Property".id_type_property,
  public.fortum_places_cnt.frt_plc
FROM
  "Tepl"."Device_cnt"
  INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
  LEFT OUTER JOIN "Tepl"."Device_Property" ON ("Tepl"."Device_cnt".dev_id = "Tepl"."Device_Property".dev_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."Device_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN public.fortum_places_cnt ON ("Tepl"."Places_cnt".plc_id = public.fortum_places_cnt.plc_id)
WHERE
  "Tepl"."TypeDevices"."Name" NOT LIKE \'%Пульсар%\'
ORDER BY
  "Tepl"."Device_Property".id_type_property');


while ($row = pg_fetch_row($sql_object)) {
    if ($row[5] == 0 or $row[5] = NULL) {
        $object[] = array(
            'plc_name' => $row[0],
            'plc_id' => $row[1],
            'dev_name' => $row[2],
            'dev_id' => $row[3],
            'dev_numb' => $row[4],
            'f_id' => $row[6]
        );
    }
}
//var_dump($object);


echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td ><b>№</b></td>
            <td ><b>plc_name</b></td>
            <td><b>adr</b></td>
            <td><b>plc_id</b></td>
            <td><b>fortum_id</b></td>
            <td><b>dev_name</b></td>
            <td><b>dev_id</b></td>
            <td><b>dev_number</b></td>
        </thead>
        <tbody>";
$n = 1;
for ($i = 0; $i < count($object); $i++) {

    $sql_adr = pg_query('SELECT DISTINCT 
            "Tepl"."Places_cnt".plc_id,
            "PropPlc_cnt1"."ValueProp",
            "Tepl"."PropPlc_cnt"."ValueProp"
          FROM
            "Tepl"."Places_cnt"
            INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
            INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
          WHERE
            "PropPlc_cnt1".prop_id = 27 AND 
            "Tepl"."PropPlc_cnt".prop_id = 26 AND 
            "Tepl"."Places_cnt".plc_id = ' . $object[$i][plc_id] . '');

    while ($r_adr = pg_fetch_row($sql_adr)) {
        $adr = $r_adr[1] . ' д. ' . $r_adr[2];
    }

    echo '<tr>'
    . '<td>' . $n . '</td>'
    . '<td>' . $object[$i][plc_name] . '</td>'
    . '<td>' . $adr . '</td>'
    . '<td>' . $object[$i][plc_id] . '</td>'
    . '<td>' . $object[$i][f_id] . '</td>'
    . '<td>' . $object[$i][dev_name] . '</td>'
    . '<td>' . $object[$i][dev_id] . '</td>'
    . '<td>' . $object[$i][dev_numb] . '</td>'
    . '</tr>';




    $sql_param = pg_query('SELECT DISTINCT 
            ("Tepl"."ParametrResourse"."Name") AS "FIELD_1",
            "Tepl"."ParamResPlc_cnt"."NameGroup",
            "Tepl"."Resourse_cnt"."Name",
            "Tepl"."ParamResPlc_cnt"."ParamRes_id",
            "Tepl"."ParamResPlc_cnt".prp_id
          FROM
            "Tepl"."ParametrResourse"
            INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
            INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
          WHERE
            "Tepl"."Resourse_cnt"."Name" = \'Тепло\' AND 
            "Tepl"."ParamResPlc_cnt".plc_id = ' . $object[$i][plc_id] . '
          ORDER BY
             "Tepl"."ParametrResourse"."Name",
             "Tepl"."ParamResPlc_cnt"."NameGroup"');
    $m = 1;
    while ($row = pg_fetch_row($sql_param)) {
        echo '<tr>'
        . '<td>' . $n . '.' . $m . '</td>'
        . '<td> - </td>'
        . '<td> - </td>'
        . '<td>' . $row[0] . '</td>'
        . '<td>' . $row[1] . '</td>'
        . '<td>' . $row[2] . '</td>'
        . '<td>' . $row[3] . '</td>'
        . '<td> - </td>'
        . '</tr>';
        $m++;
    }

    $n++;
}