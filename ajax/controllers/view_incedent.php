<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



session_start();
include '../../db_config.php';

$plc = $_POST['plc'];
$date = $_POST['date'];

function alarm_dt($plc, $date) {
    
}

function alarm_teplo($plc, $date) {
    
}

function alarm_temper($plc, $date) {

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
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND 
                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 5 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND 
                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 6 OR 
                "Tepl"."Arhiv_cnt".typ_arh = ' . $type_arch . '  AND 
               "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date2 . '\' AND 
                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date1 . '\' AND 
                "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 12 
              ORDER BY
                "Tepl"."Places_cnt".plc_id,
                "Tepl"."Arhiv_cnt"."DateValue",
                "Tepl"."ParamResPlc_cnt"."ParamRes_id"');
}
