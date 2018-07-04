<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../../db_config.php';

$sql = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."PropPlc_cnt"
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PropPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 48');


while ($row = pg_fetch_row($sql)){
    $dog[]=array(
        'plc_id'=>$row[0],
        'num'=>$row[1]
    );
}

$sql= pg_query('SELECT * FROM fias_cnt');
$i=0;
while($row= pg_fetch_row($sql)){
    $k = array_search($row[2], array_column($dog, 'plc_id'));
    if($row[3]=="" and $k!==false){
        pg_query('UPDATE fias_cnt SET cdog=\''.$dog[$k]['num'].'\' WHERE id='.$row[0].'');
        $i++;
        $k=false;
    }
}

echo "Обновлено ".$i." записей";