<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_config.php';
session_start();


$sql_coordinats = pg_query('SELECT 
  "Places_cnt1"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "PropPlc_cnt2"."ValueProp",
  "Places_cnt1".plc_id
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt2" ON ("Places_cnt1".plc_id = "PropPlc_cnt2".plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Places_cnt1".plc_id = "Tepl"."PlaceGroupRelations".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."PlaceGroupRelations".grp_id = "Tepl"."GroupToUserRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "PropPlc_cnt2".prop_id = 41 AND 
"Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
"Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
  ORDER BY
  "Places_cnt1".plc_id');




while ($row_coordinats = pg_fetch_row($sql_coordinats)) {
    $coord[] = array(
        'plc_id' => $row_coordinats[4],
        'name' => '' . $row_coordinats[0] . '',
        'coord' => '' . $row_coordinats[3] . ''
    );
}


for ($i = 0; $i < count($_SESSION[main_form]); $i++) {
    $string = "";
    $k = array_search($_SESSION[main_form][$i][plc_id], array_column($coord, 'plc_id'));
    if ($k !== false) {
        if($_POST['id_err']==$_SESSION[main_form][$i][marker]){
            echo '<li><a><span onclick="clickObject(' . $coord[$k][coord] . ')">' .  $coord[$k][name]  . '</span> </a></li>';
        }
    }
}