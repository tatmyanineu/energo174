<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../../db_config.php';
session_start();

$sql_table_alarm = pg_query('SELECT 
  public.group_limit.id,
  public.group_limit.group_id,
  public.group_limit.group_name,
  public.group_limit.group_adres,
  public."LimitPlaces_cnt".teplo,
  public."LimitPlaces_cnt".voda
FROM
  public."LimitPlaces_cnt"
  INNER JOIN public.group_limit ON (public."LimitPlaces_cnt".plc_id = public.group_limit.group_id)');

echo '<table class="table table-bordered">'
 . '<thead id="thead">'
 . '<tr id="warning">'
 . '<td><b>№</b></td>'
 . '<td><b>Название</b></td>'
 . '<td><b>Адрес</b></td>'
 . '<td><b>Идентификатор</b></td>'
 . '<td><b>Данные</b></td>'
 . '<td><b></b></td>'
 . '</tr>'
 . '</thead><tbody>';
$n = 0;
while ($result = pg_fetch_row($sql_table_alarm)) {
    $n++;
    echo '<tr   id="hover" >'
    . '<td data-href="places_group.php?id_group=' . $result[1] . '">' . $n . '</td>'
    . '<td data-href="places_group.php?id_group=' . $result[1] . '">' . $result[2] . '</td>'
    . '<td data-href="places_group.php?id_group=' . $result[1] . '">' . $result[3] . '</td>'
    . '<td data-href="places_group.php?id_group=' . $result[1] . '">' . $result[1] . '</td>'
    . '<td data-href="places_group.php?id_group=' . $result[1] . '"> Тепло: ' . $result[4] . ' Гкал ; Вода: ' . $result[5] . ' м.куб.</td>'
    . '<td class="text-center"><button id="' . $result[1] . '" class="btn btn-xs btn-primary delete">Удалить</button></td>'
    . '</tr>';

    $sql_plc_group = pg_query('SELECT 
            "Places_cnt1"."Name",
            "PropPlc_cnt1"."ValueProp",
            "Tepl"."PropPlc_cnt"."ValueProp",
            public.group_plc.group_id,
            public.group_plc.plc_id
          FROM
            "Tepl"."PropPlc_cnt"
            INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."PropPlc_cnt".plc_id = "Places_cnt1".plc_id)
            INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
            INNER JOIN public.group_plc ON ("Places_cnt1".plc_id = public.group_plc.plc_id)
          WHERE
            "PropPlc_cnt1".prop_id = 27 AND 
            "Tepl"."PropPlc_cnt".prop_id = 26 AND 
            public.group_plc.group_id = ' . $result[1] . ' 
          ORDER BY public.group_plc.id');
    while ($result1 = pg_fetch_row($sql_plc_group)) {
        echo '<tr>'
        . '<td> </td>'
        . '<td>' . $result1[0] . '</td>'
        . '<td>' . $result1[1] . ' ' . $result1[2] . '</td>'
        . '<td>' . $result1[4] . '</td>'
        . '<td></td>'
        . '<td></td>'
        . '</tr>';
    }
}
echo '</tbody></table>';
