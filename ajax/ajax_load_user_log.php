<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();



$sql_log_user = pg_query('SELECT DISTINCT 
  public.logs_user.id_user,
  public.logs_user.ip_adr,
  public.logs_user.date_con
FROM
  public.logs_user
ORDER BY
  public.logs_user.date_con DESC');

while ($result = pg_fetch_row($sql_log_user)) {
    $log[] = array(
        'id' => $result[0],
        'ip' => $result[1],
        'date' => $result[2]
    );
}



$sql_all_users = pg_query('SELECT DISTINCT 
  "Tepl"."User_cnt".usr_id,
  "Tepl"."User_cnt"."Login",
  "Tepl"."User_cnt"."SurName",
  "Tepl"."User_cnt"."PatronName",
  "Tepl"."User_cnt"."Privileges"
FROM
  "Tepl"."User_cnt"
ORDER BY
  "Tepl"."User_cnt".usr_id');


echo '<table class="table table-bordered">'
 . '<thead id="thead">'
 . '<tr id="warning">'
 . '<td><b>№</b></td>'
 . '<td><b>Логин</b></td>'
 . '<td><b>Имя</b></td>'
 . '<td><b>Адрес</b></td>'
 . '<td><b>IP</b></td>'
 . '<td><b>Дата</b></td>'
 . '</tr>'
 . '</thead><tbody>';
$n = 1;

while ($result = pg_fetch_row($sql_all_users)) {
//    $users[] =array(
//        'id'=>$result[0],
//        'name'=>$result[1],
//        'surName'=>$result[2],
//        'patronName'=>$result[3],
//        'priveleg'=>$result[4]
//    );

    echo '<tr>';
    $k = array_search($result[0], array_column($log, 'id'));
    if ($k !== false) {
        //echo ' !-> ' . $result[1] . ' ' . $result[2] . ' ' . $result[3] . ' ' . $log[$k]['ip'] . ' ' . $log[$k]['date'] . '<br>';
        echo '<td>' . $n . '</td>'
        . '<td>' . $result[1] . '</td>'
        . '<td>' . $result[2] . '</td>'
        . '<td>' . $result[3] . '</td>'
        . '<td>' . $log[$k]['ip'] . '</td>'
        . '<td>' . date("d.m.Y H:i", strtotime($log[$k]['date'])) . '</td>';
    } else {
         echo '<td>' . $n . '</td>'
        . '<td>' . $result[1] . '</td>'
        . '<td>' . $result[2] . '</td>'
        . '<td>' . $result[3] . '</td>'
        . '<td> - </td>'
        . '<td> - </td>';
    }
    echo '</tr>';
    $n++;
}
echo '</tbody></table>';
