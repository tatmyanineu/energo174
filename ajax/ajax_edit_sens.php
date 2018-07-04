<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../db_config.php';

$id_type_prop = 0;
$prp_id = $_POST['prp_id'];
$diametr = $_POST['comm'];
$number = $_POST['number'];
$sen_id = $_POST['sen_id'];
$place = "";



$sql_update_diametr = pg_query('
UPDATE 
    "Tepl"."ParamResPlc_cnt"
SET 
    "Comment" =\'' . $_POST['comm'] . '\'
WHERE 
  "Tepl"."ParamResPlc_cnt".prp_id =' . $_POST['prp_id'] . '
');


$sql_s_id = pg_query('
SELECT 
  "Tepl"."Sensor_cnt".s_id
FROM
  "Tepl"."Sensor_cnt"
WHERE
  "Tepl"."Sensor_cnt".prp_id  =' . $_POST['prp_id'] . '');
$s_id = pg_fetch_result($sql_s_id, 0, 0);

if ($s_id != '') {


    $sql_update_sens = pg_query('
        UPDATE 
            "Tepl"."Sensor_cnt"
        SET 
            sen_id =' . $sen_id . '
        WHERE 
            "Tepl"."Sensor_cnt".s_id= ' . $s_id . '');
    $sql1 = "UPDATE";
    $sql_prop_id = pg_query('SELECT 
            "Tepl"."Sensor_Property".prop_id
          FROM
            "Tepl"."Sensor_Property"
          WHERE
            "Tepl"."Sensor_Property".s_id = ' . $s_id . ' AND 
            "Tepl"."Sensor_Property".id_type_property = 0');
    $prop_id = pg_fetch_result($sql_prop_id, 0, 0);
    if ($prop_id != '') {
        if ($number != "") {
            $sql_update_number = pg_query('UPDATE
                "Tepl"."Sensor_Property"
            SET
                "Propert_Value" = \'' . $number . '\'
            WHERE
                "Tepl"."Sensor_Property".prop_id = ' . $prop_id . '');
        }
    } else {
        if ($number != "") {
            $sql_max_prop_id = pg_query('SELECT 
            MAX("Tepl"."Sensor_Property".prop_id) AS field_1
          FROM
            "Tepl"."Sensor_Property"');
            $prop_id = pg_fetch_result($sql_max_prop_id, 0, 0);
            $prop_id = $prop_id + 1;
            $sql_add_sens_numb = pg_query('INSERT INTO "Tepl"."Sensor_Property" VALUES (' . $prop_id . ', \'' . $number . '\', ' . $s_id . ',0)');
        }
    }
} else {
    $sql_max_s_id = pg_query('
    SELECT 
        MAX("Tepl"."Sensor_cnt".s_id) AS field_1
    FROM
        "Tepl"."Sensor_cnt"
    ');
    $s_id = pg_fetch_result($sql_max_s_id, 0, 0);
    $s_id = $s_id + 1;

    $sql1 = "INSERT";
    $sql_add_sens = pg_query('INSERT INTO  "Tepl"."Sensor_cnt" VALUES (' . $s_id . ', ' . $sen_id . ', \'false\', \'\', ' . $prp_id . ')');

    if ($number != "") {
        $sql_max_prop_id = pg_query('SELECT 
            MAX("Tepl"."Sensor_Property".prop_id) AS field_1
          FROM
            "Tepl"."Sensor_Property"');
        $prop_id = pg_fetch_result($sql_max_prop_id, 0, 0);
        $prop_id = $prop_id + 1;
        $sql_add_sens_numb = pg_query('INSERT INTO "Tepl"."Sensor_Property" VALUES (' . $prop_id . ', \'' . $number . '\', ' . $s_id . ',0)');
    }
}

$sql_max_id_log = pg_query('SELECT 
        MAX(public.logs.id) AS field_1
      FROM
        public.logs');

$id_log = pg_fetch_result($sql_max_id_log, 0, 0);
$id_log = $id_log + 1;
$sql_add_log = pg_query('INSERT INTO  "public"."logs" VALUES (' . $id_log . ',' . $_POST['plc_id'] . ', \'' . $_SESSION['login'] . '\' , ' . $prp_id  . ', \'' . preg_replace('/[;"\']/', '', $diametr) . '\', ' . $sen_id . ', \'\', \'' . $number . '\', \''.$sql1.'\' , \'' . date("Y-m-d") . '\')');
/*
      if (($fp = fopen("log.csv", 'a')) !== FALSE) {
      fputs($fp, "" . $_POST['plc_id'] . ""); //id учереждения
      fputs($fp, ";");
      fputs($fp, "" . $_SESSION['login'] . ""); //user
      fputs($fp, ";");
      fputs($fp, "" . $prp_id . ""); // id ресурса
      fputs($fp, ";");
      fputs($fp, "" . preg_replace('/[;"\']/', '', $diametr) . ""); //диаметр
      fputs($fp, ";");
      fputs($fp, "" . $sen_id . ""); // id расходомера
      fputs($fp, ";");
      fputs($fp, "" . preg_replace('/[;"\']/', '', $place) . ""); //местоположение
      fputs($fp, ";");
      fputs($fp, "" . $number . ""); //заводской номер
      fputs($fp, ";");
      fputs($fp, "" . $sql1 . ""); //действие
      fputs($fp, ";");
      fputs($fp, "" . date("d.m.Y") . ""); //Дата
      fputs($fp, ";");
      fputs($fp, "\r\n");
      }
      fclose($fp);
     * */

    