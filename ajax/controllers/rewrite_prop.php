<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../../db_config.php';


for($i=0;$i<count($_POST['value']);$i++){
    pg_query('UPDATE fault_cnt SET coeficient=\''.$_POST['value'][$i]['val'].'\', enabled='.$_POST['enabled'][$i]['val'].' WHERE id='.$_POST['value'][$i]['id'].'');
}