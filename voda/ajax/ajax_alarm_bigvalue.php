<?php

include '../../db_config.php';
session_start();

$date1 = $_POST['date1'];
$date2 = $_POST['date2'];
$kor_value = $_POST['kor_value'];
$id = $_POST['id'];

echo '<h3 class="text-center">Проверка за период с ' . date("d.m.Y", strtotime($date1)) . ' по ' . date("d.m.Y", strtotime($date2)) . ' </h3>';

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
                                    <td data-query='4'><b>Разность</b></td>
                                    <td><b>Параметр</b></td>
                                </tr>
                            </thead><tbody>";

$sql_archive = pg_query('SELECT DISTINCT 
  "Tepl"."Arhiv_cnt"."DateValue",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."Arhiv_cnt"."DataValue",
  "Tepl"."Places_cnt"."Name",
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."Resourse_cnt"."Name"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."ParamResGroupRelations".prp_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."ParamResGroupRelations".grp_id = "Tepl"."GroupToUserRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Tepl"."User_cnt".usr_id = '.$id.'AND 
  "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
  "Tepl"."Arhiv_cnt"."DateValue" >= \'' . date("Y-m-d", strtotime($date1)) . '\' AND
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . date("Y-m-d", strtotime($date2)) . '\' 
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
                $summ+=$arr_value[$z];
                $kol++;
                //echo "kol = ". $kol. " name = " . $arr_name[$z] . "  param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
            }
            if ($resusl_archive[2] != $arr_id[$z - 1]) {
                $sred = sqrt($summ / $kol);
                for ($i = 1; $i < $kol; $i++) {
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
                        $date_arch = explode(" ", $arr_date[$z - $i]);

                        $date_b = date("d.m.Y", strtotime($date_arch[0]));
                        echo "<tr data-href='object.php?id_object=" . $arr_id[$z - 1] . "' id ='hover'>";
                        echo "<td>" . $n . "</td><td>" . $arr_name[$z - 1] . "</td><td>" . $res[0] . " " . $res[1] . "</td><td>" . $date_b . "</td><td>" . number_format($raz, 2) . "</td><td>" . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . "</td>";
                        echo "</tr>";
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

                $summ = 0;
                $kol = 0;
                $kol++;
                $summ+=$arr_value[$z];
                //echo " name = " . $arr_name[$z] . "param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
            }
        }
        if ($resusl_archive[1] != $arr_param[$z - 1]) {
            $sred = $summ / $kol;

            for ($i = 1; $i < $kol; $i++) {
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
                    $date_arch = explode(" ", $arr_date[$z - $i]);
                    $date_b = date("d.m.Y", strtotime($date_arch[0]));
                    echo "<tr data-href='object.php?id_object=" . $arr_id[$z - 1] . "' id ='hover'>";
                    echo "<td>" . $n . "</td><td>" . $arr_name[$z - 1] . "</td><td>" . $res[0] . " " . $res[1] . "</td><td>" . $date_b . "</td><td>" . number_format($raz, 2) . "</td><td>" . $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . "</td>";
                    echo "</tr>";
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
            $summ = 0;
            $kol = 0;
            $kol++;
            $summ+=$arr_value[$z];
            //echo "kol = ". $kol. " name = " . $arr_name[$z] . "param = " . $arr_param[$z] . "  val =  " . $arr_value[$z] . "<br>";
        }
    } else {
        if ($resusl_archive[1] == $arr_param[$z]) {
            $summ+=$arr_value[$z];
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