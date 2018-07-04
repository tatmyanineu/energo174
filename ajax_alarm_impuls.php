<?php

include 'db_config.php';
session_start();
$date = $_POST['date_afte'];
$time = strtotime("-30 day");
$after_day = $_POST['date_now'];

echo '<h3 class="text-center">Проверка за период с ' . date("d.m.Y", strtotime($after_day)) . ' по ' . date("d.m.Y", strtotime($date)) . ' </h3>';



$sql_not_error = pg_query('SELECT DISTINCT 
  public.alarm.plc_id
FROM
  public.alarm
WHERE
  public.alarm.text_alarm LIKE \'%Импульс%\'');

while ($result = pg_fetch_row($sql_not_error)) {
    $not_error[] = $result[0];
}


$_SESSION['arr_id'] = '';
$_SESSION['arr_name'] = '';
$_SESSION['arr_addr'] = '';
$_SESSION['arr_date'] = '';
$_SESSION['arr_stat'] = '';
$_SESSION['arr_param'] = '';
$_SESSION['arr_plc_id'] = '';

echo "<table id='main_table' class='table table-bordered'>
                            <thead id='thead'>
                                <tr id='warning'>
                                <td rowspan=2 data-query='0'><b>№</b></td>
                                <td rowspan=2 data-query='1'><b>Учереждение</b></td>
                                <td rowspan=2 data-query='2'><b>Адрес</b></td>
                                <td colspan=3 ><b> Разность данных</b></td>
                                </tr>
                                <tr id='warning'>
                                    <td data-query='3'><b>Дата </b></td>
                                    <td data-query='4'><b>Статус</b></td>
                                    <td data-query='5'><b>Параметр</b></td>
                                </tr>
                            </thead><tbody>";
