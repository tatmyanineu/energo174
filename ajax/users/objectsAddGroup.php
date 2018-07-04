<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var_dump($_POST['objects']);

include '../../db_config.php';
session_start();

$objects = $_POST['objects'];
$id = $_POST['id'];
$log = 0;
if (count($objects) == 0) {
    echo "Не выбран ни один объект";
    //$log=1;
} else {
    while ($obj = current($objects)) {
//        echo key($user) . ' \\n\r';
        $sql = pg_query('SELECT DISTINCT *
                FROM
                  "Tepl"."PlaceGroupRelations"
                WHERE
                  "Tepl"."PlaceGroupRelations".grp_id = ' . $id . ' AND 
                  "Tepl"."PlaceGroupRelations".plc_id =' . $obj . '');
        if (pg_num_rows($sql) != 0) {
            //echo key($user).' уже состоит в данной группе';
        } else {
            $sql_add = pg_query('INSERT INTO "Tepl"."PlaceGroupRelations"(
                    plc_id, grp_id) VALUES (' . $obj . ', ' . $id . ')');
        }

        next($objects);
    }
}