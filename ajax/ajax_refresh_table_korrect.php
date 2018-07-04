<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();
$date = date('Y-m-d');

$sql_prp = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  public.korrect.date_time,
  public.korrect.old_value,
  public.korrect.new_value,
  public.korrect.name_prp,
  public.korrect.id,
  public.korrect.prim
FROM
  "Tepl"."Places_cnt"
  INNER JOIN public.korrect ON ("Tepl"."Places_cnt".plc_id = public.korrect.plc_id)
WHERE
  public.korrect.plc_id =' . $_POST['plc_id'] . ' ORDER BY public.korrect.date_time');

echo '<table class="table table-bordered">'
 . '<thead id="thead">'
 . '<tr id="warning">'
 . '<td><b>№</b></td>'
 . '<td><b>Учереждение</b></td>'
 . '<td><b>Параметр</b></td>'
 . '<td><b>Дата</b></td>'
 . '<td><b>Нач. показания</b></td>'
 . '<td><b>Кон. показания</b></td>'
 . '<td><b>Примечание</b></td>'
 . '<td><b></b></td>'
 . '</tr>'
 . '</thead><tbody>';
$n = 0;
while ($result = pg_fetch_row($sql_prp)) {
    $n++;
    echo '<tr>'
    . '<td>' . $n . '</td>'
    . '<td>' . $result[0] . '</td>'
    . '<td>' . $result[4] . '</td>'
    . '<td>' . date('d.m.Y', strtotime($result[1])) . '</td>'
    . '<td>' . $result[2] . '</td>'
    . '<td>' . $result[3] . '</td>'
    . '<td>' . $result[6] . '</td>'
    . '<td class="text-center"><button id="' . $result[5] . '" class="btn btn-xs btn-primary delete">Удалить</button></td>'
    . '</tr>';
}
echo '</tbody>'
 . '</table>';
