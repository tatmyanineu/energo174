<?php
include '../db_config.php';
session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>

        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="../css/dashboard.css"/>
        <script src="../js/jquery-2.2.1.js" type="text/javascript"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/bootstrap.js"></script>
        <script type="text/javascript" src="../js/npm.js"></script>
        <script type="text/javascript" src="../js/jquery.livequery.js"></script>
        <script src="../js/jquery.datetimepicker.js" type="text/javascript"></script>
        <link href="../css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
        <script src="../js/bootstrap-filestyle.min.js" type="text/javascript"></script>
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

                            <li><a id="forBrand" href="../index.php">Выход</a></li>
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
            <!--Верхний бар -->

            <!--Боковое меню -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-md-2 sidebar">
                        <ul class="nav nav-sidebar">
                            <li><a href="../objects.php"><span class="glyphicon glyphicon-home"></span> Главная  </a></li>
                            <li><a href="../limits.php"><span class="glyphicon glyphicon-list-alt"></span>  Лимиты  </a></li>
                            <li><a href="../alarm.php"><span class="glyphicon glyphicon-bell"></span><span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['alarm'] ?></span> Аварии   </a></li>
                            <li><a href="../maps.php"><span class="glyphicon glyphicon-globe"></span> Карта </a></li>
                            <li><a href="../logs.php"><span class="glyphicon glyphicon-book"></span> Логи </a></li>
                            <li><a href="../tickets.php"><span class="glyphicon glyphicon-tags"></span> <span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['count_ticiket']; ?></span> Заявки</a></li>
                            <li><a href="index.php"><span class="glyphicon glyphicon-cog"></span> <span id="reload_alarm" class="badge pull-right"></span> Настройки</a></li>
                            <li><a href="../password_reports.php"><span class="glyphicon glyphicon-user"></span>  <span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['reports_passord']; ?></span> Востановление пароля</a></li>
                        </ul>

                        <ul class="nav nav-sidebar">
                            <li><a href="LimitForYear.php"><span class=""></span>Распределение лимитов</a></li>
                            <li><a href="LimitForPlaces.php"><span class=""></span>Справочник лимитов</a></li>
                            <li class="active"><a href="groups.php"><span class=""></span>Группы объектов</a></li>
                        </ul>

                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="text-center">Справочник лимитов потребления на объектах</h1>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-xs-12">
                                <div class="row">
                                    <div class="col-lg-offset-3 col-md-offset-3 col-lg-4 col-md-4 col-xs-6">
                                        <h4>Введите название группы</h4>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-xs-6">
                                        <input class="form-control ui-autocomplete-input" id="name_inp" placeholder="Название группы" autocomplete="off">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-md-offset-3 col-lg-4 col-md-4 col-xs-6">
                                        <h4>Введите адрес главного корпуса</h4>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-xs-6">
                                        <input class="form-control ui-autocomplete-input" id="adr_inp" placeholder="Адрес главного корпуса" autocomplete="off">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-md-offset-3 col-lg-4 col-md-4 col-xs-6">
                                        <h4>Введите лимит на тепло</h4>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-xs-6">
                                        <input class="form-control ui-autocomplete-input" id="lim_teplo" placeholder="Лимит тепло" autocomplete="off">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-md-offset-3 col-lg-4 col-md-4 col-xs-6">
                                        <h4>Введите лимит на воду</h4>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-xs-6">
                                        <input class="form-control ui-autocomplete-input" id="lim_voda" placeholder="Лимит вода" autocomplete="off">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-md-offset-3 col-lg-4 col-md-4 col-xs-6"></div>
                                    <div class="col-lg-4 col-md-4 col-xs-6">
                                        <button type="button" id="add_btn" class="btn btn-lg btn-primary">Добавить</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <br>
                        <h4 class="text-center" id="log_text"></h4>
                        <br>
                        <div class="row" id="all_object">

                        </div>
                    </div>
                </div>
            </div>

            <!--Баковое меню -->

    </body>
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


        function reset1() {
            plc_id = null;
            $('#plc_name').val('');
            $('#limit_teplo').val('');
            $('#limit_voda').val('');
            $('#add_sim').text('Добавить');
            check();
        }


        function refresh_table() {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_group_refresh_table.php',
                success: function (html) {
                    $('#all_object').html(html);
                    $('.delete').click(function () {
                        delete_group(this.id);
                        refresh_table();
                    });
                    $('tbody td[data-href]').addClass('clickable').click(function () {
                        window.location = $(this).attr('data-href');
                    })


                }
            });
            return false;
        }

        function add_group(name, adr, teplo, voda) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_add_group.php',
                data: 'name=' + name + '&adres=' + adr + '&teplo=' + teplo + '&voda=' + voda,
                success: function (html) {
                    refresh_table();
                }
            });
            return false;
        }

        function delete_group(id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_del_group.php',
                data: 'id=' + id,
                success: function (html) {
                    //$('#result_add').html(html).fadeIn(3000).fadeOut(1000);
                    //refrash_table();
                }
            });
            return false;
        }


        $(document).ready(function () {


            $('#add_btn').click(function () {
                var name = $('#name_inp').val();
                var adr = $('#adr_inp').val();
                var teplo = $('#lim_teplo').val();
                var voda = $('#lim_voda').val();
                add_group(name, adr, teplo, voda);
            });

            frame_hieght();
            refresh_table();
            $('#reload_alarm').click(function () {
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: '../ajax_reload_error.php',
                    success: function (html) {
                        $('#reload_alarm').html(html);
                    }
                });
                return false;
            });
        });
    </script>


</html>
