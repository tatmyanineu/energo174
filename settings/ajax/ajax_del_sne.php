<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

$id = $_POST['id'];
$plc_id = $_POST['plc_id'];

$add_table = pg_query('DELETE FROM public."SimNotEroor" WHERE id=' . $id . '');


$sql_not_alarn_plc = pg_query('SELECT text_alarm FROM public.alarm where plc_id = '.$plc_id.'');
$text = pg_fetch_result($sql_not_alarn_plc, 0, 0);
if($text == '<b>Исключения SIM</b>: заблокированна Sim-карта'){
    $del_alarm = pg_query('DELETE FROM public.alarm WHERE plc_id='.$plc_id.'');
}else{
    $text = str_replace('<b>Исключения SIM</b>: заблокированна Sim-карта; ', '', $text);
    $edit_alarm = pg_query('UPDATE public.alarm SET text_alarm=\'' . $text . '\' , sim_number=\'\' WHERE plc_id='.$plc_id.'');
}
echo "<h3>Удалено</h3>";