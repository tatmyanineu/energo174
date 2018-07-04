<?php

include 'db_config.php';
session_start();
$date = $_POST['date_afte'];
$time = strtotime("-10 day");
$after_day = $_POST['date_now'];

echo '<h3 class="text-center">Проверка за период с ' . date("d.m.Y", strtotime($after_day)) . ' по ' . date("d.m.Y", strtotime($date)) . ' </h3>';

$sql_not_error = pg_query('SELECT DISTINCT 
  public.alarm.plc_id
FROM
  public.alarm
WHERE
  public.alarm.text_alarm LIKE \'%Поверка вода%\' OR
  public.alarm.text_alarm LIKE \'%Наводка%\' OR
  public.alarm.text_alarm LIKE \'%Технические работы%\' OR
  public.alarm.text_alarm LIKE \'%Заблокированная Sim-карта%\'');


while ($result = pg_fetch_row($sql_not_error)) {
    $not_error[] = $result[0];
}

$_SESSION['arr_id'] = '';
$_SESSION['arr_name'] = '';
$_SESSION['arr_addr'] = '';
$_SESSION['arr_date'] = '';
$_SESSION['arr_stat'] = '';
$_SESSION['arr_plc_id'] = '';
echo "<table id='main_table' class='table table-bordered'>
                            <thead id='thead'>
                                <tr id='warning'>
                                <td rowspan=2 data-query='0'><b>№</b></td>
                                <td rowspan=2 data-query='1'><b>Учереждение</b></td>
                                <td rowspan=2 data-query='2'><b>Адрес</b></td>
                                <td colspan=2 ><b>Передача данных</b></td>
                                </tr>
                                <tr id='warning'>
                                    <td data-query='3'><b>Дата обновления</b></td>
                                    <td data-query='4'><b>Статус</b></td>
                                </tr>
                            </thead><tbody>";


$school_name = '';
$school_hs = '';
$school_str = '';
$school_id = '';


