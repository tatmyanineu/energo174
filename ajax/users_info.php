<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();

$sqlUsersForGroup = pg_query('SELECT DISTINCT 
  "Tepl"."User_cnt".usr_id,
  "Tepl"."User_cnt"."Login",
  "Tepl"."User_cnt"."Password",
  "Tepl"."User_cnt"."SurName",
  "Tepl"."User_cnt"."PatronName",
  "Tepl"."User_cnt"."Comment",
  "Tepl"."User_cnt"."Privileges"
FROM
  "Tepl"."GroupToUserRelations"
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Tepl"."GroupToUserRelations".grp_id = ' . $_POST['group'] . '
');


echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>ID</b</td>
            <td rowspan=2 data-query='1'><b>Login</b</td>
            <td rowspan=2 data-query='2'><b>Password</b</td>
            <td rowspan=2 data-query='3'><b>Фамилия</b</td>
            <td rowspan=2 data-query='4'><b>Имя</b</td>
            <td rowspan=2 data-query='5'><b>Коментарий</b</td>
            <td rowspan=2 data-query='6'><b>Права</b</td>
            <td rowspan=2 data-query='6'><b></b</td>
        </tr>
        </thead>
        <tbody>";

while ($row = pg_fetch_row($sqlUsersForGroup)) {
    echo '<tr id=\'hover\' >'
    . '<td>' . $row[0] . '</td>'
    . '<td>' . $row[1] . '</td>'
    . '<td>' . $row[2] . '</td>'
    . '<td>' . $row[3] . '</td>'
    . '<td>' . $row[4] . '</td>'
    . '<td>' . $row[5] . '</td>'
    . '<td>' . $row[6] . '</td>'
    . '<td><div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Действие <span class="caret"></span></button>
  <ul class="dropdown-menu" role="menu">
    <li><a class="editUserMenu" id="'.$row[0] .'" href="#"><span class="glyphicon glyphicon-pencil"></span>Редактировать профиль</a></li>
    <li><a class="deleteUserGroupMenu" id="'.$row[0] .'" href="#"><span class="glyphicon glyphicon-minus"></span>Удалить из группы</a></li>
    <li><a class="deleteUserMenu" id="'.$row[0] .'" href="#"><span class="glyphicon glyphicon-trash"></span>Удалить пользователя</a></li>
  </ul>
</div></td>'
    . '</tr>';
}
echo '</table>';
