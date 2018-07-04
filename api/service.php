<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../db_config.php';
include './client.php';
$id_array=array('id'=>'4');
echo json_encode($client->getName($id_array)); 

