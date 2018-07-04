<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



include '../../db_config.php';
session_start();

$sql_all_school = pg_query('SELECT 
  public."SimPlace_cnt".plc_id,
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  public."SimPlace_cnt".sim_number,
  public."SimNotEroor".id
FROM
  public."SimNotEroor"
  INNER JOIN public."SimPlace_cnt" ON (public."SimNotEroor".sim_number = public."SimPlace_cnt".sim_number)
  INNER JOIN "Tepl"."Places_cnt" ON (public."SimPlace_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26');

echo '<table class="table table-bordered">'
 . '<thead id="thead">'
 . '<tr id="warning">'
 . '<td><b>№</b></td>'
 . '<td><b>Название</b></td>'
 . '<td><b>Адрес</b></td>'
 . '<td><b>Номер Sim-карты</b></td>'
 . '<td><b></b></td>'
 . '</tr>'
 . '</thead><tbody>';
$n = 0;

while ($result = pg_fetch_row($sql_all_school)) {
    $n++;
    echo '<tr id="hover">'
    . '<td>' . $n . '</td>'
    . '<td>' . $result[1] . '</td>'
    . '<td>' . $result[2] . ' ' . $result[3] . '</td>'
    . '<td id="table_' . $result[0] . '">' . $result[4] . '</td>'
    . '<td class="text-center"><button id="'.$result[5].'" data-plc_id="'.$result[0].'" class="btn btn-xs btn-primary delete">Удалить</button></td>'
    . '</tr>';
}
echo '</tbody></table>';
