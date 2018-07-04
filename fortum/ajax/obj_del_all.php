<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../include/db_config.php';

$sql = pg_query('TRUNCATE fortum_plc');