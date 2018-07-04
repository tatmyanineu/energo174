<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

$sql = pg_query('DELETE FROM "Tepl"."PlaceGroupRelations"
 WHERE plc_id = '.$_POST['plc_id'].' AND grp_id = '.$_POST['group_id'].'');