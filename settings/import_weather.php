<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//session_start();
//include '../db_config.php';

$conn = pg_connect("host=localhost port=5432 dbname=FondEnergo user=postgres password=postgres");
$array = file_get_contents('http://api.openweathermap.org/data/2.5/weather?q=Chelyabinsk&appid=4013f05863f5e92905cbd720a5185d9a&lang=ru&units=metric');
$array = json_decode($array, TRUE);
//var_dump($array);
echo $array['main']['temp'];

$sql_max_date = pg_query('SELECT date_cnt FROM public.weather_time ORDER BY id desc LIMIT 1');
$max_date = date("Y-m-d", strtotime(pg_fetch_result($sql_max_date, 0, 0)));
$sql_count = pg_query('SELECT id FROM public.weather_time');
$k = pg_num_rows($sql_count);
$date = date('Y-m-d H:00:00', strtotime('-1 hour'));
$date_now = date('Y-m-d', strtotime('+1 hour'));

$file = fopen("log.txt", "a+");


if ($k == 0) {
    $id = 0;
    if ($array['main']['temp'] == "" or $array['main']['temp'] == NULL) {
         $str_file = "ОШИБКА  нет значения температуры  date_now =" . strtotime($date_now) . "\n";
    } else {
        $sql_add_temp = pg_query('INSERT INTO public.weather_time(id, temp_now, date_cnt) VALUES (' . $id . ', \'' . $array['main']['temp'] . '\', \'' . $date . '\')');
        $str_file = "Пишем в базу weather_time -> id =  " . $k . " T = " . $array['main']['temp'] . "  D=" . $date . " max_date = " . strtotime($max_date) . " date_now =" . strtotime($date_now) . "\n";
        fwrite($file, $str_file . "\r\n");
    }
} else {
    if (strtotime($date_now) <= strtotime($max_date)) {
        if ($k != 0) {
            $sql_max_id = pg_query('SELECT MAX(id) FROM public.weather_time');
            $id = pg_fetch_result($sql_max_id, 0, 0);
            $id++;
            if ($array['main']['temp'] == "" or $array['main']['temp'] == NULL) {
                $str_file = "ОШИБКА  нет значения температуры  date_now =" . strtotime($date_now) . "\n";
            } else {
                $sql_add_temp = pg_query('INSERT INTO public.weather_time(id, temp_now, date_cnt) VALUES (' . $id . ', \'' . $array['main']['temp'] . '\', \'' . $date . '\')');
                $str_file = "Пишем в базу weather_time -> id =  " . $k . " T = " . $array['main']['temp'] . "  D=" . $date . " max_date = " . strtotime($max_date) . " date_now =" . strtotime($date_now) . "\n";
                fwrite($file, $str_file . "\r\n");
            }
        }
    } else {
        $sql_max_id = pg_query('SELECT MAX(id) FROM public.weather_time');
        $id = pg_fetch_result($sql_max_id, 0, 0);
        $id++;
        if ($array['main']['temp'] == "" or $array['main']['temp'] == NULL) {
            $str_file = "ОШИБКА  нет значения температуры  date_now =" . strtotime($date_now) . "\n";
        } else {
            $sql_add_temp = pg_query('INSERT INTO public.weather_time(id, temp_now, date_cnt) VALUES (' . $id . ', \'' . $array['main']['temp'] . '\', \'' . $date . '\')');
        }
        $k++;

        $str_file = "Пишем в базу weather_time -> id =  " . $k . " T = " . $array['main']['temp'] . "  D=" . $date . " max_date = " . strtotime($max_date) . " date_now =" . strtotime($date_now) . "\n";
        fwrite($file, $str_file . "\r\n");


        $sql_temp = pg_query('SELECT SUM(CAST(public.weather_time.temp_now AS float8)) FROM public.weather_time');
        $summ = pg_fetch_result($sql_temp, 0, 0);
        $summ = $summ / $k;

        //$date = date('Y-m-d 00:00:00');
        $sql_count_arch = pg_query('SELECT id FROM public.weather_archive');
        $k_arch = pg_num_rows($sql_count_arch);
        if ($k_arch != 0) {
            $sql_max_id = pg_query('SELECT MAX(id) FROM public.weather_archive');
            $id = pg_fetch_result($sql_max_id, 0, 0);
            $id++;
            $add_temp_arch = pg_query('INSERT INTO public.weather_archive (id, date_cnt, temper) VALUES (' . $id . ',\'' . $date . '\' , \'' . $summ . '\')');
            $str_file = "Пишем в базу weather_archive -> id =  " . $id . "  D=" . $date . " summ= " . $summ . "\n";
            fwrite($file, $str_file . "\r\n");
        } else {
            $id = 0;
            $add_temp_arch = pg_query('INSERT INTO public.weather_archive (id, date_cnt, temper) VALUES (' . $id . ', \'' . $date . '\', \'' . $summ . '\')');
            $str_file = "Пишем в базу weather_archive -> id =  " . $id . "  D=" . $date . " summ= " . $summ . "\n";
            fwrite($file, $str_file . "\r\n");
        }

        $del_temp_now = pg_query('TRUNCATE TABLE public.weather_time');
        $str_file = "грохаем базу weather_time чтобы пистаь следующий день\n";
        fwrite($file, $str_file . "\r\n");
    }
}