<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if ($_SESSION['privelege'] >=8) {
    echo '<ul class="nav nav-sidebar">'
    . '<li><a href="interface_voda.php">МУП ПОВВ Интерфейс</a></li>'
    . ' <li><a href="interface_teplo.php">МУП ЧКТС Интерфейс</a></li>'
    . ' <li><a href="fortum/index.php">ФОРТУМ</a></li>'
    . '</ul>';
}
