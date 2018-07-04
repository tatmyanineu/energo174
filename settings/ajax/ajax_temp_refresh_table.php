<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */




include '../../db_config.php';
session_start();

$sql_all_school = pg_query('SELECT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  public.temp_charts.t1min,
  public.temp_charts.t2min,
  public.temp_charts.t1max,
  public.temp_charts.t2max,
  public.temp_charts.thpmin,
  public.temp_charts.thpmax,
  public.temp_charts.param1,
  public.temp_charts.param2
FROM
  "Tepl"."Places_cnt"
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN public.temp_charts ON ("Tepl"."Places_cnt".plc_id = public.temp_charts.plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26');

echo '<table class="table table-bordered">'
 . '<thead id="thead">'
 . '<tr id="warning">'
 . '<td><b>№</b></td>'
 . '<td><b>Название</b></td>'
 . '<td><b>Адрес</b></td>'
 . '<td><b>tMin <br>Подачи</b></td>'
 . '<td><b>tMin <br>Обратки</b></td>'
 . '<td><b>tMax <br>Подачи</b></td>'
 . '<td><b>tMax <br>Обратки</b></td>'
 . '<td><b>tMin <br>Нар. Воздуха</b></td>'
 . '<td><b>tMin <br>Нар. Воздуха</b></td>'
 . '<td><b>Коэффиц. <br>Подачи</b></td>'
 . '<td><b>Коэффиц. <br>Обратки</b></td>'
 . '</tr>'
 . '</thead><tbody>';
$n = 0;


while ($result = pg_fetch_row($sql_all_school)) {
    $n++;
    echo '<tr id="hover">'
    . '<td>' . $n . '</td>'
    . '<td>' . $result[0] . '</td>'
    . '<td>' . $result[1] . ' ' . $result[2] . '</td>'
    . '<td>' . $result[3] . '</td>'
    . '<td>' . $result[4] . '</td>'
    . '<td>' . $result[5] . '</td>'
    . '<td>' . $result[6] . '</td>'
    . '<td>' . $result[7] . '</td>'
    . '<td>' . $result[8] . '</td>'
    . '<td>' . $result[9] . '</td>'
    . '<td>' . $result[10] . '</td>'
    . '</tr>';
}
echo '</tbody></table>';
