<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include '../db_config.php';
session_start();

if ($_POST['key'] == "") {

    $sqlObjectForGroup = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."Places_cnt"."Comment"
FROM
  "Tepl"."PlaceGroupRelations"
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."PlaceGroupRelations".grp_id = ' . $_POST['group'] . ' AND 
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26
ORDER BY
  "Tepl"."Places_cnt"."Name"
');
} else {
    $sqlObjectForGroup = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt".plc_id,
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."Places_cnt"."Comment"
FROM
  "Tepl"."PlaceGroupRelations"
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."PlaceGroupRelations".grp_id = ' . $_POST['group'] . ' AND 
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."Places_cnt"."Name" ILIKE \'%' . $_POST['key'] . '%\'
ORDER BY
  "Tepl"."Places_cnt"."Name"
');
}

echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>ID</b</td>
            <td rowspan=2 data-query='1'><b>Учреждение</b</td>
            <td rowspan=2 data-query='2'><b>Адрес</b</td>
            <td rowspan=2 data-query='3'><b>Коментарий</b</td>
            <td rowspan=2 data-query='6'><b></b</td>
        </tr>
        </thead>
        <tbody>";

while ($row = pg_fetch_row($sqlObjectForGroup)) {
    echo '<tr id=\'hover\' >'
    . '<td>' . $row[0] . '</td>'
    . '<td>' . $row[1] . '</td>'
    . '<td>' . $row[2] . ' д. ' . $row[3] . '</td>'
    . '<td>' . $row[4] . '</td>'
    . '<td><button class="btn btn-md btn-danger deleteObject"  id="'.$row[0].'">Удалить</button></td>'
    . '</tr>';
}
echo '</table>';
