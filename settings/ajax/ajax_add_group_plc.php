<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();
$max_id = pg_query('SELECT MAX(id) FROM public.group_plc');
$id = pg_fetch_result($max_id, 0, 0);
if ($id == null) {
    $id = 0;
} else {
    $id++;
}

if ($_POST['plc_id'] != "") {
    $add_sim = pg_query('INSERT INTO public.group_plc (id, group_id, plc_id) VALUES (' . $id . ', ' . $_POST['id_group'] . ', ' . $_POST['plc_id'] . ');
');
}   