<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

if(isset($_POST['key'])){
    $sql = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  "Places_cnt1".plc_id,
  "Places_cnt1"."Name",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."Places_cnt".typ_id = 10 AND 
  "Tepl"."PropPlc_cnt".prop_id = 26 AND 
  "PropPlc_cnt1".prop_id = 27 AND 
  "Places_cnt1"."Name" ILIKE \'%'.$_POST['key'].'%\'
ORDER BY
  "Tepl"."Places_cnt"."Name",
  "Places_cnt1"."Name",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."PropPlc_cnt"."ValueProp"');
}else{
    $sql = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  "Places_cnt1".plc_id,
  "Places_cnt1"."Name",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."Places_cnt".typ_id = 10 AND 
  "Tepl"."PropPlc_cnt".prop_id = 26 AND 
  "PropPlc_cnt1".prop_id = 27
ORDER BY
  "Tepl"."Places_cnt"."Name",
  "Places_cnt1"."Name",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."PropPlc_cnt"."ValueProp"');
}







echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>ID</b</td>
            <td rowspan=2 data-query='1'><b>Район</b</td>
            <td rowspan=2 data-query='3'><b>Назване</b</td>
            <td rowspan=2 data-query='4'><b>Адрес</b</td>
            <td rowspan=2 data-query='6'><b></b</td>
        </tr>
        </thead>
        <tbody>";


while ($row = pg_fetch_row($sql)) {
    echo '<tr id=\'hover\' >'
    . '<td>' . $row[1] . '</td>'
    . '<td>' . $row[0] . '</td>'
    . '<td id="name_' . $row[1] . '">' . $row[2] . '</td>'
    . '<td>' . $row[3] . ' д.'.$row[4].' </td>'
    . '<td><a class="addTag" id="' . $row[1] . '"><span class="glyphicon glyphicon-plus"></span> </a></td>'
    . '</tr>';
}