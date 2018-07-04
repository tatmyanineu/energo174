<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include 'db_config.php';
session_start();
if (isset($_SESSION['login'])) {
    $id_object = $_GET['id_object'];

    $sql_school = pg_query('SELECT 
  "Places_cnt1"."Name",
  "Places_cnt1".plc_id
FROM
  "Tepl"."Places_cnt" "Places_cnt1" 
WHERE
  "Places_cnt1".typ_id = 17 AND
  "Places_cnt1".plc_id = ' . $id_object . '');
} else {
    header('location: index.php');
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
                        <ul class="nav nav-sidebar">
                            <li><a href="object.php?id_object=<?php echo $id_object; ?>" ><span class="glyphicon glyphicon-chevron-left"></span> Назад  </a></li>
                        </ul>


                    </div>
                    <!--Боковое меню -->

                    <!--Контент -->
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1>Журнал заявок в <?php echo pg_fetch_result($sql_school, 0, 0); ?></h1>
                            </div>
                        </h1>
                        <div id="all_object">
                            <?php
                            $sql_ticket = pg_query('SELECT DISTINCT 
                                public.ticket.id,
                                public.ticket.plc_id,
                                public.ticket.date_ticket,
                                public.ticket.text_ticket,
                                public.ticket.status,
                                public.ticket.date_close,
                                public.ticket.close_text
                              FROM
                                public.ticket
                              WHERE
                                public.ticket.plc_id = ' . $id_object . '');

                            echo '<table class = "table table-responsive table-bordered" >'
                            . '<thead id = "thead">'
                            . '<tr id = "warning">'
                            . '<td><b>№</b></td>'
                            . '<td><b>Дата</b></td>'
                            . '<td><b>Описание заявки</b></td>'
                            . '<td><b>Дата закрытия </b></td>'
                            . '<td><b>Результат</b></td>'
                            . '<td><b>Статус заявки</b></td>'
                            . '</tr>'
                            . '</thead>'
                            . '<tbody>';
                            while ($result = pg_fetch_row($sql_ticket)) {
                                switch ($result[4]) {
                                    case 0:
                                        $status = "Обычная";
                                        echo '<tr>';
                                        break;
                                    case 1:
                                        $status = "Срочная";
                                        echo '<tr class="warning">';
                                        break;
                                    case 2:
                                        $status = "Критическая";
                                        echo '<tr class="danger">';
                                        break;
                                    case 4:
                                        $status = "Закрыта";
                                        echo '<tr class="success">';
                                        break;
                                    case 5:
                                        $status = "Удалена";
                                        echo '<tr class="success">';
                                        break;
                                }

                                if ($result[6] != '') {
                                    $ticket_text = 'Заявка:' . $result[3] . ' </br> Ответ:' . $result[6];
                                } else {
                                    $ticket_text = $result[3];
                                }



                                echo '<td>' . $result[0] . '</td>'
                                . '<td>' . date('d.m.Y', strtotime($result[2])) . '</td>'
                                . '<td>' . $result[3] . '</td>';
                                if ($result[6] == '') {
                                    echo '<td> - </td>'
                                    . '<td> - </td>';
                                } else {
                                    echo '<td>' . date('d.m.Y', strtotime($result[5])) . '</td>';
                                    $str = '<b>Выполненые работы: </b>' . $result[6];
                                    $sql_pod = pg_query('SELECT 
                                            public.korrect.id,
                                            public.korrect.plc_id,
                                            public.korrect.prp_id,
                                            public.korrect.id_tick,
                                            public.korrect.date_time,
                                            public.korrect.old_value,
                                            public.korrect.new_value,
                                            public.korrect.name_prp,
                                            public.korrect.date_record
                                          FROM
                                            public.korrect
                                          WHERE
                                            public.korrect.id_tick=' . $result[0] . '
                                          ORDER BY
                                            public.korrect.name_prp');

                                    if (pg_num_rows($sql_pod) != 0) {
                                        while ($result_pod = pg_fetch_row($sql_pod)) {
                                            if ($result_pod[5] != '') {
                                                $str .= " <br><b>Результат коррекции счечтика:</b>  " . $result_pod[7] . ". Дата: " . date("d.m.Y", strtotime($result_pod[4])) . " Нач. показания: " . $result_pod[5] . " Кон. показания:  " . $result_pod[6] . " <br>";
                                            } else {
                                                $str .= " <br><b>Результат подключения счечтика:</b>  " . $result_pod[7] . ". Дата: " . date("d.m.Y", strtotime($result_pod[4])) . " Нач. показания: " . $result_pod[6] . " <br>";
                                            }
                                        }
                                    }
                                    echo '<td>' . $str . '</td>';
                                }


                                if ($result[4] < 3) {
                                    echo '<td> <a href="#" class="ticket" id ="' . $result[0] . '">Открыт (Редактировать...)</a></td>';
                                } else {
                                    echo '<td> ' . $status . '</td>';
                                }
                                echo'</tr>';
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>

            <!--Баковое меню -->

    </body>

    <script type="text/javascript">

        $(document).ready(function () {
            
            
            $('.nav-sidebar li a').each(function () {
                var location = window.location.href;
                var link = this.href;
                if (location == link) {
                    $(this).parent('li').addClass("active");
                }

            });
            priveleg = <?php echo $_SESSION['privelege']; ?>


            $('.ticket').click(function () {
                //alert(this.id);
                window.location = 'edit_ticket.php?id_ticket=' + this.id;
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
