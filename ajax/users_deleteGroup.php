<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../db_config.php';
session_start();


$sql_usr = pg_query('SELECT DISTINCT 
            "Tepl"."GroupToUserRelations".grp_id,
            "Tepl"."GroupToUserRelations".usr_id
          FROM
            "Tepl"."GroupToUserRelations"
          WHERE
            "Tepl"."GroupToUserRelations".grp_id = ' . $_POST['id'] . '');

$sql_obj = pg_query('SELECT DISTINCT *
        FROM
          "Tepl"."PlaceGroupRelations"
        WHERE
          "Tepl"."PlaceGroupRelations".grp_id = ' . $_POST['id'] . '');

if (pg_num_rows($sql_usr) > 0) {
    $sql_delUserGroup = pg_query('DELETE FROM "Tepl"."GroupToUserRelations"
            WHERE "Tepl"."GroupToUserRelations".grp_id = ' . $_POST['id'] . '');
}
if (pg_num_rows($sql_obj) > 0) {
    $sql_delObjectGroup = pg_query('DELETE FROM "Tepl"."PlaceGroupRelations"
            WHERE "Tepl"."PlaceGroupRelations".grp_id = ' . $_POST['id'] . '');
}

$sql_delGroup = pg_query('DELETE FROM "Tepl"."UserGroups" WHERE grp_id = ' . $_POST['id'] . '');
