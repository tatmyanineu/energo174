<?php
include '../db_config.php';
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
        'name' => $result_school_info[0],
        'addres' => '' . $result_school_info[1] . ' ' . $result_school_info[2] . ''
    );
}


for ($i = 0; $i < count($school_info); $i++) {
    $key = array_search($school_info[$i]['plc_id'], array_column($_SESSION['main_form'], 'plc_id'));
    if ($key !== false) {
        $table[] = array(
            'plc_id' => $school_info[$i]['plc_id'],
            'name' => $school_info[$i]['name'],
            'addres' => $school_info[$i]['addres'],
            'date_t' => $_SESSION['main_form'][$key]['date_warm'],
            'error_t' => $_SESSION['main_form'][$key]['error_warm'],
            'date_w' => $_SESSION['main_form'][$key]['date_water'],
            'error_w' => $_SESSION['main_form'][$key]['error_water'],
            'label' => $school_info[$i]['name'] . " " . $school_info[$i]['addres']
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

        <link rel="stylesheet" type="text/css" href="../css/style.css"/>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="../css/dashboard.css"/>
        <script src="../js/jquery-2.2.1.js" type="text/javascript"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/bootstrap.js"></script>
        <script type="text/javascript" src="../js/npm.js"></script>
        <script type="text/javascript" src="../js/jquery.livequery.js"></script>
        <script src="../js/jquery.datetimepicker.js" type="text/javascript"></script>
        <link href="../css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
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
            <!--Верхний бар -->

            <!--Боковое меню -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-md-2 sidebar">
                        <ul class="nav nav-sidebar">
                            <li><a href="../objects.php"><span class="glyphicon glyphicon-home"></span> Главная  </a></li>
                            <li><a href="../reports.php"><span class="glyphicon glyphicon-list-alt"></span>  Лимиты  </a></li>
                            <li><a href="../alarm.php"><span class="glyphicon glyphicon-bell"></span><span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['alarm'] ?></span> Аварии   </a></li>
                            <li><a href="../maps.php"><span class="glyphicon glyphicon-globe"></span> Карта </a></li>
                            <li><a href="../logs.php"><span class="glyphicon glyphicon-book"></span> Логи </a></li>
                            <li><a href="../tickets.php"><span class="glyphicon glyphicon-tags"></span> <span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['count_ticiket'] ?> </span> Заявки</a></li>
                            <li><a href="index.php"><span class="glyphicon glyphicon-cog"></span> <span id="reload_alarm" class="badge pull-right"></span> Настройки</a></li>
                            <li><a href="../password_reports.php"><span class="glyphicon glyphicon-user"></span>  <span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['reports_passord']; ?></span> Востановление пароля</a></li>
                        </ul>

                        <ul class="nav nav-sidebar">
                            <li><a href="../export_alarm.php"><span class="glyphicon glyphicon-floppy-disk"></span>Сохранить отчет</a></li>
                            <li  class="active"><a href="settings_alarm.php"><span class="glyphicon glyphicon-eye-close"></span>Список исключений</a></li>
                        </ul>

                        <ul class="nav nav-sidebar">
                            <li><a href="../alarm_water.php">Отсутствует вода</a></li>
                            <li><a href="../alarm_heat.php">Отсутствует тепло</a></li>
                            <li><a href="../alarm_NaN.php">NaN значения</a></li>
                            <li><a href="../alarm_bigvalue.php">Корректировка ХВС</a></li>
                            <li><a href="../alarm_massa.php">Аномалии подачи/обратки</a></li>
                            <li><a href="../alarm_impuls.php">Аномалии данных ХВС</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="text-center">Настройка списка аварий</h1>

                        <div id="data_korr" style="margin-top: 50px;">
                            <div class="col-lg-4 col-md-4 col-xs-12"><h4><b></b></h4></div>
                            <div class="col-lg-7 col-md-7 col-xs-12">
                                <div class="form-group">
                                    <label for="inputVvod" class="col-sm-4 control-label">Учереждение</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" id="vvod" placeholder="Введите учереждение"> 
                                    </div>
                                </div>
                                <div class="checkbox"><label></label></div>
                                <div class="form-group">
                                    <label for="inputdatetimepicker1" class="col-sm-4 control-label">Дата</label>
                                    <div class="col-sm-8 col-lg-8 col-md-8">
                                        <input type="text" class="form-control" id="datetimepicker1"  style="width: 100%;" value="<?php echo date('d.m.Y'); ?>" placeholder="Дата">
                                    </div>
                                </div>
                                <div class="checkbox"><label></label></div>
                                <div class="form-group">
                                    <label for="inputnp" class="col-sm-4 control-label">Описание</label>
                                    <div class="col-sm-8 col-lg-8 col-md-8">
                                        <h5 id="forcheck">
                                            <input type="checkbox" class="work" value="Интерфейс тепло">Интерфейс тепло<br>
                                            <input type="checkbox" class="work" id="poverka" value="Импульс воды">Импульс воды <br>
                                            <input type="checkbox" class="work" value="Поверка вода">Поверка вода<br>
                                            <input type="checkbox" class="work" value="Поверка тепло">Поверка тепло<br>
                                            <input type="checkbox" class="work" value="Наводка">Наводка<br>
                                            <input type="checkbox" class="work" value="Технические работы">Технические работы <br>
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div id="data_inputVoda" class="row hidden">
                                <div class="col-lg-4 col-md-4 col-xs-12"><h4><b></b></h4></div>
                                <div class="col-lg-7 col-md-7 col-xs-12">
                                    <div class="form-group">
                                        <label for="inputVvod" class="col-sm-4 control-label">Ввод</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="VodaInput" placeholder="Выберите ввод"> </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>

                            <div class="row">
                                <div class="text-center col-lg-12 col-md-12 col-xs-12">
                                    <button type="button" id="add_alarm" class="btn btn-lg btn-primary">Добавить в исключения</button>
                                </div>
                            </div>
                            <div id="result_add" class="text-center"></div>
                        </div>
                        <div id="all_object" style="margin-top: 40px;">
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

        $('#datetimepicker1').datetimepicker({
            format: 'd.m.Y',
            lang: 'ru',
        });

        function add_select(plc) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: '../ajax/ajax_tickets_vvod.php',
                data: 'plc_id=' + plc,
                success: function (data) {
                    //$('#edit_ticket').html('Сохранено');
                    console.log(data);
                    for (var i = 0; i < data.length; i++) {
                        $('#VodaInput').append($('<option value="' + data[i].prp_id + '">' + data[i].name + '</option>'));
                    }
                    //$('#reload_alarm').html(html);
                    //all_object(id_distinct, priveleg);
                }
            });
            return false;
        }


        function refrash_table() {
            $.ajax({
                type: 'POST',
                chase: false,
                url: '../ajax/ajax_refresh_table.php',
                success: function (html) {
                    $('#all_object').html(html);
                    $('.delete').click(function () {
                        delete_alarm(this.id);
                        refrash_table();

                    });
                    $('.object').click(function () {
                        //alert(this.id);
                        window.open('../object.php?id_object=' + this.id);
                        return false;
                    });
                    var hash = document.location.hash; //hash == '#30';
                    //hash = hash.replace('#tr', '', hash);//hash=30;
                    hash = hash.replace('#', '', hash);//hash=30;

                    window.location.hash = "#tr" + hash; // можно и просто window.location, без hash
                    console.log(hash);
                }
            });
            return false;
        }


        function delete_alarm(id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: '../ajax/ajax_del_alarm.php',
                data: 'id=' + id,
                success: function (html) {
                    $('#result_add').html(html).fadeIn(3000).fadeOut(1000);
                }
            });
            return false;
        }

        function add_table(plc_id, date, text, prp) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: '../ajax/ajax_add_alarm.php',
                data: 'plc_id=' + plc_id + '&date=' + date + '&text=' + text + '&prp_id=' + prp,
                success: function (html) {
                    $('#result_add').html(html).fadeIn(3000).fadeOut(1000);
                    // $('#result_add').html('');
                    refrash_table();
                }
            });
            return false;
        }

        $(document).ready(function () {
            frame_hieght();
            refrash_table();
            plc_id = null;
            var plc = <?php echo json_encode($table, JSON_UNESCAPED_UNICODE); ?>;
            $("#vvod").autocomplete({
                source: plc,
                minLength: 2,
                select: function (event, ui) {
                    plc_id = ui.item.plc_id;

                }
            });

            $('#poverka').click(function () {
                if ($('#poverka').prop('checked')) {
                    $('#data_inputVoda').removeClass('hidden');
                    $('#VodaInput').empty();
                    add_select(plc_id);
                } else {
                    $('#data_inputVoda').addClass('hidden');
                    $('#VodaInput').empty();
                }

            });


            $('#add_alarm').click(function () {
                var arr = $('input.work:checked');
                var str = "";
                arr.each(function (index, el) {
                    if (str == "") {
                        str = str + el.value;
                    } else {
                        str = str + "; " + el.value;
                    }
                });

                $('input[type=checkbox]').each(function ()
                {
                    this.checked = false;
                });


                $('#vvod').val('');
                var date = $('#datetimepicker1').val();
                var prp = $('#VodaInput').val();
                add_table(plc_id, date, str, prp);
                console.log("plc_id=" + plc_id + " " + str + " " + $('#datetimepicker1').val() + ' ' + prp);
                $('#VodaInput').empty();
                $('#data_inputVoda').addClass('hidden');
            });



            $('#reload_alarm').click(function () {
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: '../ajax_reload_error.php',
                    success: function (html) {
                        $('#reload_alarm').html(html);
                    }
                });
                return false;
            });


        });
    </script>


</html>
