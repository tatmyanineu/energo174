<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//set_time_limit(0);
//$fp = fopen ('./a.xml', 'w+');
//$ch = curl_init('http://localhost/pulsar_form/fortum/archive/export.xml');// or any url you can pass which gives you the xml file
//curl_setopt($ch, CURLOPT_TIMEOUT, 50);
//curl_setopt($ch, CURLOPT_FILE, $fp);
//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//curl_exec($ch);
//curl_close($ch);
//fclose($fp);

$file = $_GET['file'];
header('Content-disposition: attachment; filename='.$file.'');
header ("Content-Type:text/xml"); 
//output the XML data
readfile('archive/export_2018-02-19-2018-02-20.xml');
 // if you want to directly download then set expires time
header("Expires: 0");