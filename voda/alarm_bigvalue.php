<?php
include '../db_config.php';
session_start();


$date1 = date("Y-m-21", strtotime("-1 month"));
$date2 = date('Y-m-20');
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

        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="../css/dashboard.css"/>
        <link rel="stylesheet" type="text/css" href="../css/jquery.datetimepicker.css"/>
        <script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="../js/bootstrap.js"></script>
        <script type="text/javascript" src="../js/npm.js"></script>
        <script type="text/javascript" src="../js/jquery.datetimepicker.js"></script>
        <script src="../js/jquery.livequery.js" type="text/javascript"></script>
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
                            <li><a href="../objects.php"><span class="glyphicon glyphicon-home"></span> Главная  </a></li>
                            <li><a href="../reports.php"><span class="glyphicon glyphicon-list-alt"></span>  Лимиты  </a></li>
                            <li><a href="../alarm.php"><span class="glyphicon glyphicon-bell"></span><span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['alarm'] ?></span> Аварии   </a></li>
                            <li><a href="../maps.php"><span class="glyphicon glyphicon-globe"></span> Карта </a></li>
                        </ul>
                        <ul class="nav nav-sidebar">
                            <li><a href="../interface_voda.php">МУП ПОВВ Интерфейс</a></li>
                            <li><a href="../interface_teplo.php">МУП ЧКТС Интерфейс</a></li>
                        </ul>
                        <ul class="nav nav-sidebar">
                            <li><a href="alarm_water.php">ПОВВ: Отсутствует вода</a></li>
                            <li class="active"><a href="alarm_bigvalue.php">ПОВВ: Корректировка ХВС</a></li>
                            <li><a href="alarm_impuls.php">ПОВВ: Аномалии данных ХВС</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="text-center">Аварии: Корректировка ХВС</h1>
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
                                <span class="input-group-addon"><span class="glyphicon glyphicon-tint"></span>Знач. </span>
                                <input type="text" class="form-control" id="kor_value" style="width: 130px;"value="20"/>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Нач. дата </span>
                                <input type="text" class="form-control" id="datetimepicker1" value="<?php echo date("d.m.Y", strtotime($date1)); ?>"/>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Кон. дата </span>
                                <input type="text" class="form-control" id="datetimepicker2" value="<?php echo date("d.m.Y", strtotime($date2)); ?> "/>
                            </div>
                            <input type="submit" class="btn btn-default" id="paramtr" value="Применить"/>
                        </div>
                        <div id="all_object">

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

        function alarm(date1, date2, kor_value, id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_alarm_bigvalue.php',
                data: 'date1=' + date1 + '&date2=' + date2 + '&kor_value=' + kor_value + '&id=' + id,
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

        $(document).ready(function () {
            frame_hieght();
            var priveleg = <?php echo $_SESSION['privelege']; ?>;
            var kor_value = $('#kor_value').val();
            var date1 = $('#datetimepicker1').val();
            var date2 = $('#datetimepicker2').val();
            var id = $('#user_select').val();
            console.log(date1, date2, kor_value, id);
            alarm(date1, date2, kor_value, id);

            $('#paramtr').click(function () {
                var kor_value = $('#kor_value').val();
                var date1 = $('#datetimepicker1').val();
                var date2 = $('#datetimepicker2').val();
                var id = $('#user_select').val();
                console.log(date1, date2, kor_value, id);
                alarm(date1, date2, kor_value, id);
            });



            $('thead td[data-query]').livequery("click", function (event) {
                var sort = $(this).attr('data-query');
                var date_afte = $('#datetimepicker1').val();
                var date_now = $('#datetimepicker2').val();
                //alert('ckack');
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: '../ajax_alarm_sort.php',
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



            $('#formSearch').click(function () {
                var str_search = $('#search').val();
                if (str_search != "") {
                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: '../ajax_serach_object.php',
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
