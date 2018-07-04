<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$_POST['plc_id'];
//$_POST['addres'];
session_start();
include '../../../db_config.php';
include '../../../include/DadataClient.php';

use Dadata\DadataClient as DadataClient;

$url = 'https://dadata.ru/api/v2/clean/address';
$token = '53fc9131f2d3559ad88fd3afab8fb9d739a011e9';
$secret = '92b0a5016511c3357d98b2f892e5b3851d0d2fa0';
$dadata = new DadataClient($url, $token, $secret);
$data = array(
    "structure" => array("ADDRESS"),
    "data" => array(array("" . $_POST['adress'] . ""))
);
//echo "\nRequest:\n";
var_dump($data);
//echo "Response:\n";
$resp = json_decode($dadata->clean($data), true);
//var_dump($resp);
//echo $resp[0][house_fias_id];
$add_fias = pg_query('INSERT INTO fias_cnt(fias, plc) VALUES (\'' . $resp[0][house_fias_id] . '\', ' . $_POST['plc_id'] . ')');
