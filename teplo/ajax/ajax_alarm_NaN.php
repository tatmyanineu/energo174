<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

$date1 = $_POST['date1'];
$date2 = $_POST['date2'];
$id=$_POST['id'];

echo  '<h3 class="text-center">Проверка за период с '.$date1. ' по '.$date2.' </h3>';


$z = 0;
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

$sql_date_archive = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                                  FROM
                                    "Tepl"."ParamResPlc_cnt"
                                    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                  WHERE
                                    "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\'
                                  ORDER BY
                                    "Tepl"."Arhiv_cnt"."DateValue" DESC');
//echo pg_num_rows($sql_date_archive);
while ($result_date = pg_fetch_row($sql_date_archive)) {
    $massiv = '';
    $pokaz = '';
    $date_arch = explode(" ", $result_date[0]);
    $time = strtotime("-1 day");
    $date_b = date("d.m.Y", strtotime("-1 day", strtotime($date_arch[0])));
    echo "<tr><td class='dist text-center' colspan='5'><b>" . $date_b . "</b></td></tr>";
    //echo $result_date[0] . "<br>";
    //echo "ДЕНЬ ПОOOOOOOOOOOOOOOOOOOOOOOOOOOOOШЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛ!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br>";
    $sql_archive = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."Arhiv_cnt"."DataValue",
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
                                    "Tepl"."Arhiv_cnt"."DateValue" = \'' . $result_date[0] . '\' AND 
                                    "Tepl"."User_cnt".usr_id = '.$id.'
                                  ORDER BY
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id"');
    $q = 1;
    //echo pg_num_rows($sql_archive) . "<br>";
    while ($resusl_archive = pg_fetch_row($sql_archive)) {
        $arr_id[$z] = $resusl_archive[2];
        $aar_name[$z] = $resusl_archive[4];
        //$arr_val[$v] = 
        //$arr_param[$v] =
        //
                                //echo "Z== " . $z . " id = " . $arr_id[$z] . "  name = " . $aar_name[$z] . " res = " . $resusl_archive[1]. "<br>";

        if ($z != 0) {
            if ($resusl_archive[2] == $arr_id[$z - 1]) {
                //$kol_res++;
                $arr_param[$v][] = $resusl_archive[1];
                $arr_val[$v][] = $resusl_archive[3];
            }
            if ($resusl_archive[2] != $arr_id[$z - 1]) {
                $arr_param[$v + 1][] = $resusl_archive[1];
                $arr_val[$v + 1][] = $resusl_archive[3];
                $plc = $aar_name[$z - 1];



                //print_r($arr_param[$v]);echo " <br>";
                //print_r($arr_val[$v]);echo " <br>";

                for ($i = 0; $i < count($arr_param[$v]); $i++) {
                    if ($arr_val[$v][$i] == 'NaN') {
                        //echo "id= " . $arr_id[$z - 1] . " " . $plc . "  kol. res = " . $kol_res . " <br>";
                        $massiv[] = $arr_id[$z - 1];
                    }
                }

                $v++;
                //$kol_res = 0;
                //$kol_res ++;
            }
        } else {
            if ($resusl_archive[2] == $arr_id[$z]) {
                //$kol_res++;
                $arr_param[$v][] = $resusl_archive[1];
                $arr_val[$v][] = $resusl_archive[3];
            }
        }
        $z++;
    }

    $arr_distinct = array_unique($massiv);
    //print_r($arr_distinct);
    foreach ($arr_distinct as $key => $value) {
        $pokaz[] = $arr_distinct[$key];
    }
    //print_r($pokaz);



    for ($j = 0; $j < count($pokaz); $j++) {

        $sql_info = pg_query('SELECT DISTINCT 
                                                    "Tepl"."Places_cnt".plc_id,
                                                    "Tepl"."Places_cnt"."Name",
                                                    "Places_cnt1"."Name",
                                                    "PropPlc_cnt1"."ValueProp",
                                                    "Tepl"."PropPlc_cnt"."ValueProp"
                                                    
                                                  FROM
                                                    "Tepl"."Places_cnt"
                                                    INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                                    INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                                    INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
                                                    INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                    INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                  WHERE
                                                    "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                                    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                                                    "Tepl"."Places_cnt".plc_id = ' . $pokaz[$j] . 'AND 
                                                    "Tepl"."PropPlc_cnt".prop_id = 26 AND 
                                                    "PropPlc_cnt1".prop_id = 27
                                                  ORDER BY
                                                    "Tepl"."Places_cnt".plc_id');
        $result = pg_fetch_row($sql_info);
        echo "<tr data-href='object.php?id_object=" . $result[0] . "' id ='hover'><td>" . $q++ . "</td><td>" . $result[1] . "</td><td>" . $result[3] . " " . $result[4] . "</td><td>" . $date_b . "</td><td>NaN</td></tr>";
    }
}
echo "</tbody></table>";
