<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../db_config.php';

$date = date('Y-m-d');
$id_ticket = $_POST['id_ticket'];
$text = $_POST['result'];
if ($_POST['file'] == 'object') {
     pg_query('UPDATE ticket SET text_ticket= \'' . $_POST['comment'] . '\', status = ' . $_POST['status'] . ' WHERE id = ' . $id_ticket . '');
} else {
    $status = 4;
    if ($text != "") {
        pg_query('UPDATE ticket SET close_text=\'' . $text . '\', date_close = \'' . $date . '\', status = ' . $status . ' WHERE id = ' . $id_ticket . '');
    } else {
        //pg_query('UPDATE tiket SET close_date = \'\', close_text=\'\'  WHERE id=' . $id_ticket . '');
    }
}