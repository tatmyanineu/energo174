<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../../../db_config.php';

$sql = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."Places_cnt"."Name",
  public.fias_cnt.fias,
  public.fias_cnt.cdog
FROM
  public.fias_cnt
  RIGHT OUTER JOIN "Tepl"."Places_cnt" ON (public.fias_cnt.plc = "Tepl"."Places_cnt".plc_id)
WHERE
  "Tepl"."Places_cnt".typ_id = 17');

$f = pg_fetch_all($sql);

$sql_rpr = pg_query('SELECT 
  "Tepl"."ParamResPlc_cnt".plc_id,
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParametrResourse"."Name"
FROM
  "Tepl"."ParamResPlc_cnt"
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
WHERE
  "Tepl"."Resourse_cnt"."Name" NOT ILIKE \'%тепло%\'');

while ($row = pg_fetch_row($sql_rpr)) {
    $k = array_search($row[0], array_column($f, 'plc_id'));

    $m[] = array(
        'plc' => $f[$k]['plc_id'],
        'name' => $f[$k]['Name'],
        'fias' => $f[$k]['fias'],
        'cdog' => $f[$k]['cdog'],
        'prp' => $row[1],
        'res' => $row[2] . ": " . $row[3]
    );
}
var_dump($m);


for($i=0;i<count($m);$i++){
    $sql_prop = pg_query('SELECT DISTINCT 
              public.prop_connect.id,
              public.prop_connect.prp_id,
              public.prop_connect.id_connect,
              public.prop_connect.date,
              public.prop_connect.cnt_numb,
              public.prop_connect.plc_id
            FROM
              public.prop_connect
            WHERE
              public.prop_connect.plc_id = ' . $m[$i]['prp']);
    if (pg_num_rows($sql_prop) != 0) {
        while ($row_prop = pg_fetch_row($sql_prop)) {
            $a['data'][] = array(
                'plc' => $m[$i]['plc'],
                'name' => mb_strimwidth($m[$i]['name'], 0, 50, "..."),
                'fias' => $m[$i]['fias'],
                'cdog' => $m[$i]['cdog'],
                'prp' => $m[$i]['prp'],
                'conn' => $row_prop[2],
                'date' => date("d.m.Y", strtotime($row_prop[3])),
                'numb' => $row_prop[4],
            );
        }
    }else{
        $a['data'][] = array(
            'plc' => $m[$i]['plc'],
            'name' => mb_strimwidth($m[$i]['name'], 0, 50, "..."),
            'fias' => $m[$i]['fias'],
            'cdog' => $m[$i]['cdog'],
            'prp' => $m[$i]['prp'],
            'conn' => $row_prop[2],
            'date' => date("d.m.Y", strtotime($row_prop[3])),
            'numb' => $row_prop[4],
        );
    }

}


$column['columns'] = array(
    array("title" => "plc_id", "data" => "plc"),
    array("title" => "Название", "data" => "name"),
    array("title" => "ФИАС", "data" => "fias"),
    array("title" => "Договор", "data" => "cdog"),
    array("title" => "prp_id", "data" => "prp"),
    array("title" => "Номер подключения", "data" => "conn"),
    array("title" => "Дата установки", "data" => "date"),
    array("title" => "Номер ПУ", "data" => "numb")
);


$main = array();
$main = array_merge($main, $column);
$main = array_merge($main, $a);
//$main = array_merge($main, $p);
//var_dump($main);
echo json_encode($main, JSON_UNESCAPED_UNICODE);
