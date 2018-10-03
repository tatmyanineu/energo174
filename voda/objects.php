<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../db_config.php';
$date = date('Y-m-d');
session_start();

$date1 = date("Y-m-21", strtotime("-1 month"));
$date2 = date("Y-m-20");

//$date1 = date("2018-06-21");
//$date2 = date("2018-07-20");


if (isset($_SESSION['login']) and isset($_SESSION['password'])) {

    $sql_disitnct = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."Places_cnt".plc_id
FROM
  "Tepl"."GroupToUserRelations"
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
WHERE
                            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
  "Tepl"."Places_cnt".typ_id = 10
ORDER BY
  "Tepl"."Places_cnt"."Name"');
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>

        <script
            src="http://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">
        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<!--        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">-->


        <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <script src="../js/jquery.datetimepicker.js" type="text/javascript"></script>




        <link rel="stylesheet" type="text/css" href="../css/dashboard.css"/>
        <link href="../css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style id="holderjs-style" type="text/css"></style></head>

    <body id="top">

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

                    </div>
                </div>
            </div>


            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-md-2 sidebar">
                        <ul class="nav nav-sidebar">
                            <li class="active"><a href="objects.php"><span class="glyphicon glyphicon-home"></span>Обьекты</a></li>
                        </ul>
                        <ul class="nav nav-sidebar">
                            <li class=""><a href="#" id="save_file"><span class="glyphicon glyphicon-floppy-disk"></span>Скачать представление</a></li>
                            <li class=""><a href="#" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-filter"></span>Установить фильтр</a></li>
                        </ul>
                    </div>


                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">

                        <div id="center_h1">



                            <div class="modal fade"  id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Параметры фильтра</h4>
                                        </div>
                                        <div class="modal-body">
                                            <p>Выберите период</p>
                                            <div class="form-inline text-center" style="margin-bottom: 10px">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Нач. дата </span>
                                                    <input type="text" class="form-control" id="datetimepicker1" value="<?php echo $date1; ?>">
                                                </div>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Кон. дата </span>
                                                    <input type="text" class="form-control" id="datetimepicker2" value="<?php echo $date2; ?>">
                                                </div>
                                            </div>
                                            <p>Выберите район</p>
                                            <div class="">
                                                <select id="district" class="form-control">
                                                    <option value="0">Все район</option>
                                                    <?php
                                                    while ($row_disitinct = pg_fetch_row($sql_disitnct)) {
                                                        if ($_GET['id_distinct'] == $row_disitinct[1]) {
                                                            echo '<option selected value="' . $row_disitinct[1] . '" >' . $row_disitinct[0] . '</option>';
                                                        } else {
                                                            echo '<option value="' . $row_disitinct[1] . '" >' . $row_disitinct[0] . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                            <button type="button" id="sub_filtr" class="btn btn-primary">Выполнить</button>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->



                            <!--                            <div class="form-inline text-center" style="margin-bottom: 10px">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Нач. дата </span>
                                                                <input type="text" class="form-control" id="datetimepicker1"  value="<?php echo date("d.m.Y", strtotime($date1)); ?>"/>
                                                            </div>
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>Кон. дата </span>
                                                                <input type="text" class="form-control" id="datetimepicker2"  value="<?php echo date("d.m.Y", strtotime($date2)); ?>"/>
                                                            </div>
                                                            <input type="submit" class="btn btn-default" id="paramtr" value="Применить"/>
                                                        </div>-->


                        </div>                       


                        <div class="col-lg-12 col-md-12 col-xs-12">
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 ">
                                        <div id="all_object">
                                            <table id="view_table">

                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap core JavaScript
            ================================================== -->
            <!-- Placed at the end of the document so the pages load faster -->


    </body>
</html>

<script type="text/javascript">
    $('#datetimepicker1').datetimepicker({
        format: 'd.m.Y',
        lang: 'ru',
    });
    $('#datetimepicker2').datetimepicker({
        format: 'd.m.Y',
        lang: 'ru',
    });
//                function frame_hieght() {
//                    if (parent.document.getElementById('blockrandom') != null) {
//                        parent.document.getElementById('blockrandom').style.height = '0px';
//                        parent.document.getElementById('blockrandom').style.height = document.documentElement.offsetHeight + 'px';
//                        var height_wind = parent.document.getElementById('blockrandom').style.height;
//                        height_wind = height_wind.slice(0, -2);
//                        console.log(height_wind);
//                        if (height_wind < 800) {
//                            parent.document.getElementById('blockrandom').style.height = '800px';
//                            parent.document.getElementById('blockrandom').style.width = '1250px';
//                        } else {
//                            parent.document.getElementById('blockrandom').style.height = height_wind + 'px';
//                            parent.document.getElementById('blockrandom').style.width = '1250px';
//                        }
//                    }
//                }
//
//




    function view_table() {
        var json = ajaxRequest();
        var tableName = '#view_table';
        $.each(json.columns, function (k, colObj) {
            str = '<th>' + colObj.title + '</th>';
            $(str).appendTo(tableName + '>thead>tr');
        });
        var tables = $(tableName).DataTable({
            destroy: true,
            dom: 'Bfrtip',
               paging: false,
            "autoWidth": false,
            oLanguage: {
                "sLengthMenu": "Отображено _MENU_ записей на страницу",
                "sSearch": "Поиск:",
                "sZeroRecords": "Ничего не найдено - извините",
                "sInfo": "Показано с _START_ по _END_ из _TOTAL_ записей",
                "sInfoEmpty": "Показано с 0 по 0 из 0 записей",
                "sInfoFiltered": "(filtered from _MAX_ total records)",
                "oPaginate": {
                    "sFirst": "Первая",
                    "sLast": "Посл.",
                    "sNext": "След.",
                    "sPrevious": "Пред.",
                }
            },
            columns: json.columns,
            data: json.data

        });
    }

    function ajaxRequest() {
        var res;
        var dist = $('#district').val();
        var date1 = $('#datetimepicker1').val(),
                date2 = $('#datetimepicker2').val();
        $.ajax({
            type: 'POST',
            cache: false,
            url: "ajax_objects_voda.php",
            data: {dist: dist, date1: date1, date2: date2},
            dataType: "json",
            async: false,
            success: function (array) {
                console.log(array);
                res = array;
            }

        })
        return res;

    }

    $(document).ready(function () {

        $("#sub_filtr").click(function () {
            //alert($('#district').val());
            $('#myModal').modal('hide');
            $("th").remove();
            var date1 = $('#datetimepicker1').val();
            var date2 = $('#datetimepicker2').val();
            view_table();
//                        history.pushState(null, null, '/pulsar_form/voda/objects.php?id_distinct=' + $('#district').val());
        });

        $('#save_file').click(function () {
            window.open('ajax/download_dbf.php');
        });

        view_table();
        $('#view_table')
                .removeClass('display')
                .addClass('table table-striped table-bordered');

    }
    );

</script>
