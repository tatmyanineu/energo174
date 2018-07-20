<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../../../db_config.php';

$sql = pg_query('SELECT id
  FROM fias_cnt 
  WHERE plc=' . $_POST['plc'] . '');

if (pg_num_rows($sql) > 0) {
    pg_query('UPDATE fias_cnt SET fias=\'' . $_POST['fias'] . '\', cdog=\'' . $_POST['cdog'] . '\' WHERE plc=' . $_POST['plc'] . '');
} else {
    pg_query('INSERT INTO fias_cnt(fias, plc, cdog)  VALUES (\'' . $_POST['fias'] . '\', ' . $_POST['plc'] . ' ,\'' . $_POST['cdog'] . '\' );');
}

echo "Запись обновлена";
