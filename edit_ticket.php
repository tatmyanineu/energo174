<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_config.php';
session_start();
if (isset($_SESSION['login'])) {
    
} else {
    header('location: index.php');
}



$sql_data_ticket = pg_query('
        SELECT 
          status
        FROM 
          public.ticket
        WHERE
          id = ' . $_GET['id_ticket'] . '');
$stat = pg_fetch_result($sql_data_ticket, 0, 0);
if ($stat == 4) {
    header('location: tickets.php');
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
        <link href="css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script src="js/jquery.datetimepicker.js" type="text/javascript"></script>
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
                        <ul class="nav nav-sidebar">
                            <li><a href="tickets.php"><span class="glyphicon glyphicon-chevron-left"></span>Назад</a></li>
                        </ul>


                    </div>
                    <!--Боковое меню -->

                    <!--Контент -->
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1> <span class="glyphicon glyphicon-pencil"></span>Редактирование заявки на обслуживание</h1>
                            </div>
                        </h1>
                        <div class="row" >
                            <div class="col-lg-12 col-md-12 col-xs-12">
                                <?php
                                $sql_ticket = pg_query('SELECT DISTINCT 
                                "Tepl"."Places_cnt"."Name",
                                public.ticket.date_ticket,
                                public.ticket.text_ticket,
                                public.ticket.status,
                                public.ticket.plc_id
                              FROM
                                "Tepl"."Places_cnt"
                                INNER JOIN public.ticket ON ("Tepl"."Places_cnt".plc_id = public.ticket.plc_id)
                              WHERE
                                public.ticket.id = ' . $_GET['id_ticket'] . '');
                                while ($result = pg_fetch_row($sql_ticket)) {
                                    $ticket = array(
                                        'name' => $result[0],
                                        'date' => $result[1],
                                        'text' => $result[2],
                                        'plc_id' => $result[4]
                                    );
                                }
                                ?>
                                <div class="row">
                                    <div class=" col-lg-5 col-md-5 col-xs-12"><h4><b>Учереждение</b></h4></div>
                                    <div class=" col-lg-5 col-md-5 col-xs-12"><h4><?php echo pg_fetch_result($sql_ticket, 0, 0); ?></h4></div>
                                </div>

                                <div class="row">
                                    <div class=" col-lg-5 col-md-5 col-xs-12"><h4><b>Дата заявки</b></h4></div>
                                    <div class=" col-lg-5 col-md-5 col-xs-12"><h4><?php echo date('d.m.Y', strtotime(pg_fetch_result($sql_ticket, 0, 1))); ?></h4></div>
                                </div>

                                <div class="row">
                                    <div class=" col-lg-5 col-md-5 col-xs-12"><h4><b>Описание заявки</b></h4></div>
                                    <div class=" col-lg-5 col-md-5 col-xs-12"><h5><?php echo pg_fetch_result($sql_ticket, 0, 2); ?></h5></div>
                                </div>

                                <div class="row">
                                    <div class=" col-lg-5 col-md-5 col-xs-12"><h4><b>Выполненые работы</b></h4></div>
                                    <div class=" col-lg-5 col-md-5 col-xs-12"><h5>
                                            <input type="checkbox" class="work" value="Замена GPRS модема">Замена GPRS модема<br>
                                            <input type="checkbox" class="work" value="Замена счетчика импульсов Регистратора">Замена счетчика импульсов Регистратора <br>
                                            <input type="checkbox" class="work" value="Обслуживание">Обслуживание <br>
                                            <input type="checkbox" class="work" value="Замена источника питания">Замена источника питания <br>
                                            <input type="checkbox" class="work" id="add_vvod" value="Подключение счетчика ХВ к системе">Подключение счетчика ХВ к системе <br>
                                            <input type="checkbox" class="work" id="korr" value="Коррекция показаний ХВ">Коррекция показаний ХВ <br>
                                            <input type="checkbox" class="work" value="Диагностика работы системы ">Диагностика работы системы <br>
                                            <input type="checkbox" class="work" value="Замена батарейки на регистраторе">Замена батарейки на регистраторе <br>
                                            <input type="checkbox" class="work" value="Обновление ПО GSM|GPRS модема">Обновление ПО GSM|GPRS модема <br>
                                            <input type="checkbox" class="work" value="Монтаж демонтаж Счетчика импульсов-регистратора (Перенос к счетчику ХВС)">Монтаж демонтаж Счетчика импульсов-регистратора (Перенос к счетчику ХВС)<br>
                                            <input type="checkbox" class="work" value="Замена кабельной трассы">Замена кабельной трассы <br>
                                            <input type="checkbox" class="work" value="Подключение тепловычислителя к системе">Подключение тепловычислителя к системе<br>
                                            <input type="checkbox" class="work" value="Замена шкаф">Замена шкафа <br>
                                        </h5></div>
                                </div>

                                <div id="data_korr" class="row hidden">
                                    <div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Данные корректировки</b></h4></div>
                                    <div class="col-lg-5 col-md-5 col-xs-12">
                                        <div class="form-group">
                                            <label for="inputVvod" class="col-sm-4 control-label">Ввод</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="vvod" placeholder="Выберите ввод"> </select>
                                            </div>
                                        </div>
                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputdatetimepicker1" class="col-sm-4 control-label">Дата</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="datetimepicker1"  style="width: 100%;" placeholder="Дата коректировки">
                                            </div>
                                        </div>
                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputnp" class="col-sm-4 control-label">Нач. показания</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="np"  style="width: 100%;" placeholder="Начальные показания">
                                            </div>
                                        </div>
                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputkp" class="col-sm-4 control-label">Кон. показания</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="kp"  style="width: 100%;" placeholder="Конечные показания">
                                            </div>
                                        </div>

                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputkp" class="col-sm-4 control-label">Примечание</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="prim"  style="width: 100%;" placeholder="Примечание">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div id="data_korr" class="row hidden">
                                        <div class="text-center col-lg-12 col-md-12 col-xs-12">
                                            <button type="button" id="add_korr" class="btn btn-lg btn-primary">Добавить</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="data_voda" class="row hidden">
                                    <div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Данные подключения счечтика</b></h4></div>
                                    <div class="col-lg-5 col-md-5 col-xs-12">
                                        <div class="form-group">
                                            <label for="inputVvod" class="col-sm-4 control-label">Ввод</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="vvod_voda" placeholder="Выберите ввод"> </select>
                                            </div>
                                        </div>
                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputdatetimepicker1" class="col-sm-4 control-label">Дата</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="datetimepicker2"  style="width: 100%;" placeholder="Дата коректировки">
                                            </div>
                                        </div>
                                        <div class="checkbox"><label></label></div>
                                        <div class="form-group">
                                            <label for="inputnp" class="col-sm-4 control-label">Нач. показания</label>
                                            <div class="col-sm-8 col-lg-8 col-md-8">
                                                <input type="text" class="form-control" id="kp_voda"  style="width: 100%;" placeholder="Начальные показания">
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div>
                                    <div id="data_voda" class="row hidden">
                                        <div class="text-center col-lg-12 col-md-12 col-xs-12">
                                            <button type="button" id="add_voda" class="btn btn-lg btn-primary">Добавить</button>
                                        </div>
                                    </div>
                                </div>

                                <br><br><br>
                                <div>
                                    <div class="row">
                                        <div id="all_object" class="text-center col-lg-12 col-md-12 col-xs-12 hidden">

                                        </div>
                                    </div>
                                </div>
                                <div class="text-center" id="result_add"></div>

                                <br><br><br><br>
                                <div class="row">
                                    <div class=" col-lg-12 col-md-12 col-xs-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"> &nbsp;Результат обследования </span>
                                            <textarea id="comment" class="form-control" rows="4" cols="70" style="width: 80%;"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <br><br>


                                <div>
                                    <div class="row">
                                        <div class="text-center col-lg-12 col-md-12 col-xs-12">
                                            <h2 id="result_delete"></h2>
                                        </div>
                                        <div class="text-center col-lg-12 col-md-12 col-xs-12">
                                            <button type="button" id="edit_ticket" class="btn btn-lg btn-primary">Редактировать <span class="glyphicon glyphicon-ok"></span></button>
                                            <button type="button" id="delete_ticket" class="btn btn-lg btn-danger">Удалить заявку<span class="glyphicon glyphicon-remove"></span></button>
                                        </div>
                                    </div>
                                </div>

                            </div>



                        </div>
                    </div>
                </div>
            </div>

            <!--Баковое меню -->

    </body>

    <script type="text/javascript">

        $('#datetimepicker1').datetimepicker({
            format: 'd.m.Y: H:00',
            lang: 'ru',
        });

        $('#datetimepicker2').datetimepicker({
            format: 'd.m.Y: H:00',
            lang: 'ru',
        });

        function add_select(plc) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_tickets_vvod.php',
                data: 'plc_id=' + plc,
                success: function (data) {
                    //$('#edit_ticket').html('Сохранено');
                    console.log(data);
                    for (var i = 0; i < data.length; i++) {
                        $('#vvod').append($('<option value="' + data[i].prp_id + '">' + data[i].name + '</option>'));
                        $('#vvod_voda').append($('<option value="' + data[i].prp_id + '">' + data[i].name + '</option>'));

                    }
                    //$('#reload_alarm').html(html);
                    //all_object(id_distinct, priveleg);
                }
            });
            return false;
        }

        function refresh_table_korrect(plc) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_refresh_table_korrect.php',
                data: 'plc_id=' + plc,
                success: function (html) {
                    $('#all_object').html(html);
                    $('#all_object').removeClass('hidden');
                    $('.delete').click(function () {
                        delete_alarm(this.id);
                        refresh_table_korrect(plc);

                    });
                }
            });
            return false;
        }

        function delete_alarm(id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_del_korrect.php',
                data: 'id=' + id,
                success: function (html) {
                    refresh_table_korrect(plc);
                    $('#result_add').html(html).fadeIn(3000).fadeOut(1000);
                }
            });
            return false;
        }
        function delete_all_korrect(id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/ajax_del_all_korrect.php',
                data: 'id=' + id,
                success: function (html) {
                }
            });
            return false;
        }


        $(document).ready(function () {
            priveleg = <?php echo $_SESSION['privelege']; ?>;
            plc = <?php echo $ticket['plc_id']; ?>;
            add_select(plc);
            var korr = 0;
            var save = 0;

            window.onbeforeunload = function (evt) {
                if (save == 0) {
                    if (korr != 0) {
                        var message = "Занесены данные корректировки счечтика, если покинуть страницу все данные будут уничтожены";
                        if (typeof evt == "undefined") {
                            evt = window.event;
                        }
                        if (evt) {
                            evt.returnValue = message;
                        }
                        return message;
                    }
                }
            }

            $('#korr').click(function () {
                if ($('#korr').prop('checked')) {
                    if ($('#add_vvod').prop('checked')) {
                        $('#add_vvod').prop({'checked': false});
                        $('div#data_voda').each(function () {
                            $(this).addClass('hidden');
                        });
                    }
                    $('div#data_korr').each(function () {
                        $(this).removeClass('hidden');
                    });
                    refresh_table_korrect(plc);
                } else {
                    $('div#data_korr').each(function () {
                        $(this).addClass('hidden');
                    });
                    $('#all_object').addClass('hidden');
                }
            })

            $(window).unload(function () {
                var id_ticket = <?php echo $_GET['id_ticket']; ?>;
                if (save == 0 & korr != 0) {
                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: 'ajax/ajax_del_all_korrect.php',
                        data: 'id=' + id_ticket,
                        success: function (html) {
                        }
                    });
                    return false;
                }
            });


            $('#add_vvod').click(function () {
                if ($('#add_vvod').prop('checked')) {
                    if ($('#korr').prop('checked')) {
                        $('#korr').prop({'checked': false});
                        $('div#data_korr').each(function () {
                            $(this).addClass('hidden');
                        });
                    }
                    $('div#data_voda').each(function () {
                        $(this).removeClass('hidden');
                    });
                    refresh_table_korrect(plc);
                } else {
                    $('div#data_voda').each(function () {
                        $(this).addClass('hidden');
                    });
                    $('#all_object').addClass('hidden');
                }
            })

            $('#add_korr').click(function () {
                var id_ticket = <?php echo $_GET['id_ticket']; ?>;
                var prp_id = $('#vvod').val();
                var date = $("#datetimepicker1").val();
                var np = $('#np').val();
                var kp = $('#kp').val();
                var name = $('#vvod option:selected').text();
                var prim = $('#prim').val();
                if (date != "" & np != "" & kp != "" & name != "") {
                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: 'ajax/ajax_add_korrect.php',
                        data: {id_ticket: id_ticket, plc_id: plc, prp_id: prp_id, date: date, np: np, kp: kp, name: name, prim: prim},
                        success: function (html) {
                            $('#result_add').html(html).fadeIn(3000).fadeOut(1000);
                            refresh_table_korrect(plc);
                            console.log(html);
                            korr++;
                            $('input[type=text]').each(function () {
                                $(this).val('');
                            });
                        }
                    });
                }
            });


            $('#add_voda').click(function () {
                var id_ticket = <?php echo $_GET['id_ticket']; ?>;
                var prp_id = $('#vvod_voda').val();
                var date = $("#datetimepicker2").val();
                var np = '';
                var kp = $('#kp_voda').val();
                var name = $('#vvod_voda option:selected').text();
                if (date != "" & kp != "" & name != "") {
                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: 'ajax/ajax_add_korrect.php',
                        data: 'id_ticket=' + id_ticket + '&plc_id=' + plc + '&prp_id=' + prp_id + '&date=' + date + '&np=' + np + '&kp=' + kp + '&name=' + name,
                        success: function (html) {
                            $('#result_add').html(html).fadeIn(3000).fadeOut(1000);
                            refresh_table_korrect(plc);
                            console.log(html);
                            korr++;
                            $('input[type=text]').each(function () {
                                $(this).val('');
                            });
                        }
                    });
                }
            });





            $('#edit_ticket').click(function () {
                var id_ticket = <?php echo $_GET['id_ticket']; ?>;
                var arr = $('input.work:checked');
                var str = "";
                arr.each(function (index, el) {
                    if (str == "") {
                        str = str + el.value;
                    } else {
                        str = str + "; " + el.value;
                    }

                });
                if (str == "") {
                    str = str + $('#comment').val();
                } else {
                    str = str + ". " + $('#comment').val();
                }

                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax/ajax_edit_ticket.php',
                    data: 'id_ticket=' + id_ticket + '&result=' + str,
                    success: function (html) {
                        $('#edit_ticket').html('Сохранено');
                        $('#edit_ticket').addClass('disabled');
                        save++;
                        //$('#reload_alarm').html(html);
                        //all_object(id_distinct, priveleg);
                    }
                });

                return false;

                console.log(str);
            });


            $('#delete_ticket').click(function () {
                var id_ticket = <?php echo $_GET['id_ticket']; ?>;
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax/ajax_del_ticket.php',
                    data: {id_ticket: id_ticket},
                    success: function (html) {
                        $('#result_delete').html('Сохранено');
                        $('#edit_ticket').addClass('disabled');
                        $('#delete_ticket').addClass('disabled');
                        save++;
                        //$('#reload_alarm').html(html);
                        //all_object(id_distinct, priveleg);
                    }
                });

                return false;
            });

            $('.ticket').click(function () {
                alert(this.id);
                return false;
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
                        }
                    });
                    return false;
                }
            });

            $('#reload_alarm').click(function () {
                //alert('okokokok');
                var id_distinct = 0;
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax_reload_error.php',
                    success: function (html) {
                        $('#reload_alarm').html(html);
                        all_object(id_distinct, priveleg);
                    }
                });
                return false;
            })
        });



    </script>

</html>
