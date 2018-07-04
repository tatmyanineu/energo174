<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include 'db_config.php';
$start = microtime(true);


$search = $_POST['search'];

//echo $search . "<br>";
$string_name = mb_strtoupper($search);
//echo $string_name . "<br>";
$string_street = mb_convert_case($search, MB_CASE_TITLE, "UTF-8");
//echo $string_street . "<br>";




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
  UPPER("Tepl"."PropPlc_cnt"."ValueProp") LIKE UPPER( \'%' . $string_street . '%\') OR 
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
  "PropPlc_cnt1"."ValueProp" LIKE \'%' . $search . '%\' OR 
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
  UPPER("Places_cnt1"."Name") LIKE UPPER (\'%' . $string_name . '%\')
ORDER BY
  "Tepl"."Places_cnt".plc_id');

echo '<BR>';
if (pg_num_rows($sql_seach_school_info) > 0) {
    echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2 ><b>Передача данных</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
            </tr>
        </thead>";

    unset($school_info);
    while ($result = pg_fetch_row($sql_seach_school_info)) {
        $school_info[] = array(
            'plc_id' => $result[3],
            'name' => $result[0],
            'addres' => "" . $result[1] . " " . $result[2] . ""
        );
    }



    /*

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
      "Tepl"."Places_cnt".plc_id
      FROM
      "Tepl"."ParamResPlc_cnt"
      INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
      INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
      INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
      INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
      INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
      INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
      INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
      WHERE
      "Tepl"."Arhiv_cnt".typ_arh = 2 AND
      "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date_now . '\' AND
      "Tepl"."PropPlc_cnt".prop_id = 26 AND
      "PropPlc_cnt1".prop_id = 27 AND
      "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND
      "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
      "Tepl"."Places_cnt"."Name" LIKE \'%' . $string_name . '%\' OR
      "Tepl"."Arhiv_cnt".typ_arh = 2 AND
      "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date_now . '\' AND
      "Tepl"."PropPlc_cnt".prop_id = 26 AND
      "PropPlc_cnt1".prop_id = 27 AND
      "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND
      "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
      "PropPlc_cnt1"."ValueProp" LIKE \'%' . $string_street . '%\' OR
      "Tepl"."Arhiv_cnt".typ_arh = 2 AND
      "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date_now . '\' AND
      "Tepl"."PropPlc_cnt".prop_id = 26 AND
      "PropPlc_cnt1".prop_id = 27 AND
      "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND
      "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
      "Tepl"."PropPlc_cnt"."ValueProp" LIKE \'%' . $search . '%\'
      ORDER BY
      "Tepl"."Places_cnt".plc_id,
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
      };

     */




    echo "<tbody>";
    $m = 1;


    for ($i = 0; $i < count($school_info); $i++) {
        $key = array_search($school_info[$i]['plc_id'], array_column($_SESSION['main_form'], 'plc_id'));
        if ($key !== false) {
            echo "<tr data-href='object.php?id_object=" . $school_info[$i]['plc_id'] . "' id='hover' >"
            . "<td>" . $m . "</td>"
            . "<td>" . $school_info[$i]['name'] . "</td>"
            . "<td>" . $school_info[$i]['addres'] . "</td>";
            if ($_SESSION['main_form'][$key]['error_warm'] == 1) {
                echo "<td class='warning'>" . date("d.m.Y", strtotime($_SESSION['main_form'][$key]['date_warm'])) . "</td>";
            } elseif ($_SESSION['main_form'][$key]['error_warm'] == 3) {
                echo "<td class='danger'> Нет данных </td>";
            } elseif ($_SESSION['main_form'][$key]['error_warm'] == 4) {
                echo "<td class='text-center'> -</td>";
            } elseif ($_SESSION['main_form'][$key]['error_warm'] == 0) {
                echo "<td>" . date("d.m.Y", strtotime($_SESSION['main_form'][$key]['date_warm'])) . "</td>";
            }

            if ($_SESSION['main_form'][$key]['error_water'] == 1) {
                echo "<td class='warning'>" . date("d.m.Y", strtotime($_SESSION['main_form'][$key]['date_water'])) . "</td>";
            } elseif ($_SESSION['main_form'][$key]['error_water'] == 3) {
                echo "<td class='danger'> Нет данных </td>";
            } elseif ($_SESSION['main_form'][$key]['error_water'] == 4) {
                echo "<td class='text-center'> - </td>";
            } elseif ($_SESSION['main_form'][$key]['error_water'] == 0) {
                echo "<td>" . date("d.m.Y", strtotime($_SESSION['main_form'][$key]['date_water'])) . "</td>";
            }
            $m++;
            echo "</tr>";
        }
    }

    /* for ($sc = 0; $sc < count($school_id); $sc++) {
      $td = 0;
      echo "<tr data-href='object.php?id_object=" . $school_id[$sc] . "' id='hover' >";

      echo "<td>" . $m . "</td>";

      echo "<td>" . $school_name[$sc] . "</td>";

      echo "<td>" . $school_hs[$sc] . "  " . $school_str[$sc] . "</td>";

      for ($a = 0; $a < count($id_plc); $a++) {
      if ($school_id[$sc] == $id_plc[$a]) {
      if ($id_plc[$a] != $id_plc[$a + 1]) {
      $date_arch = explode(' ', $date_val[$a]);
      $kol_day = (strtotime($date_now) - strtotime($date_arch[0])) / (60 * 60 * 24);
      //echo '<td>' . $school_id[$sc] . '</td>';
      $date_b = date("d.m.Y", strtotime($date_arch[0]));
      $td = 1;
      if ($kol_day > 3) {
      echo "<td class='danger'><b>" . $date_b . "</b></td>";
      echo "<td class='danger'><b> Нет связи </b></td>";
      } else {
      echo "<td>" . $date_b . "</td>";
      echo "<td> OK </td>";
      }
      }
      }
      }
      if ($td == 0) {
      echo "<td class='danger'><b>Нет данных</b></td> <td class='danger'><b>Нет связи</b></td>";
      $_SESSION['arr_date'][] = 'Нет данных';
      $_SESSION['arr_stat'][] = ' Нет связи';
      $_SESSION['arr_plc_err'][] = 0;
      }
      $m++;
      echo "</tr>";
      } */
    echo '</tbody></table>';
} else {
    echo "<h2>Данных по вашему запросу не найдено</h2>";
}


