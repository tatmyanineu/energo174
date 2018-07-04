<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../db_config.php';


$_SESSION['rep_id'] = '';
$_SESSION['rep_m'] = '';
$_SESSION['rep_name'] = '';
$_SESSION['rep_addr'] = '';


$_SESSION['rep_date'] = '';
$_SESSION['rep_stat'] = '';

$_SESSION['rep_tep_val'] = '';
$_SESSION['rep_tep_lim'] = '';
$_SESSION['rep_tep_col'] = '';

$_SESSION['rep_vod_val'] = '';
$_SESSION['rep_vod_lim'] = '';
$_SESSION['rep_vod_col'] = '';

$_SESSION['rep_plc_err'] = '';




$id_distinct = $_POST['id_dist'];


$sql_school_info = pg_query('
SELECT 
  "Places_cnt1"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Places_cnt1".plc_id
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Places_cnt1".plc_id = "Tepl"."PlaceGroupRelations".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."PlaceGroupRelations".grp_id = "Tepl"."GroupToUserRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Places_cnt1".place_id = ' . $id_distinct . ' AND 
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\'  AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
ORDER BY
  "Tepl"."Places_cnt".plc_id');

$data1 = date('' . $_POST['year'] . '-' . '01-01');
$data2 = date('' . $_POST['year'] + 1 . '-' . '01-01');

while ($result = pg_fetch_row($sql_school_info)) {
    $arr_school[] = array(
        'plc_id' => $result[3],
        'name' => $result[0],
        'adres' => '' . $result[1] . ' ' . $result[2] . ''
    );
}

$sql_data = pg_query('
        SELECT DISTINCT 
          ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
          "Places_cnt1"."Name",
          "Tepl"."PropPlc_cnt"."ValueProp",
          "PropPlc_cnt1"."ValueProp",
          "Tepl"."ParamResPlc_cnt"."ParamRes_id",
          "Tepl"."Arhiv_cnt"."DataValue",
          "Places_cnt1".plc_id
        FROM
          "Tepl"."Places_cnt" "Places_cnt1"
          INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
          INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
          INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."ParamResPlc_cnt".plc_id)
          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
          INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
          INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
          INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
          INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
        WHERE
          "Tepl"."PropPlc_cnt".prop_id = 27 AND 
          "PropPlc_cnt1".prop_id = 26 AND 
          "Tepl"."Arhiv_cnt".typ_arh = 3 AND 
          "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $data1 . '\' AND 
          "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $data2 . '\' AND 
          "Tepl"."Places_cnt".plc_id = ' . $id_distinct . '
        ORDER BY
          "Places_cnt1"."Name",
          "Tepl"."ParamResPlc_cnt"."ParamRes_id",
          "Tepl"."Arhiv_cnt"."DateValue"');

while ($result = pg_fetch_row($sql_data)) {
    $arr_plc_id[] = $result[6];
    $arr_data[] = array(
        'plc_id' => $result[6],
        'data' => $result[0],
        'value' => $result[5],
        'res_id' => $result[4]
    );
}

$sql_all_limit = pg_query('SELECT DISTINCT 
  public."LimitPlaces_cnt".plc_id,
  public."LimitPlaces_cnt".teplo,
  public."LimitPlaces_cnt".voda
FROM
  public."LimitPlaces_cnt"');

while ($result = pg_fetch_row($sql_all_limit)) {
    $arr_all_limit[] = array(
        'plc_id' => $result[0],
        'teplo' => $result[1],
        'voda' => $result[2]
    );
}

echo '<div class="tab-pane fade in active" id="home">';

echo "<h2 id='center_h1'>Отчет за " . pg_fetch_result($sql_limit_part, 0, 2) . " " . $_POST['year'] . "г.</h2>";

echo "<table id='main_table' class='table table-bordered'>
    <thead id='thead'>
    <tr id='warning'>
        <td rowspan=2 data-query='0'><b>№</b</td>
        <td rowspan=2 data-query='1'><b>Учереждение</b</td>
        <td rowspan=2 data-query='2'><b>Адрес</b</td>
        <td colspan=2><b>Передача данных</b</td>
        <td colspan=2><b>Тепло (ГКал)</b</td>
        <td colspan=2><b>Вода (м<sup>3</sup>)</b</td>
      </tr>  <tr id='warning'>
            <td data-query='3'><b>Дата обновления</b</td>
            <td data-query='4'><b>Статус</b></td>
            <td data-query='5'><b>Данные</b</td>
            <td data-query='6'><b>Лимит</b</td>
            <td data-query='7'><b>Данные</b</td>
            <td data-query='8'><b>Лимит</b</td>
        </tr>
    </thead>
    <tbody>";
$m = 1;

for ($i = 0; $i < count($arr_school); $i++) {
    $sql_limit_group = pg_query('SELECT 
                        public.group_limit.group_name,
                        public.group_limit.group_id,
                        public."LimitPlaces_cnt".teplo,
                        public."LimitPlaces_cnt".voda,
                        public.group_limit.group_adres
                      FROM
                        public.group_limit
                        INNER JOIN public."LimitPlaces_cnt" ON (public.group_limit.group_id = public."LimitPlaces_cnt".plc_id)
                        INNER JOIN public.group_plc ON (public.group_limit.group_id = public.group_plc.group_id)
                      WHERE
                        public.group_plc.plc_id =' . $arr_school[$i]['plc_id']);
    if (pg_num_rows($sql_limit_group) != 0) {
        //echo "<h3>Попался обьект из группы " . pg_fetch_result($sql_limit_group, 0, 0) . "</h3>";
        $key_gr = array_search(pg_fetch_result($sql_limit_group, 0, 1), $arr_gr_limit);
        if ($key_gr === false) {
            $arr_gr_limit[] = pg_fetch_result($sql_limit_group, 0, 1);

            echo "<tr id='hover'>"
            . "<td>" . $m . "</td>"
            . "<td>" . pg_fetch_result($sql_limit_group, 0, 0) . "</td>"
            . "<td> " . pg_fetch_result($sql_limit_group, 0, 4) . " </td>";

            $_SESSION['rep_id'][] = pg_fetch_result($sql_limit_group, 0, 1);
            $_SESSION['rep_m'][] = $m;
            $_SESSION['rep_name'][] = pg_fetch_result($sql_limit_group, 0, 0);
            $_SESSION['rep_addr'][] = pg_fetch_result($sql_limit_group, 0, 4);

            view_archive_group(pg_fetch_result($sql_limit_group, 0, 1));
            echo "</tr>";
            $m++;
        }
    } else {

        echo "<tr data-href='charts_limit_month.php?id_object=" . $arr_school[$i]['plc_id'] . "' id='hover' >"
        . "<td>" . $m . "</td>"
        . "<td>" . $arr_school[$i]['name'] . "</td>"
        . "<td>" . $arr_school[$i]['adres'] . "</td>";

        $_SESSION['rep_id'][] = $arr_school[$i]['plc_id'];
        $_SESSION['rep_m'][] = $m;
        $_SESSION['rep_name'][] = $arr_school[$i]['name'];
        $_SESSION['rep_addr'][] = $arr_school[$i]['adres'];

        $sql_device = pg_query('SELECT
                                    MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
                                    FROM
                                    "Tepl"."Places_cnt" "Places_cnt1"
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                    WHERE
                                    "Places_cnt1".plc_id = ' . $arr_school[$i]['plc_id'] . '');
        $dev_id = pg_fetch_result($sql_device, 0, 0);

        unset($vte);
        unset($voda);

        $teplo = 0;
        $max_date = '';
        $vte = array();
        $voda = array();

        array_archive($arr_school[$i]['plc_id'], $dev_id);

        if ($row_device[0] == 214 or $id_plc[$a] == 314 or $id_plc[$a] == 251 or $id_plc[$a] == 316 or $id_plc[$a] == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            //print_r($voda)."<br>";
            //echo "count mass_voda = ".count($voda)."<br>";
            $val = 0;
            for ($l = count($vte) - 1; $l >= 0; $l--) {
                //echo  "id == " .$id_plc[$a] .    "     l ==   " . $l . "  val ==  " . $vte[$l];
                if ($l - 1 >= 0) {
                    $p = $vte[$l] - $vte[$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        }

        $val = 0;
        for ($l = 0; $l < count($voda); $l++) {
            //print_r($voda[$l])."<br>";
            $n1 = count($voda[$l]) - 1;
            $z = 0;
            for ($n = 0; $n < count($voda[$l]); $n++) {

                if ($n == $n1) {
                    $z = $z;
                    //echo "n=" .$n." mas = ". $voda[$l][$n]."    z=".$z."  <br>" ;
                }
                if ($n >= 0 and $n < $n1) {
                    if ($voda[$l][$n]) {
                        $z = $z + $voda[$l][$n + 1] - $voda[$l][$n];
                    }
                    //echo "n=" .$n." mas = ". $voda[$l][$n]."   mas+1 =  ".$voda[$l][$n+1]. "     z=".$z."  <br>" ;
                }
            }
            $val = $val + $z;
            //echo "Z ====".$z."  <br>";
        }

        echo_data($arr_school[$i]['plc_id'], $max_date, $teplo, $val);
        //var_dump($keys);
        $g+=count($keys);
        echo "</tr>";
        $m++;
    }
}

echo '</tbody>'
 . '</table></div></div>';

function echo_data($id, $max_date, $teplo, $val) {
    global $arr_all_limit, $date_now;

    if ($max_date == "") {
        echo "<td> - </td>";
        echo "<td>OK</td>";
        $_SESSION['rep_date'][] = "-";
        $_SESSION['rep_stat'][] = " - ";
        $_SESSION['rep_plc_err'][] = 1;
    } else {
        echo "<td>" . date('d.m.Y', strtotime($max_date)) . "</td>";
        echo "<td>OK</td>";
        $_SESSION['rep_date'][] = date('d.m.Y', strtotime($max_date));
        $_SESSION['rep_stat'][] = " - ";
        $_SESSION['rep_plc_err'][] = 1;
    }




    $key_limit = array_search($id, array_column($arr_all_limit, 'plc_id'));
    if ($key_limit !== false) {
        $lim1 = (float) $arr_all_limit[$key_limit]['teplo'];
        $lim2 = (float) $arr_all_limit[$key_limit]['voda'];
        if ($teplo == 0) {
            echo "<td>" . number_format($teplo, 2, ',', '') . "</td>";
            echo "<td> " . number_format($lim1, 2, ',', '') . " </td>";

            $_SESSION['rep_tep_val'][] = number_format($teplo, 2, ',', '');
            $_SESSION['rep_tep_lim'][] = number_format($lim1, 2, ',', '');
            $_SESSION['rep_tep_col'][] = 3;
        } elseif ($teplo > $lim1 * 0.9 and $teplo < $lim1) {
            echo "<td  class='warning'>" . number_format($teplo, 2, ',', '') . "</td>";
            echo "<td  class='warning'> " . str_replace('.', ',', $lim1) . " </td>";

            $_SESSION['rep_tep_val'][] = number_format($teplo, 2, ',', '');
            $_SESSION['rep_tep_lim'][] = number_format($lim1, 2, ',', '');
            $_SESSION['rep_tep_col'][] = 1;
        } elseif ($teplo < $lim1 * 0.9) {
            echo "<td  class='success'>" . number_format($teplo, 2, ',', '') . "</td>";
            echo "<td  class='success'> " . str_replace('.', ',', $lim1) . " </td>";

            $_SESSION['rep_tep_val'][] = number_format($teplo, 2, ',', '');
            $_SESSION['rep_tep_lim'][] = number_format($lim1, 2, ',', '');
            $_SESSION['rep_tep_col'][] = 2;
        } elseif ($teplo > $lim1) {
            echo "<td class='danger'>" . number_format($teplo, 2, ',', '') . "</td>";
            echo "<td  class='danger'> " . number_format($lim1, 2, ',', '') . " </td>";

            $_SESSION['rep_tep_val'][] = number_format($teplo, 2, ',', '');
            $_SESSION['rep_tep_lim'][] = number_format($lim1, 2, ',', '');
            $_SESSION['rep_tep_col'][] = 0;
        }

        if ($val == 0) {
            echo "<td>" . number_format($val, 2, ',', '') . "</td>";
            echo "<td> " . number_format($lim2, 2, ',', '') . " </td>";

            $_SESSION['rep_vod_val'][] =  number_format($val, 2, ',', '');
            $_SESSION['rep_vod_lim'][] = number_format($lim2, 2, ',', '');
            $_SESSION['rep_vod_col'][] = 3;
        } elseif ($val > $lim2 * 0.9 and $val < $lim2) {
            echo "<td  class='warning'>" . number_format($val, 2, ',', '') . "</td>";
            echo "<td  class='warning'> " . number_format($lim2, 2, ',', '') . " </td>";

            $_SESSION['rep_vod_val'][] = number_format($val, 2, ',', '');
            $_SESSION['rep_vod_lim'][] = number_format($lim2, 2, ',', '');
            $_SESSION['rep_vod_col'][] = 1;
        } elseif ($val < $lim2 * 0.9) {
            echo "<td  class='success'>" . number_format($val, 2, ',', ''). "</td>";
            echo "<td  class='success'> " . number_format($lim2, 2, ',', '') . " </td>";

            $_SESSION['rep_vod_val'][] = number_format($val, 2, ',', '');
            $_SESSION['rep_vod_lim'][] = number_format($lim2, 2, ',', '');
            $_SESSION['rep_vod_col'][] = 2;
        } elseif ($val > $lim2) {
            echo "<td class='danger'>" . number_format($val, 2, ',', '') . "</td>";
            echo "<td  class='danger'> " . number_format($lim2, 2, ',', '') . " </td>";

            $_SESSION['rep_vod_val'][] = number_format($val, 2, ',', '');
            $_SESSION['rep_vod_lim'][] = number_format($lim2, 2, ',', '');
            $_SESSION['rep_vod_col'][] = 0;
        }
    } else {
        echo "<td>" . number_format($teplo, 2, ',', '') . "</td>";
        echo "<td> - </td>";
        echo "<td>" . number_format($val, 2, ',', '') . "</td>";
        echo "<td> - </td>";

        $_SESSION['rep_tep_val'][] = number_format($teplo, 2, ',', '');
        $_SESSION['rep_tep_lim'][] = $lim1;
        $_SESSION['rep_tep_col'][] = 3;
        $_SESSION['rep_vod_val'][] = number_format($val, 2, ',', '');
        $_SESSION['rep_vod_lim'][] = $lim2;
        $_SESSION['rep_vod_col'][] = 3;
    }
}

function array_archive($plc_id, $dev_id) {

    global $arr_plc_id, $arr_data;
    global $teplo, $voda, $max_date, $vte;

    $keys = array_keys($arr_plc_id, $plc_id);
    for ($j = 0; $j < count($keys); $j++) {
        if (strtotime($max_date) < strtotime($arr_data[$keys[$j]]['data'])) {
            $max_date = $arr_data[$keys[$j]]['data'];
        }

        if ($arr_data[$keys[$j]]['res_id'] == 9) {
            if ($dev_id == 214 or $plc_id == 314 or $plc_id == 251 or $plc_id == 316 or $plc_id == 318) {
                $vte[] = $data_val[$b];
            } else {
                $teplo +=$arr_data[$keys[$j]]['value'];
            }
        } elseif ($arr_data[$keys[$j]]['res_id'] == 16) {
            $teplo += $arr_data[$keys[$j]]['value'];
        }

        if ($arr_data[$keys[$j]]['res_id'] == 1) {
            $voda[0][] = $arr_data[$keys[$j]]['value'];
        }
        if ($arr_data[$keys[$j]]['res_id'] == 308) {
            $voda[1][] = $arr_data[$keys[$j]]['value'];
        }
        if ($arr_data[$keys[$j]]['res_id'] == 310) {
            $voda[2][] = $arr_data[$keys[$j]]['value'];
        }
        if ($arr_data[$keys[$j]]['res_id'] == 414) {
            $voda[3][] = $arr_data[$keys[$j]]['value'];
        }
        if ($arr_data[$keys[$j]]['res_id'] == 420) {
            $voda[4][] = $arr_data[$keys[$j]]['value'];
        }
    }
}

function view_archive_group($gr_id) {
    $summ_teplo = 0;
    $summ_voda = 0;

    global $teplo, $voda, $max_date, $vte;


    $max_date = '';
    $sql_plc_group = pg_query('SELECT 
                    public.group_plc.plc_id
                  FROM
                    public.group_plc
                  WHERE
                    public.group_plc.group_id = ' . $gr_id);
    while ($resutl = pg_fetch_row($sql_plc_group)) {
        $teplo = 0;

        $vte = array();
        $voda = array();


        $sql_device = pg_query('SELECT
                                    MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
                                    FROM
                                    "Tepl"."Places_cnt" "Places_cnt1"
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                    WHERE
                                    "Places_cnt1".plc_id = ' . $resutl[0] . '');
        $dev_id = pg_fetch_result($sql_device, 0, 0);
        array_archive($resutl[0], $dev_id);


        if ($dev_id == 214 or $resutl[0] == 314 or $resutl[0] == 251 or$resutl[0] == 316 or $resutl[0] == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            //print_r($voda)."<br>";
            //echo "count mass_voda = ".count($voda)."<br>";
            $val = 0;
            for ($l = count($vte) - 1; $l >= 0; $l--) {
                //echo  "id == " .$id_plc[$a] .    "     l ==   " . $l . "  val ==  " . $vte[$l];
                if ($l - 1 >= 0) {
                    $p = $vte[$l] - $vte[$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        }
        $summ_teplo+=$teplo;

        $val = 0;
        for ($l = 0; $l < count($voda); $l++) {
            //print_r($voda[$l])."<br>";
            $n1 = count($voda[$l]) - 1;
            $z = 0;
            for ($n = 0; $n < count($voda[$l]); $n++) {

                if ($n == $n1) {
                    $z = $z;
                    //echo "n=" .$n." mas = ". $voda[$l][$n]."    z=".$z."  <br>" ;
                }
                if ($n >= 0 and $n < $n1) {
                    if ($voda[$l][$n]) {
                        $z = $z + $voda[$l][$n + 1] - $voda[$l][$n];
                    }
                    //echo "n=" .$n." mas = ". $voda[$l][$n]."   mas+1 =  ".$voda[$l][$n+1]. "     z=".$z."  <br>" ;
                }
            }
            $val = $val + $z;
            //echo "Z ====".$z."  <br>";
        }
        $summ_voda+=$val;
    }

    echo_data($gr_id, $max_date, $summ_teplo, $summ_voda);
}