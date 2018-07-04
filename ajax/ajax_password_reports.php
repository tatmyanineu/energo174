<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';

switch ($_POST['action']) {
    case 'table':
        action_table_refresh();
        break;
    case 'update':
        action_update_table($_POST['id'], $_POST['move']);
        break;
    case 'delete':
        action_delete($_POST['id']);
        break;
}

function action_table_refresh() {

    echo '<table class="table table-bordered">'
    . '<thead id="thead">'
    . '<tr id="warning">'
    . '<td><b>№</b></td>'
    . '<td><b>Название</b></td>'
    . '<td><b>Адрес</b></td>'
    . '<td><b>ФИО</b></td>'
    . '<td><b>e-mail</b></td>'
    . '<td><b>Статус</b></td>'
    . '<td><b>Действие</b></td>'
    . '</tr>'
    . '</thead><tbody>';


    $sql_refresh_table = pg_query('SELECT id, plc_name, plc_address, fio, email, status FROM password_forgot ORDER BY id');
    $n = 1;
    while ($row = pg_fetch_row($sql_refresh_table)) {

        switch ($row[5]) {
            case 0:
                $status = 'На расмотрении';
                $move = '<div class="btn-group">
                            <button type="button"  class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Действие <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                              <li><a href="#" class="move" id="' . $row[0] . '" data-action ="ok">Востановить</a></li>
                              <li><a href="#" class="move" id="' . $row[0] . '" data-action= "dont_ok">Отклонить</a></li>
                              <li class="divider"></li>
                              <li><a href="#" class="move" id="' . $row[0] . '" data-action= "delete">Удалить</a></li>
                            </ul>
                          </div>';
                break;
            case 1: $status = 'Выполнено';
                $move = '';
                break;
            case 2: $status = 'Отказано';
                $move = '';
                break;
        }

        echo '<tr>'
        . '<td>' . $n . '</td>'
        . '<td>' . $row[1] . '</td>'
        . '<td>' . $row[2] . '</td>'
        . '<td>' . $row[3] . '</td>'
        . '<td>' . $row[4] . '</td>'
        . '<td>' . $status . '</td>'
        . '<td>' . $move . '</td>';

        $n++;
    }

    echo '</tbody></table>';
}

function action_update_table($id, $move) {
    switch ($move) {
        case 'ok':
            $flag = 1;
            $sql_update = pg_query('UPDATE password_forgot SET status=' . $flag . ' WHERE id =' . $id . '');
            break;
        case 'dont_ok':
            $flag = 2;
            $sql_update = pg_query('UPDATE password_forgot SET status=' . $flag . ' WHERE id =' . $id . '');
            break;
        case 'delete':
            $sql_delete = pg_query('DELETE FROM password_forgot WHERE id= ' . $id . '');
            break;
    }    
}
