<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../../db_config.php';
session_start();

$teplo = $_POST['arr1'];
$voda = $_POST['arr2'];


for ($i = 0; $i <= count($teplo); $i++) {
    if (is_float((float) $teplo[$i]['val']) or is_int((int) $teplo[$i]['val'])) {
        $sql_update_limit = pg_query('UPDATE public."LimitMonth_cnt" SET teplo=' . $teplo[$i]['val'] . ' WHERE id=' . $teplo[$i]['id'] . '');
    } else {
        echo "<h3>Значение не было записано т.к. не является числом</h3>";
    }
}

for ($i = 0; $i <= count($voda); $i++) {
    if (is_float((float) $voda[$i]['val']) or is_int((int) $voda[$i]['val'])) {
        $sql_update_limit = pg_query('UPDATE public."LimitMonth_cnt" SET voda=' . $voda[$i]['val'] . ' WHERE id=' . $voda[$i]['id'] . '');
    } else {
        echo "<h3>Значение не было записано т.к. не является числом</h3>";
    }
}