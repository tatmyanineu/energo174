<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../db_config.php';


$plc_id = $_POST['plc_id'];
$dev_id = $_POST['dev_id'];
$dev_type_id = $_POST['dev_type_id'];
$number = $_POST['number'];
$comm = $_POST['comm'];
if (strtotime($_POST['date']) == strtotime(date('1970-01-01'))) {
    $date_pov = '';
} else {
    $date_pov = date('Y-m-d', strtotime($_POST['date']));
}
$id_type_prop = 0;



$sql_edit_come = pg_query('
  UPDATE 
  	"Tepl"."Device_cnt" 
  SET 
  	"Comment" =\'' . $comm . '\'
  WHERE 
  	"Tepl"."Device_cnt".plc_id = ' . $plc_id . ' AND
  	"Tepl"."Device_cnt".dev_typ_id = ' . $dev_type_id . '');


//обработка даты комплекта тепловычислителя

$sql_prp_id = pg_query('SELECT 
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Sensor_cnt".sen_id
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Sensor_cnt".prp_id)
  INNER JOIN "Tepl"."TypeSensor" ON ("Tepl"."Sensor_cnt".sen_id = "Tepl"."TypeSensor".sen_id)
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
WHERE
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" != 9 AND 
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $plc_id . ' AND 
  "Tepl"."Resourse_cnt"."Name" = \'Тепло\'
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."ParametrResourse"."Name"');

while ($row = pg_fetch_row($sql_prp_id)) {
    $sql_sens_prop = pg_query('SELECT
    "Tepl"."Sensor_Property"."Propert_Value",
    "Tepl"."Sensor_Property".prop_id
    FROM
    "Tepl"."Sensor_Property"
    INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."Sensor_Property".s_id = "Tepl"."Sensor_cnt".s_id)
    WHERE
    "Tepl"."Sensor_cnt".prp_id = ' . $row[0] . ' AND
    "Tepl"."Sensor_Property".id_type_property = 2
    ORDER BY
    "Tepl"."Sensor_Property".id_type_property');
    if (pg_num_rows($sql_sens_prop) != 0) {
        $sql_update_pov = pg_query('UPDATE
                "Tepl"."Sensor_Property"
            SET
                "Propert_Value" = \'' . $date_pov . '\'
            WHERE
                "Tepl"."Sensor_Property".prop_id = ' . pg_fetch_result($sql_sens_prop, 0, 1) . '');
    } else {
        //Добавление новых дат в таблицу
        $sql_s_id = pg_query('
            SELECT 
              "Tepl"."Sensor_cnt".s_id
            FROM
              "Tepl"."Sensor_cnt"
            WHERE
              "Tepl"."Sensor_cnt".prp_id  =' . $row[0] . '');
        $s_id = pg_fetch_result($sql_s_id, 0, 0);
        //$s_id = $s_id + 1;
        //$sql_add_sens = pg_query('INSERT INTO  "Tepl"."Sensor_cnt" VALUES (' . $s_id . ', ' . $row[1] . ', \'false\', \'\', ' . $row[0] . ')');

        $sql_max_prop_id = pg_query('SELECT 
            MAX("Tepl"."Sensor_Property".prop_id) AS field_1
          FROM
            "Tepl"."Sensor_Property"');
        $prop_id = pg_fetch_result($sql_max_prop_id, 0, 0);
        $prop_id = $prop_id + 1;

        $sql_add_sens_date = pg_query('INSERT INTO "Tepl"."Sensor_Property" VALUES (' . $prop_id . ', \'' . $date_pov . '\', ' . $s_id . ',2)');
    }
}


//обработка даты комплекта тепловычислителя









$sql_dev_prop_id = pg_query('
SELECT 
  "Tepl"."Device_Property".prop_id,
  "Tepl"."Device_Property".id_type_property
FROM
  "Tepl"."Device_Property"
WHERE
  "Tepl"."Device_Property".dev_id = ' . $dev_id . '
ORDER BY
  "Tepl"."Device_Property".id_type_property    
');

if (pg_fetch_result($sql_dev_prop_id, 0, 1) == 0) {
    $dev_prop_id = pg_fetch_result($sql_dev_prop_id, 0, 0);
    $dev_prop_date_pov = pg_fetch_result($sql_dev_prop_id, 1, 0);
} elseif (pg_fetch_result($sql_dev_prop_id, 0, 1) == 2) {
    $dev_prop_id = pg_fetch_result($sql_dev_prop_id, 1, 0);
    $dev_prop_date_pov = pg_fetch_result($sql_dev_prop_id, 0, 0);
}

//$dev_prop_id = pg_fetch_result($sql_dev_prop_id, 0, 0);
//$dev_prop_date_pov = pg_fetch_result($sql_dev_prop_id, 1, 0);
if ($dev_prop_id != '' and $dev_prop_date_pov != '') {
    $sql_update_sens = pg_query('
        UPDATE 
           "Tepl"."Device_Property"
        SET 
            "Propert_Value"= \'' . $number . '\'
        WHERE 
           "Tepl"."Device_Property".dev_id= ' . $dev_id . ' and
            "Tepl"."Device_Property".prop_id = ' . $dev_prop_id . ' ');
    $sql_update_sens = pg_query('
        UPDATE 
           "Tepl"."Device_Property"
        SET 
            "Propert_Value"= \'' . $date_pov . '\'
        WHERE 
           "Tepl"."Device_Property".dev_id= ' . $dev_id . ' and
            "Tepl"."Device_Property".prop_id = ' . $dev_prop_date_pov . ' ');
} else {
    $sql_max_dev_prop_id = pg_query('SELECT 
        MAX("Tepl"."Device_Property".prop_id) AS field_1
      FROM
        "Tepl"."Device_Property"');
    $dev_prop_id_new = pg_fetch_result($sql_max_dev_prop_id, 0, 0);
    $dev_prop_id_new = $dev_prop_id_new + 1;

    if ($dev_prop_id != false and $dev_prop_date_pov == false) {
        if ($date_pov != '') {
            $sql_add_dev_prop = pg_query(' INSERT INTO "Tepl"."Device_Property" VALUES (' . $dev_prop_id_new . ', \'' . $date_pov . '\', ' . $dev_id . ', 2)');
        }
        $sql_update_sens = pg_query('
        UPDATE 
           "Tepl"."Device_Property"
        SET 
            "Propert_Value"= \'' . $number . '\'
        WHERE 
           "Tepl"."Device_Property".dev_id= ' . $dev_id . ' and
            "Tepl"."Device_Property".prop_id = ' . $dev_prop_id . ' ');
        $n++;
    } elseif ($dev_prop_id == '' and $dev_prop_date_pov != '') {
        if ($number != '') {
            $sql_add_dev_prop = pg_query(' INSERT INTO "Tepl"."Device_Property" VALUES (' . $dev_prop_id_new . ', \'' . $number . '\', ' . $dev_id . ', 0)');
        }
        $sql_update_sens = pg_query('
        UPDATE 
           "Tepl"."Device_Property"
        SET 
            "Propert_Value"= \'' . $date_pov . '\'
        WHERE 
           "Tepl"."Device_Property".dev_id= ' . $dev_id . ' and
            "Tepl"."Device_Property".prop_id = ' . $dev_prop_date_pov . ' ');

        $n++;
    } else {
        if ($number != '') {
            $sql_add_dev_prop = pg_query(' INSERT INTO "Tepl"."Device_Property" VALUES (' . $dev_prop_id_new . ', \'' . $number . '\', ' . $dev_id . ', 0)');
            $dev_prop_id_new++;
        }
        if ($date_pov != '') {
            $sql_add_dev_prop = pg_query(' INSERT INTO "Tepl"."Device_Property" VALUES (' . $dev_prop_id_new . ', \'' . $date_pov . '\', ' . $dev_id . ', 2)');
        }
        $n++;
    }

    //$sql_add_dev_prop = pg_query(' INSERT INTO "Tepl"."Device_Property" VALUES (' . $dev_prop_id . ', \'' . $number . '\', ' . $dev_id . ', 0)');
}