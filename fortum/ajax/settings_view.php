<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../include/db_config.php';

$sql_object = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  "Places_cnt1"."Name",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "Places_cnt1".plc_id,
  public.fortum_places_cnt.frt_plc
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN public.fortum_places_cnt ON ("Places_cnt1".plc_id = public.fortum_places_cnt.plc_id)
WHERE
  "PropPlc_cnt1".prop_id = 27 AND 
  "Tepl"."PropPlc_cnt".prop_id = 26
ORDER BY
  "Tepl"."Places_cnt"."Name",
  "Places_cnt1"."Name"');

while ($row = pg_fetch_row($sql_object)) {
    $plc[] = $row[4];
    $arObj[] = array(
        'id' => $row[4],
        'dist' => $row[0],
        'name' => $row[1],
        'adr' => $row[2] . ' ' . $row[3],
        'f_id' => $row[5]
    );
}


$sql_object_fr = pg_query('SELECT plc_id FROM fortum_plc');
while ($row = pg_fetch_row($sql_object_fr)) {
    $fortum[] = $row[0];
}



echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td ><b>№</b></td>
            <td ><b>Район</b></td>
            <td><b>Учереждение</b></td>
            <td><b>Адрес</b></td>
            <td><b>Идентификатор</b></td>
            <td><b>Id fortum<td>
            <td><b></b></td>
        </thead>
        <tbody>";
$n = 1;
//while ($row = pg_fetch_row($sql_object)) {
//    echo '<tr  id="hover">'
//    . '<td>'.$n.'</td>'
//    . '<td>'.$row[0].'</td>'
//    . '<td>'.$row[1].'</td>'
//    . '<td>'.$row[2].' ' .$row[3].'</td>'
//    . '<td>'.$row[4].'</td>'
//    . '<td><button class="btn btn-primary btn-sm add_to" id="'.$row[4].'">Добавить</button></td>'
//            . '</tr>';
//    $n++;
//}


$array = array_diff($plc, $fortum);
//var_dump($array);

for ($i = 0; $i < count($arObj); $i++) {
    echo '<tr  id="hover">'
    . '<td>' . $n . '</td>'
    . '<td>' . $arObj[$i][dist] . '</td>'
    . '<td>' . $arObj[$i][name] . '</td>'
    . '<td>' . $arObj[$i][adr] . '</td>'
    . '<td>' . $arObj[$i][id] . '</td>'
    . '<td>' . $arObj[$i][f_id] . '</td>';
    if ($array !== null) {
        $key = array_search($arObj[$i][id], $array);
        if ($key !== false) {
            echo '<td><button class="btn btn-primary btn-sm add_to" id="' . $arObj[$i][id] . '">Добавить</button></td>';
        } else {
            echo '<td><button class="btn btn-danger  btn-sm del_to" id="' . $arObj[$i][id] . '">Удалить</button></td>';
        }
    } else {
        echo '<td><button class="btn btn-primary btn-sm add_to" id="' . $arObj[$i][id] . '">Добавить</button></td>';
    }
    echo '</tr>';
    $n++;
}