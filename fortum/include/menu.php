
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include './db_config.php';



echo '<ul class="nav nav-sidebar">'
 . '<li><a href="../objects.php"><span class="glyphicon glyphicon-chevron-left"></span> Назад  </a></li>'
 . '</ul>'
 . '<ul class = "nav nav-sidebar">'
 . '<li><a href = "index.php"><span class = "glyphicon glyphicon-asterisk"></span> Фортум</a></li>'
 . '<li><a href = "settings.php"><span class = "glyphicon glyphicon-cog"></span>  Настройка</a></li>'
 . '<li><a href = "objects.php"><span class = "glyphicon glyphicon-list"></span>  Объекты инфо</a></li>'
 . '<li><a href = "sens.php"><span class = "glyphicon glyphicon-dashboard"></span>  Приборы инфо</a></li>'
 . '<li><a href = "mail_settings.php"><span class = "glyphicon glyphicon-envelope"></span>  Настройки отправки</a></li>'
 . '</ul>';
?>