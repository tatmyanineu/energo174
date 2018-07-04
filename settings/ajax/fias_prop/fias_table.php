<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../../db_config.php';
$date = date('Y-m-d');
session_start();

$sql_fias = pg_query('SELECT plc, fias, cdog, id FROM fias_cnt');
$fias = pg_fetch_all($sql_fias);


$sql = pg_query('SELECT DISTINCT 
                "Tepl"."Places_cnt".plc_id,
                "Tepl"."Places_cnt"."Name",
                "Tepl"."Places_cnt"."Comment",
                "Places_cnt1"."Name"
              FROM
                "Tepl"."Places_cnt"
                INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
              WHERE
                "Tepl"."Places_cnt".typ_id = 17
              ORDER BY
                "Tepl"."Places_cnt"."Name"');


$sql_street = pg_query('SELECT DISTINCT 
                "Tepl"."PropPlc_cnt"."ValueProp",
                "Tepl"."PropPlc_cnt".plc_id
              FROM
                "Tepl"."PropPlc_cnt"
              WHERE
                "Tepl"."PropPlc_cnt".prop_id = 27');
$streets = pg_fetch_all($sql_street);

$sql_house = pg_query('SELECT DISTINCT 
                "Tepl"."PropPlc_cnt"."ValueProp",
                "Tepl"."PropPlc_cnt".plc_id
              FROM
                "Tepl"."PropPlc_cnt"
              WHERE
                "Tepl"."PropPlc_cnt".prop_id = 26');
$houses = pg_fetch_all($sql_house);

$i = 1;
while ($row = pg_fetch_row($sql)) {

    $k = array_search($row[0], array_column($fias, 'plc'));

    $s = array_search($row[0], array_column($streets, 'plc_id'));
    $h = array_search($row[0], array_column($houses, 'plc_id'));

    if ($k !== false) {
        $arrData['data'][] = array(
            'plc' => $row[0],
            'num' => $i,
            'name' => mb_strimwidth($row[1], 0, 50, "..."),
            'dist' => $row[3],
            'adr' => $streets[$s]['ValueProp'] . ' ' . $houses[$h]['ValueProp'],
            'fias' => $fias[$k]['fias'],
            'cdog' => $fias[$k]['cdog'],
            'fias_id' => $fias[$k]['id']
        );
    } else {
        $arrData['data'][] = array(
            'plc' => $row[0],
            'num' => $i,
            'name' => mb_strimwidth($row[1], 0, 50, "..."),
            'dist' => $row[3],
            'adr' => $streets[$s]['ValueProp'] . ' ' . $houses[$h]['ValueProp'],
            'fias' => '-',
            'cdog' => '-',
            'fias_id' => NULL
        );
//        $not_fias[] = array(
//            'plc_id' => $row[0],
//            'addres' => 'Челябинск, ' . $row[3] . ', д.  ' . $row[4]
//        );
    }

    $i++;
}

$main = array();

$column['columns'] = array(
    array("title" => "№", "data" => "num"),
    array("title" => "plc_id", "data" => "plc_id"),
    array("title" => "Название", "data" => "name"),
    array("title" => "Район", "data" => "dist"),
    array("title" => "Адрес", "data" => "adr"),
    array("title" => "ФИАС", "data" => "fias"),
    array("title" => "Договор", "data" => "cdog"),
    array("title" => "", "data" => null, )
);
$main = array_merge($main, $column);
$main = array_merge($main, $arrData);
echo json_encode($main, JSON_UNESCAPED_UNICODE);
