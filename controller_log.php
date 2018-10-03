<?php
include 'db_config.php';
$date = date('Y-m-d');
session_start();

$sql_inc = pg_query('SELECT 
  public.fault_inc.date_time,
  public.fault_inc.view_stat,
  public.fault_inc.comments,
  public.fault_cnt.name,
  public.fault_inc.user_comment
FROM
  public.fault_cnt
  INNER JOIN public.fault_inc ON (public.fault_cnt.id = public.fault_inc.numb)
WHERE
  public.fault_inc.id = ' . $_GET['inc']);

$inc = pg_fetch_all($sql_inc);

if ($inc[0]['view_stat'] == 0) {
    $upd_status = pg_query('UPDATE fault_inc SET view_stat=1 WHERE id=' . $_GET['inc']);
}


$sql_name = pg_query('SELECT 
  "Tepl"."Places_cnt"."Name"
FROM
  "Tepl"."Places_cnt"
WHERE
  "Tepl"."Places_cnt".plc_id = ' . $_GET['plc']);
$name = pg_fetch_all($sql_name);
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
                        <div id="center_h1"> Режим диспетчера: <?php echo "<a href='object.php?id_object=" . $_GET[plc] . "&inc=" . $_GET['inc'] . "' >" . $name[0][Name] . "</a>";?>
                        </div>
                    </h1>
                    <div>
                        <ul class="nav nav-tabs nav-justified" id="tabs" role="tablist">
                            <li class="active"><a href="#journal" id="journal-tab" role="tab" >Журнал инцидента</a></li>
                            <li><a href="#view" id="view-tab">Воспроизвести ошибку</a></li>
                        </ul>


                        <div id="" class="tab-content">

                            <div class="tab-pane fade in active" id="journal">
                                <div class="col-lg-12 col-md-12 col-xs-12">
                                    <div>
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-lg-3 col-md-5 col-xs-12"><h4><b>Учереждение:</b></h4></div>
                                            <div class="col-lg-7 col-md-7 col-xs-12"><h4><?php echo "<a href='object.php?id_object=" . $_GET[plc] . "&inc=" . $_GET['inc'] . "' >" . $name[0][Name] . "</a>"; ?></h4></div>
                                        </div>
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-lg-3 col-md-5 col-xs-12"><h4><b>Дата ошибки:</b></h4></div>
                                            <div class="col-lg-7 col-md-7 col-xs-12"><h4><?php echo date("d.m.Y", strtotime($inc[0]['date_time'])); ?></h4></div>
                                        </div>
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-lg-3 col-md-5 col-xs-12"><h4><b>Тип инцидента:</b></h4></div>
                                            <div class="col-lg-7 col-md-7 col-xs-12"><h4><?php echo $inc[0]['name']; ?></h4></div>
                                        </div>
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-lg-3 col-md-5 col-xs-12"><h4><b>Параметры инцидента:</b></h4></div>
                                            <div class="col-lg-7 col-md-7 col-xs-12"><h4><?php echo $inc[0]['comments']; ?></h4></div>
                                        </div>
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-lg-3 col-md-5 col-xs-12"><h4><b>Статус инцидента:</b></h4></div>
                                            <div class="col-lg-7 col-md-7 col-xs-12"><h4>
                                                    <select class="form-control" id="stat_inc">
                                                        <?php
                                                        $stat_arr = ['0' => 'Новый', '1' => 'Просмотрен', '2' => 'В работе', '3' => 'Завершен', '4' => 'Удален'];
                                                        foreach ($stat_arr as $key => $value) {
                                                            echo ($key == $inc[0]['view_stat'] ? '<option value="' . $key . '" selected>' . $value . '</option>' : '<option value="' . $key . '">' . $value . '</option>');
                                                        }
                                                        ?>
                                                    </select>
                                                </h4></div>
                                        </div>
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-lg-3 col-md-5 col-xs-12"><h4><b>Комментарий пользователя:</b></h4></div>
                                            <div class="col-lg-7 col-md-7 col-xs-12"><textarea class="form-control" id="comm_inc"><?php echo $inc[0]['user_comment']; ?></textarea></div>
                                        </div>
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-lg-3 col-lg-offset-3 col-md-4 col-md-offset-4 col-xs-6"><button class="btn btn-primary btn-lg edit_inc" id="<?php echo $_GET['inc']; ?>">Обновить информацию</button></div>
                                            <div class="col-lg-3 col-md-4  col-xs-6"><button class="btn btn-danger btn-lg del_inc" id="<?php echo $_GET['inc']; ?>">Удалить информацию</button></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade in" id="view" >

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


        $(document).ready(function () {

            $('#tabs a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
                if (this.id == "view-tab") {
                    var plc =<?php echo $_GET['plc']; ?>;
                    var id =<?php echo $_GET['inc']; ?>;
                    var date = "<?php echo $_GET['date']; ?>";
                    $.ajax({
                        type: 'POST',
                        cache: false,
                        url: "ajax/controllers/view_incedent.php",
                        data: {id: id, plc: plc, date: date},
                        beforeSend: function () {
                            $('#view').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                        },
                        success: function (html) {
                            $('#view').html(html);
                        }
                    });
                    return false;
                }
                console.log(this.id);
            })

            $('.edit_inc').click(function () {
                var stat = $('#stat_inc').val();
                var comm = $('#comm_inc').val();
                var id = this.id;
                var param = 1;
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: "ajax/controllers/edit_comments.php",
                    data: {stat: stat, comm: comm, id: id, param: param},
                    success: function (html) {
                    }
                });
                return false;
                //console.log(stat + " " + comm);
            });

            $('.del_inc').click(function () {
                var id = this.id;
                var param = 2;
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: "ajax/controllers/edit_comments.php",
                    data: {param: param, id: id},
                    success: function (html) {
                        window.open('controller.php');
                    }
                });
                return false;
                //console.log(stat + " " + comm);
            });


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
