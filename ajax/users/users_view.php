<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

if ($_POST['key'] != "") {
    $sqlUsers = pg_query('SELECT DISTINCT 
  "Tepl"."User_cnt".usr_id,
  "Tepl"."User_cnt"."Login",
  "Tepl"."User_cnt"."SurName",
  "Tepl"."User_cnt"."PatronName"
FROM
  "Tepl"."User_cnt"
WHERE
  "Tepl"."User_cnt"."Login" ILIKE \'%' . $_POST['key'] . '%\'');
} else {
    $sqlUsers = pg_query('SELECT DISTINCT 
  "Tepl"."User_cnt".usr_id,
  "Tepl"."User_cnt"."Login",
  "Tepl"."User_cnt"."SurName",
  "Tepl"."User_cnt"."PatronName"
FROM
  "Tepl"."User_cnt"
ORDER BY
  "Tepl"."User_cnt".usr_id');
}




echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>ID</b</td>
            <td rowspan=2 data-query='1'><b>Login</b</td>
            <td rowspan=2 data-query='3'><b>Фамилия</b</td>
            <td rowspan=2 data-query='4'><b>Имя</b</td>
            <td rowspan=2 data-query='6'><b></b</td>
        </tr>
        </thead>
        <tbody>";

while ($row = pg_fetch_row($sqlUsers)) {
    echo '<tr id=\'hover\' >'
    . '<td>' . $row[0] . '</td>'
    . '<td id="name_'.$row[0].'">' . $row[1] . '</td>'
    . '<td>' . $row[2] . '</td>'
    . '<td>' . $row[3] . '</td>'
    . '<td><a class="addTag" id="' . $row[0] . '"><span class="glyphicon glyphicon-plus"></span> </a></td>'
    . '</tr>';
}
echo '</table>';
