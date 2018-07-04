<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_config.php';

$user_id = $_POST['id'];

$sql_disitnct = pg_query('SELECT DISTINCT 
                                        "Places_cnt1"."Name",
                                        "Places_cnt1".plc_id
                                      FROM
                                        "Tepl"."GroupToUserRelations"
                                        INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                        INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                        INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                        INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
                                      WHERE
                                        "Places_cnt1".typ_id = 10   AND
                                         "Tepl"."User_cnt".usr_id = ' . $user_id . '
                                      ORDER BY
                                        "Places_cnt1"."Name"');
echo '<button class="btn btn-default distinct" type="submit" id="0">Все</button>';
while ($row_disitinct = pg_fetch_row($sql_disitnct)) {
    echo '<button class="btn btn-default distinct" type="submit" id="' . $row_disitinct[1] . '" >' . $row_disitinct[0] . '</button>';
}