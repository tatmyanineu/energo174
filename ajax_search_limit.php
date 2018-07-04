<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_config.php';
session_start();




$id_distinct = explode(" ", $_POST['id_dist']);
$search = $_POST['search'];

//echo $search . "<br>";
$string_name = mb_strtoupper($search);
//echo $string_name . "<br>";
$string_street = mb_convert_case($search, MB_CASE_TITLE, "UTF-8");
//echo $string_street . "<br>";
//echo $_POST['year']."<br>";
//echo $_POST['month']."<br>";

if (strtotime(date('Y-m-d')) == strtotime(date(''.$_POST['year'].'-'.$_POST['month'].'-01'))) {
    //echo "Первое число месяца<br>";
    $mon = strtotime("-1 month");
    $month = date('m', $mon);
    //echo $month."<br>";
    
    $num = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);
    $first_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-01');
    $first_date = date('Y-m-d', strtotime("+1 day", strtotime($first_date)));
    $first_date = date('Y-m-d', strtotime("-1 month", strtotime($first_date)));
    //echo $first_date."<br>";
    
    $second_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-' . $num);
    $second_date = date('Y-m-d', strtotime("+1 day", strtotime($second_date)));
    $second_date = date('Y-m-d', strtotime("-1 month", strtotime($second_date)));
    //echo $second_date;
    
    $date_now = $second_date;
    
}else{
    //echo "Не первое число месяца<br>";
    $month = $_POST['month'];
    //echo $month."<br>";
    $num = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);
    $first_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-01');
    $first_date = date('Y-m-d', strtotime("+1 day", strtotime($first_date)));
    
    $second_date = date('' . $_POST['year'] . '-' . $_POST['month'] . '-' . $num);
    $second_date = date('Y-m-d', strtotime("+1 day", strtotime($second_date)));
    
    if(strtotime( date('m')) == strtotime(date($month))){
        $date_now = date('Y-m-d');
    } else{
        $date_now =$second_date;
    }
    
    //echo $date_now;
    //$date_now =$second_date;
    
}


switch ($month) {
    case "01":$limit_month = 29;
        $month_name = "ЯНВАРЬ";
        break;

    case "02":$limit_month = 30;
        $month_name = "ФЕВРАЛЬ";
        break;

    case "03":$limit_month = 31;
        $month_name = "МАРТ";
        break;

    case "04":$limit_month = 32;
        $month_name = "АПРЕЛЬ";
        break;

    case "05":$limit_month = 33;
        $month_name = "МАЙ";
        break;

    case "06":$limit_month = 34;
        $month_name = "ИЮНЬ";
        break;

    case "07":$limit_month = 35;
        $month_name = "ИЮЛЬ";
        break;

    case "08":$limit_month = 36;
        $month_name = "АВГУСТ";
        break;

    case "09":$limit_month = 37;
        $month_name = "СЕНТЯБРЬ";
        break;

    case "10":$limit_month = 38;
        $month_name = "ОКТЯБРЬ";
        break;

    case "11":$limit_month = 39;
        $month_name = "НОЯБРЬ";
        break;

    case "12":$limit_month = 40;
        $month_name = "ДЕКАБРЬ";
        break;
}

echo "<h2 id='center_h1'>Отчет за ".$month_name." </h2>";


