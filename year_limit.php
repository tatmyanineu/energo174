<?php

include 'db_config.php';
$date = date('Y-m-d');
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
        <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script src="http://maps.api.2gis.ru/2.0/loader.js?pkg=full" data-id="dgLoader"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/highcharts-3d.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
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
                        <?php include './include/menu.php'?>
                        <ul class="nav nav-sidebar">
                            <li class="active"><a id="" href="year_limit.php"> <span class="glyphicon glyphicon-stats"></span>Годовой график лимитов</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="page-header">
                            <div id="center_h1">
                                <div class="btn-group">
                                    <?php
                                    $sql_user_plc = pg_query('SELECT DISTINCT 
                                                                "Tepl"."User_cnt"."SurName",
                                                                "Tepl"."User_cnt"."PatronName",
                                                                "Tepl"."User_cnt".usr_id,
                                                                "Tepl"."Places_cnt"."Name",
                                                                "Tepl"."User_cnt"."Login",
                                                                "Tepl"."User_cnt"."Password",
                                                                "Tepl"."Places_cnt".plc_id
                                                              FROM
                                                                "Tepl"."GroupToUserRelations"
                                                                INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                                                INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                                                INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                                                INNER JOIN "Tepl"."PlaceTyp_cnt" ON ("Tepl"."Places_cnt".typ_id = "Tepl"."PlaceTyp_cnt".typ_id)
                                                              WHERE
                                                                "Tepl"."PlaceTyp_cnt".typ_id = 10 AND 
                                                                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                                                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
                                                              ORDER BY
                                                                "Tepl"."Places_cnt"."Name"');
                                    if (pg_num_rows($sql_user_plc) != 0) {
                                        $j = 0;
                                        $_SESSION['id_distinct'] = '';
                                        while ($row_user_plc = pg_fetch_row($sql_user_plc)) {

                                            $_SESSION['id_distinct'][$j] = $row_user_plc[6];
                                            $array_name_dist[$j] = $row_user_plc[3];
                                            $j++;
                                        }
                                        for ($i = 0; $i < count($_SESSION['id_distinct']); $i++) {
                                            echo '<button type="submit" class="btn btn-default" id="' . $_SESSION['id_distinct'][$i] . '">' . $array_name_dist[$i] . '</button>';
                                        }
                                        //echo '<button type="submit" class="btn btn-default" id = "' . implode(' ', $_SESSION['id_distinct']) . '">Все</button>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-inline text-center">
                                <!--<div class="input-group">
                                 <span class="input-group-addon"><span class="glyphicon glyphicon-th-list"></span>Тип архива </span>
                                <?php
                                $sql_type_archiv = pg_query('SELECT 
                                        "Tepl"."TypeArhiv"."Name",
                                        "Tepl"."TypeArhiv".typ_arh
                                    FROM
                                        "Tepl"."TypeArhiv"');
                                echo "<select class='form-control' id='dropdown-select'>";
                                while ($row_type_archiv = pg_fetch_row($sql_type_archiv)) {
                                    if ($row_type_archiv[1] >= 2 and $row_type_archiv[1] < 4) {
                                        echo "<option class='dropdown-option' value=" . $row_type_archiv[1] . ">" . $row_type_archiv[0] . "</option>";
                                    }
                                }
                                echo "</select>";
                                ?>
                                </div>-->
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
                        <div id="reports_ajax" class="tab-content"></div>

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


        function reports_date(id_distinct, year, priveleg) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_limit_year_view.php',
                data: {id_dist: id_distinct, year: year},
                beforeSend: function () {
                    $('#reports_ajax').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#reports_ajax').html(html);
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



        $(document).ready(function () {

            var year = $('#year_select').val();
            var priveleg = <?php echo $_SESSION['privelege']; ?>;
            var id_distinct = <?php
                                        if (isset($_SESSION['id_distinct'][0])) {
                                            echo $_SESSION['id_distinct'][0];
                                        } else {
                                            echo 0;
                                        }
                                        ?>;
            reports_date(id_distinct, year, priveleg);

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



            $('button[type=submit]').click(function () {
                var month = $('#month_select').val();
                var year = $('#year_select').val();
                var priveleg = <?php echo $_SESSION['privelege']; ?>;
                var id_distr = $(this).attr('id');
                reports_date(id_distr, year, month, priveleg);
            });


            $('#paramtr').click(function () {
                var month = $('#month_select').val();
                var year = $('#year_select').val();
                var priveleg = <?php echo $_SESSION['privelege']; ?>;
                var id_distinct = <?php
                                        if (isset($_SESSION['id_distinct'][0])) {
                                            echo $_SESSION['id_distinct'][0];
                                        } else {
                                            echo 0;
                                        }
                                        ?>;
                reports_date(id_distinct, year, month, priveleg);
            });
        });

    </script>
</html>