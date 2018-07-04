<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../include/db_config.php';

$sql_object = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt".plc_id
FROM
  "Tepl"."PropPlc_cnt"
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PropPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27');


$sql_count = pg_query('SELECT count (id) FROM fortum_plc');

if (pg_fetch_result($sql_count, 0, 0) > 0) {
    $sql = pg_query('TRUNCATE fortum_plc');
    while ($row = pg_fetch_row($sql_object)) {
        $ar[] = $row[0];
        $sql = pg_query('INSERT INTO fortum_plc (plc_id, exception) VALUES (' . $row[0] . ', 1)');
    }
} else {
    while ($row = pg_fetch_row($sql_object)) {
        $ar[] = $row[0];
        $sql = pg_query('INSERT INTO fortum_plc (plc_id, exception) VALUES (' . $row[0] . ', 1)');
    }
}

