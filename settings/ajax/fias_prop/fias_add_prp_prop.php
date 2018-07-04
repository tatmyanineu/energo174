<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../../../db_config.php';

$sql_prp = pg_query('SELECT id, prp_id, id_connect, date, cnt_numb, plc_id
  FROM prop_connect');
$prp = pg_fetch_all($sql_prp);
$json = $_POST['json'];
$kol;
for ($i = 0; $i < count($json); $i++) {
    $k = array_search($json[$i]['prp'], array_column($prp, 'prp_id'));
    if ($k !== false) {
        $sql = pg_query('UPDATE prop_connect SET id_connect=\''.$json[$i]['id_connect'].'\', date=\''.$json[$i]['date'].'\', cnt_numb=\''.$json[$i]['numb'].'\'
                        WHERE id='.$prp[$k]['id']);
    } else {
        //update
        //insert
        $sql = pg_query('INSERT INTO prop_connect(prp_id, id_connect, date, cnt_numb, plc_id)
                  VALUES (' . $json[$i]['prp'] . ', \'' . $json[$i]['id_connect'] . '\', \'' . $json[$i]['date'] . '\', \'' . $json[$i]['numb'] . '\', ' . $json[$i]['plc'] . ')');

        $kol++;
    }
}

echo " Добавлено $kol записей";
