<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../../db_config.php';

if($_POST['param']==1){
    pg_query('UPDATE fault_inc SET  view_stat=\''.$_POST['stat'].'\', user_comment=\''.$_POST['comm'].'\' WHERE id='.$_POST['id']);
}else{
   pg_query('DELETE FROM fault_inc WHERE id= '.$_POST['id']); 
}
