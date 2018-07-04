<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../db_config.php';
session_start();
$id_object = $_GET['id_object'];
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
        <link href="../css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="../js/bootstrap.js"></script>
        <script type="text/javascript" src="../js/npm.js"></script>
        <script src="../js/jquery.datetimepicker.js" type="text/javascript"></script>
        <script type="text/javascript" src="../js/jquery.livequery.js"></script>
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
                        <ul class="nav nav-sidebar">
                            <li><a href="../objects.php"><span class="glyphicon glyphicon-home"></span> Главная  </a></li>
                            <li><a href="../limits.php"><span class="glyphicon glyphicon-list-alt"></span>  Лимиты  </a></li>
                            <?php
                            if ($_SESSION['privelege'] > 1) {
                                echo '<li><a href="../alarm.php" data-toggle="tooltip" data-placement="right" title="Процент неисправных обьектов: ' . number_format($_SESSION['proc'], 2) . '%"><span class="glyphicon glyphicon-bell" ></span><span id="reload_alarm" class="badge pull-right">' . $_SESSION['alarm'] . '</span> Аварии   </a></li>'
                                . '<li><a href="../maps.php"><span class="glyphicon glyphicon-globe"></span> Карта </a></li>'
                                . '<li><a href="../logs.php"><span class="glyphicon glyphicon-book"></span> Логи </a></li>'
                                . '<li><a href="../tickets.php"><span class="glyphicon glyphicon-tags"></span> <span id="reload_alarm" class="badge pull-right">' . $_SESSION['count_ticiket'] . '</span> Заявки</a></li>'
                                . '<li class="active"><a href="index.php"><span class="glyphicon glyphicon-cog"></span> <span id="reload_alarm" class="badge pull-right"></span> Настройки</a></li>'
                                . '<li><a href="password_reports.php"><span class="glyphicon glyphicon-user"></span>  <span id="reload_alarm" class="badge pull-right">' . $_SESSION['reports_passord'] . '</span> Востановление пароля</a></li>'
                                . '</ul>'
                                . '<ul class="nav nav-sidebar">'
                                . '<li><a href="../object.php?id_object=' . $id_object . '"><span class="glyphicon glyphicon-chevron-left"></span>Назад</a></li>'
                                . '</ul>';
                            }
                            ?>



                    </div>
                    <!--Боковое меню -->

                    <!--Контент -->
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1> <span class="glyphicon glyphicon-pencil"></span>Коррекция показаний</h1>
                            </div>
                        </h1>
                        <div class="row" >
                            <div class="col-lg-12 col-md-12 col-xs-12">

                                <div id="data_korr" class="row">
                                    <div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Данные корректировки</b></h4></div>
                                    <div class="col-lg-5 col-md-5 col-xs-12">
                                        <div class="form-group">
                                            <label for="inputVvod" class="col-sm-4 control-label">Ввод</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="vvod" placeholder="Выберите ввод"> </select>
                                            </div>
                                        </div>
                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputdatetimepicker1" class="col-sm-4 control-label">Дата</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="datetimepicker1"  style="width: 100%;" placeholder="Дата коректировки">
                                            </div>
                                        </div>
                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputnp" class="col-sm-4 control-label">Нач. показания</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="np"  style="width: 100%;" placeholder="Начальные показания">
                                            </div>
                                        </div>
                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputkp" class="col-sm-4 control-label">Кон. показания</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="kp"  style="width: 100%;" placeholder="Конечные показания">
                                            </div>
                                        </div>
                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputkp" class="col-sm-4 control-label">Примечание</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="prim"  style="width: 100%;" placeholder="Примечание">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div id="data_korr" class="row">
                                    <div class="text-center col-lg-12 col-md-12 col-xs-12">
                                        <button type="button" id="add_korr" class="btn btn-lg btn-primary">Добавить</button>
                                    </div>
                                </div>
                            </div>


                            <br><br><br>
                            <div>
                                <div class="row">
                                    <div id="all_object" class="text-center col-lg-12 col-md-12 col-xs-12 hidden">

                                    </div>
                                </div>
                            </div>
                            <div class="text-center" id="result_add"></div>
                        </div>
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

        function add_select(plc) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: '../ajax/ajax_tickets_vvod.php',
                data: 'plc_id=' + plc,
                success: function (data) {
                    //$('#edit_ticket').html('Сохранено');
                    console.log(data);
                    for (var i = 0; i < data.length; i++) {
                        $('#vvod').append($('<option value="' + data[i].prp_id + '">' + data[i].name + '</option>'));
                        $('#vvod_voda').append($('<option value="' + data[i].prp_id + '">' + data[i].name + '</option>'));

                    }
                    //$('#reload_alarm').html(html);
                    //all_object(id_distinct, priveleg);
                }
            });
            return false;
        }

        function refresh_table_korrect(plc) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: '../ajax/ajax_refresh_table_korrect.php',
                data: 'plc_id=' + plc,
                success: function (html) {
                    $('#all_object').html(html);
                    $('#all_object').removeClass('hidden');
                    $('.delete').click(function () {
                        delete_alarm(this.id);
                        refresh_table_korrect(plc);

                    });
                }
            });
            return false;
        }

        function delete_alarm(id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: '../ajax/ajax_del_korrect.php',
                data: 'id=' + id,
                success: function (html) {
                    refresh_table_korrect(plc);
                    $('#result_add').html(html).fadeIn(3000).fadeOut(1000);
                }
            });
            return false;
        }


        $(document).ready(function () {
            priveleg = <?php echo $_SESSION['privelege']; ?>;
            plc = <?php echo $id_object; ?>;
            add_select(plc);
            var korr = 0;
            var save = 0;
            refresh_table_korrect(plc);

            $('#add_korr').click(function () {
                var id_ticket = 0;
                var prp_id = $('#vvod').val();
                var date = $("#datetimepicker1").val();
                var np = $('#np').val();
                var kp = $('#kp').val();
                var name = $('#vvod option:selected').text();
                var prim = $('#prim').val();
                if (date != "" & np != "" & kp != "" & name != "") {
                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: '../ajax/ajax_add_korrect.php',
                        data: {id_ticket: id_ticket, plc_id: plc, prp_id: prp_id, date: date, np: np, kp: kp, name: name, prim: prim},
                        success: function (html) {
                            $('#result_add').html(html).fadeIn(3000).fadeOut(1000);
                            refresh_table_korrect(plc);
                            console.log(html);
                            korr++;
                            $('input[type=text]').each(function () {
                                $(this).val('');
                            });
                        }
                    });
                }
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
                        }
                    });
                    return false;
                }
            });

            $('#reload_alarm').click(function () {
                //alert('okokokok');
                var id_distinct = 0;
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax_reload_error.php',
                    success: function (html) {
                        $('#reload_alarm').html(html);
                        all_object(id_distinct, priveleg);
                    }
                });
                return false;
            })
        });



    </script>

</html>
