<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();

$key = $_POST['key'];


if ($key == "") {
    $sqlAllGroup = pg_query('SELECT DISTINCT  * FROM "Tepl"."UserGroups" ORDER BY "Tepl"."UserGroups".grp_id');
} else {
    $sqlAllGroup = pg_query('SELECT DISTINCT *
FROM
  "Tepl"."UserGroups"
WHERE
  "Tepl"."UserGroups"."Name" ILIKE \'%' . $key . '%\'
ORDER BY
  "Tepl"."UserGroups".grp_id');
}
echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b</td>
            <td rowspan=2 data-query='1'><b>Название группы</b</td>
            <td rowspan=2 data-query='2'><b>Коментарий</b</td>
            <td rowspan=2 data-query='2'><b></b</td>
        </tr>
        </thead>
        <tbody>";

while ($row = pg_fetch_row($sqlAllGroup)) {
    echo '<tr  id=\'hover\' >'
    . '<td>' . $row[0] . '</td>'
    . '<td  data-href=\'group_view.php?gr_id=' . $row[0] . '\'>' . $row[1] . '</td>'
    . '<td>' . $row[2] . '</td>'
    . '<td><button class="btn btn-md btn-danger deleteGroup"  id="'.$row[0].'">Удалить</button></td>'
    . '</tr>';
}