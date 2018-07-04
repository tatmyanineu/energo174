<?php
include '../db_config.php';
session_start();
$date = date('Y-m-d');
$time = strtotime("-10 day");
$after_day = date("Y-m-d", $time);

$sql_group_name = pg_query('SELECT 
  public.group_limit.group_name
FROM
  public.group_limit
WHERE
  public.group_limit.group_id =' . $_GET['id_group']);



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
            'label' => $school_info[$i]['name'] . " " . $school_info[$i]['addres']
        );
    }
}


$tmp2 = Array();

foreach ($table as &$ma) {
    $tmp2[] = &$ma["name"];
}
$tmp3 = Array();

foreach ($table as &$ma) {
    $tmp3[] = &$ma["addres"];
}
array_multisort($tmp2, $tmp3, $table);
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
        <script src="../js/bootstrap-filestyle.min.js" type="text/javascript"></script>
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
                            <li><a href="../limits.php"><span class="glyphicon glyphicon-list-alt"></span>  Лимиты  </a></li>
                            <li><a href="../alarm.php"><span class="glyphicon glyphicon-bell"></span><span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['alarm'] ?></span> Аварии   </a></li>
                            <li><a href="../maps.php"><span class="glyphicon glyphicon-globe"></span> Карта </a></li>
                            <li><a href="../logs.php"><span class="glyphicon glyphicon-book"></span> Логи </a></li>
                            <li><a href="../tickets.php"><span class="glyphicon glyphicon-tags"></span> <span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['count_ticiket']; ?></span> Заявки</a></li>
                            <li><a href="index.php"><span class="glyphicon glyphicon-cog"></span> <span id="reload_alarm" class="badge pull-right"></span> Настройки</a></li>
                            <li><a href="../password_reports.php"><span class="glyphicon glyphicon-user"></span>  <span id="reload_alarm" class="badge pull-right"><?php echo $_SESSION['reports_passord']; ?></span> Востановление пароля</a></li>
                        </ul>

                        <ul class="nav nav-sidebar">
                            <li><a href="LimitForYear.php"><span class=""></span>Распределение лимитов</a></li>
                            <li><a href="LimitForPlaces.php"><span class=""></span>Справочник лимитов</a></li>
                            <li class="active"><a href="groups.php"><span class=""></span>Группы объектов</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="text-center">Редактирование обьектов в группе <?php echo pg_fetch_result($sql_group_name, 0, 0) ?></h1>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-xs-12">
                                <div class="row">
                                    <div class="col-lg-offset-3 col-md-offset-3 col-lg-4 col-md-4 col-xs-6">
                                        <h4>Выберите учереждение</h4>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-xs-6">
                                        <input class="form-control ui-autocomplete-input" id="plc_name" placeholder="Введите учереждение" autocomplete="off" onkeyup="check();">
                                        <button id="clearSearchForm" type="button" class="close" data-dismiss="alert" onclick="reset1();" style="z-index: 3; margin-top: -27px; margin-right: 8px; visibility: hidden" aria-hidden="true">&times;</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-md-offset-3 col-lg-4 col-md-4 col-xs-6"></div>
                                    <div class="col-lg-4 col-md-4 col-xs-6">
                                        <button type="button" id="add_btn" class="btn btn-lg btn-primary">Добавить</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <br>
                        <h4 class="text-center" id="log_text"></h4>
                        <br>
                        <div class="row" id="all_object">

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


        function check() {
            X = document.getElementById('plc_name'); // обращаемся к елементу страницы по ID
            if (X.value != '') { // проверяем поле по регулярному выражению, разрешаем ввод только цифр - \d
                //alert('буквы низя!'); 
                //inp.style.borderColor = 'red'; // краснеем
                document.getElementById('clearSearchForm').style.visibility = 'visible';
                return false;
            } else {
                //inp.style.borderColor = 'green'; // зеленеем
                document.getElementById('clearSearchForm').style.visibility = 'hidden';
                return true;
            }
        }

        function reset1() {
            plc_id = null;
            $('#plc_name').val('');
            $('#limit_teplo').val('');
            $('#limit_voda').val('');
            $('#add_sim').text('Добавить');
            check();
        }

        function refresh_table(group_id) {
            $.ajax({
                type: 'POST',
                chase: false,
                data: 'id_group=' + group_id,
                url: 'ajax/ajax_group_plc_refresh_table.php',
                success: function (html) {
                    $('#all_object').html(html);
                    $('.delete').click(function () {
                        del_plc_group(this.id);
                    });
                }
            });
            return false;
        }

        function add_plc(plc_id, group_id) {
            $.ajax({
                type: 'POST',
                chase: false,
                data: 'id_group=' + group_id + '&plc_id=' + plc_id,
                url: 'ajax/ajax_add_group_plc.php',
                success: function (html) {
                    //$('#all_object').html(html);
                    refresh_table(group_id);
                    reset1();
                }
            });
            return false;
        }

        function del_plc_group(id) {
            $.ajax({
                type: 'POST',
                chase: false,
                data: 'id=' + id,
                url: 'ajax/ajax_del_group_plc.php',
                success: function (html) {
                    refresh_table(group_id);
                }
            });
            return false;
        }

        $(document).ready(function () {
            $(":file").filestyle({buttonBefore: true});
            group_id = <?php echo $_GET['id_group']; ?>;
            plc_id = null;
            plc = <?php echo json_encode($table, JSON_UNESCAPED_UNICODE); ?>;
            $("#plc_name").autocomplete({
                source: plc,
                minLength: 2,
                select: function (event, ui) {
                    plc_id = ui.item.plc_id;
                    $('#add_sim').text('Добавить');
                }
            });

            $('#add_btn').click(function () {
                add_plc(plc_id, group_id);
            });

            frame_hieght();
            refresh_table(group_id);
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
