<?php
include 'db_config.php';
$date = date('Y-m-d');
session_start();

switch ($_SESSION['privelege']) {
    case 0:
        $sql_kol_object = pg_query('SELECT 
            "Places_cnt1".plc_id
          FROM
            "Tepl"."Places_cnt" "Places_cnt1"
            INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
            INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
            INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
            INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
            INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
            INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
          WHERE
            "Tepl"."PropPlc_cnt".prop_id = 27 AND 
            "PropPlc_cnt1".prop_id = 26 AND 
          "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
          "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
          ORDER BY
            "Tepl"."Places_cnt".plc_id');
        if (pg_num_rows($sql_kol_object) == 1) {
            $id_object = pg_fetch_row($sql_kol_object);
            echo $id_object[3];
            header("location: object.php?id_object=" . $id_object[0] . "");
        }

        break;
    case 1:
        header("location: voda/objects.php");
        break;
    case 2:
        header("location: teplo/objects.php");
        break;
}
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
        <script type="text/javascript" src="../js/bootstrap.js"></script>
        <script type="text/javascript" src="../js/npm.js"></script>
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
                            . '<li><a href="../password_reports.php"><span class="glyphicon glyphicon-user"></span>  <span id="reload_alarm" class="badge pull-right">' . $_SESSION['reports_passord'] . '</span> Востановление пароля</a></li>'
                            . '</ul>'
                            . '<ul class="nav nav-sidebar">'
                            . '<li><a href="../interface_voda.php">МУП ПОВВ Интерфейс</a></li>'
                            . ' <li><a href="../interface_teplo.php">МУП ЧКТС Интерфейс</a></li>'
                            . '</ul>';
                        }
                        ?>



                </div>
                <!--Боковое меню -->

                <!--Контент -->
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">
                        <div id="center_h1">
                        </div>
                    </h1>
                    <div id="all_object" >
                        <div class="row">
                            <div class="col-xs-6 col-md-4 col-lg-4">
                                <a href="simforplaces.php" class="thumbnail">
                                    <h4 class="text-center">Справочник сим карт</h4>
                                    <img data-src="holder.js/100%x180" alt="100%x180" style="height: 240px; width: 240px; display: block;" src="../img/sim.jpg">

                                </a>

                            </div>
                            <div class="col-xs-6 col-md-4 col-lg-4">
                                <a href="LimitForYear.php" class="thumbnail">
                                    <h4 class="text-center">Справочник лимитов потребления</h4>
                                    <img data-src="holder.js/100%x180" alt="100%x180" style="height: 240px; width: 240px; display: block;" src="../img/5.png">

                                </a>

                            </div>
                            <div class="col-xs-6 col-md-4 col-lg-4">
                                <a href="tempforplaces.php" class="thumbnail">
                                    <h4 class="text-center">Справочник температурных показателей</h4>
                                    <img data-src="holder.js/100%x180" alt="100%x180" style="height: 240px; width: 240px; display: block;" src="../img/temp.png">

                                </a>

                            </div>

                            <div class="col-xs-6 col-md-4 col-lg-4">
                                <a href="fias_edit.php" class="thumbnail">
                                    <h4 class="text-center">ФИАС Коды учреждений</h4>
                                   
                                    <img src="../img/ФИАС-300x269.png" alt=""/>
                                </a>

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

        $(document).ready(function () {

            priveleg = <?php echo $_SESSION['privelege']; ?>

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
                //alert('okokokok');
                var id_distinct = 0;
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax_reload_error.php',
                    success: function (html) {
                        $('#reload_alarm').html(html);
                        all_object(id_distinct, priveleg);
                        alert("Обновление отработано");
                    }
                });
                return false;
            })



        });



    </script>

</html>
