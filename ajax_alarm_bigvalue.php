<?php

include 'db_config.php';
session_start();
$date = $_POST['date_afte'];
$time = strtotime("-30 day");
$after_day = $_POST['date_now'];
$kor_value = $_POST['kor_value'];
$type_arch = $_POST['type_arch'];
$alarm_box = $_POST['alarm_box'];
echo '<h3 class="text-center">Проверка за период с ' . date("d.m.Y", strtotime($after_day)) . ' по ' . date("d.m.Y", strtotime($date)) . ' </h3>';

$_SESSION['arr_id'] = '';
$_SESSION['arr_name'] = '';
$_SESSION['arr_addr'] = '';
$_SESSION['arr_date'] = '';
$_SESSION['arr_stat'] = '';
$_SESSION['arr_param'] = '';
$_SESSION['arr_plc_id'] = '';

$sql_ticket = pg_query('SELECT DISTINCT 
  public.ticket.plc_id
FROM
  public.ticket
WHERE
  public.ticket.status < 4');

while ($row = pg_fetch_row($sql_ticket)) {
    $ticket[] = $row[0];
}

$sql_alarm = pg_query('SELECT DISTINCT 
  public.alarm.plc_id
FROM
  public.alarm
WHERE
  public.alarm.text_alarm LIKE \'%Импульс%\' OR
  public.alarm.text_alarm LIKE \'%Поверка%\'');

while ($result = pg_fetch_row($sql_alarm)) {
    $alarm[] = $result[0];
}


echo "<table id='main_table' class='table table-bordered'>
                            <thead id='thead'>
                                <tr id='warning'>
                                <td rowspan=2 data-query='0'><b>№</b></td>
                                <td rowspan=2 data-query='1'><b>Учереждение</b></td>
                                <td rowspan=2 data-query='2'><b>Адрес</b></td>
                                <td colspan=3 ><b> Разность данных</b></td>
                                <td rowspan=2 data-query='5'><b>С.О.</b></td>
                                </tr>
                                <tr id='warning'>
                                    <td data-query='3'><b>Дата </b></td>
                                    <td data-query='4'><b>Разность</b></td>
                                    <td><b>Параметр</b></td>
                                </tr>
                            </thead><tbody>";
$sql_archive = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."Arhiv_cnt"."DataValue",
                                    "Tepl"."Places_cnt"."Name",
                                    "Tepl"."ParametrResourse"."Name",
                                    "Tepl"."Resourse_cnt"."Name"
                                  FROM
                                    "Tepl"."ParamResPlc_cnt"
                                    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                    INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
                                    INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                                  WHERE
                                    "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . ' AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND
                                    "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND
                                  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\'AND 
                                    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
                                  ORDER BY
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id"');

//echo pg_num_rows($sql_archive) . "<br>";
$z = 0;
$kol = 0;
$n = 0;
while ($resusl_archive = pg_fetch_row($sql_archive)) {
    $arr_id[$z] = $resusl_archive[2];
    $arr_name[$z] = $resusl_archive[4];
    $arr_param[$z] = $resusl_archive[1];
    $arr_value[$z] = $resusl_archive[3];
    $arr_date[$z] = $resusl_archive[0];
    $arr_name_param[$z] = $resusl_archive[5];
    $arr_name_resours[$z] = $resusl_archive[6];
    if ($z != 0) {
        if ($resusl_archive[1] == $arr_param[$z - 1]) {
            if ($resusl_archive[2] == $arr_id[$z - 1]) {
                $summ += $arr_value[$z];
                $kol++;
                //echo "kol = ". $kol. " name = " . $arr_name[$z] . "  param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
            }
            if ($resusl_archive[2] != $arr_id[$z - 1]) {
                $sred = sqrt($summ / $kol);
                for ($i = 1; $i < $kol; $i++) {
                    if ($arr_param[$z - 1] == 1 or $arr_param[$z - 1] == 308 or $arr_param[$z - 1] == 310 or $arr_param[$z - 1] == 414 or $arr_param[$z - 1] == 420 or $arr_param[$z - 1] == 436 or $arr_param[$z - 1] == 787 or $arr_param[$z - 1] == 2 or $arr_param[$z - 1] == 44 or $arr_param[$z - 1] == 377 or $arr_param[$z - 1] == 442 or $arr_param[$z - 1] == 402 or $arr_param[$z - 1] == 408 or $arr_param[$z - 1] == 922) {
                        $raz = $arr_value[$z - $i] - $arr_value[$z - $i - 1];
                        if ($raz > $kor_value) {
                            $sql_addres = pg_query('SELECT DISTINCT 
                                                                        "Tepl"."PropPlc_cnt"."ValueProp",
                                                                        "PropPlc_cnt1"."ValueProp"
                                                                      FROM
                                                                        "Tepl"."Places_cnt"
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                                      WHERE
                                                                        "Tepl"."Places_cnt".plc_id = ' . $arr_id[$z - 1] . ' AND 
                                                                        "PropPlc_cnt1".prop_id = 26 AND 
                                                                        "Tepl"."PropPlc_cnt".prop_id = 27');
                            $res = pg_fetch_row($sql_addres);
                            $n++;
                            if ($type_arch == 1) {
                                $date_b = date("d.m.Y H:i", strtotime($arr_date[$z - $i]));
                            } elseif ($type_arch == 2) {
                                $date_b = date("d.m.Y", strtotime('-1 day', strtotime($arr_date[$z - $i])));
                            }

                            $plc_in_arr = array_search($arr_id[$z - 1], $ticket);

                            if ($alarm_box == 0) {
                                echo "<tr id ='hover'>";
                                echo "<td>" . $n . "</td>"
                                        . "<td><a  class='object' id='" . $arr_id[$z - 1] . "'>" . $arr_name[$z - 1] . "</a></td>"
                                        . "<td>" . $res[0] . " " . $res[1] . "</td><td>" . $date_b . "</td>"
                                        . "<td>" . number_format($raz, 2) . "</td>"
                                        . "<td>" . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . "</td>";
                                if ($plc_in_arr !== FALSE) {
                                    echo '<td class="text-center"><a class="tickets" id="' . $arr_id[$z - 1] . '"><span class="glyphicon glyphicon-wrench"></span></a></td>';
                                } else {
                                    echo '<td></td>';
                                }
                                echo "</tr>";
                            } else {
                                $key_alarm = array_search($arr_id[$z - 1], $alarm);
                                if ($key_alarm === false) {
                                    echo "<tr id ='hover'>";
                                    echo "<td>" . $n . "</td><td><a  class='object' id='" . $arr_id[$z - 1] . "'>" . $arr_name[$z - 1] . "</a></td>"
                                            . "<td>" . $res[0] . " " . $res[1] . "</td>"
                                            . "<td>" . $date_b . "</td><td>" . number_format($raz, 2) . "</td>"
                                            . "<td>" . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . "</td>";
                                    if ($plc_in_arr !== FALSE) {
                                        echo '<td class="text-center"><a class="tickets" id="' . $arr_id[$z - 1] . '"><span class="glyphicon glyphicon-wrench"></span></a></td>';
                                    } else {
                                        echo '<td></td>';
                                    }
                                    echo "</tr>";
                                }
                            }

                            //echo " name = " . $arr_name[$z - 1] . "  param = " . $arr_param[$z - 1];
                            $_SESSION['arr_id'][] = $n;
                            $_SESSION['arr_name'][] = $arr_name[$z - 1];
                            $_SESSION['arr_addr'][] = $res[0] . " " . $res[1];
                            $_SESSION['arr_date'][] = $date_b;
                            $_SESSION['arr_stat'][] = number_format($raz, 2);
                            $_SESSION['arr_param'][] = $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1];
                            $_SESSION['arr_plc_id'][] = $arr_id[$z - 1];
                            //echo "дата = " . $arr_date[$z - $i] . "  значение= " . $arr_value[$z - $i] . " дата= " . $arr_date[$z - $i - 1] . " значение= " . $arr_value[$z - $i - 1] . " разность =   " . $raz . "<br>";

                            break;
                        }
                    }
                }

                $summ = 0;
                $kol = 0;
                $kol++;
                $summ += $arr_value[$z];
                //echo " name = " . $arr_name[$z] . "param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
            }
        }
        if ($resusl_archive[1] != $arr_param[$z - 1]) {
            $sred = $summ / $kol;

            for ($i = 1; $i < $kol; $i++) {
                if ($arr_param[$z - 1] == 1 or $arr_param[$z - 1] == 308 or $arr_param[$z - 1] == 310 or $arr_param[$z - 1] == 414 or $arr_param[$z - 1] == 420 or $arr_param[$z - 1] == 436 or $arr_param[$z - 1] == 787 or $arr_param[$z - 1] == 2 or $arr_param[$z - 1] == 44 or $arr_param[$z - 1] == 377 or $arr_param[$z - 1] == 442 or $arr_param[$z - 1] == 402 or $arr_param[$z - 1] == 408 or $arr_param[$z - 1] == 922) {

                    $raz = $arr_value[$z - $i] - $arr_value[$z - $i - 1];
                    if ($raz > $kor_value) {
                        $sql_addres = pg_query('SELECT DISTINCT 
                                                                        "Tepl"."PropPlc_cnt"."ValueProp",
                                                                        "PropPlc_cnt1"."ValueProp"
                                                                      FROM
                                                                        "Tepl"."Places_cnt"
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                                      WHERE
                                                                        "Tepl"."Places_cnt".plc_id = ' . $arr_id[$z - 1] . ' AND 
                                                                        "PropPlc_cnt1".prop_id = 26 AND 
                                                                        "Tepl"."PropPlc_cnt".prop_id = 27');
                        $res = pg_fetch_row($sql_addres);
                        $n++;

                        if ($type_arch == 1) {
                            $date_b = date("d.m.Y H:i", strtotime($arr_date[$z - $i]));
                        } elseif ($type_arch == 2) {
                            $date_b = date("d.m.Y", strtotime('-1 day', strtotime($arr_date[$z - $i])));
                        }

                        $plc_in_arr = array_search($arr_id[$z - 1], $ticket);
                        if ($alarm_box == 0) {
                            echo "<tr id ='hover'>";
                            echo "<td>" . $n . "</td>"
                                    . "<td><a class='object' id='" . $arr_id[$z - 1] . "'>" . $arr_name[$z - 1] . "</a></td>"
                                    . "<td>" . $res[0] . " " . $res[1] . "</td>"
                                    . "<td>" . $date_b . "</td><td>" . number_format($raz, 2) . "</td>"
                                    . "<td>" . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . "</td>";
                            if ($plc_in_arr !== FALSE) {
                                echo '<td class="text-center"><a class="tickets" id="' . $arr_id[$z - 1] . '"><span class="glyphicon glyphicon-wrench"></span></a></td>';
                            } else {
                                echo '<td></td>';
                            }
                            echo "</tr>";
                        } else {
                            $key_alarm = array_search($arr_id[$z - 1], $alarm);
                            if ($key_alarm === false) {
                                echo "<tr id ='hover'>";
                                echo "<td>" . $n . "</td>"
                                        . "<td><a class='object' id='" . $arr_id[$z - 1] . "'>" . $arr_name[$z - 1] . "</a></td>"
                                        . "<td>" . $res[0] . " " . $res[1] . "</td><td>" . $date_b . "</td>"
                                        . "<td>" . number_format($raz, 2) . "</td><td>" . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . "</td>";
                                if ($plc_in_arr !== FALSE) {
                                    echo '<td class="text-center"><a class="tickets" id="' . $arr_id[$z - 1] . '"><span class="glyphicon glyphicon-wrench"></span></a></td>';
                                } else {
                                    echo '<td></td>';
                                }
                                echo "</tr>";
                            }
                        }


                        //echo "name = " . $arr_name[$z - 1] . "  param = " . $arr_param[$z - 1];
                        //echo "  дата = " . $arr_date[$z - $i] . "  значение= " . $arr_value[$z - $i] . " дата= " . $arr_date[$z - $i - 1] . " значение= " . $arr_value[$z - $i - 1] . " разность =   " . $raz . "<br>";
                        $_SESSION['arr_id'][] = $n;
                        $_SESSION['arr_name'][] = $arr_name[$z - 1];
                        $_SESSION['arr_addr'][] = $res[0] . " " . $res[1];
                        $_SESSION['arr_date'][] = $date_b;
                        $_SESSION['arr_stat'][] = number_format($raz, 2);
                        $_SESSION['arr_param'][] = $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1];
                        $_SESSION['arr_plc_id'][] = $arr_id[$z - 1];
                        break;
                    }
                }
            }
            $summ = 0;
            $kol = 0;
            $kol++;
            $summ += $arr_value[$z];
            //echo "kol = ". $kol. " name = " . $arr_name[$z] . "param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
        }
    } else {
        if ($resusl_archive[1] == $arr_param[$z]) {
            $summ += $arr_value[$z];
            $kol++;
            //echo "kol = ". $kol. " name = " . $arr_name[$z] . "param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
        }
    }

    $z++;
}

//$for($i=0;$i<count($arr))

$n++;
echo "</tbody></table>";
?>