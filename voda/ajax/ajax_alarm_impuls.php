<?php

include '../../db_config.php';
session_start();
$date1 = $_POST['date1'];
$date2 = $_POST['date2'];
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
                                    <td data-query='4'><b>Статус</b></td>
                                    <td data-query='5'><b>Параметр</b></td>
                                </tr>
                            </thead><tbody>";




$sql_archive = pg_query('SELECT DISTINCT 
  "Tepl"."Arhiv_cnt"."DateValue",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."Arhiv_cnt"."DataValue",
  "Tepl"."Places_cnt"."Name",
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."Resourse_cnt"."Name"
FROM
  "Tepl"."User_cnt"
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
WHERE
  "Tepl"."User_cnt".usr_id = ' . $id . ' AND 
  "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' AND
  "Tepl"."Arhiv_cnt".typ_arh = 2
ORDER BY
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."ParamResPlc_cnt"."ParamRes_id"');

//echo pg_num_rows($sql_archive) . "<br>";
$z = 0;
$kol = 0;
$n = 0;
/*
  while ($resusl_archive = pg_fetch_row($sql_archive)) {
  $array_archive[] = array(
  'plc_id' => $resusl_archive[2],
  'name' => $resusl_archive[4],
  'param_id' => $resusl_archive[1],
  'date' => $resusl_archive[0],
  'value' => $resusl_archive[3],
  'param_name' => $resusl_archive[5],
  'group_name' => $resusl_archive[6]
  );
  }

  echo "ok";
  $summ=0; $kol=0;
  for ($i = 0; $i < count($array_archive); $i++) {
  if ($array_archive[$i][plc_id] == $array_archive[$i + 1][plc_id]) {
  $summ +=$array_archive[$i][plc_id];
  $kol++;

  echo ''.$array_archive[$i][plc_id].''.$array_archive[$i][param_id].''.$array_archive[$i][value].'';
  }
  if($array_archive[$i][plc_id] != $array_archive[$i + 1][plc_id]){
  $summ +=$array_archive[$i][plc_id];
  $kol++;
  }
  }

 */


while ($resusl_archive = pg_fetch_row($sql_archive)) {
    $arr_id[$z] = $resusl_archive[2];
    $arr_name[$z] = $resusl_archive[4];
    $arr_param[$z] = $resusl_archive[1];
    $arr_value[$z] = $resusl_archive[3];
    $arr_date[$z] = $resusl_archive[0];
    $arr_name_param[$z] = $resusl_archive[5];
    $arr_name_resours[$z] = $resusl_archive[6];
    $arr_res[$z] = $resusl_archive[7];
    if ($z != 0) {
        if ($arr_id[$z] == $arr_id[$z - 1]) {
            if ($arr_param[$z] == $arr_param[$z - 1]) {
                $summ +=$arr_value[$z - 1];
                $kol++;
            }
            //echo $z . " " . $arr_name[$z - 1] . " " . $arr_date[$z - 1] . " " . number_format($arr_value[$z - 1], 2, '.', '') . "" . $arr_name_resours[$z - 1] . " " . $arr_name_param[$z - 1] . " " . $summ . " " . $kol . "<br>";
        }
        if ($arr_id[$z] != $arr_id[$z - 1] or $arr_param[$z] != $arr_param[$z - 1]) {
            $summ +=$arr_value[$z - 1];
            $kol++;
            /*
              if ($summ / $kol == $arr_value[$z - 1]) {

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

              //echo $z . "!!!!!!!!!!!!!! " . $arr_name[$z - 1] . " " . $arr_date[$z - 1] . " " . $arr_value[$z - 1] . "" . $arr_name_resours[$z - 1] . " " . $arr_name_param[$z - 1] . " " . $summ . " " . $kol . "<br>";
              }
             */
            $x = $summ / $kol;
            // echo $z . "!!!!!!!!!!!!!! " . $arr_name[$z - 1] . " " . $arr_date[$z - 1] . " " . number_format($arr_value[$z - 1], 2, ".", "") . " " . $arr_name_resours[$z - 1] . " " . $arr_name_param[$z - 1] . " " . $summ . " " . $kol . " ---> " . number_format($x, 2, '.','') . "<br>";
            if (number_format($x, 2, '.', '') == number_format($arr_value[$z - 1], 2, '.', '')) {
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
                . "<td>".$arr_res[$z-1]. " ". $arr_name_resours[$z - 1] . ": " . $arr_name_param[$z - 1] . "</td>"
                . "</tr>";


                //echo "дохлый импульс<br>";
            }
            $summ = 0;
            $kol = 0;
        }
        if ($z + 1 == pg_num_rows($sql_archive)) {
            $summ +=$arr_value[$z - 1];
            $kol++;

            if ($summ / $kol == $arr_value[$z - 1]) {
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
                //echo $z . "!!!!!!!!!!!!!! " . $arr_name[$z - 1] . " " . $arr_date[$z - 1] . " " . $arr_value[$z - 1] . "" . $arr_name_resours[$z - 1] . " " . $arr_name_param[$z - 1] . " " . $summ . " " . $kol . "<br>";
            }

            //echo $z + 1 . "!!!!!!!!!! " . $arr_name[$z] . " " . $arr_date[$z] . " " . $arr_value[$z] . "" . $arr_name_resours[$z] . " " . $arr_name_param[$z] . " " . $summ . " " . $kol . "<br>";
        }
    }
    $z++;
}

//$for($i=0;$i<count($arr))

$n++;
echo "</tbody></table>";
