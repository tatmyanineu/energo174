<?php
 header('Content-Type: application/json');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_config.php';
session_start();
$search = $_POST['search'];
//echo $search;
$string_name = mb_strtoupper($search);
//echo $string_name . "<br>";
$string_street = mb_convert_case($search, MB_CASE_TITLE, "UTF-8");
//echo $string_street . "<br>";

$sql_seach_school_info = pg_query('
          SELECT 
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
            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
            UPPER("Places_cnt1"."Name") LIKE UPPER(\'%' . $string_name . '%\') OR
             "Tepl"."PropPlc_cnt".prop_id = 27 AND 
            "PropPlc_cnt1".prop_id = 26 AND 
            "PropPlc_cnt2".prop_id = 41 AND 
            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
            UPPER("Tepl"."PropPlc_cnt"."ValueProp") LIKE UPPER(\'%' . $string_street . '%\') OR
             "Tepl"."PropPlc_cnt".prop_id = 27 AND 
            "PropPlc_cnt1".prop_id = 26 AND 
            "PropPlc_cnt2".prop_id = 41 AND 
            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
            UPPER("PropPlc_cnt1"."ValueProp") LIKE UPPER(\'%' . $search . '%\')');
$school_name="";$school_hs="";$school_koordinat="";$array='';
while ($result_school_info=  pg_fetch_row($sql_seach_school_info)){
    $koord =  explode(", ", $result_school_info[3]);
    $array[] = array(
        'name' => $result_school_info[0],
        'addr' => $result_school_info[1]." ".$result_school_info[2],
        'koord1' =>$koord[0],
        'koord2' =>$koord[1],
        'id' => $result_school_info[4]
    );
    
    
}
//print_r($school_name);
echo json_encode($array);