<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../../db_config.php';

$sql = pg_query('SELECT 
  "Tepl"."Places_cnt"."Name",
  public.fault_cnt.name,
  public.fault_inc.date_time,
  public.fault_inc.param,
  public.fault_inc.view_stat,
  public.fault_inc.comments,
  public.fault_inc.plc_id,
  public.fault_inc.id
FROM
  public.fault_inc
  INNER JOIN public.fault_cnt ON (public.fault_inc.numb = public.fault_cnt.id)
  INNER JOIN "Tepl"."Places_cnt" ON (public.fault_inc.plc_id = "Tepl"."Places_cnt".plc_id)
ORDER BY
  public.fault_inc.date_time DESC');
$i=1;
while($row = pg_fetch_row($sql)){
    
    switch ($row[4]){
        case 0:
            $status = "Новый";
            break;
        case 1:
            $status = "Просмотрен";
            break;
        case 2:
            $status = "В работе";
            break;
        case 3:
            $status = "Завершен";
            break;
        case 4:
            $status = "Удален";
            break;
    }
    
    $result['data'][] = array(
        'id'=>$i,
        'name'=>mb_strimwidth($row[0], 0, 50, "..."),
        'incedent'=>$row[1],
        'date'=> date("d.m.Y" , strtotime($row[2])),
        'view'=>$status,
        'comment'=>$row[3],
        'plc_id'=>$row[6],
        'inc_id'=>$row[7]
    );
    $i++;
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);