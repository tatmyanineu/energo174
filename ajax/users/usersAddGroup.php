<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../../db_config.php';
session_start();

$user = $_POST['users'];
$id = $_POST['id'];
$log =0;
if (count($user) == 0) {
    echo "Не выбран ни один пользователь";
    //$log=1;
} else {
    while ($usr = current($user)) {
//        echo key($user) . ' \\n\r';
        $sql = pg_query('SELECT DISTINCT *
                        FROM
                          "Tepl"."GroupToUserRelations"
                        WHERE
                          "Tepl"."GroupToUserRelations".usr_id = '.$usr.' AND 
                          "Tepl"."GroupToUserRelations".grp_id = '.$id.'');
        if(pg_num_rows($sql)!=0){
            //echo key($user).' уже состоит в данной группе';
        }else{
            $sql_add= pg_query('INSERT INTO "Tepl"."GroupToUserRelations"(grp_id, usr_id)
                               VALUES (' . $id . ', ' . $usr . ')');

        }
        
        next($user);
    }
}

