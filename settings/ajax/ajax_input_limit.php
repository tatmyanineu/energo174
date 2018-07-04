<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

$js = $_POST['arr'];

$sql_max_id = pg_query('SELECT max(id) FROM public."LimitPlaces_cnt"');
$max_id = pg_fetch_result($sql_max_id, 0, 0);
$col = 0;

for ($i = 0; $i < count($js); $i++) {
    //$numb = preg_replace('~[^0-9]+~', '', $js[$i]['number']);
    $teplo = str_replace(',', '.', $js[$i]['teplo']);
    $voda = str_replace(',', '.', $js[$i]['voda']);
    $max_id++;
    $col++;
    $search_numb = pg_query('SELECT plc_id FROM public."LimitPlaces_cnt" WHERE plc_id = \'' . $js[$i]['plc_id'] . '\'');
    $number = pg_fetch_result($search_numb, 0, 0);

    if ($number != null) {
        echo "<h2 class='text-center'>Данное учереждение уже присутствует в списке лимитов</h2>";
    } else {
        $add_sne = pg_query('INSERT INTO public."LimitPlaces_cnt" (id, plc_id, teplo, voda) VALUES (' . $max_id . ', ' . $js[$i]['plc_id'] . ',\'' . $teplo . '\',\'' . $voda . '\')');
    }
}

