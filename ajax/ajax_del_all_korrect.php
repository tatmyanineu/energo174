<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();
$id = $_POST['id'];
$date = date('Y-m-d');

$add_table = pg_query('DELETE FROM public.korrect WHERE id_tick= ' . $id . ' and date_record = \'' . $date . '\'');
echo "<h3>Удалено</h3>";
