<?php

include 'db_config.php';
$date = date('Y-m-d');
session_start();
$id_object = $_GET['id_object'];
$file = basename($_SERVER['PHP_SELF'], ".php");

$sql_search_object = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt".plc_id
FROM
  "Tepl"."User_cnt"
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
WHERE
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."Places_cnt".plc_id = ' . $id_object . '');

if (pg_num_rows($sql_search_object) != 0) {
    $sql_tickets = pg_query('SELECT DISTINCT 
                                public.ticket.id,
                            public.ticket.plc_id,
                            public.ticket.date_ticket,
                            public.ticket.text_ticket,
                            public.ticket.status,
                            public.ticket.close_date,
                            public.ticket.close_text
                          FROM
                            public.ticket
                          WHERE
                            public.ticket.plc_id = ' . $_GET['id_object'] . ' AND 
                            public.ticket.status < 4');

    $sql_all_tickets = pg_query('SELECT 
                                public.ticket.id
                              FROM
                                public.ticket
                              WHERE
                                public.ticket.plc_id = ' . $_GET['id_object'] . '');

    $sql_all_correct = pg_query('SELECT 
                                public.korrect.id
                              FROM
                                public.korrect
                              WHERE
                                public.korrect.plc_id =  ' . $_GET['id_object'] . '');
} else {
    header("location: 404.php");
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>

        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="css/dashboard.css"/>
        <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script src="http://maps.api.2gis.ru/2.0/loader.js?pkg=full" data-id="dgLoader"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script type="text/javascript" src="js/jquery.datetimepicker.js"></script>
        <script src="js/jquery.livequery.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="content">
            <!-- Верхний бар -->
            <div class="navbar navbar-default navbar-fixed-top" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a id="forBrand" class="navbar-brand" href="#">Измерительная система контроля и учета энергоресурсов</a>
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">

                            <li><a id="forBrand" href="index.php">Выход</a></li>
                        </ul>
                        <?php
                        if ($_SESSION['privelege'] > 0) {
                            echo ' <form  class="navbar-form navbar-right">
                                        <div class = "input-group">
                                            <input type="search" class="form-control" autocomplete="off" id="search" placeholder="Поиск..."/>
                                            <span class="input-group-btn">
                                                <button class="form-control btn btn-default btn-primary" id="formSearch" autofocus type="search"> <span class="glyphicon glyphicon-search"></span> </button>
                                            </span>

                                        </div> 
                                    </form>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!--Верхний бар -->


        <div class="container-fluid">
            <div class="row">
                <!--Боковое меню -->
                <div class="col-sm-3 col-md-2 sidebar">
                    <ul class="nav nav-sidebar">
                        <li class="active"><a href="objects.php"><span class="glyphicon glyphicon-home"></span> Главная  </a></li>
                        <li><a href="limits.php"><span class="glyphicon glyphicon-list-alt"></span>  Лимиты   </a></li>
                        <?php
                        if ($_SESSION['privelege'] > 0) {
                            echo '<li><a href="alarm.php" data-toggle="tooltip" data-placement="right" title="Процент неисправных обьектов: ' . number_format($_SESSION['proc'], 2) . '%"><span class="glyphicon glyphicon-bell" ></span><span id="reload_alarm" class="badge pull-right">' . $_SESSION['alarm'] . '</span> Аварии   </a></li>'
                            . '<li><a href="maps.php"><span class="glyphicon glyphicon-globe"></span> Карта </a></li>'
                            . '<li><a href="logs.php"><span class="glyphicon glyphicon-book"></span> Логи </a></li>'
                            . '<li><a href="tickets.php"><span class="glyphicon glyphicon-tags"></span> <span id="reload_alarm" class="badge pull-right">' . $_SESSION['count_ticiket'] . '</span> Заявки</a></li>'
                            . '<li><a href="settings/index.php"><span class="glyphicon glyphicon-cog"></span> Настройки</a></li>'
                            . '<li><a href="password_reports.php"><span class="glyphicon glyphicon-user"></span>  <span id="reload_alarm" class="badge pull-right">' . $_SESSION['reports_passord'] . '</span> Востановление пароля</a></li>'
                            ;
                        }
                        ?>
                    </ul>

                    <ul class="nav nav-sidebar">
                        <li><a id="export_arch_teplo" href="#"> <span class="glyphicon glyphicon-floppy-disk"></span>Экспорт архива</a></li>

                    </ul>
                    <ul class="nav nav-sidebar">
                        <?php
                        if ($_SESSION['privelege'] > 0) {
                            echo '<li><a id="" href="settings/correction.php?id_object=' . $id_object . '"> <span class="glyphicon glyphicon-dashboard"></span>Коррекция показаний <span class="badge pull-right">' . pg_num_rows($sql_all_correct) . '</span></a></li>';


                            $a = pg_num_rows($sql_tickets);
                            if (pg_num_rows($sql_tickets) == 0) {
                                echo '<li><a id="ticket" class="" data-toggle="modal" data-target="#myModal" href="#"><span class="glyphicon glyphicon-edit"></span>Оставить заявку</a></li>';
                                echo '<li><a class="" href="ticket_object.php?id_object=' . $_GET['id_object'] . '"><span class="glyphicon glyphicon-tag"></span>Просмотр всех заявок <span class="badge pull-right">' . pg_num_rows($sql_all_tickets) . '</span></a></li>';
                            } else {
                                echo '<li><a id="ticket2" data-toggle="modal"  data-target="#myModal2" href="#"><span class="glyphicon glyphicon-edit"></span>Редактировать заявку</a></li>';
                                echo '<li><a class="" href="ticket_object.php?id_object=' . $_GET['id_object'] . '"><span class="glyphicon glyphicon-tag"></span>Просмотр всех заявок  <span class="badge pull-right">' . pg_num_rows($sql_all_tickets) . '</span></a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
                <!--Боковое меню -->

                <!--Контент -->
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title text-center" id="myModalLabel">Добавить заявку на сервисное обслуживание</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="input-group">
                                        <span class="input-group-addon">Срочность заявки: </span>
                                        <select id="stat_comment" class="form-control">
                                            <option value="0">Обычная</option>
                                            <option value="1">Срочная</option>
                                            <option value="2">Критическая</option>
                                        </select>
                                    </div>
                                    </br>
                                    <div class="input-group">
                                        <span class="input-group-addon">&emsp;&nbsp;Текст заявки:&emsp;</span>
                                        <textarea id="comment" class="form-control" rows="4" cols="70" style="width: 100%;"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div id="result_comment"></div>
                                    <button type="button" id="add_comment" class="btn btn-primary">Добавить</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if (pg_num_rows($sql_tickets) > 0) {
                        echo '<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title text-center" id="myModalLabel">Редактирование заявки на сервисное обслуживание</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="input-group">
                                        <span class="input-group-addon">Срочность заявки: </span>
                                        <select id="stat_comment2" class="form-control">';
                        switch (pg_fetch_result($sql_tickets, 0, 4)) {
                            case 0:echo '<option value = "0" selected>Обычная</option> 
                                    <option value = "1">Срочная</option>
                                    <option value = "2">Критическая</option>';
                                break;
                            case 1:echo '<option value = "0">Обычная</option> 
                                    <option value = "1" selected>Срочная</option>
                                    <option value = "2">Критическая</option>';
                                break;
                            case 2:echo '<option value = "0">Обычная</option> 
                                    <option value = "1">Срочная</option>
                                    <option value = "2" selected>Критическая</option>';
                                break;
                        }


                        echo'</select>
                                    </div>
                                    </br>
                                    <div class="input-group">
                                        <span class="input-group-addon">&emsp;&nbsp;Текст заявки:&emsp;</span>
                                        <textarea id="comment2" class="form-control" rows="4" cols="70" style="width: 100%;">' . pg_fetch_result($sql_tickets, 0, 3) . '</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer" id="' . pg_fetch_result($sql_tickets, 0, 0) . '">
                                    <div id="result_comment"></div>
                                    <button type="button" id="edit_comment" class="btn btn-primary">Изменить</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>

                                </div>
                            </div>
                        </div>
                    </div>';
                    }

                    $sql_cootd = pg_query('SELECT 
                                  "Tepl"."Places_cnt"."Name",
                                  "Places_cnt1"."Name",
                                  "Places_cnt2"."Name",
                                  "Tepl"."PropPlc_cnt"."ValueProp",
                                  "PropPlc_cnt1"."ValueProp",
                                  "PropPlc_cnt2"."ValueProp",
                                  "Places_cnt1".plc_id
                                FROM
                                  "Tepl"."Places_cnt" "Places_cnt1"
                                  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                  INNER JOIN "Tepl"."Places_cnt" "Places_cnt2" ON ("Places_cnt1".plc_id = "Places_cnt2".place_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt2".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt2".plc_id = "PropPlc_cnt1".plc_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt2" ON ("Places_cnt2".plc_id = "PropPlc_cnt2".plc_id)
                                WHERE
                                  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                                  "PropPlc_cnt1".prop_id = 26 AND 
                                  "Places_cnt2".plc_id =' . $id_object . '');

                    $result_cootd = pg_fetch_row($sql_cootd);
                    echo '<ol class="breadcrumb">
              <li><a href="objects.php?id_distinct=' . $result_cootd[6] . '">' . $result_cootd[1] . '</a></li>
              <li>' . $result_cootd[2] . '</a></li>
              <li> ул. ' . $result_cootd[3] . '</li>
              <li> д. ' . $result_cootd[4] . '</li>
            </ol>'
                    ?>  
                    <h4 class=""></h4>
                    <div class="panel panel-primary" style=" border-color: #428bca;">
                        <div id="panel_head" class="panel-heading panel-heading-map" style=" background-color: #428bca;  border-color: #428bca;">
                            <h3 class="panel-title text-left" ><span id="maps_link">Карта</span></h3>

                        </div>
                        <div class="panel-body  panel-body-map" style="display: none;">
                            <div id="map" style="width:100%; height:240px; margin-bottom: 20px;"></div>
                        </div>
                    </div>
                    <?php
                    if ($_SESSION['privelege'] > 0) {

                        $sql_alarm = pg_query('SELECT 
                                            public.alarm.text_alarm,
                                            public.alarm.date_err,
                                            public.alarm.id
                                          FROM
                                            public.alarm
                                          WHERE
                                            public.alarm.plc_id = ' . $id_object);
                        //echo pg_fetch_result($sql_alarm, 0, 0);
                        if (pg_num_rows($sql_alarm) > 0) {
                            echo '<div class="panel panel-primary" style=" border-color: #428bca;">
                              <div id="panel_head" class="panel-heading go-to-alarm" data_id="' . pg_fetch_result($sql_alarm, 0, 2) . '" style=" background-color: #428bca;  border-color: #428bca;">
                              <h3 class="panel-title text-left" ><span ><b>В списке исключений с ' . date("d.m.Y", strtotime(pg_fetch_result($sql_alarm, 0, 1))) . '</b>: ' . pg_fetch_result($sql_alarm, 0, 0) . ' </span></h3>
                              </div>
                              </div>';
                        }
                    }
                    ?>


                    <ul class="nav nav-tabs nav-justified">
                        <li class="active"><a href="#">Таблица потребления ресурсов</a></li>
                        <li><a href="object_charts.php?id_object=<?php echo $_GET['id_object'] ?>" >Графики потребления ресурсов</a></li>
                        <li><a href="quality_warm.php?id_object=<?php echo $_GET['id_object'] ?>" >Графики качества тепла</a></li>
                        <li><a href="object_device.php?id_object=<?php echo $_GET['id_object'] ?>" >Информация о приборах</a></li>
                    </ul>
                    <label class="checkbox"></label><label class="checkbox"></label>

                    <div class="form-inline text-center">
                        <!--<div class="input-group">
                         <span class="input-group-addon"><span class="glyphicon glyphicon-th-list"></span>Тип архива </span>
                        <?php
                        /* sql_type_archiv = pg_query('SELECT 
                          "Tepl"."TypeArhiv"."Name",
                          "Tepl"."TypeArhiv".typ_arh
                          FROM
                          "Tepl"."TypeArhiv"');
                          echo "<select class='form-control' id='dropdown-select'>";
                          while ($row_type_archiv = pg_fetch_row($sql_type_archiv)) {
                          if ($row_type_archiv[1] >= 2 and $row_type_archiv[1] < 4) {
                          echo "<option class='dropdown-option' value=" . $row_type_archiv[1] . ">" . $row_type_archiv[0] . "</option>";
                          }
                          }
                          echo "</select>"; */
                        ?>
                        </div>-->
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span>Архивы</span>
                            <select id="type_archive" class="form-control"> 
                                <option value="1">Часовые</option>
                                <option selected value="2">Суточние</option>
                            </select>
                        </div>


                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Месяц</span>
                            <select id="month_select" class="form-control"> 
                                <?php
                                $m = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
                                $num_month = date('m');
                                $r = 0;
                                for ($i = 0; $i < count($m); $i++) {
                                    $r = $i + 1;
                                    if ($r < 10) {
                                        if ($num_month == $r) {
                                            echo '<option value="0' . $r . '" selected>' . $m[$i] . '</option>';
                                        } else {
                                            echo '<option value="0' . $r . '">' . $m[$i] . '</option>';
                                        }
                                    } else {
                                        if ($num_month == $r) {
                                            echo '<option value="' . $r . '" selected>' . $m[$i] . '</option>';
                                        } else {
                                            echo '<option value="' . $r . '">' . $m[$i] . '</option>';
                                        }
                                    }
                                }
                                ?>

                            </select>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Год</span>
                            <select id="year_select" class="form-control">
                                <?php
                                $y = array(2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023);
                                $num_year = date('Y');
                                $r = 0;
                                echo $num_year;
                                for ($i = 0; $i < count($y); $i++) {
                                    $r = $y[$i];
                                    if ($num_year == $r) {
                                        echo '<option value="' . $r . '" selected>' . $y[$i] . '</option>';
                                    } else {
                                        echo '<option value="' . $r . '">' . $y[$i] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <input type="submit" class="btn btn-default" id="paramtr" value="Применить"/>
                    </div> 
                    <div id="center_h1"></div>
                    <div id="view_archive" class="table-responsive text-center"></div>
                </div>
            </div>

            <!--Баковое меню -->

    </body>
    <?php
    $sql_cootd = pg_query('SELECT 
                                  "Tepl"."Places_cnt"."Name",
                                  "Places_cnt1"."Name",
                                  "Places_cnt2"."Name",
                                  "Tepl"."PropPlc_cnt"."ValueProp",
                                  "PropPlc_cnt1"."ValueProp",
                                  "PropPlc_cnt2"."ValueProp"
                                FROM
                                  "Tepl"."Places_cnt" "Places_cnt1"
                                  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                  INNER JOIN "Tepl"."Places_cnt" "Places_cnt2" ON ("Places_cnt1".plc_id = "Places_cnt2".place_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt2".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt2".plc_id = "PropPlc_cnt1".plc_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt2" ON ("Places_cnt2".plc_id = "PropPlc_cnt2".plc_id)
                                WHERE
                                  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                                  "PropPlc_cnt1".prop_id = 26 AND 
                                  "PropPlc_cnt2".prop_id = 41 AND 
                                  "Places_cnt2".plc_id =' . $_GET['id_object'] . '');
    $result_cootd = pg_fetch_row($sql_cootd);
    ?>
    <script type="text/javascript">

        function frame_hieght() {
            if (parent.document.getElementById('blockrandom') != null) {
                parent.document.getElementById('blockrandom').style.height = '0px';
                parent.document.getElementById('blockrandom').style.height = document.documentElement.offsetHeight + 'px';
                var height_wind = parent.document.getElementById('blockrandom').style.height;
                height_wind = height_wind.slice(0, -2);
                console.log(height_wind);
                if (height_wind < 800) {
                    parent.document.getElementById('blockrandom').style.height = '800px';
                    parent.document.getElementById('blockrandom').style.width = '1250px';
                } else {
                    parent.document.getElementById('blockrandom').style.height = height_wind + 'px';
                    parent.document.getElementById('blockrandom').style.width = '1250px';
                }
            }
        }


        function view_archive_date(id_object, type_arch, year, month) {
            $.ajax({
                type: 'POST',
                cache: false,
                url: 'ajax_archive.php',
                data: 'id_object=' + id_object + '&type_arch=' + type_arch + '&year=' + year + '&month=' + month,
                beforeSend: function () {
                    $('#view_archive').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#view_archive').html(html);
                    frame_hieght();
                }
            });
            return false;
        }


        $(document).ready(function () {
            var month = $('#month_select').val();
            var year = $('#year_select').val();
            var priveleg = <?php echo $_SESSION['privelege']; ?>;
            var id_object = <?php echo $id_object; ?>;
            var type_arch = $('#type_archive').val();
            view_archive_date(id_object, type_arch, year, month);

            $('#reload_alarm').click(function () {
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax_reload_error.php',
                    success: function (html) {
                        $('#reload_alarm').html(html);
                    }
                });
                return false;
            })


            $('#formSearch').click(function () {
                var str_search = $('#search').val();
                if (str_search != "") {
                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: 'ajax_serach_object.php',
                        data: 'search=' + $('#search').val(),
                        beforeSend: function () {
                            $('.main').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                        },
                        success: function (html) {
                            $('.main').html(html);
                            $('tbody tr[data-href]').addClass('clickable').click(function () {

                                if (priveleg > 0) {
                                    window.open($(this).attr('data-href'));
                                } else {
                                    window.location = $(this).attr('data-href');
                                }

                            })
                            frame_hieght();
                        }
                    });
                    return false;
                }
            });

            $('.panel-heading-map').click(function () {
                if ($('.panel-body-map').css('display') == 'none') {
                    $('.panel-body-map').show();
                    DG.then(function () {
                        map = DG.map('map', {
                            'center': [<?php echo str_replace('. ', ', ', $result_cootd[5]) ?>],
                            'zoom': 16
                        });
                        DG.marker([<?php echo str_replace('. ', ', ', $result_cootd[5]) ?>]).addTo(map).bindPopup(<?php echo "'<p> " . $result_cootd[2] . " <br> ул. " . $result_cootd[3] . " дом " . $result_cootd[4] . "</p>'" ?>).openPopup();
                    });
                } else {
                    $('.panel-body-map').hide();
                }

            });

            $('#add_comment').click(function () {
                var id_object = <?php echo $_GET['id_object']; ?>;
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: 'ajax/ajax_add_ticket.php',
                    data: 'plc_id=' + id_object + '&comment=' + $('#comment').val() + '&status=' + $('#stat_comment').val(),
                    success: function (html) {
                        $('#add_comment').html(html);
                        $('#add_comment').attr('disabled', true);
                        $('#ticket').addClass('disabled');
                        $('#ticket').html('<span class="glyphicon glyphicon-edit"></span> Заявка добавлена');
                    }
                });
            });

            $('.go-to-alarm').click(function () {
                var anchor = $(this).attr('data_id');
                window.location = "settings/settings_alarm.php#" + anchor;
            });


            $('#edit_comment').click(function () {
                var id_object = <?php echo $_GET['id_object']; ?>;
                var file = <?php echo "'" . $file . "'"; ?>;
                var id_tick = $('#myModal2 > .modal-dialog > .modal-content > .modal-footer').attr('id');
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: 'ajax/ajax_edit_ticket.php',
                    data: 'comment=' + $('#comment2').val() + '&status=' + $('#stat_comment2').val() + '&id_ticket=' + id_tick + '&file=' + file,
                    success: function (html) {
                        $('#add_comment').html(html);
                        //$('#add_comment').attr('disabled', true);
                        //$('#ticket').html('<span class="glyphicon glyphicon-edit"></span> Заявка добавлена');
                    }
                });
            });


            $('#paramtr').click(function () {
                var month = $('#month_select').val();
                var year = $('#year_select').val();
                var type_arch = $('#type_archive').val();
                view_archive_date(id_object, type_arch, year, month);
            });

            $('#export_arch_teplo').click(function () {
                var month = $('#month_select').val();
                var year = $('#year_select').val();
                window.location = 'export_object_teplo.php?year=' + year + '&month=' + month;
            });
        });
    </script>

</html>
