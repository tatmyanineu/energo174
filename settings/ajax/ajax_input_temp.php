<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

$js = $_POST['arr'];
$max_id = 0;

$sql_max_id = pg_query('SELECT max(id) FROM public."SimNotEroor"');
$max_id = pg_fetch_result($sql_max_id, 0, 0);
$col = 0;

for ($i = 0; $i < count($js); $i++) {
    $max_id++;
    $t1min = str_replace(',', '.', $js[$i]['t1min']);
    $t2min = str_replace(',', '.', $js[$i]['t2min']);
    $t1max = str_replace(',', '.', $js[$i]['t1max']);
    $t2max = str_replace(',', '.', $js[$i]['t2max']);
    $thpmin = str_replace(',', '.', $js[$i]['tMin']);
    $thpmax = str_replace(',', '.', $js[$i]['tMax']);

    
    
    $param1 = ($t1max - $t1min) / ($thpmin - $thpmax);
    $param2 = ($t2max - $t2min) / ($thpmin - $thpmax);

    $param1 = str_replace(',', '.', $param1);
    $param2 = str_replace(',', '.', $param2);

    $search_numb = pg_query('SELECT public.temp_charts.id FROM public.temp_charts where public.temp_charts.plc_id= ' . $js[$i]['plc_id'] . '');
    $number = pg_fetch_result($search_numb, 0, 0);
    if ($number != null) {
        echo "<h2 class='text-center'>Данное учереждение уже присутствует в списке</h2>";
    } else {
        $add_sne = pg_query('INSERT INTO temp_charts(id, plc_id, t1min, t2min, t1max, t2max, thpmin, thpmax, param1, param2)'
                . 'VALUES (' . $max_id . ',' . $js[$i]['plc_id'] . ',' . $t1min . ', ' . $t2min . ', ' . $t1max . ', ' . $t2max . ', ' . $thpmin . ', ' . $thpmax . ', ' . $param1 . ', ' . $param2 . ');');
    }
}