$sql_param_water = pg_query('SELECT DISTINCT 
                            "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                            "Tepl"."Places_cnt".plc_id,
                            "Tepl"."Places_cnt"."Name",
                            "Tepl"."ParametrResourse"."Name",
                            "Tepl"."Resourse_cnt"."Name"
                          FROM
                            "Tepl"."ParamResPlc_cnt"
                            INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                            INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                            INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                            INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                            INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
                            INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                          WHERE
                            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' 
                          ORDER BY
                            "Tepl"."Places_cnt".plc_id,
                            "Tepl"."ParamResPlc_cnt"."ParamRes_id"');
$water_param_id = '';
while ($result_water_param = pg_fetch_row($sql_param_water)) {
    if ($result_water_param[0] == 1 or $result_water_param[0] == 308 or $result_water_param[0] == 310
            or $result_water_param[0] == 414 or $result_water_param[0] == 420 or $result_water_param[0] == 436
            or $result_water_param[0] == 787 or $result_water_param[0] == 2 or $result_water_param[0] == 44
            or $result_water_param[0] == 377 or $result_water_param[0] == 442 or $result_water_param[0] == 402
            or $result_water_param[0] == 408 or $result_water_param[0] == 922) {
        $water_param_id[] = $result_water_param[1];
        $water_param_res[] = $result_water_param[0];
        $water_param_name[] = $result_water_param[2];
        $water_param_type[] = $result_water_param[3];
        $water_param_type_name[] = $result_water_param[4];
    }
}



$sql_water = pg_query('SELECT DISTINCT 
                                           ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                                            "Tepl"."Places_cnt".plc_id,
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                            "Tepl"."Places_cnt"."Name"
                                          FROM
                                            "Tepl"."ParamResPlc_cnt"
                                            INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                            INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                            INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                            INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                            INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                          WHERE
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                                            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' 
                                          ORDER BY
                                            "Tepl"."Places_cnt".plc_id,
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                            "Tepl"."Arhiv_cnt"."DateValue"');
$water_date = '';
$water_id = '';
$water_res = '';
$z = 0;
$n = 0;
while ($result_water = pg_fetch_row($sql_water)) {

    if ($result_water[2] == 1 or $result_water[2] == 308 or $result_water[2] == 310
            or $result_water[2] == 414 or $result_water[2] == 420 or $result_water[2] == 436
            or $result_water[2] == 787 or $result_water[2] == 2 or $result_water[2] == 44
            or $result_water[2] == 377 or $result_water[2] == 442 or $result_water[2] == 402
            or $result_water[2] == 408 or $result_water[2] == 922) {
        $water_date[] = $result_water[0];
        $water_id[] = $result_water[1];
        $water_res[] = $result_water[2];
        $water_name[] = $result_water[3];
        //echo $water_name[$z] . " " . $water_date[$z] . " " . $water_res[$z] . "<br>";
    }
}
//echo pg_num_rows($sql_water);
//echo " z= " . $z . "<br>";
//print_r($arr_id);
//print_r($water_param_id);
//echo " z= " . $z . "<br>";
//print_r($water_param_res);
$n = 0;

for ($j = 0; $j < count($water_param_id); $j++) {
    $resuours = 0;
    for ($i = 0; $i < count($water_id); $i++) {

        if ($water_id[$i] == $water_param_id[$j]) {
            $name = $water_name[$i];
            $res = $water_param_res[$j];
            if ($water_res[$i] == $water_param_res[$j]) {

                if ($water_id[$i] != $water_id[$i + 1] or $water_res[$i] != $water_res[$i + 1] or count($water_res) == $i + 1) {
                    $resuours = 1;
                    if (strtotime($water_date[$i]) != strtotime($date)) {

                        $k = array_search($water_id[$i], $not_error);
                        if ($k === false) {
                            $n++;
                            $sql_addres = pg_query('SELECT DISTINCT 
                                                                        "Tepl"."PropPlc_cnt"."ValueProp",
                                                                        "PropPlc_cnt1"."ValueProp"
                                                                      FROM
                                                                        "Tepl"."Places_cnt"
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                                      WHERE
                                                                        "Tepl"."Places_cnt".plc_id = ' . $water_id[$i] . ' AND 
                                                                        "PropPlc_cnt1".prop_id = 26 AND 
                                                                        "Tepl"."PropPlc_cnt".prop_id = 27');
                            $res = pg_fetch_row($sql_addres);
                            echo '<tr data-href="object.php?id_object=' . $water_id[$i] . ' ">'
                            . '<td>' . $n . '</td>'
                            . '<td>' . $water_name[$i] . '</td>'
                            . '<td>' . $res[0] . ' ' . $res[1] . '</td>'
                            . '<td>' . date('d.m.Y', strtotime($water_date[$i])) . '</td>'
                            . '<td>Отсутствует: ' . $water_param_type_name[$j] . ' ' . $water_param_type[$j] . '</td>'
                            . '</tr>';
                            $_SESSION['arr_id'][] = $n;
                            $_SESSION['arr_name'][] = $water_name[$i];
                            $_SESSION['arr_addr'][] = $res[0] . ' ' . $res[1];
                            $_SESSION['arr_date'][] = date('d.m.Y', strtotime($water_date[$i]));
                            $_SESSION['arr_stat'][] = 'Отсутствует:' . $water_param_type_name[$j] . ' ' . $water_param_type[$j];
                            $_SESSION['arr_plc_id'][] = $water_id[$i];
                            // echo $water_id[$i] . " " . $water_name[$i] . " " . $water_date[$i] . " " . $water_res[$i] . "<br>";
                        }
                    }
                }
            }
        }
    }
    if ($resuours == 0) {
        $k = array_search($water_param_id[$j], $not_error);
        if ($k === false) {
            $n++;
            $sql_addres = pg_query('SELECT DISTINCT 
                                                                        "Tepl"."PropPlc_cnt"."ValueProp",
                                                                        "PropPlc_cnt1"."ValueProp"
                                                                      FROM
                                                                        "Tepl"."Places_cnt"
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                                        INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                                      WHERE
                                                                        "Tepl"."Places_cnt".plc_id = ' . $water_param_id[$j] . ' AND 
                                                                        "PropPlc_cnt1".prop_id = 26 AND 
                                                                        "Tepl"."PropPlc_cnt".prop_id = 27');
            $res = pg_fetch_row($sql_addres);
            echo "<tr data-href= 'object.php?id_object=" . $water_param_id[$j] . "'>"
            . "<td>" . $n . "</td>"
            . "<td>" . $water_param_name[$j] . "</td>"
            . "<td>" . $res[0] . " " . $res[1] . "</td>"
            . "<td>Нет данных</td>"
            . "<td>Отсутствует: " . $water_param_type_name[$j] . " " . $water_param_type[$j] . "</td>"
            . "</tr>";
            $_SESSION['arr_id'][] = $n;
            $_SESSION['arr_name'][] = $water_param_name[$j];
            $_SESSION['arr_addr'][] = $res[0] . ' ' . $res[1];
            $_SESSION['arr_date'][] = 'Нет данных';
            $_SESSION['arr_stat'][] = 'Отсутствует:  ' . $water_param_type_name[$j] . ' ' . $water_param_type[$j];
            $_SESSION['arr_plc_id'][] = $water_param_id[$j];
            // echo $water_param_id[$j] . " " . $water_param_name[$j] . " нет параметра " .  $water_param_type[$j] . " ".$water_param_type_name[$j]."<br>";
        }
    }
}
echo "</tbody></table>";
?>
