<?php
include 'db_config.php';
$date = date('Y-m-d');
session_start();

$upd_status = pg_query('UPDATE fault_inc SET view_stat=1 WHERE id='.$_GET['inc']);

$sql_name = pg_query('SELECT 
  "Tepl"."Places_cnt"."Name"
FROM
  "Tepl"."Places_cnt"
WHERE
  "Tepl"."Places_cnt".plc_id = ' . $_GET['plc']);
$name = pg_fetch_all($sql_name);

$sql_inc = pg_query('SELECT 
  public.fault_inc.date_time,
  public.fault_inc.view_stat,
  public.fault_inc.comments,
  public.fault_cnt.name
FROM
  public.fault_cnt
  INNER JOIN public.fault_inc ON (public.fault_cnt.id = public.fault_inc.numb)
WHERE
  public.fault_inc.id = ' . $_GET['inc']);

$inc = pg_fetch_all($sql_inc);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>


        <script
            src="http://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>

        <link href="modules/DataTables-1.10.16/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="modules/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="modules/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="modules/bootstrap-3.3.7-dist/js/bootstrap.js" type="text/javascript"></script>

        <link href="modules/css/dashboard.css" rel="stylesheet" type="text/css"/>

    </head>
    <body>

        <div id="content">
            <!-- Верхний бар -->
            <div class="navbar navbar-default navbar-fixed-top" style=".navbar a{
                     color: #337AB7;
                 }" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a id="forBrand" class="navbar-brand" href="#">Измерительная система контроля и учета энергоресурсов</a>
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a id="forBrand" href="index.php">Выход</a></li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
        <!--Верхний бар -->


        <div class="container-fluid">
            <div class="row">
                <!--Боковое меню -->
                <div class="col-sm-3 col-md-2 sidebar">
                    <?php include './include/menu.php'; ?>
                </div>
                <!--Боковое меню -->

                <!--Контент -->
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">
                        <div id="center_h1"> Режим диспетчера
                        </div>
                    </h1>
                    <div>
                        <ul class="nav nav-tabs nav-justified">
                            <li class="active"><a href="#">Журнал инцидента</a></li>
                            <li><a href="#">Воспроизвести ошибку</a></li>
                        </ul>
                        <div>
                            <div class="col-lg-12 col-md-12 col-xs-12">
                                <div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Учереждение:</b></h4></div>
                                        <div class="col-lg-4 col-md-4 col-xs-12"><h4><?php echo "<a href='object.php?id_object=" . $_GET[plc] . "' >" . $name[0][Name] . "</a>"; ?></h4></div>
                                    </div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Дата ошибки:</b></h4></div>
                                        <div class="col-lg-4 col-md-4 col-xs-12"><h4><?php echo date("d.m.Y", strtotime($inc[0]['date_time'])); ?></h4></div>
                                    </div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Тип инцидента:</b></h4></div>
                                        <div class="col-lg-4 col-md-4 col-xs-12"><h4><?php echo $inc[0]['name']; ?></h4></div>
                                    </div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Параметры инцидента:</b></h4></div>
                                        <div class="col-lg-4 col-md-4 col-xs-12"><h4><?php echo $inc[0]['comments']; ?></h4></div>
                                    </div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Статус инцидента:</b></h4></div>
                                        <div class="col-lg-4 col-md-4 col-xs-12"><h4><?php
                                        
                                                switch ($inc[0]['view_stat']){
                                                    case 0:$stat = "Новый";
                                                        break;
                                                    case 1:$stat = "Просмотрен";
                                                        break;
                                                    case 2:$stat = "В работе";
                                                        break;
                                                    case 3:$stat = "Закрыт";
                                                        break;
                                                    case 4:$stat = "Удален";
                                                        break;
                                                }
                                        ?></h4></div>
                                    </div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Комментарий пользователя:</b></h4></div>
                                        <div class="col-lg-4 col-md-4 col-xs-12"><h4><?php ?></h4></div>
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

        var refresh_table = function (plc, inc, date) {
            $.ajax({
                type: 'POST',
                chashe: false,
                url: 'ajax/controllers/view_incedent.php',
                success: function (html) {
                    alert(html);
                    table.ajax.reload();
                }
            });
            return false;
        };



        $(document).ready(function () {

            var date = <?php echo $_GET['date']; ?>
            var plc = <?php echo $_GET['plc']; ?>
            var inc = <?php echo $_GET['inc']; ?>

            refresh_table(plc, inc, date);


            $('.nav-sidebar li a').each(function () {
                var location = window.location.href;
                var link = this.href;
                if (location == link) {
                    $(this).parent('li').addClass("active");
                }

            });

        });



    </script>

</html>
