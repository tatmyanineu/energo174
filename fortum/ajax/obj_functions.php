<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../include/db_config.php';
switch ($_POST['action']) {
    case 'insert':
        add_obj($_POST[plc]);
        break;

    case 'delete':
        del_obj($_POST[plc]);
        break;
}

function add_obj($id) {
    $sql_id = pg_query('SELECT id, plc_id, exception
                        FROM fortum_plc where plc_id =' . $id);
    if (pg_num_rows($sql_id) == 0) {
        $sql = pg_query('INSERT INTO fortum_plc(
                        plc_id, exception)
                        VALUES (' . $id . ', 1)');
    }
}

function del_obj($id) {
    $sql = pg_query('DELETE FROM fortum_plc WHERE plc_id = ' . $id);
}
