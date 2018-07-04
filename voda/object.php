<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../db_config.php';
$date = date('Y-m-d');
session_start();

$date1 = date("Y-m-01");
$date2 = date("Y-m-d");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>

        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="../css/dashboard.css"/>
        <link href="../css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style id="holderjs-style" type="text/css"></style></head>

    <body id="top">

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


            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-md-2 sidebar">
                        <ul class="nav nav-sidebar">
                            <li class="active"><a href="objects.php"><span class="glyphicon glyphicon-home"></span>Обьекты</a></li>
                        </ul>

                    </div>


                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
                        <br>
                        <?php
                        $sql_cootd = pg_query('SELECT 
                                  "Tepl"."Places_cnt"."Name",
                                  "Places_cnt1"."Name",
                                  "Places_cnt2"."Name",
                                  "Tepl"."PropPlc_cnt"."ValueProp",
                                  "PropPlc_cnt1"."ValueProp",
                                  "PropPlc_cnt2"."ValueProp",
                                  "Places_cnt1".plc_id
                                FROM
                                  "Tepl"."Places_cnt" "Places_cnt1"
                                  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                  INNER JOIN "Tepl"."Places_cnt" "Places_cnt2" ON ("Places_cnt1".plc_id = "Places_cnt2".place_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt2".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt2".plc_id = "PropPlc_cnt1".plc_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt2" ON ("Places_cnt2".plc_id = "PropPlc_cnt2".plc_id)
                                WHERE
                                  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                                  "PropPlc_cnt1".prop_id = 26 AND 
                                  "Places_cnt2".plc_id =' . $_GET['id_object'] . '');

                        $result_cootd = pg_fetch_row($sql_cootd);
                        echo '<ol class="breadcrumb">
                            <li><a href="objects.php?id_distinct=' . $result_cootd[6] . '">' . $result_cootd[1] . '</a></li>
                            <li>' . $result_cootd[2] . '</a></li>
                            <li> ул. ' . $result_cootd[3] . '</li>
                            <li> д. ' . $result_cootd[4] . '</li>
                          </ol>';
                        ?>  

                        <div id="center_h1">

                            <div class="form-inline text-center" style="margin-bottom: 10px">
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


                        </div>                       


                        <div class="col-lg-12 col-md-12 col-xs-12">
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 ">
                                        <div id="all_object"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap core JavaScript
            ================================================== -->
            <!-- Placed at the end of the document so the pages load faster -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
            <script src="../js/bootstrap.js" type="text/javascript"></script>
            <script src="../js/npm.js" type="text/javascript"></script>
            <script src="../js/jquery.datetimepicker.js" type="text/javascript"></script>
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
                
                function view_archive(id_object, date1, date2) {
                    $.ajax({
                        type: 'POST',
                        cache: false,
                        url: "ajax/ajax_archive.php",
                        data: "id_object=" + id_object + "&date1=" + date1 + "&date2=" + date2,
                        beforeSend: function () {
                            $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                        }, success: function (html) {
                            $('#all_object').html(html);
                            frame_hieght()
                        }

                    });
                    return false;
                }

                $(document).ready(function () {
                    var id_object = <?php echo $_GET['id_object']; ?>;
                    var date1 = $('#datetimepicker1').val();
                    var date2 = $('#datetimepicker2').val();

                    view_archive(id_object, date1, date2);


                    $('#paramtr').click(function () {
                        var id_object = <?php echo $_GET['id_object']; ?>;
                        var date1 = $('#datetimepicker1').val();
                        var date2 = $('#datetimepicker2').val();
                        view_archive(id_object, date1, date2);
                    });
                });
            </script>

    </body>
</html>