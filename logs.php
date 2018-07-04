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
                        <ul  class="nav nav-sidebar">
                            <li><a href="login_user.php">Лог авторизаций</a></li>
                        </ul>


                    </div>
                    <!--Боковое меню -->

                    <!--Контент -->
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1>Журнал изменений</h1>
                            </div>
                        </h1>
                        <div id="all_object" class="table-responsive">
                            <?php
                            $sql_log = pg_query('SELECT 
                            public.logs.id,
                            public.logs.plc_id,
                            public.logs."user",
                            public.logs.prp_id,
                            public.logs.diametr,
                            public.logs.sen_id,
                            public.logs.place,
                            public.logs.nember,
                            public.logs.action,
                            public.logs.date
                          FROM
                            public.logs
                          ORDER BY
                            public.logs.id DESC');

                            while ($result = pg_fetch_row($sql_log)) {
                                $logs[] = array(
                                    'plc_id' => $result[1],
                                    'user' => $result[2],
                                    'prp_id' => $result[3],
                                    'diametr' => $result[4],
                                    'sen_id' => $result[5],
                                    'place' => $result[6],
                                    'number' => $result[7],
                                    'action' => $result[8],
                                    'date' => $result[9]
                                );
                            }

                            $tmp1 = Array();
                            foreach ($logs as &$ma) {
                                $tmp1[] = &$ma["date"];
                            }
                            $tmp2 = Array();

                            foreach ($logs as &$ma) {
                                $tmp2[] = &$ma["plc_id"];
                            }
                            $tmp3 = Array();

                            foreach ($logs as &$ma) {
                                $tmp3[] = &$ma["user"];
                            }
                            array_multisort($tmp1, SORT_DESC, $tmp2, $tmp3, $logs);


                            echo '<table class = "table table-responsive table-bordered" >'
                            . '<thead id = "thead">'
                            . '<tr id = "warning">'
                            . '<td><b>Дата</b></td>'
                            . '<td><b>Учереждение</b></td>'
                            . '<td><b>Пользователь</b></td>'
                            . '<td><b>Параметр</b></td>'
                            . '<td><b>Расходомер</b></td>'
                            . '<td><b>Диаметр</b></td>'
                            . '<td><b>Зав. Номер</b></td>'
                            . '<td><b>Местоположение</b></td>'
                            . '<td><b>Действия</b></td>'
                            . '</tr>'
                            . '</thead>'
                            . '<tbody>';

                            $kol = 0;
                            for ($i = 0; $i < count($logs); $i++) {
                                if ($logs[$i][plc_id] == $logs[$i + 1][plc_id]) {
                                    $kol++;
                                }
                                if ($logs[$i][plc_id] != $logs[$i + 1][plc_id]) {
                                    $kol++;
                                    $key_school = array_search($logs[$i][plc_id], array_column($array_school, 'plc_id'));
                                    //$key_sens = array_search($logs[$i][plc_id], array_column($sens_cnt, 'sens_id'));


                                    echo '<tr class = "for_click warning" id = "' . $logs[$i][plc_id] . '' . date('dm', strtotime($logs[$i][date])) . '">'
                                    . '<td>' . date('d.m.Y', strtotime($logs[$i][date])) . '</td>'
                                    . '<td>' . $array_school[$key_school][name] . '</td>'
                                    . '<td colspan = 7>' . $kol . '</td>'
                                    . '</tr>';

                                    for ($j = $i; $j >= $i - $kol + 1; $j--) {
                                        if ($logs[$i][plc_id] == $logs[$j][plc_id]) {
                                            $sql_param = pg_query('SELECT
                                                "Tepl"."ParametrResourse"."Name",
                                                "Tepl"."Resourse_cnt"."Name"
                                                FROM
                                                "Tepl"."ParamResPlc_cnt"
                                                INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
                                                INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                                                WHERE
                                                "Tepl"."ParamResPlc_cnt".prp_id = ' . $logs[$j][prp_id] . '');
                                            $key_sens = array_search($logs[$j][sen_id], array_column($sens_cnt, 'sens_id'));
                                            if ($key_sens !== false) {
                                                $sens = $sens_cnt[$key_sens][sens_name];
                                            } else {
                                                $sens = "";
                                            }
                                            echo '<tr id = "hide_' . $logs[$j][plc_id] . '' . date('dm', strtotime($logs[$j][date])) . '" style = "display: none;">'
                                            . '<td>' . date('d.m.Y', strtotime($logs[$j][date])) . '</td>'
                                            . '<td>' . $array_school[$key_school][name] . '</td>'
                                            . '<td>' . $logs[$j][user] . '</td>'
                                            . '<td>' . pg_fetch_result($sql_param, 0, 1) . ': ' . pg_fetch_result($sql_param, 0, 0) . '</td>'
                                            . '<td>' . $sens . '</td>'
                                            . '<td>' . $logs[$j][diametr] . '</td>'
                                            . '<td>' . $logs[$j][number] . '</td>'
                                            . '<td>' . $logs[$j][place] . '</td>'
                                            . '<td>' . $logs[$j][action] . '</td>'
                                            . '</tr>';
                                        }
                                    }

                                    $kol = 0;
                                }
                            }



                            echo '</tbody>'
                            . '</table>';
                            ?>

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



        $(document).ready(function () {
            priveleg = <?php echo $_SESSION['privelege']; ?>;
            frame_hieght();

            $('.nav-sidebar li a').each(function () {
                var location = window.location.href;
                var link = this.href;
                if (location == link) {
                    $(this).parent('li').addClass("active");
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
