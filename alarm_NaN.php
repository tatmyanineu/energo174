<?php
include 'db_config.php';
session_start();
$date = date('Y-m-d');
$time = strtotime("-10 day");
$after_day = date("Y-m-d", $time);
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
        <script type="text/javascript" src="js/npm.js"></script>
        <script type="text/javascript" src="js/jquery.datetimepicker.js"></script>
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
                        <?php include './include/menu.php'; ?> 
                        <ul class="nav nav-sidebar">
                            <li><a href="export_alarm_NaN.php"><span class="glyphicon glyphicon-floppy-disk"></span>Сохранить отчет</a></li>
                        </ul>
                        <ul class="nav nav-sidebar">
                            <li><a href="alarm_water.php">Отсутствует вода</a></li>
                            <li><a href="alarm_heat.php">Отсутствует тепло</a></li>
                            <li  class="active"><a href="alarm_NaN.php">NaN значения</a></li>
                            <li><a href="alarm_bigvalue.php">Корректировка ХВС</a></li>
                            <li><a href="alarm_massa.php">Аномалии теплоносителя</a></li>
                            <li><a href="alarm_temper.php">Аномалии тепературы</a></li>
                            <li><a href="alarm_impuls.php">Аномалии данных ХВС</a></li>
                            <li><a href="alarm_night.php">Аварии ХВС(Ночная утечка) </a></li>
                            <li><a href="alarm_dt.php">Заниженная dt </a></li>
                        </ul>
                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="text-center">Аварии: NaN значения</h1>
                        <h3 class="text-center">Проверка за период с <?php echo date("d.m.Y", strtotime($after_day)); ?> по <?php echo date("d.m.Y", strtotime($date)); ?> </h3>
                        <div id="all_object">
                            <?php
                            $z = 0;
                            echo "<table id='main_table' class='table table-bordered'>
                            <thead id='thead'>
                                <tr id='warning'>
                                <td rowspan=2 data-query='0'><b>№</b></td>
                                <td rowspan=2 data-query='1'><b>Учереждение</b></td>
                                <td rowspan=2 data-query='2'><b>Адрес</b></td>
                                <td colspan=2 ><b>Передача данных</b></td>
                                </tr>
                                <tr id='warning'>
                                    <td data-query='3'><b>Дата обновления</b></td>
                                    <td data-query='4'><b>Статус</b></td>
                                </tr>
                            </thead><tbody>";

                            $sql_date_archive = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                                  FROM
                                    "Tepl"."ParamResPlc_cnt"
                                    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                  WHERE
                                    "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\'
                                  ORDER BY
                                    "Tepl"."Arhiv_cnt"."DateValue" DESC');
                            //echo pg_num_rows($sql_date_archive);
                            while ($result_date = pg_fetch_row($sql_date_archive)) {
                                $massiv = '';
                                $pokaz = '';
                                $date_arch = explode(" ", $result_date[0]);
                                $time = strtotime("-1 day");
                                $date_b = date("d.m.Y", strtotime("-1 day", strtotime($date_arch[0])));
                                echo "<tr><td class='dist text-center' colspan='5'><b>" . $date_b . "</b></td></tr>";
                                //echo $result_date[0] . "<br>";
                                //echo "ДЕНЬ ПОOOOOOOOOOOOOOOOOOOOOOOOOOOOOШЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЕЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛЛ!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!<br>";
                                $sql_archive = pg_query('SELECT DISTINCT 
                                    ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."Arhiv_cnt"."DataValue",
                                    "Tepl"."Places_cnt"."Name"
                                  FROM
                                    "Tepl"."ParamResPlc_cnt"
                                    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                  WHERE
                                    "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                    "Tepl"."Arhiv_cnt"."DateValue" = \'' . $result_date[0] . '\' AND 
                                    "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\'AND 
                                    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
                                  ORDER BY
                                    "Tepl"."Places_cnt".plc_id,
                                    "Tepl"."ParamResPlc_cnt"."ParamRes_id"');
                                $q = 1;
                                //echo pg_num_rows($sql_archive) . "<br>";
                                while ($resusl_archive = pg_fetch_row($sql_archive)) {
                                    $arr_id[$z] = $resusl_archive[2];
                                    $aar_name[$z] = $resusl_archive[4];
                                    //$arr_val[$v] = 
                                    //$arr_param[$v] =
                                    //
                                //echo "Z== " . $z . " id = " . $arr_id[$z] . "  name = " . $aar_name[$z] . " res = " . $resusl_archive[1]. "<br>";

                                    if ($z != 0) {
                                        if ($resusl_archive[2] == $arr_id[$z - 1]) {
                                            //$kol_res++;
                                            $arr_param[$v][] = $resusl_archive[1];
                                            $arr_val[$v][] = $resusl_archive[3];
                                        }
                                        if ($resusl_archive[2] != $arr_id[$z - 1]) {
                                            $arr_param[$v + 1][] = $resusl_archive[1];
                                            $arr_val[$v + 1][] = $resusl_archive[3];
                                            $plc = $aar_name[$z - 1];



                                            //print_r($arr_param[$v]);echo " <br>";
                                            //print_r($arr_val[$v]);echo " <br>";

                                            for ($i = 0; $i < count($arr_param[$v]); $i++) {
                                                if ($arr_val[$v][$i] == 'NaN') {
                                                    //echo "id= " . $arr_id[$z - 1] . " " . $plc . "  kol. res = " . $kol_res . " <br>";
                                                    $massiv[] = $arr_id[$z - 1];
                                                }
                                            }

                                            $v++;
                                            //$kol_res = 0;
                                            //$kol_res ++;
                                        }
                                    } else {
                                        if ($resusl_archive[2] == $arr_id[$z]) {
                                            //$kol_res++;
                                            $arr_param[$v][] = $resusl_archive[1];
                                            $arr_val[$v][] = $resusl_archive[3];
                                        }
                                    }
                                    $z++;
                                }

                                $arr_distinct = array_unique($massiv);
                                //print_r($arr_distinct);
                                foreach ($arr_distinct as $key => $value) {
                                    $pokaz[] = $arr_distinct[$key];
                                }
                                //print_r($pokaz);



                                for ($j = 0; $j < count($pokaz); $j++) {

                                    $sql_info = pg_query('SELECT DISTINCT 
                                                    "Tepl"."Places_cnt".plc_id,
                                                    "Tepl"."Places_cnt"."Name",
                                                    "Places_cnt1"."Name",
                                                    "Tepl"."PropPlc_cnt"."ValueProp",
                                                    "PropPlc_cnt1"."ValueProp"
                                                  FROM
                                                    "Tepl"."Places_cnt"
                                                    INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                                    INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                                    INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
                                                    INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                                    INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                  WHERE
                                                    "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                                    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
                                                    "Tepl"."Places_cnt".plc_id = ' . $pokaz[$j] . 'AND 
                                                    "Tepl"."PropPlc_cnt".prop_id = 26 AND 
                                                    "PropPlc_cnt1".prop_id = 27
                                                  ORDER BY
                                                    "Tepl"."Places_cnt".plc_id');
                                    $result = pg_fetch_row($sql_info);
                                    echo "<tr data-href='object.php?id_object=" . $result[0] . "' id ='hover'><td>" . $q++ . "</td><td>" . $result[1] . "</td><td>" . $result[3] . " " . $result[4] . "</td><td>" . $date_b . "</td><td>NaN</td></tr>";
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
            frame_hieght();
            var priveleg = <?php echo $_SESSION['privelege']; ?>;
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
        });
    </script>


</html>
