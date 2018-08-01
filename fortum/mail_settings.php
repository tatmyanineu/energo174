
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>

        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="css/dashboard.css"/>
        <script src="js/jquery-2.2.1.js" type="text/javascript"></script>
        <script src="js/jquery.datetimepicker.js" type="text/javascript"></script>
        <link href="css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
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
        </div>
        <!--Верхний бар -->


        <div class="container-fluid">
            <div class="row">
                <!--Боковое меню -->
                <div class="col-sm-3 col-md-2 sidebar">
                    <?php include './include/menu.php'; ?> 


                    <ul class="nav nav-sidebar">
                        <li><a href = "#" id="forMail" data-toggle="modal" data-target=".bs-example-modal-xs"><span class = "glyphicon glyphicon-asterisk"></span> Экспорт</a></li>
                    </ul>
                </div>


                <!--Боковое меню -->

                <!--Контент -->
                <div class="modal fade bs-example-modal-xs" id="forMail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel">Сформировать файл за период</h4>
                            </div>
                            <div class="modal-body" >
                                <form class="form-inline" role="form">
                                    <div class="input-group">
                                        <span class="input-group-addon">Нач. дата </span>
                                        <input type="text" class="form-control" id="datetimepicker1"  value="<?php echo date("d.m.Y", strtotime('-1 day')); ?>"/>
                                    </div> 
                                    <div class="input-group">
                                        <span class="input-group-addon">Кон. дата </span>
                                        <input type="text" class="form-control" id="datetimepicker2"  value="<?php echo date("d.m.Y", strtotime('-1 day')); ?>"/>
                                    </div> 
                                </form>
                                <br>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                <button type="button" class="btn btn-primary" id="forMailSend">Формировать</button>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">
                        <div id="center_h1">

                        </div>
                    </h1>
                    <div>
                        <div id="all_object" class="table-responsive"></div>
                    </div>
                </div>
            </div>
        </div>

        <!--Баковое меню -->

    </body>

    <script type="text/javascript">

        function load_table() {
            $.ajax({
                type: 'POST',
                cache: false,
                url: "ajax/mail/mail_table.php",
                success: function (data) {
                    $('#all_object').html(data);
                }
            });
            return false;

        }

        $(document).ready(function () {
            load_table();
        });
    </script>

</html>
