<?php

include 'db_config.php';
session_start();

$sql_all_school = pg_query('SELECT 
  "Places_cnt1"."Name",
  "Places_cnt1".plc_id
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
WHERE
  "Places_cnt1".typ_id = 17');
while ($result = pg_fetch_row($sql_all_school)) {
    $array_school[] = array(
        'plc_id' => $result[1],
        'name' => $result[0]
    );
}

$sql_sens_name = pg_query('SELECT 
    "Tepl"."TypeSensor".sen_id,
    "Tepl"."TypeSensor"."Name"
  FROM
    "Tepl"."TypeSensor"');
while ($result = pg_fetch_row($sql_sens_name)) {
    $sens_cnt[] = array(
        'sens_id' => $result[0],
        'sens_name' => $result[1]
    );
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
                            <li   class="active"><a href="login_user.php">Лог авторизаций</a></li>
                        </ul>
                    </div>
                    <!--Боковое меню -->

                    <!--Контент -->
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1>Журнал авторизаций пользователей</h1>
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

        function read_logs() {

        }

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

        function update_table() {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_load_user_log.php',
                success: function (html) {
                    $('#all_object').html(html);
                    //all_object(id_distinct, priveleg);
                }
            });
            return false;
        }

        $(document).ready(function () {
            priveleg = <?php echo $_SESSION['privelege']; ?>;
            frame_hieght();
            update_table();


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

            $('.for_click').click(function () {
                $('tr#hide_' + this.id).each(function (i, elem) {
                    if ($(elem).css('display') == 'none') {
                        $(elem).show();
                        frame_hieght();
                    } else {
                        $(elem).hide();
                        frame_hieght();
                    }


                });
            });

            $('#reload_alarm').click(function () {
                var id_distinct = 0;
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax_reload_error.php',
                    success: function (html) {
                        $('#reload_alarm').html(html);
                        //all_object(id_distinct, priveleg);
                    }
                });
                return false;
            })
        });



    </script>

</html>
