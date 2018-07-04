<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class client {

    public function __construct() {
        $params = array('location' => 'http://localhost/pulsar_form/api/server.php',
            'uri' => 'urn://localhost/pusar_form/api/server.php',
            'trace' => 1);
        $this->instance = new SoapClient(NULL, $params);
        //$auth_params = new stdClass();
    }

    public function getName($id_array) {
        return $this->instance->__soapCall('getUsersName', $id_array);
    }

    public function getObjects($id_array) {
        return $this->instance->__soapCall('getObjectsId', $id_array);
    }

}

$client = new client();
