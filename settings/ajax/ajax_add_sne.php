<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

$plc_id = $_POST['plc_id'];
$sim_numb = $_POST['number'];

$search_numb = pg_query('SELECT sim_number FROM public."SimNotEroor" WHERE sim_number = \''.$sim_numb.'\'');
$numb = pg_fetch_result($search_numb, 0, 0);

if ($numb != null) {
    echo "Данный номер уже пристувует в списе исключений";
} else {

    $sql_max_id = pg_query('SELECT max(id) FROM public."SimNotEroor"');
    $max_id = pg_fetch_result($sql_max_id, 0, 0);
    $max_id++;
    $add_sne = pg_query('INSERT INTO public."SimNotEroor"(id, sim_number) VALUES (' . $max_id . ', \'' . $sim_numb . '\')');

    unset($max_id);
    unset($sql_max_id);

    $search_plc = pg_query('SELECT plc_id, text_alarm FROM public.alarm WHERE plc_id=' . $plc_id . '');
    $plc = pg_fetch_result($search_plc, 0, 0);

    if ($plc != null) {
        $text = '<b>Исключения SIM</b>: заблокированна Sim-карта; ' . pg_fetch_result($search_plc, 0, 1);
        $edit_alarm = pg_query('UPDATE public.alarm SET text_alarm=\'' . $text . '\' , sim_number=\'' . $sim_numb . '\' WHERE plc_id='.$plc_id.'');
    } else {
        $sql_max_id = pg_query('SELECT max(id) FROM public.alarm');
        $max_id = pg_fetch_result($sql_max_id, 0, 0);
        $max_id++;
        $text = '<b>Исключения SIM</b>: заблокированна Sim-карта';
        $add_alarm = pg_query('INSERT INTO public.alarm(plc_id, date_err, text_alarm, prp_id, sim_number) VALUES (' . $plc_id . ', \'' . date('Y-m-d') . '\', \'' . $text . '\', \'\', \'' . $sim_numb . '\')');
    }
}
