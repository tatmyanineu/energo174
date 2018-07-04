<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../db_config.php';
session_start();
$id = $_POST['id'];


$add_table = pg_query('DELETE FROM public.alarm WHERE id=' . $id . '');
echo "<h3>Удалено</h3>";

