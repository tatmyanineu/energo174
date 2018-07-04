<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();


$edit_sim = pg_query('UPDATE public."LimitPlaces_cnt" SET  teplo=\''.$_POST['teplo'].'\', voda=\''.$_POST['voda'].'\' WHERE plc_id='.$_POST['plc_id'].'');
