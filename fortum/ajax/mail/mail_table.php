<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../include/db_config.php';
session_start();


$sql = pg_query('SELECT id, mail_name, mail_pass, mail_smtp, mail_port, contacts
  FROM mail_settings;
');

$data = pg_fetch_all($sql);

echo '<table id="main_table" class="table table-bordered">'
 . '<thed id="thead">'
 . '<tr id="warning">'
 . '<th>email отправителя</th>'
 . '<th>smtp server</th>'
 . '<th>port</th>'
 . '<th>email получателя</th>'
 . '<th></th>'
 . '</tr>'
 . '</thead>'
 . '<tbody>';

for ($i = 0; $i < count($data); $i++) {
    echo '<tr>'
    . '<td>' . $data[$i]['mail_name'] . '</td>'
    . '<td>' . $data[$i]['mail_smtp'] . '</td>'
    . '<td>' . $data[$i]['mail_port'] . '</td>'
    . '<td>' . $data[$i]['contacts'] . '</td>'
    . '<td><button class="btn btn-danger btn-md del_mail" id="'.$data[$i]['id'].'" >Удалить</button></td>'
    . '</td>';
}
