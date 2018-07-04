<?php
include '../db_config.php';
session_start();
$date = date('Y-m-d');
$time = strtotime("-10 day");
$after_day = date("Y-m-d", $time);

$sql_school_info = pg_query('SELECT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  public."SimPlace_cnt".sim_number,
  "Tepl"."Places_cnt".plc_id
FROM
  "Tepl"."Places_cnt"
  INNER JOIN public."SimPlace_cnt" ON ("Tepl"."Places_cnt".plc_id = public."SimPlace_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26
ORDER BY
  "Tepl"."Places_cnt"."Name"');

while ($result_school_info = pg_fetch_row($sql_school_info)) {
    $table[] = array(
        'plc_id' => $result_school_info[4],
        'name' => $result_school_info[0],
        'number' => $result_school_info[3],
        'label' => $result_school_info[0] . ' ' . $result_school_info[1] . ' ' . $result_school_info[2] . ' ' . $result_school_info[3]
    );
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
                            <li><a href="simforplaces.php"><span class=""></span>Справочник Sim-карт</a></li>
                            <li class="active"><a href="simnoterror.php"><span class=""></span>Список исключений Sim</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="text-center">Исписок исключений по Sim-картам</h1>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-xs-12">
                                <div class="row">
                                    <div class="col-lg-offset-1 col-md-offset-1 col-lg-4 col-md-4 col-xs-6">
                                        <h4>Выберите учереждение</h4>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xs-6">
                                        <input class="form-control ui-autocomplete-input" id="sim_numb" placeholder="Введите учереждение" autocomplete="off">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-1 col-md-offset-1 col-lg-4 col-md-4 col-xs-6">
                                        <h4>Загрузить массив Sim карт</h4>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xs-6">
                                        <input type="file" id="for_file" class="filestyle" data-buttonBefore="true">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-md-offset-3 col-lg-4 col-md-4 col-xs-6"></div>
                                    <div class="col-lg-4 col-md-4 col-xs-6">
                                        <button type="button" id="add_sim" class="btn btn-lg btn-primary">Добавить</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <br>
                        <div class="row" id="log_text">
                        </div>
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


        function refresh_table() {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_sne_refresh_table.php',
                success: function (html) {
                    $('#all_object').html(html);
                    $('.delete').click(function () {
                        //delete_alarm(this.id);
                        var id = this.id;
                        var plc_id = $(this).attr('data-plc_id');
                        delete_sne(id, plc_id);

                        //refrash_table();

                    });
                }
            });
            return false;
        }

        function delete_sne(id, plc_id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_del_sne.php',
                data: 'id=' + id + '&plc_id=' + plc_id,
                success: function (html) {
                    //$('#log_text').html(html);
                    refresh_table();
                }
            });
            return false;
        }

        function add_sim_plc(plc_id, number) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_add_sne.php',
                data: 'plc_id=' + plc_id + '&number=' + number,
                success: function (html) {
                    refresh_table();
                    $('#add_sim').text('Добавлено');
                    $('#log_text').html(html);
                    setTimeout("$('#log_text').html(''); $('#add_sim').text('Добавить');", 2000);
                    $('#plc_name').val('');
                    $('#sim_numb').val('');
                }
            });
            return false;
        }

        $(document).ready(function () {
            $(":file").filestyle({buttonBefore: true});
            plc_id = null;
            numb = null;
            array = null;
            plc = <?php echo json_encode($table, JSON_UNESCAPED_UNICODE); ?>;
            $("#sim_numb").autocomplete({
                source: plc,
                minLength: 2,
                select: function (event, ui) {
                    plc_id = ui.item.plc_id;
                    numb = ui.item.number;
                    $('#add_sim').text('Добавить');
                }
            });


            $("#for_file").change(function (e) {
                var ext = $("input#for_file").val().split(".").pop().toLowerCase();

                if ($.inArray(ext, ["csv"]) == -1) {
                    alert('Upload CSV');
                    return false;
                }
                array = [];
                if (e.target.files != undefined) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var csvval = e.target.result.split("\n");

                        for (var j = 0; j < csvval.length; j++) {
                            var csvvalue = csvval[j].split(";");
                            var inputrad = "";
                            if (csvvalue[0] != "") {
                                array.push(csvvalue[0]);
                            }
                        }
                        console.log(array);

                    };
                    reader.readAsText(e.target.files.item(0));

                }

                return false;

            });



            $('#add_sim').click(function () {
                if ($('#for_file').val() == "") {
                    var number = $('#sim_numb').val();
                    if (number != "") {
                        add_sim_plc(plc_id, numb);
                    } else {
                        alert("Одно из полей не заполенно");
                    }
                } else {
                    //alert("Функция находится в разработке");

                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: 'ajax/ajax_input_sne.php',
                        data: {arr: array},
                        success: function (html) {
                            $('#log_text').html(html);
                            refresh_table();
                            setTimeout("$('#log_text').html('');", 6000);
                        }
                    });
                    return false;

                    array = null;
                }

            });

            frame_hieght();
            refresh_table();
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
        }
        );
    </script>


</html>
