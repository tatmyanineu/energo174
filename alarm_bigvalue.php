<?php
include 'db_config.php';
session_start();
$date = date('Y-m-d');
$time = strtotime("-20 day");
$after_day = date("Y-m-d", $time);

/*

  "Tepl"."Arhiv_cnt"."DateValue" >= \'2015-07-01\' AND
  "Tepl"."Arhiv_cnt"."DateValue" <= \'2015-05-01\' AND

  "Tepl"."Arhiv_cnt"."DateValue" >= \''.$after_day .'\' AND
  "Tepl"."Arhiv_cnt"."DateValue" <= \''.$date.'\' AND
 * 
 */
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
            <!--Верхний бар -->

            <!--Боковое меню -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-md-2 sidebar">
                        <ul class="nav nav-sidebar">
                            <li><a href="objects.php"><span class="glyphicon glyphicon-home"></span> Главная  </a></li>
                            <li><a href="limits.php"><span class="glyphicon glyphicon-list-alt"></span>  Лимиты  </a></li>
                            <?php
                            if ($_SESSION['privelege'] > 0) {
                                echo '<li><a href="alarm.php" data-toggle="tooltip" data-placement="right" title="Процент неисправных обьектов: ' . number_format($_SESSION['proc'], 2) . '%"><span class="glyphicon glyphicon-bell" ></span><span id="reload_alarm" class="badge pull-right">' . $_SESSION['alarm'] . '</span> Аварии   </a></li>'
                                . '<li><a href="maps.php"><span class="glyphicon glyphicon-globe"></span> Карта </a></li>'
                                . '<li><a href="logs.php"><span class="glyphicon glyphicon-book"></span> Логи </a></li>'
                                . '<li><a href="tickets.php"><span class="glyphicon glyphicon-tags"></span> <span id="reload_alarm" class="badge pull-right">' . $_SESSION['count_ticiket'] . '</span> Заявки</a></li>';
                            }
                            ?>
                        </ul>
                        <ul class="nav nav-sidebar">
                            <li><a href="#" id="export_limit"><span class="glyphicon glyphicon-floppy-disk"></span>Сохранить отчет</a></li>
                        </ul>
                        <ul class="nav nav-sidebar">
                            <li><a href="alarm_water.php">Отсутствует вода</a></li>
                            <li><a href="alarm_heat.php">Отсутствует тепло</a></li>
                            <li><a href="alarm_NaN.php">NaN значения</a></li>
                            <li  class="active"><a href="alarm_bigvalue.php">Корректировка ХВС</a></li>
                            <li><a href="alarm_massa.php">Аномалии теплоносителя</a></li>
                            <li><a href="alarm_temper.php">Аномалии тепературы</a></li>
                            <li><a href="alarm_impuls.php">Аномалии данных ХВС</a></li>
                            <li><a href="alarm_night.php">Аварии ХВС(Ночная утечка) </a></li>
                            <li><a href="alarm_dt.php">Заниженная dt </a></li>
                        </ul>
                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="text-center">Аварии: Корректировка ХВС</h1>
                        <div class="form-inline text-center">

                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span>Архивы</span>
                                <select id="type_archive" class="form-control"> 
                                    <option value="1">Часовые</option>
                                    <option selected value="2">Суточние</option>
                                </select>
                            </div>

                            <div class="input-group" style="width: 200px;">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-tint"></span>Значение </span>
                                <input type="text" class="form-control" id="kor_value" value="20"/>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Нач. дата </span>
                                <input type="text" class="form-control" id="datetimepicker1" value="<?php echo date("d.m.Y", strtotime($after_day)); ?>"/>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Кон. дата </span>
                                <input type="text" class="form-control" id="datetimepicker2" value="<?php echo date("d.m.Y", strtotime($date)); ?> "/>
                            </div>
                            <input type="submit" class="btn btn-default" id="paramtr" value="Применить"/>
                        </div>
                        <div style="margin-top: 20px" class="form-inline text-left"> <input type="checkbox" id="view_alarm"> Скрыть исключения</div>
                        <div id="all_object">

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


        $('#datetimepicker1').datetimepicker({
            format: 'd.m.Y H:00',
            lang: 'ru',
        });
        $('#datetimepicker2').datetimepicker({
            format: 'd.m.Y H:00',
            lang: 'ru',
        });

        function alarm(date_afte, date_now, kor_value, type_arch, alarm_box) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax_alarm_bigvalue.php',
                data: {date_afte: date_afte, date_now: date_now, kor_value: kor_value, type_arch: type_arch, alarm_box: alarm_box},
                beforeSend: function () {
                    $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#all_object').html(html);
                    $('.object').click(function () {
                        //alert(this.id);
                        window.open('object.php?id_object=' + this.id);
                        return false;
                    });

                    $('.tickets').click(function () {
                        //alert(this.id);
                        window.open('ticket_object.php?id_object=' + this.id);
                        return false;
                    });


                    frame_hieght();
                }
            });
            return false;
        }

        $(document).ready(function () {
            frame_hieght();
            priveleg = 31;
            var kor_value = $('#kor_value').val();
            var date_now = $('#datetimepicker1').val();
            var date_afte = $('#datetimepicker2').val();
            var type_arch = $('#type_archive').val();
            console.log(date_afte, date_now, kor_value);
            var alarm_box = ($("#view_alarm").is(':checked')) ? 1 : 0;
            console.log(alarm_box);
            alarm(date_afte, date_now, kor_value, type_arch, alarm_box);

            $('#export_limit').click(function () {
                var date1 = $('#datetimepicker1').val();
                var date2 = $('#datetimepicker2').val();
                var value = $('#kor_value').val();
                console.log(date1 + " " + date2);
                console.log(date1, date2, value);
                window.location = 'export_alarm_bigvalue.php?date1=' + date1 + '&date2=' + date2 + '&value=' + value;

            });

            $('#view_alarm').click(function () {
                if ($('#view_alarm').is(":checked")) {
                    var kor_value = $('#kor_value').val();
                    var date_now = $('#datetimepicker1').val();
                    var date_afte = $('#datetimepicker2').val();
                    var type_arch = $('#type_archive').val();
                    var alarm_box = ($("#view_alarm").is(':checked')) ? 1 : 0;

                    alarm(date_afte, date_now, kor_value, type_arch, alarm_box);
                } else {
                    var kor_value = $('#kor_value').val();
                    var date_now = $('#datetimepicker1').val();
                    var date_afte = $('#datetimepicker2').val();
                    var type_arch = $('#type_archive').val();
                    var alarm_box = ($("#view_alarm").is(':checked')) ? 1 : 0;


                    alarm(date_afte, date_now, kor_value, type_arch, alarm_box);
                }
            });


            $('thead td[data-query]').livequery("click", function (event) {
                var sort = $(this).attr('data-query');
                //alert('ckack');
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax_alarm_sort.php',
                    data: 'id_sort=' + sort + '&date_afte=' + date_afte + '&date_now=' + date_now + '&param_sort=1',
                    success: function (html) {
                        $('#all_object').html(html);
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
            });

            $('#reload_alarm').click(function () {
                //alert('okokokok');
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


            $('tbody tr[data-href]').addClass('clickable').click(function () {

                if (priveleg > 0) {
                    window.open($(this).attr('data-href'));
                } else {
                    window.location = $(this).attr('data-href');
                }

            });

            $('#paramtr').click(function () {

                var date_now = $('#datetimepicker1').val();
                var date_afte = $('#datetimepicker2').val();
                var kor_value = $('#kor_value').val();
                var type_arch = $('#type_archive').val();
                var alarm_box = ($("#view_alarm").is(':checked')) ? 1 : 0;
                console.log(date_afte, date_now, kor_value);
                alarm(date_afte, date_now, kor_value, type_arch, alarm_box);
            });

            $('#formSearch').click(function () {
                var str_search = $('#search').val();
                if (str_search != "") {
                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: 'ajax_serach_object.php',
                        data: 'search=' + $('#search').val(),
                        beforeSend: function () {
                            $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                        },
                        success: function (html) {
                            $('#all_object').html(html);
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
        });
    </script>

</html>
