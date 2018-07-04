<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
header('Content-type: application/x-dbase'); 
header('Content-Disposition: attachment; filename="../' . $_SESSION['file_name']. '"'); 
readfile("../". $_SESSION['file_name']. ""); 