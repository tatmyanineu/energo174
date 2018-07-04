<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../db_config.php';
session_start();

$id_object = $_POST[id];

$data1 = date('' . $_POST['year'] . '-' . '01-01');
$data2 = date('' . $_POST['year'] + 1 . '-' . '02-01');


$sql_limit = pg_query('SELECT 
  public."LimitPlaces_cnt".teplo,
  public."LimitPlaces_cnt".voda
FROM
  public."LimitPlaces_cnt"
WHERE
  public."LimitPlaces_cnt".plc_id = ' . $id_object);


$sql_limit_month = pg_query('SELECT 
  public."LimitMonth_cnt".id,
  public."LimitMonth_cnt".teplo,
  public."LimitMonth_cnt".voda,
  public."LimitMonth_cnt".name
FROM
  public."LimitMonth_cnt"');

while ($result = pg_fetch_row($sql_limit_month)) {
    $lim_month[] = array(
        'id' => $result[0],
        'teplo' => (pg_fetch_result($sql_limit, 0, 0) / 100) * $result[1],
        'voda' => (pg_fetch_result($sql_limit, 0, 1) / 100) * $result[2],
        'month' => $result[3]
    );
}

//var_dump($lim_month);

$sql_all_archive = pg_query('SELECT DISTINCT 
  ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."Arhiv_cnt"."DataValue",
  "Places_cnt1".plc_id
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."ParamResPlc_cnt".plc_id)
  INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
WHERE
  "Tepl"."Arhiv_cnt".typ_arh = 3 AND 
  "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $data1 . '\' AND 
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $data2 . '\' AND 
  "Places_cnt1".plc_id = ' . $id_object . '
ORDER BY
  "Tepl"."Arhiv_cnt"."DateValue",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id"
');



while ($result = pg_fetch_row($sql_all_archive)) {
    $archive[] = array(
        'plc_id' => $result[3],
        'date' => $result[0],
        'pr_id' => $result[1],
        'value' => $result[2]
    );
}
$vte[] = array();
$year = $_POST['year'];
for ($i = 0; $i <= count($lim_month); $i++) {
    $teplo = 0;
    $date = "";
    for ($j = 0; $j < count($archive); $j++) {
        //echo (int)date('m', strtotime($archive[$j]['date']))."<br>";
        if ($lim_month[$i]['id'] == (int) date('m', strtotime($archive[$j]['date']))) {

            //echo $archive[$i]['pr_id'] . "<br>";
            if (date('Y', strtotime($archive[$j]['date'])) == $year and $lim_month[$i]['id'] != 1) {
                if ($archive[$j]['pr_id'] == 9) {
                    if ($dev_id == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
                        $vte[] = $archive[$j]['value'];
                    } else {
                        $teplo +=$archive[$j]['value'];
                        $date = $archive[$j]['date'];
                    }
                } elseif ($archive[$j]['pr_id'] == 16) {
                    $teplo += $archive[$j]['value'];
                }

                if ($archive[$j]['pr_id'] == 1) {
                    $voda[0][$i] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 308) {
                    $voda[1][$i] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 310) {
                    $voda[2][$i] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 414) {
                    $voda[3][$i] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 420) {
                    $voda[4][$i] = $archive[$j]['value'];
                }
            } elseif (date('Y', strtotime($archive[$j]['date'])) == $year and $lim_month[$i]['id'] == 1) {
                if ($archive[$j]['pr_id'] == 1) {
                    $voda[0][$i] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 308) {
                    $voda[1][$i] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 310) {
                    $voda[2][$i] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 414) {
                    $voda[3][$i] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 420) {
                    $voda[4][$i] = $archive[$j]['value'];
                }
            } elseif (date('Y', strtotime($archive[$j]['date'])) != $year and $lim_month[$i]['id'] == 1) {
                if ($archive[$j]['pr_id'] == 9) {
                    if ($dev_id == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
                        $vte[] = $archive[$j]['value'];
                    } else {
                        $teplo +=$archive[$j]['value'];
                        $date = $archive[$j]['date'];
                    }
                } elseif ($archive[$j]['pr_id'] == 16) {
                    $teplo += $archive[$j]['value'];
                }

                if ($archive[$j]['pr_id'] == 1) {
                    $voda[0][11] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 308) {
                    $voda[1][11] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 310) {
                    $voda[2][11] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 414) {
                    $voda[3][11] = $archive[$j]['value'];
                }
                if ($archive[$j]['pr_id'] == 420) {
                    $voda[4][11] = $archive[$j]['value'];
                }
            }
        }
    }
    if ($i == 0) {
        $t = $teplo;
    } else {
        $arr_teplo[] = $teplo;
    }


    // echo $lim_month[$i]['id'] . "teplo = " . $teplo . " voda = " . $val . " date =" . $date . " <br>";
}
//var_dump($arr_teplo);

$arr_teplo[11] = $t;

for ($l = 0; $l < count($voda); $l++) {
    //print_r($voda[$l])."<br>";
    $n1 = count($voda[$l]) - 1;
    $z = 0;
    for ($n = 0; $n < count($voda[$l]); $n++) {

        if ($n == $n1) {
            $z = $z;
            //$g[] = $z;
            //echo "n=" . $n . " mas = " . $voda[$l][$n] . "    z=" . $z . "  <br>";
        }
        if ($n >= 0 and $n < $n1) {
            if ($voda[$l][$n]) {
                $z = $z + $voda[$l][$n + 1] - $voda[$l][$n];
                $g[] = $voda[$l][$n + 1] - $voda[$l][$n];
            }

            //echo "n=" . $n . " mas = " . $voda[$l][$n] . "   mas+1 =  " . $voda[$l][$n + 1] . "     z=" . $z . "  <br>";
        }
    }
    $val[] = $z;
    //echo "Z ====".$z."  <br>";
}

//var_dump($g);


for ($i = 0; $i < count($lim_month); $i++) {
    $limits[] = array(
        'id' => $lim_month[$i]['id'],
        'teplo_limit' => number_format($lim_month[$i]['teplo'], 2, ',', ''),
        'teplo' => number_format($arr_teplo[$i], 2, ',', ''),
        'voda_limit' => number_format($lim_month[$i]['voda'], 2, ',', ''),
        'voda' => number_format($g[$i], 2, ',', ''),
        'name' => $lim_month[$i]['month']
    );
}
//var_dump($limits);

$sql_name_object = pg_query('SELECT DISTINCT 
  "Places_cnt1"."Name"
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
WHERE
  "Places_cnt1".plc_id = ' . $id_object . '
');


echo '<div> <h1 class="text-center">' . pg_fetch_result($sql_name_object, 0, 0) . '</h1> </div>'
 . '<div id="container" style = "width: 90%">'
 . '</div><div id="container_voda" style = "width: 90%"></div>';


echo "<script type='text/javascript'>";
echo "$(function () { "
 . " var chart = new Highcharts.Chart({ "
 . "   chart: { "
 . "      renderTo: 'container', "
 . "      type: 'column', "
 . "      options3d: { "
 . "          enabled: true, "
 . "          alpha: 0, "
 . "          beta: 2, "
 . "          depth: 63, "
 . "          viewDistance: 25 "
 . "      } "
 . "  }, "
 . " title: {  "
 . " text: 'Помесячный график расхода тепла за " . $_POST['year'] . " г.' "
 . " }, colors: ['#ef3038', '#50B432'], "
 . " subtitle: {  "
 . " text: ''  "
 . " }, yAxis: {title: {text: 'Теп. энергия'}}, xAxis: {  "
 . " categories: [ ";
//."'Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas'"
$name = '';
for ($i = 0; $i < count($limits); $i++) {
    $name .= "'" . $limits[$i]['name'] . "', ";
}
echo "" . $name . "]  "
 . " }, "
 . " plotOptions: { "
 . "column: { "
 . "depth: 25 "
 . "},"
 . "series: {
                borderWidth: 0,
                dataLabels: {
                    rotation: -90,
                    enabled: true,
                    format: '{point.y:.1f} Гкал'
                }
            } "
 . "}, "
 . " series: [ { name: 'Тепло (ГКал)', "
 . "data: [";
$data1 = '';
$data2 = '';
for ($i = 0; $i < count($limits); $i++) {
    $data1 .= str_replace(',', '.', $limits[$i]['teplo']) . ", ";
    $data2 .= str_replace(',', '.', $limits[$i]['teplo_limit']) . ", ";
}

echo"" . $data1 . "] "
 . "}, { name: 'Лимит (ГКал)', "
 . "data: [" . $data2 . "] "
 . "}] "
 . "}); "
 . "}); ";



echo "$(function () { "
 . " var chart = new Highcharts.Chart({ "
 . "   chart: { "
 . "      renderTo: 'container_voda', "
 . "      type: 'column', "
 . "      options3d: { "
 . "          enabled: true, "
 . "          alpha: 0, "
 . "          beta: 2, "
 . "          depth: 63, "
 . "          viewDistance: 25 "
 . "      } "
 . "  }, "
 . " title: {  "
 . " text: 'График превышения расхода воды за " . $_POST['year'] . "г.' "
 . " }, colors: ['#337ab7', '#50B432'], "
 . " subtitle: {  "
 . " text: ''  "
 . " }, yAxis: {title: {text: 'Объем'}}, xAxis: {  "
 . " categories: [ ";
//."'Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas'"
$name = '';
for ($i = 0; $i < count($limits); $i++) {
    $name .= "'" . $limits[$i]['name'] . "', ";
}
echo "" . $name . "]  "
 . " }, "
 . " plotOptions: { "
 . "column: { "
 . "depth: 25 "
 . "} ,"
 . "series: {
        borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f} м.куб.'
            }
        }"
 . "}, "
 . " series: [ { name: 'Объем (м. куб.)', "
 . "data: [";
$data1 = '';
$data2 = '';
for ($i = 0; $i < count($limits); $i++) {
    $data1 .= str_replace(',', '.', $limits[$i]['voda']) . ", ";
    $data2 .= str_replace(',', '.', $limits[$i]['voda_limit']) . ", ";
}


echo"" . $data1 . "] "
 . "}, { name: 'Лимит (м. куб.)', "
 . "data: [" . $data2 . "] "
 . "}] "
 . "}); "
 . "}); ";
echo "</script>";
