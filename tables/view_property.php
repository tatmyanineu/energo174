<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../db_config.php';

$sql= pg_query('SELECT 
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Resourse_cnt"."Name"
FROM
  "Tepl"."ParamResPlc_cnt"
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = '.$_POST['id'].' AND 
  "Tepl"."Resourse_cnt"."Name" LIKE \'%ВС%\'
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParametrResourse"."Name"');