$sql_seach_school_info = pg_query('SELECT 
  "Places_cnt1"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Places_cnt1".plc_id
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
  UPPER("Tepl"."PropPlc_cnt"."ValueProp") LIKE UPPER(\'%' . $string_street . '%\') OR 
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
  UPPER("PropPlc_cnt1"."ValueProp") LIKE UPPER(\'%' . $search . '%\') OR 
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
  UPPER("Places_cnt1"."Name") LIKE UPPER(\'%' . $string_name . '%\')
ORDER BY
  "Tepl"."Places_cnt".plc_id');
if (pg_num_rows($sql_seach_school_info) > 0) {
    echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr  id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (ГКал)</b></td>
            <td colspan=2><b>Вода (м<sup>3</sup>)</b></td>
           </tr> <tr  id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
    $school_name = '';
    $school_hs = '';
    $school_str = '';
    $school_id = '';
    while ($result = pg_fetch_row($sql_seach_school_info)) {
        $school_name[] = $result[0];
        $school_hs[] = $result[1];
        $school_str[] = $result[2];
        $school_id[] = $result[3];
    }
    $sql_search_school_archive = pg_query('SELECT DISTINCT 
                                            ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",                                            
                                            "Tepl"."Arhiv_cnt"."DataValue",
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id",
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
                                          WHERE
                                            "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                                            "PropPlc_cnt1".prop_id = 26 AND 
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $first_date . '\' AND
                                              "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $second_date . '\' AND 
                                            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                                            "Places_cnt1"."Name" LIKE \'%' . $string_name . '%\'OR 
                                            "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                                            "PropPlc_cnt1".prop_id = 26 AND 
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $first_date . '\' AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $second_date . '\' AND 
                                            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                                            "Tepl"."PropPlc_cnt"."ValueProp" LIKE \'%' . $string_street . '%\' OR 
                                            "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                                            "PropPlc_cnt1".prop_id = 26 AND 
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $first_date . '\' AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $second_date . '\' AND 
                                            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                                            "PropPlc_cnt1"."ValueProp" LIKE \'%' . $search . '%\'
                                          ORDER BY
                                            "Places_cnt1".plc_id,
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id"');
    $date_val = "";
    $data_val = "";
    $res_id = "";
    $id_plc = "";
    while ($result_archive = pg_fetch_row($sql_search_school_archive)) {
        $date_val[] = $result_archive[0];
        $data_val[] = $result_archive[1];
        $res_id[] = $result_archive[2];
        $id_plc[] = $result_archive[3];
    }
     echo "<tbody>";
    $m=1;
    for ($sc = 0; $sc < count($school_id); $sc++) {
        $td = 0;
        echo "<tr data-href='object.php?id_object=" . $school_id[$sc] . "' id='hover' >";

        echo "<td>" . $m . "</td>";

        echo "<td>" . $school_name[$sc] . "</td>";

        echo "<td>" . $school_hs[$sc] . "  " . $school_str[$sc] . "</td>";
        
        
        
        
        $max_date = ''; 
                       for ($a = 0; $a < count($id_plc); $a++) {
                    if ($school_id[$sc] == $id_plc[$a]) {
                        if (strtotime($max_date) < strtotime($date_val[$a])) {
                            $max_date = $date_val[$a];
                        }
                        if ($id_plc[$a] != $id_plc[$a + 1]) {
                            $td = 1;

                            $date_value = explode(' ', $max_date);
                            $max_date='';
                            $kol_day = (strtotime($date_now) - strtotime($date_value[0])) / (60 * 60 * 24);
                            $date_b = date("d.m.Y", strtotime("-1 day", strtotime($date_value[0])));
                            if ($kol_day > 3) {
                                echo "<td class='danger'>" . $date_b . "</td>";
                                //echo "  d_pok =".$date_b."   td = ".$td.  "<br>";
                                $_SESSION['rep_date'][] = $date_b;
                                echo "<td class='danger'>Нет связи </td>";
                                $_SESSION['rep_stat'][] = 'Нет связи ';
                                $_SESSION['rep_plc_err'][] = 0;
                            } else {
                                echo "<td>" . $date_b . "</td>";
                                //echo "  d_pok =".$date_b."   td = ".$td.  " <br>";
                                $_SESSION['rep_date'][] = $date_b;
                                echo "<td>OK</td>";
                                $_SESSION['rep_stat'][] = 'OK';
                                $_SESSION['rep_plc_err'][] = 1;
                            }
                            
                            $teplo = 0;
                            $voda = '';
                            $vte = '';
                            $sql_device = pg_query('SELECT
                                    MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
                                    FROM
                                    "Tepl"."Places_cnt" "Places_cnt1"
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                    WHERE
                                    "Places_cnt1".plc_id = ' . $id_plc[$a] . '');
                            $row_device = pg_fetch_row($sql_device);
                            for ($b = 0; $b < count($id_plc); $b++) {
                                if ($id_plc[$b] == $id_plc[$a]) {
                                    if ($res_id[$b] == 9) {
                                        if ($row_device[0] == 214 or $id_plc[$a] == 314 or $id_plc[$a] == 251 or $id_plc[$a] == 316 or $id_plc[$a] == 318) {
                                            $vte[] = $data_val[$b];
                                        }
                                        $teplo = $teplo + $data_val[$b];
                                    }
                                    if ($res_id[$b] == 16) {
                                        $teplo = $teplo + $data_val[$b];
                                    }
                                }
                            }
                            

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

                            //echo "  o==".$o;


                            $sql_limit = pg_query('SELECT
                                    "Tepl"."PropPlc_cnt"."ValueProp"
                                    FROM
                                    "Tepl"."Places_cnt"
                                    INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                    WHERE
                                    "Tepl"."Places_cnt".plc_id = ' . $id_plc[$a] . ' AND
                                    "Tepl"."PropPlc_cnt".prop_id = ' . $limit_month . '');
                            $limit_all = pg_fetch_row($sql_limit);

                            $exp_limit = explode(";", $limit_all[0]);

                            $limit = str_replace(',', '.', $exp_limit[0]);
                            $limet = str_replace(',', '.', $exp_limit[0]) * 0.9;

                            //echo '4ixto--=' . $exp_limit[0] . 'limit = ' . $limit . '        90% limit =   ' . $limet . '     teplo = ' . $teplo . ' <br>';

                            if ($limit == '') {
                                echo "<td>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
                                echo "<td> - </td>";

                                $_SESSION['rep_tep_val'][] = substr(str_replace('.', ',', $teplo), 0, 6);
                                $_SESSION['rep_tep_lim'][] = '-';
                                $_SESSION['rep_tep_col'][] = 3;
                            } elseif ($teplo > $limit) {
                                //лимит
                                echo "<td class='danger'>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
                                echo "<td class='danger'>" . str_replace('.', ',', $limit) . "</td>";
                                $_SESSION['rep_tep_val'][] = substr(str_replace('.', ',', $teplo), 0, 6);
                                $_SESSION['rep_tep_lim'][] = str_replace('.', ',', $limit);
                                $_SESSION['rep_tep_col'][] = 0;
                            } elseif ($teplo < $limet) {
                                //лимит
                                echo "<td class='success'>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
                                echo "<td class='success'>" . str_replace('.', ',', $limit) . "</td>";
                                $_SESSION['rep_tep_val'][] = substr(str_replace('.', ',', $teplo), 0, 6);
                                $_SESSION['rep_tep_lim'][] = str_replace('.', ',', $limit);
                                $_SESSION['rep_tep_col'][] = 2;
                            } elseif ($teplo > $limet and $teplo < $limit) {
                                echo "<td class='warning'>" . substr(str_replace('.', ',', $teplo), 0, 6) . "</td>";
                                echo "<td class='warning'>" . str_replace('.', ',', $limit) . "</td>";
                                $_SESSION['rep_tep_val'][] = substr(str_replace('.', ',', $teplo), 0, 6);
                                $_SESSION['rep_tep_lim'][] = str_replace('.', ',', $limit);
                                $_SESSION['rep_tep_col'][] = 1;
                            }

                            for ($b = 0; $b < count($id_plc); $b++) {
                                if ($id_plc[$b] == $id_plc[$a]) {

                                    if ($res_id[$b] == 1) {
                                        $voda[0][] = $data_val[$b];
                                    }
                                    if ($res_id[$b] == 308) {
                                        $voda[1][] = $data_val[$b];
                                    }
                                    if ($res_id[$b] == 310) {
                                        $voda[2][] = $data_val[$b];
                                    }
                                    if ($res_id[$b] == 414) {
                                        $voda[3][] = $data_val[$b];
                                    }
                                    if ($res_id[$b] == 420) {
                                        $voda[4][] = $data_val[$b];
                                    }
                                }
                            }

                            $z = 0;
                            //print_r($voda)."<br>";
                            //echo "count mass_voda = ".count($voda)."<br>";
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

                            $limit_voda = str_replace(',', '.', $exp_limit[1]);
                            $limet_voda = str_replace(',', '.', $exp_limit[1]) * 0.9;

                            if ($limit_voda == '') {
                                echo "<td>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";
                                echo "<td> - </td>";
                                $_SESSION['rep_vod_val'][] = substr(str_replace('.', ',', $val), 0, 6);
                                $_SESSION['rep_vod_lim'][] = '-';
                                $_SESSION['rep_vod_col'][] = 3;
                            } elseif ($val > $limit_voda) {
                                //лимит
                                echo "<td class='danger'>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";
                                echo "<td class='danger'>" . str_replace('.', ',', $limit_voda) . "</td>";
                                $_SESSION['rep_vod_val'][] = substr(str_replace('.', ',', $val), 0, 6);
                                $_SESSION['rep_vod_lim'][] = str_replace('.', ',', $limit_voda);
                                $_SESSION['rep_vod_col'][] = 0;
                            } elseif ($val < $limet_voda) {
                                //лимит
                                echo "<td class='success'>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";
                                echo "<td class='success'>" . str_replace('.', ',', $limit_voda) . "</td>";
                                $_SESSION['rep_vod_val'][] = substr(str_replace('.', ',', $val), 0, 6);
                                $_SESSION['rep_vod_lim'][] = str_replace('.', ',', $limit_voda);
                                $_SESSION['rep_vod_col'][] = 2;
                            } elseif ($val > $limet_voda and $val < $limit_voda) {
                                echo "<td class='warning'>" . substr(str_replace('.', ',', $val), 0, 6) . "</td>";
                                echo "<td class='warning'>" . str_replace('.', ',', $limit_voda) . "</td>";
                                $_SESSION['rep_vod_val'][] = substr(str_replace('.', ',', $val), 0, 6);
                                $_SESSION['rep_vod_lim'][] = str_replace('.', ',', $limit_voda);
                                $_SESSION['rep_vod_col'][] = 1;
                            }
                            echo "</tr>";
                        }
                    }
                }
                if ($td == 0) {
                    echo "<td class='danger'><b>Нет данных</b></td> "
                    . "<td class='danger'><b>Нет связи</b></td>"
                    . "<td class='danger'><b>-</b></td>"
                    . "<td class='danger'><b>-</b></td>"
                    . "<td class='danger'><b>-</b></td>"
                    . "<td class='danger'><b>-</b></td>";
                    //echo "--->Нет данных<----<br>";
                    $_SESSION['rep_date'][] = 'Нет данных';
                    $_SESSION['rep_stat'][] = ' Нет связи';
                    $_SESSION['rep_plc_err'][] = 0;

                    $_SESSION['rep_tep_val'][] = '-';
                    $_SESSION['rep_tep_lim'][] = '-';
                    $_SESSION['rep_tep_col'][] = 0;

                    $_SESSION['rep_vod_val'][] = '-';
                    $_SESSION['rep_vod_lim'][] = '-';
                    $_SESSION['rep_vod_col'][] = 0;
                }
                $m++;
                echo "</tr>"; 
    }
    echo "</tbody></table>";
} else {
    echo "<h3>Данных по вашему запросу не найдено</h3>";
}