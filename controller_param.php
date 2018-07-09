<?php
session_start();
include './db_config.php';
?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">

        <title>Диспетчер mode</title>
        <!-- Latest compiled and minified CSS -->
        <script
            src="http://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>

        <link href="modules/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="modules/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="modules/bootstrap-3.3.7-dist/js/bootstrap.js" type="text/javascript"></script>


        <link href="modules/css/dashboard.css" rel="stylesheet" type="text/css"/>
    </head> 
    <body>
        <div id="content">
            <!-- Верхний бар -->
            <div class="navbar navbar-default navbar-fixed-top" style=".navbar a{
                     color: #337AB7;
                 }" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a id="forBrand" class="navbar-brand" href="#">Измерительная система контроля и учета энергоресурсов</a>
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a id="forBrand" href="#">Пользователь: <?php echo $_SESSION['login']; ?></a></li>
                            <li><a id="forBrand" href="index.php">Выход</a></li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <!--Боковое меню -->
                <div class="col-sm-3 col-md-2 sidebar">
                    <?php include 'include/menu.php'; ?>

                    <ul class = "nav nav-sidebar">
                        <li class="active"><a href="controller_param.php" class="toggle-vis" data-column="2"><span class=""></span> Параметры  </a></li>
                    </ul>
                </div>


                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">  
                    <h1 class="page-header">
                        <div id="center_h1">Параметры диспетчера
                        </div>
                    </h1>
                    <div id="example_wrapper" >
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-xs-12 col-lg-offset-2 col-md-offset-2">

                                <?php
                                $style = ['panel-primary', 'panel-success', 'panel-info', 'panel-warning'];

                                $sql_inc = pg_query('SELECT id, name, coeficient, date_time
                                                     FROM fault_cnt');

                                while ($row = pg_fetch_row($sql_inc)) {
                                    $i = rand(0, 3);

                                    echo '<div class="panel ' . $style[$i] . '">'
                                    . '<div class="panel-heading"><h4>' . $row[1] . '</h4></div>'
                                    . '<div class="panel-body">'
                                    . '<div class="row" style="margin-top: 20px;">'
                                    . '<div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Коэффициент</b></h4></div>'
                                    . '<div class="col-lg-4 col-md-4 col-xs-12"><h4> <input type="text" id="' . $row[0] . '" class="form-control get_value" value="' . $row[2] . '"></h4></div>'
                                    . '</div>'
                                    . '<div class="row" style="margin-top: 20px;">'
                                    . '<div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Дата последнего выполнения</b></h4></div>'
                                    . '<div class="col-lg-4 col-md-4 col-xs-12"><h4>' . date('d.m.Y h:s:00', strtotime($row[3])) . '</h4></div>'
                                    . '</div>'
                                    . '</div>'
                                    . '</div>';
                                }
                                ?>

                                <button class="btn btn-primary btn-lg" id="get_value_btn">Обновить настройки</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script type="text/javascript" charset="utf-8">

            $(document).ready(function () {

                $('#get_value_btn').click(function () {
                    var value = [];
                    $('.get_value').each(function () {
                        var elem = {id: this.id, val: $('#' + this.id).val()};
                        value.push(elem);

                    });
                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: 'ajax/controllers/rewrite_prop.php',
                        data: {value: value},
                        success: function (html) {

                        }
                    });
                    return false;
                });
                //console.log(value);

                $('.nav-sidebar li a').each(function () {
                    var location = window.location.href;
                    var link = this.href;
                    if (location == link) {
                        $(this).parent('li').addClass("active");
                    }

                });
            }
            );
        </script>



    </body></html>