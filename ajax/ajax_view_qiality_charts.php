<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../db_config.php';
session_start();

$plc_id = $_POST['plc_id'];


$sql_param = pg_query('SELECT 
  public.temp_charts.plc_id,
  public.temp_charts.t1min,
  public.temp_charts.t2min,
  public.temp_charts.t1max,
  public.temp_charts.t2max,
  public.temp_charts.thpmin,
  public.temp_charts.thpmax,
  public.temp_charts.param1,
  public.temp_charts.param2
FROM
  public.temp_charts
WHERE
  public.temp_charts.plc_id = ' . $plc_id . '');


$sql_temp = pg_query('SELECT 
        public.weather_archive.id,
        public.weather_archive.date_cnt,
        public.weather_archive.temper
      FROM
        public.weather_archive
      WHERE
        public.weather_archive.date_cnt >= \'' . $_POST['date1'] . '\' AND 
        public.weather_archive.date_cnt <= \'' . $_POST['date2'] . '\'
      ORDER BY
        public.weather_archive.date_cnt');


if (pg_num_rows($sql_param) != 0) {

    $arr_param = array(
        't_nar_voz_min' => pg_fetch_result($sql_param, 0, 5),
        't_nar_voz_max' => pg_fetch_result($sql_param, 0, 6),
        't_pod_min' => pg_fetch_result($sql_param, 0, 1),
        't_pod_max' => pg_fetch_result($sql_param, 0, 3),
        't_obr_min' => pg_fetch_result($sql_param, 0, 2),
        't_obr_max' => pg_fetch_result($sql_param, 0, 4),
        'k_pod' => pg_fetch_result($sql_param, 0, 7),
        'k_obr' => pg_fetch_result($sql_param, 0, 8)
    );


    $t1min = pg_fetch_result($sql_param, 0, 1);
    $t2min = pg_fetch_result($sql_param, 0, 2);
    $tnr = pg_fetch_result($sql_param, 0, 5);
    $step1 = pg_fetch_result($sql_param, 0, 7);
    $step2 = pg_fetch_result($sql_param, 0, 8);

    /*
      echo $t1min . "<br>";
      echo $t2min . "<br>";
      echo $tnr . "<br>";
      echo $step1 . "<br>";
      echo $step2 . "<br>";
     */
    $sql_weather_temp = pg_query('SELECT
  public.weather_archive.temper,
  public.weather_archive.date_cnt
  FROM
  public.weather_archive
  WHERE
  public.weather_archive.date_cnt >= \'' . $_POST['date1'] . '\' AND
  public.weather_archive.date_cnt <= \'' . $_POST['date2'] . '\'');

    while ($result = pg_fetch_row($sql_weather_temp)) {
        if ($result[0] < 0) {
            $value = ($result[0] * -1);
        } else {
            $value = $result[0];
        }
        $weather[] = array(
            'date' => date('Y-m-d', strtotime($result[1])),
            'value1' => (($value + $tnr) * $step1) + $t1min,
            'value2' => (($value + $tnr) * $step2) + $t2min,
            'temp' => $result[0]
        );
    }

    $sql_podacha = pg_query('SELECT
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."Arhiv_cnt"."DataValue",
  "Tepl"."Arhiv_cnt"."DateValue"
  FROM
  "Tepl"."Arhiv_cnt"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."Arhiv_cnt".pr_id = "Tepl"."ParamResPlc_cnt".prp_id)
  WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $plc_id . ' AND
  "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $_POST['date1'] . '\' AND
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $_POST['date2'] . '\' AND
  "Tepl"."Arhiv_cnt".typ_arh = 2 AND
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 5');

    while ($result = pg_fetch_row($sql_podacha)) {
        $k = array_search(date('Y-m-d', strtotime($result[2])), array_column($weather, 'date'));
        if ($k !== false) {
            $data[] = array(
                'date' => $result[2],
                'pokaz' => $result[1],
                'etalon' => $weather[$k]['value1'],
                'temp' => $weather[$k]['temp']
            );
        }
    }

    $sql_podacha = pg_query('SELECT
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."Arhiv_cnt"."DataValue",
  "Tepl"."Arhiv_cnt"."DateValue"
  FROM
  "Tepl"."Arhiv_cnt"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."Arhiv_cnt".pr_id = "Tepl"."ParamResPlc_cnt".prp_id)
  WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $plc_id . ' AND
  "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $_POST['date1'] . '\' AND
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $_POST['date2'] . '\' AND
  "Tepl"."Arhiv_cnt".typ_arh = 2 AND
  "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 6');

    while ($result = pg_fetch_row($sql_podacha)) {
        $k = array_search(date('Y-m-d', strtotime($result[2])), array_column($weather, 'date'));
        if ($k !== false) {
            $data2[] = array(
                'date' => $result[2],
                'pokaz' => $result[1],
                'etalon' => $weather[$k]['value2'],
                'temp' => $weather[$k]['temp']
            );
        }
    }
    //var_dump($data);
    //var_dump($data2);
//var_dump($weather);




    echo '<div id="weather" style = "width: 90%"></div>'
    . '<div id="chart_etalon" style = "width: 90%"></div>'
    . '<div id="chart_podacha" style = "width: 90%"></div>'
    . '<div id="chart_obratka" style = "width: 90%"></div>';






    echo "<script type='text/javascript'>";
    echo "$(function () { ";

    while ($result = pg_fetch_row($sql_temp)) {
        $temp_arr[] = array(
            'id' => $result[0],
            'data' => $result[2],
            'date' => $result[1]
        );
    }

    $date_charts = "[";
    for ($i = 0; $i < count($temp_arr); $i++) {
        $date_charts .= "'" . date('d.m.Y', strtotime($temp_arr[$i]['date'])) . '\', ';
    }
    $date_charts .= "]";

    $str_voda = "  
                $('#weather').highcharts({
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: 'График температуры наружнего воздуха'
                    },colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
                    xAxis: {
                        categories:" . $date_charts . " },
                    yAxis: {
                        title: {
                            text: 'Температура (°C)'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: false
                        }
                    },
                    tooltip: {
                        valueSuffix: '°C'
                    },                   
                    series:[";
    echo $str_voda;
    $string = "";
    $string = "{\n name:'Температура наружнего возвуха',\n data: [";
    for ($i = 0; $i < count($temp_arr); $i++) {

        $string .= number_format($temp_arr[$i]['data'], 2) . ', ';
    }
    $string = $string . "]},\n";
    echo $string;
    echo '] });';


    echo " var chart = new Highcharts.Chart({ "
    . "   chart: { "
    . "      renderTo: 'chart_etalon', "
    . "      type: 'line', "
    . "      options3d: { "
    . "          enabled: true, "
    . "          alpha: 0, "
    . "          beta: 0, "
    . "          depth: 100, "
    . "          viewDistance: 0 "
    . "      } "
    . "  }, "
    . " title: {  "
    . " text: 'Эталонный график температуры(режимная карта ЧКТС)'"
    . " }, colors: ['#ef3038', '#50B432'], "
    . " subtitle: {  "
    . " text: ''  "
    . " }"
    . ", yAxis: {title: {text: 'температура (C)'}}, xAxis: {  "
    . " categories: [ ";
//."'Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas'"
    $date = '';
    for ($i = 0; $i >= $arr_param['t_nar_voz_max']; $i--) {
        $date .= '' . $i . ', ';
    }
    echo "" . $date . "]  "
    . " }, "
    . " plotOptions: { "
    . "column: { "
    . "depth: 40 "
    . "},"
    . "series: {
  borderWidth: 0

  } "
    . "}, "
    . " series: [ { name: 't подачи', "
    . "data: [";
    $pokaz = '';
    $etalon = '';
    for ($i = 0; $i >= $arr_param['t_nar_voz_max']; $i--) {
        if ($i != 0) {
            $pod = $arr_param['t_pod_min'] + ((($i * -1) - 1) * $arr_param['k_pod']);
            $obr = $arr_param['t_obr_min'] + ((($i * -1) - 1) * $arr_param['k_obr']);
        } else {
            $pod = $arr_param['t_pod_min'] + (($i * -1) * $arr_param['k_pod']);
            $obr = $arr_param['t_obr_min'] + (($i * -1) * $arr_param['k_obr']);
        }
        $pokaz .= '' . $pod . ', ';
        $etalon .= '' . $obr . ', ';
        //$etalon .= str_replace(',', '.', $data[$i]['etalon']) . ", ";
    }

    echo"" . $pokaz . "] "
    . "}, { name: 't обратки', "
    . "data: [" . $etalon . "] "
    . "}] "
    . "}); "
    . ""
    . "";


    echo " var chart = new Highcharts.Chart({ "
    . "   chart: { "
    . "      renderTo: 'chart_podacha', "
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
    . " text: 'График качества тепла подающей трубы' "
    . " }, colors: ['#ef3038', '#50B432'], "
    . " subtitle: {  "
    . " text: ''  "
    . " }, yAxis: {title: {text: 'температура (C)'}}, xAxis: {  "
    . " categories: [ ";
//."'Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas'"
    $date = '';
    for ($i = 0; $i < count($data); $i++) {
        $date .= "'" . date('d.m.Y', strtotime($data[$i]['date'])) . "', ";
    }
    echo "" . $date . "]  "
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
  format: '{point.y:.1f} t'
  }
  } "
    . "}, "
    . " series: [ { name: 't(факт.)', "
    . "data: [";
    $pokaz = '';
    $etalon = '';
    for ($i = 0; $i < count($data); $i++) {
        $pokaz .= str_replace(',', '.', $data[$i]['pokaz']) . ", ";
        $etalon .= str_replace(',', '.', $data[$i]['etalon']) . ", ";
    }

    echo"" . $pokaz . "] "
    . "}, { name: 't(эталон)', "
    . "data: [" . $etalon . "] "
    . "}] "
    . "});"
    . ""
    . " ";


    echo " var chart = new Highcharts.Chart({ "
    . "   chart: { "
    . "      renderTo: 'chart_obratka', "
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
    . " text: 'График качества тепла обратной трубы' "
    . " }, colors: ['#ef3038', '#50B432'], "
    . " subtitle: {  "
    . " text: ''  "
    . " }, yAxis: {title: {text: 'температура (C)'}}, xAxis: {  "
    . " categories: [ ";
//."'Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas'"
    $date = '';
    for ($i = 0; $i < count($data2); $i++) {
        $date .= "'" . date('d.m.Y', strtotime($data2[$i]['date'])) . "', ";
    }
    echo "" . $date . "]  "
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
  format: '{point.y:.1f} t'
  }
  } "
    . "}, "
    . " series: [ { name: 't(факт.)', "
    . "data: [";
    $pokaz = '';
    $etalon = '';
    for ($i = 0; $i < count($data2); $i++) {
        $pokaz .= str_replace(',', '.', $data2[$i]['pokaz']) . ", ";
        $etalon .= str_replace(',', '.', $data2[$i]['etalon']) . ", ";
    }

    echo"" . $pokaz . "] "
    . "}, { name: 't(эталон)', "
    . "data: [" . $etalon . "] "
    . "}] "
    . "}); ";



    echo "}); ";
} else {
    echo '<h2>Ошибка</h2>'
    . '<h3>Данные для построения графика еще не добавлены на сайт</h3>';
}