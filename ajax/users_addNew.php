<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../db_config.php';
session_start();

$login = $_POST['login'];
$passwd = $_POST['passwd'];
$group = $_POST['group'];
$sqlMaxId = pg_query('SELECT max(usr_id)
  FROM "Tepl"."User_cnt"');
$id = pg_fetch_result($sqlMaxId, 0, 0);
$id++;
$sql_addUser = pg_query('INSERT INTO "Tepl"."User_cnt"(usr_id, "Login", "Password", "SurName", "PatronName", "Comment", prof_id, "Privileges")
            VALUES ('.$id.', \''.$login.'\', \''.$passwd.'\', \''.$_POST['surname'].'\', \''.$_POST['name'].'\', \'\', 0, '.$_POST['role'].')');


$sqlAddUserinGroup = pg_query('INSERT INTO "Tepl"."GroupToUserRelations"(grp_id, usr_id)
                               VALUES (' . $group . ', ' . $id . ')');
