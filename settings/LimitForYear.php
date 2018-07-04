<?php
include '../db_config.php';
session_start();

$sql_limit = pg_query('SELECT * FROM public."LimitMonth_cnt" ORDER BY id');
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
                            <li class="active"><a href="LimitForYear.php"><span class=""></span>Распределение лимитов</a></li>
                            <li><a href="LimitForPlaces.php"><span class=""></span>Справочник лимитов</a></li>
                            <li><a href="groups.php"><span class=""></span>Группы объектов</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="text-center">Справочник распределения лимитов потребления</h1>
                        <div id="for_log"></div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4></h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"><h4 class="text-center">Тепло</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"><h4 class="text-center">Вода</h4> </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Январь</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4">  <b> <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 0, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4">  <b> <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 0, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Февраль</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 1, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4">  <b> <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 1, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Март</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4">  <b> <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 2, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 2, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Апрель</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 3, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 3, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Май</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 4, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 4, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Июнь</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4">  <b> <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 5, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 5, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Июль</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4">  <b> <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 6, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 6, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Август</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"><b>   <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 7, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 7, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Сентябрь</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 8, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 8, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Октябрь</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4">  <b> <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 9, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4">  <b> <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 9, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Ноябрь</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 10, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 10, 2) ?>"></b></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> Декабрь</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4">  <b> <input class="form-control ui-autocomplete-input teplo" id="sim_numb" placeholder="Значение тепло" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 11, 1) ?>"></b></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> <b>  <input class="form-control ui-autocomplete-input voda" id="sim_numb" placeholder="Значение вода" autocomplete="off" value="<?php echo pg_fetch_result($sql_limit, 11, 2) ?>"></b></div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-bottom: 10px; border-top: 3px solid black;">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"><h4> ИТОГО:</h4> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4" id="all_teplo"> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4" id="all_voda"> </div>

                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-xs-12" >
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-4 col-lg-offset-1 col-md-offset-1 text-right"></div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"><button class="btn btn-lg btn-primary" id="save"> Сохранить</button> </div>
                                    <div class="col-lg-3 col-md-3 col-xs-4"> </div>

                                </div>
                            </div>
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

        function refresh_summ() {
            var summ = 0;
            $('.teplo').each(function (i, elem) {
                if (elem.value != '') {
                    summ = Number(summ) + Number(elem.value);
                }
            });
            if (summ < 100) {
                $('#all_teplo').html('<h4 class="text-success">' + summ + '</h4>');
            } else if (summ == 100) {
                $('#all_teplo').html('<h4>' + summ + '</h4>');
            } else if (summ > 100) {
                $('#all_teplo').html('<h4 class="text-danger">' + summ + '</h4>');
            }
            summ = 0;
            $('.voda').each(function (i, elem) {
                if (elem.value != '') {
                    summ = Number(summ) + Number(elem.value);
                }
            });
            if (summ < 100) {
                $('#all_voda').html('<h4 class="text-success">' + summ + '</h4>');
            } else if (summ == 100) {
                $('#all_voda').html('<h4>' + summ + '</h4>');
            } else if (summ > 100) {
                $('#all_voda').html('<h4 class="text-danger">' + summ + '</h4>');
            }
        }

        $(document).ready(function () {
            refresh_summ();
            var summ = 0;
            $('.teplo').keyup(function () {
                summ = 0;
                if ($.isNumeric($(this).val())) {
                    $('.teplo').each(function (i, elem) {
                        if (elem.value != '') {
                            summ = Number(summ) + Number(elem.value);
                        }
                    });
                    if (summ < 100) {
                        $('#all_teplo').html('<h4 class="text-success">' + summ + '</h4>');
                    } else if (summ == 100) {
                        $('#all_teplo').html('<h4>' + summ + '</h4>');
                    } else if (summ > 100) {
                        $('#all_teplo').html('<h4 class="text-danger">' + summ + '</h4>');
                    }

                }
            }).change(function () {
                if ($.isNumeric($(this).val())) {
                    summ = 0;
                    $('.teplo').each(function (i, elem) {
                        if (elem.value != '') {
                            summ = Number(summ) + Number(elem.value);
                        }
                    });
                    if (summ < 100) {
                        $('#all_teplo').html('<h4 class="text-success">' + summ + '</h4>');
                    } else if (summ == 100) {
                        $('#all_teplo').html('<h4>' + summ + '</h4>');
                    } else if (summ > 100) {
                        $('#all_teplo').html('<h4 class="text-danger">' + summ + '</h4>');
                    }
                }
            }).keypress(function () {
                if ($.isNumeric($(this).val())) {
                    summ = 0;
                    $('.teplo').each(function (i, elem) {
                        if (elem.value != '') {
                            summ = Number(summ) + Number(elem.value);
                        }
                    });
                    if (summ < 100) {
                        $('#all_teplo').html('<h4 class="text-success">' + summ + '</h4>');
                    } else if (summ == 100) {
                        $('#all_teplo').html('<h4>' + summ + '</h4>');
                    } else if (summ > 100) {
                        $('#all_teplo').html('<h4 class="text-danger">' + summ + '</h4>');
                    }
                }
            }).click(function () {
                if ($.isNumeric($(this).val())) {
                    summ = 0;
                    $('.teplo').each(function (i, elem) {
                        if (elem.value != '') {
                            summ = Number(summ) + Number(elem.value);
                        }
                    });
                    if (summ < 100) {
                        $('#all_teplo').html('<h4 class="text-success">' + summ + '</h4>');
                    } else if (summ == 100) {
                        $('#all_teplo').html('<h4>' + summ + '</h4>');
                    } else if (summ > 100) {
                        $('#all_teplo').html('<h4 class="text-danger">' + summ + '</h4>');
                    }
                }
            });


            $('.voda').keyup(function () {
                summ = 0;
                if ($.isNumeric($(this).val())) {
                    $('.voda').each(function (i, elem) {
                        if (elem.value != '') {
                            summ = Number(summ) + Number(elem.value);
                        }
                    });
                    if (summ < 100) {
                        $('#all_voda').html('<h4 class="text-success">' + summ + '</h4>');
                    } else if (summ == 100) {
                        $('#all_voda').html('<h4>' + summ + '</h4>');
                    } else if (summ > 100) {
                        $('#all_voda').html('<h4 class="text-danger">' + summ + '</h4>');
                    }

                }
            }).change(function () {
                if ($.isNumeric($(this).val())) {
                    summ = 0;
                    $('.voda').each(function (i, elem) {
                        if (elem.value != '') {
                            summ = Number(summ) + Number(elem.value);
                        }
                    });
                    if (summ < 100) {
                        $('#all_voda').html('<h4 class="text-success">' + summ + '</h4>');
                    } else if (summ == 100) {
                        $('#all_voda').html('<h4>' + summ + '</h4>');
                    } else if (summ > 100) {
                        $('#all_voda').html('<h4 class="text-danger">' + summ + '</h4>');
                    }
                }
            }).keypress(function () {
                if ($.isNumeric($(this).val())) {
                    summ = 0;
                    $('.voda').each(function (i, elem) {
                        if (elem.value != '') {
                            summ = Number(summ) + Number(elem.value);
                        }
                    });
                    if (summ < 100) {
                        $('#all_voda').html('<h4 class="text-success">' + summ + '</h4>');
                    } else if (summ == 100) {
                        $('#all_voda').html('<h4>' + summ + '</h4>');
                    } else if (summ > 100) {
                        $('#all_voda').html('<h4 class="text-danger">' + summ + '</h4>');
                    }
                }
            }).click(function () {
                if ($.isNumeric($(this).val())) {
                    summ = 0;
                    $('.voda').each(function (i, elem) {
                        if (elem.value != '') {
                            summ = Number(summ) + Number(elem.value);
                        }
                    });
                    if (summ < 100) {
                        $('#all_voda').html('<h4 class="text-success">' + summ + '</h4>');
                    } else if (summ == 100) {
                        $('#all_voda').html('<h4>' + summ + '</h4>');
                    } else if (summ > 100) {
                        $('#all_voda').html('<h4 class="text-danger">' + summ + '</h4>');
                    }
                }
            });





            $('#save').click(function () {
                var array_teplo = [];
                var array_voda = [];
                var a = 1;
                var b = 1;
                $('.teplo').each(function (i, elem) {
                    if (elem.value != '') {
                        array_teplo.push({
                            id: a,
                            val: elem.value
                        });
                    }
                    a++;
                });
                $('.voda').each(function (i, elem) {
                    if (elem.value != '') {
                        array_voda.push({
                            id: b,
                            val: elem.value
                        });
                    }
                    b++;
                });
                console.log(array_teplo);
                console.log(array_voda);

                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax/ajax_save_limit.php',
                    data: {arr1: array_teplo, arr2: array_voda},
                    success: function (html) {
                        $('#for_log').html(html);
                        $('#save').text("Сохранено");
                        setTimeout("$('#for_log').html(''); $('#save').text('Сохранить');", 3000);
                    }
                });
                return false;
            });

        });
    </script>


</html>
