<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../db_config.php';
session_start();
$id_object = $_POST['plc_id'];
$date = $_POST['date'];
$text = $_POST['text'];
$prp = $_POST['prp_id'];
$search_id = pg_query('SELECT DISTINCT 
  public.alarm.plc_id
FROM
  public.alarm
WHERE
  public.alarm.plc_id = ' . $id_object . '');

if (pg_num_rows($search_id) == 0) {
    if ($id_object != '' and $date != '' and $text != '') {
        if ($prp == NULL) {
            $add_table = pg_query('INSERT INTO alarm(plc_id, date_err, text_alarm, prp_id, sim_number) VALUES (' . $id_object . ', \'' . $date . '\', \'' . $text . '\', \'\', \'\')');
        }else{
            $add_table = pg_query('INSERT INTO alarm(plc_id, date_err, text_alarm, prp_id, sim_number) VALUES (' . $id_object . ', \'' . $date . '\', \'' . $text . '\', \''.$prp.'\', \'\')');
        }
    } else {
        echo "<h3>Не все поля заполнены</h3>";
    }
} else {
    echo "<h3>Данное учереждение присутствует в списке исключений</h3>";
}