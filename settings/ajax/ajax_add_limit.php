<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

$search_plc = pg_query('SELECT id FROM public."LimitPlaces_cnt" WHERE plc_id = ' . $_POST['plc_id'] . '');
$plc= pg_fetch_result($search_plc, 0, 0);
if($plc!=null){
    echo "Данный адрес уже пристувует в списе сим карт";
}else{
    $max_id = pg_query('SELECT MAX(id) FROM public."LimitPlaces_cnt"');
    $id = pg_fetch_result($max_id, 0, 0);
    $id++;
    $add_sim = pg_query('INSERT INTO public."LimitPlaces_cnt"(id, plc_id, teplo, voda)  VALUES ('.$id.', '.$_POST['plc_id'].', \''.$_POST['teplo'].'\', \''.$_POST['voda'].'\')');
}