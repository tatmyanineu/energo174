<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$arr=array('1'=>a, '2'=>b);
session_start();
include '../../../db_config.php';
$sql_numb = pg_query('SELECT DISTINCT 
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."Device_cnt"."Numbe",
  "Tepl"."TypeSensor"."Name",
  "Tepl"."Sensor_cnt"."Comment",
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."ParamResPlc_cnt"."Comment"
FROM
  "Tepl"."ParamResPlc_cnt"
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."PointRead" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."PointRead".prp_id)
  INNER JOIN "Tepl"."Device_cnt" ON ("Tepl"."PointRead".dev_id = "Tepl"."Device_cnt".dev_id)
  INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."PointRead".prp_id = "Tepl"."Sensor_cnt".prp_id)
  INNER JOIN "Tepl"."TypeSensor" ON ("Tepl"."Sensor_cnt".sen_id = "Tepl"."TypeSensor".sen_id)
  INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
WHERE
  "Tepl"."ParamResPlc_cnt".prp_id = '.$_POST['prp'].'
ORDER BY
  "Tepl"."ParametrResourse"."Name"');

$n = pg_fetch_all($sql_numb);



$sql_prop = pg_query('SELECT 
  "Tepl"."Sensor_Property"."Propert_Value"
FROM
  "Tepl"."Sensor_Property"
  INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."Sensor_Property".s_id = "Tepl"."Sensor_cnt".s_id)
WHERE
  "Tepl"."Sensor_cnt".prp_id = '.$_POST['prp'].' AND 
  "Tepl"."Sensor_Property".id_type_property = 3
ORDER BY
  "Tepl"."Sensor_Property".id_type_property');

$d = pg_fetch_all($sql_prop);
$date = date("d.m.Y", strtotime($d[0]['Propert_Value']));
$arr = array(
        'numb'=>$n[0]['Numbe'],
        'name'=>$n[0]['Name'],
        'date'=>$date
        ); 


echo json_encode($arr, JSON_UNESCAPED_UNICODE);