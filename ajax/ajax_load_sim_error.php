<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../db_config.php';

//var_dump($_POST['paramName']);
$data = $_POST['paramName'];
echo "<h3>В файле найдено " . count($data) . " записей</h3>";


$sql_sim_plc = pg_query('SELECT * FROM public."SimPlace_cnt"');
while ($resul = pg_fetch_row($sql_sim_plc)) {
    $sim[]  = $resul[2];
}

$a = $sim[0];
$b =$data[0];

if(iconv("ASCII", "UTF-8", $a)==iconv("ASCII", "UTF-8", $b)){
    echo "ok";
}


for ($i = 0; $i < count($data); $i++) {
    for($j=0;$j<count($sim);$j++){
        if((string)$data[$i] == (string)$sim[$j]){
            echo "Совпадение найдено";
        }
    }
}