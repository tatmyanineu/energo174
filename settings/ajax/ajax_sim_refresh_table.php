<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../../db_config.php';
session_start();

$sql_table_alarm = pg_query('SELECT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  public."SimPlace_cnt".sim_number,
  "Tepl"."Places_cnt".plc_id
FROM
  "Tepl"."Places_cnt"
  INNER JOIN public."SimPlace_cnt" ON ("Tepl"."Places_cnt".plc_id = public."SimPlace_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26
ORDER BY
  "Tepl"."Places_cnt"."Name"');

echo '<table class="table table-bordered">'
 . '<thead id="thead">'
 . '<tr id="warning">'
 . '<td><b>№</b></td>'
 . '<td><b>Название</b></td>'
 . '<td><b>Адрес</b></td>'
 . '<td><b>Номер Sim-карты</b></td>'
 . '</tr>'
 . '</thead><tbody>';
$n = 0;
while ($result = pg_fetch_row($sql_table_alarm)) {
    $n++;
    echo '<tr id="hover" data-id="'.$result[4].'">'
    . '<td>' . $n . '</td>'
    . '<td>' . $result[0] . '</td>'
    . '<td>' . $result[1] . ' ' . $result[2] . '</td>'
    . '<td id="table_'.$result[4].'">' . $result[3] . '</td>'
    . '</tr>';
}
echo '</tbody></table>';
