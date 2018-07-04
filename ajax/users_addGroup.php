<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../db_config.php';
session_start();

$name = $_POST['name'];
$comment = $_POST['comment'];

$sqlMaxId = pg_query('SELECT max(grp_id) FROM "Tepl"."UserGroups"');
$id = pg_fetch_result($sqlMaxId, 0, 0);
$id++;

$sqlAddGroup = pg_query('INSERT INTO "Tepl"."UserGroups"(grp_id, "Name", "Comment")
                        VALUES ('.$id.', \''.$name.'\', \''.$comment.'\')');

echo 'Группа '.$name.' бала успешна добавлена, чтобы приступить к редактированию группы <a href="group_view.php?gr_id='.$id.'">Передите по ссылке</a>';