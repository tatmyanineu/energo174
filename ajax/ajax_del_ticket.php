<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();


$id_ticket = $_POST['id_ticket'];

$status = 5;
$delete_tickets = pg_query('UPDATE ticket SET date_close = \''.date('Y-m-d').'\', status = ' . $status . ' WHERE id = ' . $id_ticket . '');
