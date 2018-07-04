<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


session_start();
include './db_config.php';



echo '<ul class = "nav nav-sidebar">'
 . '<li><a href="objects.php"><span class="glyphicon glyphicon-home"></span> Главная  </a></li>'
 . '<li><a href="limits.php"><span class="glyphicon glyphicon-list-alt"></span>  Лимиты  </a></li>'
 . '<li><a href="alarm.php" data-toggle="tooltip" data-placement="right" title="Процент неисправных обьектов: ' . number_format($_SESSION['proc'], 2) . '%"><span class="glyphicon glyphicon-bell" ></span><span id="reload_alarm" class="badge pull-right">' . $_SESSION['alarm'] . '</span> Аварии   </a></li>'
 . '<li><a href="maps.php"><span class="glyphicon glyphicon-globe"></span> Карта </a></li>'
 . '<li><a href="logs.php"><span class="glyphicon glyphicon-book"></span> Логи </a></li>'
 . '<li><a href="tickets.php"><span class="glyphicon glyphicon-tags"></span> <span id="reload_alarm" class="badge pull-right">' . $_SESSION['count_ticiket'] . '</span> Заявки</a></li>'
 . '<li><a href="settings/index.php"><span class="glyphicon glyphicon-cog"></span> <span id="reload_alarm" class="badge pull-right"></span> Настройки</a></li>'
 . '<li><a href="password_reports.php"><span class="glyphicon glyphicon-lock"></span>  <span id="reload_alarm" class="badge pull-right">' . $_SESSION['reports_passord'] . '</span> Востановление пароля</a></li>'
 . '<li><a href="users.php"><span class="glyphicon glyphicon-user"></span>Пользователи</a></li>'
 . '<li><a href="controller.php"><span class="glyphicon glyphicon-user"></span>Диспетчер</a></li>'
 . '</ul>';
