<?php
 header('Content-Type: application/json');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();
$id_object = $_POST['plc_id'];

$sql_vvod = pg_query('SELECT 
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."Device_cnt"."Numbe"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."PointRead" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."PointRead".prp_id)
  INNER JOIN "Tepl"."Device_cnt" ON ("Tepl"."PointRead".dev_id = "Tepl"."Device_cnt".dev_id)
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = '.$id_object.' AND 
  "Tepl"."Resourse_cnt"."Name" = \'ХВС\' OR 
  "Tepl"."ParamResPlc_cnt".plc_id = '.$id_object.' AND 
  "Tepl"."Resourse_cnt"."Name" = \'ГВС\'
ORDER BY
"Tepl"."Device_cnt"."Numbe",
  "Tepl"."Resourse_cnt"."Name" DESC,
  "Tepl"."ParametrResourse"."Name"');


while($result = pg_fetch_row($sql_vvod)){
    $vvod[] = array(
        'prp_id'=>$result[1],
        'name'=> $result[2].': '.$result[0].'( рег. '.$result[3].')'
    );
}

echo json_encode($vvod);