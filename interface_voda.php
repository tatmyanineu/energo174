<?php
include 'db_config.php';
$date1 = date("Y-m-21", strtotime("-1 month"));
$date2 = date('Y-m-20');
session_start();
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
        <link href="css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script type="text/javascript" src="js/jquery.livequery.js"></script>
        <script src="js/jquery.datetimepicker.js" type="text/javascript"></script>
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


            <div class="container-fluid">
                <div class="row">
                    <!--Боковое меню -->
                    <div class="col-sm-3 col-md-2 sidebar">
                        <?php include './include/menu.php';?>


                        <ul class="nav nav-sidebar">
                            <li><a href="voda/alarm_water.php">ПОВВ: Отсутствует вода</a></li>
                            <li><a href="voda/alarm_bigvalue.php">ПОВВ: Корректировка ХВС</a></li>
                            <li><a href="voda/alarm_impuls.php">ПОВВ: Аномалии данных ХВС</a></li>
                        </ul>
                        <ul class="nav nav-sidebar" id="download"></ul>
                    </div>
                    <!--Боковое меню -->

                    <!--Контент -->
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="page-header">
                            <div class="form-inline text-center">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span>Польз.</span>
                                    <select id="user_select" class="form-control"> 
                                        <?php
                                        $sql_user_povv = pg_query('SELECT DISTINCT 
                                                "Tepl"."User_cnt"."SurName",
                                                "Tepl"."User_cnt"."PatronName",
                                                "Tepl"."User_cnt"."Privileges",
                                                "Tepl"."User_cnt".usr_id
                                              FROM
                                                "Tepl"."User_cnt"
                                              WHERE
                                                "Tepl"."User_cnt"."Privileges" = 1');
                                        $n = 0;
                                        while ($row_user = pg_fetch_row($sql_user_povv)) {
                                            if ($n == 0) {
                                                echo '<option value="' . $row_user[3] . '" selected>' . $row_user[0] . ' ' . $row_user[1] . '</option>';
                                            } else {

                                                echo '<option value="' . $row_user[3] . '">' . $row_user[0] . ' ' . $row_user[1] . '</option>';
                                            }
                                            $n++;
                                        }
                                        ?>

                                    </select>
                                </div>

                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Нач. дата </span>
                                    <input type="text" class="form-control" id="datetimepicker1"  value="<?php echo date("d.m.Y", strtotime($date1)); ?>"/>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Кон. дата </span>
                                    <input type="text" class="form-control" id="datetimepicker2"  value="<?php echo date("d.m.Y", strtotime($date2)); ?>"/>
                                </div>
                                <input type="submit" class="btn btn-default" id="paramtr" value="Применить"/>
                            </div>



                        </h1>
                        <div id="all_object" class="table-responsive">
                        </div>
                    </div>
                </div>
            </div>

            <!--Баковое меню -->

    </body>

    <script type="text/javascript">
        $('#datetimepicker1').datetimepicker({
            format: 'd.m.Y',
            lang: 'ru',
        });
        $('#datetimepicker2').datetimepicker({
            format: 'd.m.Y',
            lang: 'ru',
        });

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

        function view_reports(data1, data2, id) {
            $.ajax({
                type: 'POST',
                cache: false,
                url: 'ajax_reports_voda.php',
                data: 'date1=' + data1 + '&date2=' + data2 + '&id=' + id,
                beforeSend: function () {
                    $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#all_object').html(html);
                    $('.go_object').click(function () {
//                        $('tr#hide_' + this.id).each(function (i, elem) {
                        //                      });
                        window.open('object.php?id_object=' + this.id + '');
                    });
                    $('.go_ticket').click(function () {
//                        $('tr#hide_' + this.id).each(function (i, elem) {
                        //                      });
                        window.open('ticket_object.php?id_object=' + this.id + '');
                    });
                    $('#download').html("<li><a href='export_reports_voda.php?data1=" + data1 + "&data2=" + data2 + "&id=" + id + "'> <span class='glyphicon glyphicon-floppy-disk'></span>Скачать отчет</a></li>");
                    frame_hieght();
                }
            });

        }

        $(document).ready(function () {
            var data1 = $('#datetimepicker1').val();
            var data2 = $('#datetimepicker2').val();
            var id = $('#user_select').val();

            view_reports(data1, data2, id);

            $('#paramtr').click(function () {

                var data1 = $('#datetimepicker1').val();
                var data2 = $('#datetimepicker2').val();
                var id = $('#user_select').val();
                view_reports(data1, data2, id);
                //frame_hieght();
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
