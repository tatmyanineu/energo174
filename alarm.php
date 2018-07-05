<?php

include 'db_config.php';
session_start();
$date = date('Y-m-d');
$time = strtotime("-10 day");
$after_day = date("Y-m-d", $time);


$sql_school_info = pg_query('SELECT 
                                "Places_cnt1"."Name",
                                "Tepl"."PropPlc_cnt"."ValueProp",
                                "PropPlc_cnt1"."ValueProp",
                                "Places_cnt1".plc_id,
                                "Tepl"."Places_cnt"."Name",
                                "Tepl"."Places_cnt".plc_id
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

while ($result_school_info = pg_fetch_row($sql_school_info)) {
    $school_info[] = array(
        'plc_id' => $result_school_info[3],
        'distinct' => $result_school_info[4],
        'id_dist' => $result_school_info[5],
        'name' => $result_school_info[0],
        'addres' => '' . $result_school_info[1] . ' ' . $result_school_info[2] . ''
    );
}


for ($i = 0; $i < count($school_info); $i++) {
    $key = array_search($school_info[$i]['plc_id'], array_column($_SESSION['main_form'], 'plc_id'));
    if ($key !== false) {
        $table[] = array(
            'plc_id' => $school_info[$i]['plc_id'],
            'distinct' => $school_info[$i]['distinct'],
            'id_dist' => $school_info[$i]['id_dist'],
            'name' => $school_info[$i]['name'],
            'addres' => $school_info[$i]['addres'],
            'date_t' => $_SESSION['main_form'][$key]['date_warm'],
            'error_t' => $_SESSION['main_form'][$key]['error_warm'],
            'date_w' => $_SESSION['main_form'][$key]['date_water'],
            'error_w' => $_SESSION['main_form'][$key]['error_water'],
        );
    }
}

$tmp1 = Array();
foreach ($table as &$ma) {
    $tmp1[] = &$ma["distinct"];
}
$tmp2 = Array();

foreach ($table as &$ma) {
    $tmp2[] = &$ma["name"];
}
$tmp3 = Array();

foreach ($table as &$ma) {
    $tmp3[] = &$ma["addres"];
}
array_multisort($tmp1, $tmp2, $tmp3, $table);
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

            <!--Боковое меню -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-md-2 sidebar">
                        <?php include './include/menu.php'; ?> 

                        <ul class="nav nav-sidebar">
                            <li><a href="export_alarm.php"><span class="glyphicon glyphicon-floppy-disk"></span>Сохранить отчет</a></li>
                            <li><a href="settings/settings_alarm.php"><span class="glyphicon glyphicon-eye-close"></span>Список исключений</a></li>
                        </ul>

                        <ul class="nav nav-sidebar">
                            <li><a href="alarm_water.php">Отсутствует вода</a></li>
                            <li><a href="alarm_heat.php">Отсутствует тепло</a></li>
                            <li><a href="alarm_NaN.php">NaN значения</a></li>
                            <li><a href="alarm_bigvalue.php">Корректировка ХВС</a></li>
                            <li><a href="alarm_massa.php">Аномалии теплоносителя</a></li>
                            <li><a href="alarm_temper.php">Аномалии тепературы</a></li>
                            <li><a href="alarm_impuls.php">Аномалии данных ХВС</a></li>
                            <li><a href="alarm_night.php">Аварии ХВС(Ночная утечка) </a></li>
                            <li><a href="alarm_dt.php">Заниженная dt </a></li>
                        </ul>
                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="text-center">Аварии: Нет связи с обьектом</h1>
                        <div id="all_object">
                            <?php
                            echo "<table id='main_table' class='table table-bordered'>
                            <thead id='thead'>
                                <tr id='warning'>
                                <td rowspan=2 data-query='0'><b>№</b></td>
                                <td rowspan=2 data-query='1'><b>Учереждение</b></td>
                                <td rowspan=2 data-query='2'><b>Адрес</b></td>
                                <td colspan=2 ><b>Дата последней передачи</b></td>
                                </tr>
                                <tr id='warning'>
                                    <td data-query='3'><b>ТЕПЛО</b></td>
                                    <td data-query='4'><b>ХВС</b></td>
                                </tr>
                            </thead><tbody>";

                            unset($_SESSION['arr_plc_id']);
                            unset($_SESSION['arr_id']);
                            unset($_SESSION['arr_name']);
                            unset($_SESSION['arr_addr']);
                            unset($_SESSION['arr_date_t']);
                            unset($_SESSION['arr_error_t']);
                            unset($_SESSION['arr_date_w']);
                            unset($_SESSION['arr_error_w']);


                            $m = 1;

                            for ($i = 0; $i < count($table); $i++) {
                                if ($table[$i]['error_t'] > 0 and $table[$i]['error_t'] < 4 or $table[$i]['error_w'] > 0 and $table[$i]['error_w'] < 4) {
                                    echo "<tr data-href='object.php?id_object=" . $table[$i]['plc_id'] . "' id='hover' >"
                                    . "<td>" . $m . "</td>"
                                    . "<td>" . $table[$i]['name'] . "</td>"
                                    . "<td>" . $table[$i]['addres'] . "</td>";
                                    if ($table[$i]['error_t'] == 1) {
                                        echo "<td class='warning'>" . date("d.m.Y", strtotime($table[$i]['date_t'])) . "</td>";
                                        $_SESSION['arr_date_t'][] = date("d.m.Y", strtotime($table[$i]['date_t']));
                                    } elseif ($table[$i]['error_t'] == 3) {
                                        echo "<td class='danger'> Нет данных </td>";
                                        $_SESSION['arr_date_t'][] = "Нет данных";
                                    } elseif ($table[$i]['error_t'] == 4) {
                                        echo "<td class='text-center'> - </td>";
                                        $_SESSION['arr_date_t'][] = "-";
                                    } elseif ($table[$i]['error_t'] == 0) {
                                        echo "<td>" . date("d.m.Y", strtotime($table[$i]['date_t'])) . "</td>";
                                        $_SESSION['arr_date_t'][] = date("d.m.Y", strtotime($table[$i]['date_t']));
                                    }

                                    if ($table[$i]['error_w'] == 1) {
                                        echo "<td class='warning'>" . date("d.m.Y", strtotime($table[$i]['date_w'])) . "</td>";
                                        $_SESSION['arr_date_w'][] = date("d.m.Y", strtotime($table[$i]['date_w']));
                                    } elseif ($table[$i]['error_w'] == 3) {
                                        echo "<td class='danger'> Нет данных </td>";
                                        $_SESSION['arr_date_w'][] = "Нет данных";
                                    } elseif ($table[$i]['error_w'] == 4) {
                                        echo "<td class='text-center'> - </td>";
                                        $_SESSION['arr_date_w'][] = "-";
                                    } elseif ($table[$i]['error_w'] == 0) {
                                        echo "<td>" . date("d.m.Y", strtotime($table[$i]['date_w'])) . "</td>";
                                        $_SESSION['arr_date_w'][] = date("d.m.Y", strtotime($table[$i]['date_w']));
                                    }

                                    $_SESSION['arr_plc_id'][] = $table[$i]['plc_id'];
                                    $_SESSION['arr_id'][] = $m;
                                    $_SESSION['arr_name'][] = $table[$i]['name'];
                                    $_SESSION['arr_addr'][] = $table[$i]['addres'];
                                    $_SESSION['arr_error_t'][] = $table[$i]['error_t'];

                                    $_SESSION['arr_error_w'][] = $table[$i]['error_w'];

                                    $m++;
                                    echo "</tr>";
                                }
                            }

                            echo "</tbody></table>";
                            ?>
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

            $('.nav-sidebar li a').each(function () {
                var location = window.location.href;
                var link = this.href;
                if (location == link) {
                    $(this).parent('li').addClass("active");
                }

            });

            frame_hieght();
            var priveleg = <?php echo $_SESSION['privelege']; ?>;
            $('thead td[data-query]').livequery("click", function (event) {
                var sort = $(this).attr('data-query');
                //alert('ckack');
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax_alarm_sort.php',
                    data: 'id_sort=' + sort + '&param_sort=0',
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

            $('tbody tr[data-href]').addClass('clickable').click(function () {

                if (priveleg > 0) {
                    window.open($(this).attr('data-href'));
                } else {
                    window.location = $(this).attr('data-href');
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
