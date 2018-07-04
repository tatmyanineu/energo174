<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();

$id_object = $_POST['plc_id'];
$comment = $_POST['comment'];
$date = date('Y-m-d');
$status = $_POST['status'];


$sql_max_id_ticket = pg_query('SELECT 
  MAX(public.ticket.id) AS field_1
FROM
  public.ticket');

$id = pg_fetch_result($sql_max_id_ticket, 0, 0);

if ($id == NULL) {
    $id = 1;
    $sql_add_ticket = pg_query('INSERT INTO "public"."ticket" VALUES (' . $id . ', ' . $id_object . ', \'' . $date . '\', \'' . $comment . '\' , ' . $status . ' , \'\', \'\', \''.$_SESSION['login'].'\') ');
} else {
    $id = $id + 1;
    $sql_add_ticket = pg_query('INSERT INTO "public"."ticket" VALUES (' . $id . ', ' . $id_object . ', \'' . $date . '\', \'' . $comment . '\' , ' . $status . ' , \'\', \'\', \''.$_SESSION['login'].'\') ');
}
echo "Заявка добавлена";

