<?php
include 'db_config.php';
$date1 = date('Y-m-d', strtotime('-10 day'));
$date2 = date('Y-m-d');
session_start();
$id_object = $_GET['id_object'];
$file = basename($_SERVER['PHP_SELF'], ".php");

$sql_search_object = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt".plc_id
FROM
  "Tepl"."User_cnt"
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
WHERE
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."Places_cnt".plc_id = ' . $id_object . '');

if (pg_num_rows($sql_search_object) == 0) {
    header("location: 404.php");
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
        <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script src="http://maps.api.2gis.ru/2.0/loader.js?pkg=full" data-id="dgLoader"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script type="text/javascript" src="js/jquery.datetimepicker.js"></script>
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
        </div>
        <!--Верхний бар -->


        <div class="container-fluid">
            <div class="row">
                <!--Боковое меню -->
                <div class="col-sm-3 col-md-2 sidebar">
                    <ul class="nav nav-sidebar">
                        <li class="active"><a href="objects.php"><span class="glyphicon glyphicon-home"></span> Главная  </a></li>
                        <li><a href="limits.php"><span class="glyphicon glyphicon-list-alt"></span>  Лимиты   </a></li>
                        <?php
                        if ($_SESSION['privelege'] > 0) {
                            echo '<li><a href="alarm.php" data-toggle="tooltip" data-placement="right" title="Процент неисправных обьектов: ' . number_format($_SESSION['proc'], 2) . '%"><span class="glyphicon glyphicon-bell" ></span><span id="reload_alarm" class="badge pull-right">' . $_SESSION['alarm'] . '</span> Аварии   </a></li>'
                            . '<li><a href="maps.php"><span class="glyphicon glyphicon-globe"></span> Карта </a></li>'
                            . '<li><a href="logs.php"><span class="glyphicon glyphicon-book"></span> Логи </a></li>'
                            . '<li><a href="tickets.php"><span class="glyphicon glyphicon-tags"></span> <span id="reload_alarm" class="badge pull-right">' . $_SESSION['count_ticiket'] . '</span> Заявки</a></li>'
                            . '<li><a href="settings/index.php"><span class="glyphicon glyphicon-cog"></span> Настройки</a></li>'
                            . '<li><a href="password_reports.php"><span class="glyphicon glyphicon-user"></span>  <span id="reload_alarm" class="badge pull-right">' . $_SESSION['reports_passord'] . '</span> Востановление пароля</a></li>'
                            ;
                        }
                        ?>
                    </ul>
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
                        <li class="active"><a href="#" >Графики качества тепла</a></li>
                        <li><a href="object_device.php?id_object=<?php echo $_GET['id_object'] ?>" >Информация о приборах</a></li>
                    </ul>
                    <label class="checkbox"></label><label class="checkbox"></label>

                    <div class="form-inline text-center">
                        <div class="input-group">
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

                    function view_charts(plc_id, date1, date2) {
                        $.ajax({
                            type: 'POST',
                            chase: false,
                            url: 'ajax/ajax_view_qiality_charts.php',
                            data: {plc_id: plc_id, date1: date1, date2: date2},
                            beforeSend: function () {
                                $('#view_archive').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                            },
                            success: function (html) {
                                $('#view_archive').html(html);
                                frame_hieght();
                            }
                        });
                        return false;
                    }



                    $(document).ready(function () {
                        var date1 = $('#datetimepicker1').val();
                        var date2 = $('#datetimepicker2').val();
                        var priveleg = <?php echo $_SESSION['privelege']; ?>;
                        var id_object = <?php echo $id_object; ?>;
                        view_charts(id_object, date1, date2);

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

                        $('#edit_comment').click(function () {
                            var id_object = <?php echo $_GET['id_object']; ?>;
                            var file = <?php echo "'" . $file . "'"; ?>;
                            var id_tick = $('#myModal2 > .modal-dialog > .modal-content > .modal-footer').attr('id');
                            $.ajax({
                                type: 'POST',
                                cache: false,
                                url: 'ajax/ajax_edit_ticket.php',
                                data: 'comment=' + $('#comment2').val() + '&status=' + $('#stat_comment2').val() + '&id_ticket=' + id_tick + '&file=' + file,
                                success: function (html) {
                                    $('#add_comment').html(html);
                                    //$('#add_comment').attr('disabled', true);
                                    //$('#ticket').html('<span class="glyphicon glyphicon-edit"></span> Заявка добавлена');
                                }
                            });
                        });


                        $('#paramtr').click(function () {
                            var date1 = $('#datetimepicker1').val();
                            var date2 = $('#datetimepicker2').val();
                            var id_object = <?php echo $id_object; ?>;
                            view_charts(id_object, date1, date2);
                        });

                    });
                </script>

                </html>
