<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class server {

    private $con;

    public function __construct() {
        $this->con = (is_null($this->con)) ? self::connect() : $this->con;
    }

    static function connect() {
        $con = pg_connect("host=localhost port=5432 dbname=FondEnergo user=postgres password=postgres");
        return $con;
    }

    public function getUsersName($id_array) {
        $id = $id_array['id'];
        $qry = pg_query($this->con, 'SELECT "Login", "Privileges" FROM "Tepl"."User_cnt" WHERE usr_id = ' . $id . '');
        //$res = pg_fetch_array($sql);
        while ($res = pg_fetch_row($qry)) {
            $ar = array(
                'id' => $id,
                'db' => pg_dbname($this->con),
                'name' => $res[0],
                'level' => $res[1]
            );
        }

        return $ar;
    }

    public function getObjectsId($id_array) {
        $id = $id_array['id'];
        $qry = pg_query($this->con, 'SELECT "Login", "Privileges" FROM "Tepl"."User_cnt" WHERE usr_id = ' . $id . '');
        //$res = pg_fetch_array($sql);
        while ($res = pg_fetch_row($qry)) {
            $ar = array(
                'id' => $id,
                'db' => pg_dbname($this->con),
                'name' => $res[0],
                'level' => $res[1]
            );
        }

        return $ar;
    }

}

$params = array('uri' => 'api/server.php');
$server = new SoapServer(NULL, $params);
$server->setClass('server');
$server->handle();
