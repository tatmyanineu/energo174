<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../db_config.php';
session_start();


$id = $_POST['id'];
$group = $_POST['group'];

$sql_delUser = pg_query('DELETE FROM "Tepl"."User_cnt"
 WHERE usr_id='.$id.'');

$sql_delUserGroup = pg_query('DELETE FROM "Tepl"."GroupToUserRelations"
 WHERE usr_id = '.$id.' and grp_id ='.$group.'');