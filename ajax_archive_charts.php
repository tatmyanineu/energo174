<?php

include 'db_config.php';
session_start();
$id_object = $_POST['id_object'];
$type_arch = $_POST['type_arch'];
//$page = $_POST['page_num'];
//$num=31;
//$start = $page * $num - $num; 

$sql_device = pg_query('SELECT 
                                    MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
                                  FROM
                                    "Tepl"."Places_cnt" "Places_cnt1"
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                  WHERE
                                    "Places_cnt1".plc_id = ' . $id_object . '');
$row_device = pg_fetch_row($sql_device);



if (isset($_POST['date_now']) && isset($_POST['date_afte'])) {

    if ($type_arch == 2) {
        if ($_POST['date_now'] > $_POST['date_afte']) {
            $date_now = date('Y-m-d', strtotime($_POST['date_now']));
            $date_afte = date('Y-m-d', strtotime($_POST['date_afte']));
        } else {
            $date_afte = date('Y-m-d', strtotime($_POST['date_now']));
            $date_now = date('Y-m-d', strtotime($_POST['date_afte']));
        }
    }
//Запром на вывод архива для конкретного объекта
    $sql_date = pg_query('SELECT DISTINCT 
                          ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND
                          "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date_afte . '\' AND 
                          "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date_now . '\'
                        ORDER BY
                          "Tepl"."Arhiv_cnt"."DateValue"');

    $sql_temp = pg_query('SELECT 
  public.weather_archive.id,
  public.weather_archive.date_cnt,
  public.weather_archive.temper
FROM
  public.weather_archive
WHERE
  public.weather_archive.date_cnt >= \'' . $date_afte . '\' AND 
  public.weather_archive.date_cnt <= \'' . $date_now . '\' 
ORDER BY
  public.weather_archive.date_cnt');
} else {
    //echo "без даты<br />";
    $num = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
    $date_num = date('Y-m-' . $num . '');
    $sql_date = pg_query('SELECT DISTINCT 
                          ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND
                          "Tepl"."Arhiv_cnt"."DateValue" >= \'' . date('Y-m-01') . '\' AND
                          "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date_num . '\'
                        ORDER BY
                          "Tepl"."Arhiv_cnt"."DateValue"
                        ');

    $sql_temp = pg_query('SELECT 
        public.weather_archive.id,
        public.weather_archive.date_cnt,
        public.weather_archive.temper
      FROM
        public.weather_archive
      WHERE
        public.weather_archive.date_cnt >= \'' . date('Y-m-01') . '\' AND 
        public.weather_archive.date_cnt <= \'' . $date_num . '\'
      ORDER BY
        public.weather_archive.date_cnt');
}
//Запрос на вывод параметров на объекте
$sql_resurse = pg_query('SELECT DISTINCT 
                          ("Tepl"."ParametrResourse"."Name") AS "FIELD_1",
                          "Tepl"."ParamResPlc_cnt"."NameGroup",
                          "Tepl"."Resourse_cnt"."Name",
                          "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                        FROM
                          "Tepl"."ParametrResourse"
                          INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
                          INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                        WHERE
                          "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . '
                        ORDER BY
                          "Tepl"."Resourse_cnt"."Name",
                          "Tepl"."ParamResPlc_cnt"."NameGroup"');

//while ($row_resours = pg_fetch_row($sql_resurse)){
//        echo '<td>'.$row_resours[0].'</td>';
//        $id_resours[]=$row_resours[3];
//}
$i = 0;
$j = 0;
while ($row_resours = pg_fetch_row($sql_resurse)) {
    $id_resours[] = $row_resours[3];
    $arr_resours[] = $row_resours[0];
    if ($arr_name[$i] == "")
        $arr_name[$i] = $row_resours[2];
    if ($arr_group[$j] == "")
        $arr_group[$j] = $row_resours[1];

    if ($arr_name[$i] == $row_resours[2]) {
        $par[$i] ++;
        // echo " arr_name = ".$arr_name[$i]."   par=".$par[$i]."   i=".$i."<br>";
    } else {
        $i++;
        $arr_name[$i] = $row_resours[2];
        $par[$i] ++;
        $j++;
        $arr_group[$j] = $row_resours[1];
    };

    if ($arr_group[$j] == $row_resours[1]) {

        $grou[$j] ++;
        //echo  "arr_grou = ". $arr_group[$j]."   grou=".$grou[$j]."  j=".$j." <br>";
    } else {
        //echo "j++<br>";
        $j++;
        $arr_group[$j] = $row_resours[1];
        $grou[$j] ++;
        //echo  "arr_grou = ". $arr_group[$j]."   grou=".$grou[$j]."  j=".$j." <br>";
    };
}



$s = 0;
$mass_voda = '';
$count_date_charts = 1;
$date_charts = '[';
while ($row_date = pg_fetch_row($sql_date)) {
    //echo '<tr id="hover">';
    $s++;
    //echo "<td>" .pg_num_rows($row_date) . "</td>";
    $date_b = date("d.m.Y H:i", strtotime($row_date[0]));
    //echo "дата=  ".$row_date[0] ."  ресурс ";
    $date_arch = explode(' ', $date_b);
    //echo '<td>' . $date_arch[0] . '</td>';
    if ($count_date_charts == pg_num_rows($sql_date)) {
        $date_charts = $date_charts . ' \'' . $date_arch[0] . '\']';
    } else {
        $date_charts = $date_charts . ' \'' . $date_arch[0] . '\', ';
    }

    $sql_archive = pg_query('SELECT 
                                  "Tepl"."Arhiv_cnt"."DataValue",
                                  "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                                FROM
                                  "Tepl"."ParametrResourse"
                                  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
                                  INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                                WHERE
                                  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
                                  "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
                                  "Tepl"."Arhiv_cnt"."DateValue" = \'' . $row_date[0] . '\'
                                ORDER BY
                                  "Tepl"."Arhiv_cnt"."DateValue",
                                  "Tepl"."Resourse_cnt"."Name",
                                  "Tepl"."ParamResPlc_cnt"."NameGroup",
                                  "Tepl"."ParametrResourse"."Name"
                                ');

    $kol = pg_num_rows($sql_archive);
    $arr_DatVal = "";
    $arr_ResId = "";
    $f = 0;
    $pokaz = '';
    while ($row_archive = pg_fetch_row($sql_archive)) {
        //echo $f."  ";
        $arr_DatVal[] = $row_archive[0];
        //echo $arr_DatVal[$f]. "  ". $row_archive[0];
        $arr_ResId[] = $row_archive[1];
        //echo "<br>";
        $f++;
    }

    //$arr_DatVal = array_unique($arr_DatVal);
    //$arr_DatVal = array_values($arr_DatVal);
    $arr_ResDoble = $arr_ResId;
    $arr_ResId = array_unique($arr_ResId);
    //
    //проверяем сколько элементов в массиве
    //print_r($id_resours);
    //print_r($arr_DatVal);


    foreach ($arr_ResId as $key => $value) {
        $pokaz[] = $arr_DatVal[$key];
    }

    $arr_ResId = array_values($arr_ResId);

    for ($i = 0; $i < count($id_resours); $i++) {
        $prov = 0;
        for ($j = 0; $j < count($arr_ResId); $j++) {
            if ($id_resours[$i] == $arr_ResId[$j]) {
                $prov = 1;
                //echo "<td>" .number_format($pokaz[$j], 3 ,",",""). "</td>";
                //ХВС787
                if ($id_resours[$i] == 1) {
                    $mass_voda[0][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 308) {
                    $mass_voda[1][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 310) {
                    $mass_voda[2][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 414) {
                    $mass_voda[3][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 420) {
                    $mass_voda[4][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 436) {
                    $mass_voda[5][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 787) {
                    $mass_voda[6][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 2) {
                    $mass_voda2[0][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 44) {
                    $mass_voda2[1][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 377) {
                    $mass_voda2[2][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 442) {
                    $mass_voda2[3][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 402) {
                    $mass_voda2[4][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 408) {
                    $mass_voda2[5][] = $pokaz[$j];
                } elseif ($id_resours[$i] == 922) {
                    $mass_voda2[6][] = $pokaz[$j];
                } else {
                    $mass_arch[$i][] = $pokaz[$j];
                }
            }
        }
        if ($prov == 0) {
            //$mass_arch[$i] = $mass_arch[$i] + 0;
            //echo "<td>—</td>";
        }
    }
    $count_date_charts++;
}

/*  for ($i = 0; $i < count($id_resours); $i++) {
  if ($id_resours[$i] == $arr_ResId[$i]) {
  if ($id_resours[$i] == 1) {
  $mass_voda[0][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 308) {
  $mass_voda[1][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 310) {
  $mass_voda[2][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 414) {
  $mass_voda[3][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 420) {
  $mass_voda[4][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 436) {
  $mass_voda[5][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 2) {
  $mass_voda2[0][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 44) {
  $mass_voda2[1][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 377) {
  $mass_voda2[2][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 442) {
  $mass_voda2[3][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 402) {
  $mass_voda2[4][] = $pokaz[$i];
  } elseif ($id_resours[$i] == 408) {
  $mass_voda2[5][] = $pokaz[$i];
  } else {
  $mass_arch[$i][] = $pokaz[$i];
  }
  }
  }
  $count_date_charts++;
  } */

echo $count_date_charts . " " . pg_num_rows($sql_date);
//print_r($mass_arch); echo "<br>";
//print_r($mass_arch[1]); echo "<br>";
//print_r($mass_arch[2]); echo "<br>";
$h = 0;
for ($l = 0; $l < count($mass_voda); $l++) {
    $z = '';
    $arr = '';
    for ($n = 0; $n < count($mass_voda[$l]); $n++) {
        if ($n + 1 < count($mass_voda[$l])) {
            $arr = $mass_voda[$l][$n + 1] - $mass_voda[$l][$n];
            $z = $z . "" . substr($arr, 0, 7) . ", ";
        }
    }
    $val[$l] = $z;
    //echo "Z ====" . $val[$l] . "  <br>";
}


for ($l = 0; $l < count($mass_voda2); $l++) {
    $z = '';
    $arr = '';
    for ($n = 0; $n < count($mass_voda2[$l]); $n++) {
        if ($n + 1 < count($mass_voda2[$l])) {
            $arr = $mass_voda2[$l][$n + 1] - $mass_voda2[$l][$n];
            $z = $z . "" . substr($arr, 0, 7) . ", ";
        }
    }
    $val2[$l] = $z;

    //echo "M ====" . $val2[$l] . "  <br>";
}
for ($g = 0; $g < count($id_resours); $g++) {
    if ($id_resours[$g] == 5) {
        echo '<div id="container1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
    }
    if ($id_resours[$g] == 19) {
        echo '<div id="container2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
    }
    if ($id_resours[$g] == 9) {
        echo '<div id="container3" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
    }
    if ($id_resours[$g] == 1) {
        echo '<div id="container4" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
    }
    if ($id_resours[$g] == 2) {
        echo '<div id="container5" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
    }
     
}
echo '<div id="container6" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
echo "<script>
        $(function () {";
//для графика температуры
for ($g = 0; $g < count($id_resours); $g++) {
    if ($id_resours[$g] == 5) {

        echo "
                $('#container1').highcharts({
                    title: {
                        text: 'График расхода температуры',
                        x: -20 //center
                    },colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
                    xAxis: {
                        categories:" . $date_charts . " },
                    yAxis: {
                        title: {
                            text: 'Температура, (°C)'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    tooltip: {
                        valueSuffix: 'С'
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series:[";
        for ($i = 0; $i < count($id_resours); $i++) {
            if ($id_resours[$i] == 5 or $id_resours[$i] == 6 or $id_resours[$i] == 12) {
                $string = "";
                $count_mass_arch = 1;
                $string = "{\n name:'" . $arr_resours[$i] . "',\n data: [";
                for ($j = 0; $j < count($mass_arch[$i]); $j++) {
                    if ($count_mass_arch == count($mass_arch[$i])) {
                        $string = $string . '' . substr($mass_arch[$i][$j], 0, 4) . '';
                    } else {
                        $string = $string . '' . substr($mass_arch[$i][$j], 0, 4) . ', ';
                    }
                    $count_mass_arch++;
                }
                $string = $string . "]},\n";
                echo $string;
            }
        }
        echo '] }); ';
    }
}
//конец для графика температуры
//
//для графика массы
for ($g = 0; $g < count($id_resours); $g++) {
    if ($id_resours[$g] == 19) {
        echo "    
            $('#container2').highcharts({
                    title: {
                        text: 'График расхода в теплоносителе',
                        x: -20 //center
                    },colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
                    xAxis: {
                        categories:" . $date_charts . " },
                    yAxis: {
                        title: {
                            text: 'Масса, Т'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    tooltip: {
                        valueSuffix: ' Т'
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series:[";


        for ($i = 0; $i < count($id_resours); $i++) {
            if ($id_resours[$i] == 19 or $id_resours[$i] == 20 or $id_resours[$i] == 21) {
                $string = "";
                $count_mass_arch = 1;
                $string = "{\n name:'" . $arr_resours[$i] . "',\n data: [";
                //print_r($mass_arch[$i]);
                if ($row_device[0] == 214 or $id_object == 314) {
                    for ($r = 0; $r < count($mass_arch[$i]); $r++) {
                        if ($r + 1 < count($mass_arch[$i])) {
                            $rrr = $mass_arch[$i][$r + 1] - $mass_arch[$i][$r];
                            $string = $string . '' . substr($rrr, 0, 4) . ',';
                        }
                    }

                    $string = $string . "]},\n";
                    echo $string;
                } else {





                    for ($j = 0; $j < count($mass_arch[$i]); $j++) {
                        if ($count_mass_arch == count($mass_arch[$i])) {
                            $string = $string . '' . substr($mass_arch[$i][$j], 0, 4) . '';
                        } else {
                            $string = $string . '' . substr($mass_arch[$i][$j], 0, 4) . ', ';
                        }
                        $count_mass_arch++;
                    }
                    $string = $string . "]},\n";
                    echo $string;
                }
            }
        }
        echo '] }); ';
    }
}
//конец для графика массы
//
//
//для графика энергии
for ($g = 0; $g < count($id_resours); $g++) {
    if ($id_resours[$g] == 9) {
        echo "
                $('#container3').highcharts({
                    title: {
                        text: 'График расхода Тепловой энергии',
                        x: -20 //center
                    },colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
                    xAxis: {
                        categories:" . $date_charts . " },
                    yAxis: {
                        title: {
                            text: 'Теп.энергия, ГКал'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    tooltip: {
                        valueSuffix: 'Гкал'
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series:[";
//для графика энергии
        for ($i = 0; $i < count($id_resours); $i++) {
            if ($id_resours[$i] == 9 or $id_resours[$i] == 16) {

                $string = "";
                $count_mass_arch = 1;
                $string = "{\n name:'" . $arr_resours[$i] . "',\n data: [";
                //print_r($mass_arch[$i]);
                if ($row_device[0] == 214 or $id_object == 314) {
                    for ($r = 0; $r < count($mass_arch[$i]); $r++) {
                        if ($r + 1 < count($mass_arch[$i])) {
                            $rrr = $mass_arch[$i][$r + 1] - $mass_arch[$i][$r];
                            $string = $string . '' . substr($rrr, 0, 4) . ',';
                        }
                    }

                    $string = $string . "]},\n";
                    echo $string;
                } else {


                    for ($j = 0; $j < count($mass_arch[$i]); $j++) {
                        if ($count_mass_arch == count($mass_arch[$i])) {
                            $string = $string . '' . substr($mass_arch[$i][$j], 0, 4) . '';
                        } else {
                            $string = $string . '' . substr($mass_arch[$i][$j], 0, 4) . ', ';
                        }
                        $count_mass_arch++;
                    }
                    $string = $string . "]},\n";
                    echo $string;
                }
            }
        }
        echo '] }); ';
    }
}

//конец для графика энергии
//
//
//для графика ХВС
for ($g = 0; $g < count($id_resours); $g++) {
    if ($id_resours[$g] == 1) {
        $m = 0;
        $str_voda = "  
                
                $('#container4').highcharts({
                    title: {
                        text: 'График расхода ХВС',
                        x: -20 //center
                    },colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
                    xAxis: {
                        categories:" . $date_charts . " },
                    yAxis: {
                        title: {
                            text: 'Обьем, м3'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    tooltip: {
                        valueSuffix: 'м3'
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series:[";
        echo $str_voda;
        for ($i = 0; $i < count($id_resours); $i++) {
            if ($id_resours[$i] == 1 or $id_resours[$i] == 308 or $id_resours[$i] == 310 or $id_resours[$i] == 414 or $id_resours[$i] == 420 or $id_resours[$i] == 436) {
                $string = "";
                //echo $val[0];
                $string = "{\n name:'" . $arr_resours[$i] . "',\n data: [";
                $string = $string . ' ' . $val[$m];
                $string = $string . "]},\n";
                echo $string;
                $m++;
            }
        }
        echo '] });';
    }
}
//конец для графика ХВС
//
//
//для графика ГВС
for ($g = 0; $g < count($id_resours); $g++) {
    if ($id_resours[$g] == 1) {
        $m = 0;
        $str_voda = "  
                
                $('#container5').highcharts({
                    title: {
                        text: 'График расхода ГВС',
                        x: -20 //center
                    },colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
                    xAxis: {
                        categories:" . $date_charts . " },
                    yAxis: {
                        title: {
                            text: 'Обьем, м3'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    tooltip: {
                        valueSuffix: 'м3'
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series:[";
        echo $str_voda;
        for ($i = 0; $i < count($id_resours); $i++) {
            if ($id_resours[$i] == 2 or $id_resours[$i] == 44 or $id_resours[$i] == 377 or $id_resours[$i] == 442 or $id_resours[$i] == 402 or $id_resours[$i] == 408) {
                $string = "";
                //echo $val[0];
                $string = "{\n name:'" . $arr_resours[$i] . "',\n data: [";
                $string = $string . ' ' . $val2[$m];
                $string = $string . "]},\n";
                echo $string;
                $m++;
            }
        }
        echo '] });';
    }
}

echo ' }); </script>';



?>