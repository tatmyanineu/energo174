<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();
$text = $_POST['name'];
$date = date('Y-m-d');

$sql_prp = pg_query('SELECT DISTINCT 
  public.korrect.id
FROM
  public.korrect
WHERE
  public.korrect.prp_id =' . $_POST['prp_id'] . ' AND 
  public.korrect.date_record =\'' . $date . '\' ');

if (pg_num_rows($sql_prp) > 0) {
    echo "<h3>Корректировка по данному параметру сегодня уже занесена</h2>";
} else {
    $sql_max_id = pg_query('SELECT 
        MAX(public.korrect.id) AS field_1
      FROM
        public.korrect
      ');

    if (pg_fetch_result($sql_max_id, 0, 0) != NULL) {
        $id = pg_fetch_result($sql_max_id, 0, 0) + 1;
    } else {
        $id = 1;
    }

//'id_ticket=' + id_ticket + '&plc_id=' + plc + '&prp_id=' + prp_id + '&date=' + date + '&np=' + np + '&kp=' + kp,
    $add_korrect = pg_query('INSERT INTO "public"."korrect" VALUES (' . $id . ', ' . $_POST['plc_id'] . ', ' . $_POST['prp_id'] . ' , ' . $_POST['id_ticket'] . ', \'' . $_POST['date'] . '\', \'' . $_POST['np'] . '\', \'' . $_POST['kp'] . '\',  \'' . $_POST['name'] . '\' , \'' . $date . '\', \'' . $_POST['prim'] . '\')');
    //echo "Ok";
}