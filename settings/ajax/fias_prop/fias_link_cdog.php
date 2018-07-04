<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../../../db_config.php';
$plc =$_POST['plc'];
$sql = pg_query('SELECT 
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."PropPlc_cnt"
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 48 AND 
  "Tepl"."PropPlc_cnt".plc_id = '.$plc);

$result = pg_fetch_all($sql);

echo $result[0]['ValueProp'];