$sql_archive = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."Arhiv_cnt"."DataValue",
                                    "Tepl"."Places_cnt"."Name",
                                    "Tepl"."ParametrResourse"."Name",
                                    "Tepl"."Resourse_cnt"."Name",
                                    "Tepl"."ParamResPlc_cnt".prp_id
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
                                    "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
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
    $arr_value[$z] = round($resusl_archive[3], 4);
    $arr_date[$z] = $resusl_archive[0];
    $arr_name_param[$z] = $resusl_archive[5];
    $arr_name_resours[$z] = $resusl_archive[6];
    $arr_prp_id[$z] = $resusl_archive[7];
    if ($z != 0) {
        if ($arr_param[$z - 1] == 1 or $arr_param[$z - 1] == 308 or $arr_param[$z - 1] == 310 or $arr_param[$z - 1] == 414 or $arr_param[$z - 1] == 420 or $arr_param[$z - 1] == 436 or $arr_param[$z - 1] == 787 or $arr_param[$z - 1] == 2 or $arr_param[$z - 1] == 44 or $arr_param[$z - 1] == 377 or $arr_param[$z - 1] == 442 or $arr_param[$z - 1] == 402 or $arr_param[$z - 1] == 408 or $arr_param[$z - 1] == 922) {
            if ($arr_prp_id[$z] == $arr_prp_id[$z - 1]) {
                $summ +=$arr_value[$z - 1];
                $kol++;
                //echo $z . " prp=" . $arr_prp_id[$z - 1] . " " . $arr_name[$z - 1] . " " . $arr_date[$z - 1] . " " . $arr_value[$z - 1] . "" . $arr_name_resours[$z - 1] . " " . $arr_name_param[$z - 1] . " " . $summ . " " . $kol . "<br>";
            }
            if ($arr_prp_id[$z] != $arr_prp_id[$z - 1]) {
                $summ +=$arr_value[$z - 1];
                $kol++;
                $nm = $summ / $kol;
                //$z . " prp=" . $arr_prp_id[$z - 1] . " " . $arr_name[$z - 1] . " " . $arr_date[$z - 1] . " " . $arr_value[$z - 1] . "" . $arr_name_resours[$z - 1] . " " . $arr_name_param[$z - 1] . " " . $summ . " " . $kol . "<br>";

                $k = array_search($arr_id[$z - 1], $not_error);
                if ($k === false) {
                    if (round($nm, 2) == round($arr_value[$z - 1], 2)) {

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
                        $date_arch = explode(" ", $arr_date[$z - 1]);
                        $date_b = date("d.m.Y", strtotime($date_arch[0]));
                        $n++;
                        echo "<tr  data-href='object.php?id_object=" . $arr_id[$z - 1] . "' id ='hover'>"
                        . "<td>" . $n . "</td>"
                        . "<td>" . $arr_name[$z - 1] . "</td>"
                        . "<td>" . $res[0] . " " . $res[1] . "</td>"
                        . "<td>" . $date_b . "</td>"
                        . "<td> Нет импульса</td>"
                        . "<td>" . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . "</td>"
                        . "</tr>";

                        $_SESSION['arr_id'][] = $n;
                        $_SESSION['arr_name'][] = $arr_name[$z - 1];
                        $_SESSION['arr_addr'][] = $res[0] . " " . $res[1];
                        $_SESSION['arr_date'][] = $date_b;
                        $_SESSION['arr_stat'][] = 'Нет импульса';
                        $_SESSION['arr_param'][] = $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1];
                        $_SESSION['arr_plc_id'][] = $arr_id[$z - 1];

                        //echo $z . " " . $arr_name[$z - 1] . " " . $arr_date[$z - 1] . " " . $arr_value[$z - 1] . "" . $arr_name_resours[$z - 1] . " " . $arr_name_param[$z - 1] . " " . $summ . " " . $kol . "<br>";
                        //echo "дохлый импульс<br>";
                    }
                }
                $summ = 0;
                $kol = 0;
            }
            if ($z + 1 == pg_num_rows($sql_archive)) {

                $summ +=$arr_value[$z];
                $kol++;
                $nm = $summ / $kol;
                if ($k === false) {
                    if (round($nm, 2) == round($arr_value[$z - 1], 2)) {

                        $sql_addres = pg_query('SELECT DISTINCT
                        "Tepl"."PropPlc_cnt"."ValueProp",
                        "PropPlc_cnt1"."ValueProp"
                        FROM
                        "Tepl"."Places_cnt"
                        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                        WHERE
                        "Tepl"."Places_cnt".plc_id = ' . $arr_id[$z] . ' AND
                        "PropPlc_cnt1".prop_id = 26 AND
                        "Tepl"."PropPlc_cnt".prop_id = 27');
                        $res = pg_fetch_row($sql_addres);
                        $n++;
                        echo "<tr  data-href='object.php?id_object=" . $arr_id[$z] . "' id ='hover'>"
                        . "<td>" . $n . "</td>"
                        . "<td>" . $arr_name[$z] . "</td>"
                        . "<td>" . $res[0] . " " . $res[1] . "</td>"
                        . "<td>" . $date_b . "</td>"
                        . "<td> Нет импульса</td>"
                        . "<td>" . $arr_name_resours[$z] . ": " . $arr_name_param[$z] . "</td>"
                        . "</tr>";

                        $_SESSION['arr_id'][] = $n;
                        $_SESSION['arr_name'][] = $arr_name[$z];
                        $_SESSION['arr_addr'][] = $res[0] . " " . $res[1];
                        $_SESSION['arr_date'][] = $date_b;
                        $_SESSION['arr_stat'][] = 'Нет импульса';
                        $_SESSION['arr_param'][] = $arr_name_resours[$z] . ": " . $arr_name_param[$z];
                        $_SESSION['arr_plc_id'][] = $arr_id[$z];

                        //echo $z + 1 . " " . $arr_name[$z] . " " . $arr_date[$z] . " " . $arr_value[$z] . "" . $arr_name_resours[$z] . " " . $arr_name_param[$z] . " " . $summ . " " . $kol . "<br>";
                        //echo "дохлый импульс<br>";
                    }
                }
            }
        }
    }
    $z++;
}

//$for($i=0;$i<count($arr))

$n++;
echo "</tbody></table>";
?>