<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../../db_config.php';
session_start();

$id = $_POST['id'];

$del_group= pg_query('DELETE FROM public.group_limit WHERE group_id=' . $id . '');
$del_group_plc = pg_query('DELETE FROM public.group_plc WHERE group_id=' . $id . '');
$del_limit_group = pg_query('DELETE FROM public."LimitPlaces_cnt" WHERE plc_id=' . $id . '');