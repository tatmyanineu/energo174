<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../db_config.php';
session_start();

$sql_table_alarm = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  public.alarm.date_err,
  public.alarm.text_alarm,
  public.alarm.id,
  public.alarm.sim_number,
  "Tepl"."Places_cnt".plc_id
FROM
  "Tepl"."PropPlc_cnt"
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PropPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN public.alarm ON ("Tepl"."Places_cnt".plc_id = public.alarm.plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26
ORDER BY
  public.alarm.text_alarm,
    public.alarm.date_err,
  "Tepl"."Places_cnt"."Name"');

echo '<table class="table table-bordered">'
 . '<thead id="thead">'
 . '<tr id="warning">'
 . '<td><b>№</b></td>'
 . '<td><b>Название</b></td>'
 . '<td><b>Адрес</b></td>'
 . '<td><b>Дата</b></td>'
 . '<td><b>Причина отклчюения</b></td>'
 . '<td><b></b></td>'
 . '</tr>'
 . '</thead><tbody>';
$n = 0;
while ($result = pg_fetch_row($sql_table_alarm)) {
    $n++;
    echo '<tr id="tr'. $result[5] . '">'
    . '<td >' . $n . '</td>'
    . '<td><a class="object" id ="' . $result[7] . '">' . $result[0] . '</a></td>'
    . '<td>' . $result[1] . ' ' . $result[2] . '</td>'
    . '<td>' . date('d.m.Y', strtotime($result[3])) . '</td>'
    . '<td>' . $result[4] . '</td>';
    if ($result[6] != null or $result[6] != "") {
        echo '<td class="text-center"></td>';
    } else {
        echo '<td class="text-center"><button id="' . $result[5] . '" class="btn btn-xs btn-primary delete">Удалить</button></td>';
    }

    echo '</tr>';
}
echo '</tbody></table>';
