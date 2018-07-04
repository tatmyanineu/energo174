<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../db_config.php';


function Error($p1, $p2) {
    exit('{"error":' . $p1 . ', "text":"' . $p2 . '"}');
}

if ($Module == 'users') {
    if (!$Param['login'])
        Error(1, 'Не указан логин пользователя');

    $Param['login'] = FormChars($Param['login']);

    $array = array('usr_id');

    $Exp = explode('.', $Param['param']);

    foreach ($Exp as $key)
        if ($Param != 'all' and ! in_array($key, $array))
            Error(3, "Параметр указан неверно");

    if ($Param != 'all')
        $Select = $array;
    else
        $Select = $Exp;


    foreach ($Select as $key)
        $SQL .= "$key,";
    $SQL = substr($sql, 0, -1);
    
   echo pg_fetch_row(pg_query('SELECT '.$sql.' FROM "Tepl"."User_cnt" WHERE Login=\''.$Param['login'].'\''), JSON_UNESCAPED_UNICODE);
    
}else {
    Error(0, "Метод не указан");
}