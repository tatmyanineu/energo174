<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();
$max_id = pg_query('SELECT MAX(id) FROM public.group_limit');
$id = pg_fetch_result($max_id, 0, 0);
if ($id == null) {
    $id = 0;
} else {
    $id++;
}
$id_group = $id + 100000;
if ($_POST['name'] != "") {
    $add_sim = pg_query('INSERT INTO public.group_limit (id, group_id, group_name, group_adres)  VALUES (' . $id . ', ' . $id_group . ', \'' . $_POST['name'] . '\', \'' . $_POST['adres'] . '\')');

    if ($_POST['teplo'] != "" and $_POST['voda'] != "") {
        $search_plc = pg_query('SELECT id FROM public."LimitPlaces_cnt" WHERE plc_id = ' . $id_group . '');
        $plc = pg_fetch_result($search_plc, 0, 0);
        if ($plc == null) {

            $max_id = pg_query('SELECT MAX(id) FROM public."LimitPlaces_cnt"');
            $id = pg_fetch_result($max_id, 0, 0);
            $id++;
            $add_sim = pg_query('INSERT INTO public."LimitPlaces_cnt"(id, plc_id, teplo, voda)  VALUES (' . $id . ', ' . $id_group . ', \'' . $_POST['teplo'] . '\', \'' . $_POST['voda'] . '\')');
        }
    }
}   