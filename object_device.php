<?php
include 'db_config.php';
$date = date('Y-m-d');
session_start();
$_SESSION['id_object'] = $_GET['id_object'];
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
        <script type="text/javascript" src="js/jquery.datetimepicker.js"></script>
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
                    <?php include './include/menu.php';?>
                </div>
                <!--Боковое меню -->

                <!--Контент -->
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

                    <?php
                    $sql_cootd = pg_query('SELECT 
                                  "Tepl"."Places_cnt"."Name",
                                  "Places_cnt1"."Name",
                                  "Places_cnt2"."Name",
                                  "Tepl"."PropPlc_cnt"."ValueProp",
                                  "PropPlc_cnt1"."ValueProp",
                                  "PropPlc_cnt2"."ValueProp"
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
                                  "Places_cnt2".plc_id =' . $_SESSION['id_object'] . '');

                    $result_cootd = pg_fetch_row($sql_cootd);
                    echo '<ol class="breadcrumb">
                        <li>' . $result_cootd[1] . '</a></li>
                        <li>' . $result_cootd[2] . '</a></li>
                        <li> ул. ' . $result_cootd[3] . '</li>
                        <li> д. ' . $result_cootd[4] . '</li>
                      </ol>'
                    ?>  
                    <h4 class=""></h4>
                    <div class="panel panel-primary" style=" border-color: #428bca;">
                        <div id="panel_head" class="panel-heading" style=" background-color: #428bca;  border-color: #428bca;">
                            <h3 class="panel-title text-left" ><span id="maps_link">Карта</span></h3>

                        </div>
                        <div class="panel-body" style="display: none;">
                            <div id="map" style="width:100%; height:240px; margin-bottom: 20px;"></div>
                        </div>
                    </div>
                    <ul class="nav nav-tabs nav-justified">
                        <li><a href="object.php?id_object=<?php echo $_GET['id_object'] ?>">Таблица потребления ресурсов</a></li>
                        <li><a href="object_charts.php?id_object=<?php echo $_GET['id_object'] ?>" >Графики потребления ресурсов</a></li>
                        <li><a href="quality_warm.php?id_object=<?php echo $_GET['id_object'] ?>" >Графики качества тепла</a></li>
                        <li  class="active"><a href="#" >Информация о приборах</a></li>
                    </ul>
                    <label class="checkbox"></label><label class="checkbox"></label>

                    <div id="center_h1"></div>
                    <div id="view_archive" class="table-responsive text-center"></div>
                </div>
            </div>

            <!--Баковое меню -->

    </body>
    <?php
    $sql_cootd = pg_query('SELECT 
                                  "Tepl"."Places_cnt"."Name",
                                  "Places_cnt1"."Name",
                                  "Places_cnt2"."Name",
                                  "Tepl"."PropPlc_cnt"."ValueProp",
                                  "PropPlc_cnt1"."ValueProp",
                                  "PropPlc_cnt2"."ValueProp"
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
                                  "PropPlc_cnt2".prop_id = 41 AND 
                                  "Places_cnt2".plc_id =' . $_GET['id_object'] . '');
    $result_cootd = pg_fetch_row($sql_cootd);
    ?>
    <script type="text/javascript">
        $('#datetimepicker1').datetimepicker({
            format: 'd.m.Y',
            lang: 'ru'
        });
        $('#datetimepicker2').datetimepicker({
            format: 'd.m.Y',
            lang: 'ru'
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


        function device(id_object) {
            $.ajax({
                type: 'POST',
                cache: false,
                url: 'ajax_archive_device.php',
                data: 'id_object=' + id_object,
                beforeSend: function () {
                    $('#view_archive').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#view_archive').html(html);
                    $('#edit_teplo').click(function () {
                        window.location = "edit_teplo.php?id_object=" + id_object;
                    });
                    $('#edit_voda').click(function () {
                        window.location = "edit_voda.php?id_object=" + id_object;
                    });
                    frame_hieght();
                }
            });
            return false;
        }

        $(document).ready(function () {
            var priveleg = <?php echo $_SESSION['privelege']; ?>;
            $('.panel-heading').click(function () {
                if ($('.panel-body').css('display') == 'none') {
                    $('.panel-body').show();
                    DG.then(function () {
                        map = DG.map('map', {
                            'center': [<?php echo str_replace('. ', ', ', $result_cootd[5]) ?>],
                            'zoom': 16
                        });
                        DG.popup([<?php echo str_replace('. ', ', ', $result_cootd[5]) ?>])
                                .setLatLng([<?php echo str_replace('. ', ', ', $result_cootd[5]) ?>])
                                .setContent(<?php echo "'<p> " . $result_cootd[2] . " <br> ул. " . $result_cootd[3] . "</br> дом " . $result_cootd[4] . "</p>'" ?>)
                                .openOn(map);
                    });
                } else {
                    $('.panel-body').hide();
                }

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

            var id_object = <?php echo $_SESSION['id_object']; ?>;
            device(id_object);


        });
    </script>

</html>
