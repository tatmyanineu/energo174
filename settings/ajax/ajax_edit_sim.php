<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();


$edit_sim = pg_query('UPDATE public."SimPlace_cnt" SET  sim_number=\''.$_POST['number'].'\' WHERE plc_id='.$_POST['plc_id'].'');
