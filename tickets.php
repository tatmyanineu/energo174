<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_config.php';
session_start();
if (isset($_SESSION['login'])) {
    
} else {
    header('location: index.php');
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
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script type="text/javascript" src="js/jquery.livequery.js"></script>
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
                        <?php include './include/menu.php'; ?> 
                        <ul class="nav nav-sidebar">
                            <li><a id="export_tickets" href="#"> <span class="glyphicon glyphicon-floppy-disk"></span>Экспорт заявок</a></li>
                        </ul>


                    </div>
                    <!--Боковое меню -->

                    <!--Контент -->
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1>Журнал заявок на обслуживание</h1>
                            </div>
                            <div class="form-inline text-center">
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
                        </h1>
                        <div id="all_object" class="table-responsive">
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

        function view_tickets(month, year, param) {
            $.ajax({
                type: 'POST',
                cache: false,
                url: 'ajax/ajax_tickets.php',
                data: 'month=' + month + '&year=' + year + '&param=' + param,
                beforeSend: function () {
                    $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#all_object').html(html);
                    $('.ticket').click(function () {
                        //alert(this.id);
                        window.location = 'edit_ticket.php?id_ticket=' + this.id;
                        return false;
                    });
                    $('.object').click(function () {
                        //alert(this.id);
                        window.open('object.php?id_object=' + this.id);
                        return false;
                    });
                    frame_hieght();
                }
            });
            return false;
        }

        function all_tickets(param) {
            $.ajax({
                type: 'POST',
                cache: false,
                url: 'ajax/ajax_tickets.php',
                data: 'param=' + param,
                beforeSend: function () {
                    $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#all_object').html(html);
                    $('.ticket').click(function () {
                        //alert(this.id);
                        window.location = 'edit_ticket.php?id_ticket=' + this.id;
                        return false;
                    });

                    $('.object').click(function () {
                        //alert(this.id);
                        window.open('object.php?id_object=' + this.id);
                        return false;
                    });
                    frame_hieght();
                }
            });
            return false;
        }


        $(document).ready(function () {

            $('.nav-sidebar li a').each(function () {
                var location = window.location.href;
                var link = this.href;
                if (location == link) {
                    $(this).parent('li').addClass("active");
                }

            });


            priveleg = <?php echo $_SESSION['privelege']; ?>;
            frame_hieght();
            param = 1;
            //view_tickets_list(param);
            all_tickets(param);
            $('a[class ^="tickets"').click(function () {
                $('#' + param).parent('li').removeClass('active');
                $('#tickets_activ').removeClass('active');
                //alert(this.id);
                $('#' + this.id).parent('li').addClass('active');
                param = this.id;
                all_tickets(param);
            });

            var month = $('#month_select').val();
            var year = $('#year_select').val();



            $('#paramtr').click(function () {
                var month = $('#month_select').val();
                var year = $('#year_select').val();
                m = $('#month_select').val();
                y = $('#year_select').val();
                view_tickets(month, year, param);
            });


            console.log(month + ' ' + year);

            $('#export_tickets').click(function () {

                window.location = 'export_tickets.php?param=' + param;

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

                            frame_hieght();
                        }
                    });
                    return false;
                }
            });

            $('#reload_alarm').click(function () {
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
