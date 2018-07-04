<?php
session_start();
include './include/db_config.php';

/*
 * 
 * 
  -- Table: fortum_plc

  -- DROP TABLE fortum_plc;

  CREATE TABLE fortum_plc
  (
  id serial NOT NULL, -- идентификатор
  plc_id integer,
  exception integer,
  CONSTRAINT id_fort PRIMARY KEY (id)
  )
  WITH (
  OIDS=FALSE
  );
  ALTER TABLE fortum_plc
  OWNER TO postgres;
  COMMENT ON COLUMN fortum_plc.id IS 'идентификатор
  ';



 */
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
        <script src="js/jquery-2.2.1.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script type="text/javascript" src="js/jquery.livequery.js"></script>
        <script src="js/functions.js" type="text/javascript"></script>
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
                        echo ' <form  class="navbar-form navbar-right">
                                        <div class = "input-group">
                                            <input type="search" class="form-control" autocomplete="off" id="search" placeholder="Поиск..."/>
                                            <span class="input-group-btn">
                                                <button class="form-control btn btn-default btn-primary" id="formSearch" autofocus type="search"> <span class="glyphicon glyphicon-search"></span> </button>
                                            </span>

                                        </div> 
                                    </form>';
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
                </div>
                <!--Боковое меню -->

                <!--Контент -->
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">
                        <div id="center_h1">

                        </div>
                    </h1>
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active"><a href="">Добавление обьектов для загрузки в "Фортум"</a></li>
                        <li><a href="">Просмотр объектов</a></li>
                    </ul>
                    <div  id="center_h1" style="margin-top: 40px;">
                        <div class="bs-example">
                            <button class="btn btn-lg btn-primary add_all">Добавить все обьекты</button>
                            <button class="btn btn-lg btn-danger del_all">Удалить все обьекты</button>
                        </div>
                    </div>
                    <div id="all_object" class="table-responsive" style="margin-top: 40px;">
                    </div>
                </div>
            </div>
        </div>

        <!--Баковое меню -->

    </body>

    <script type="text/javascript">

        function edit_object(action, id) {
            $.ajax({
                type: 'POST',
                cache: false,
                url: "ajax/obj_functions.php",
                data: {action: action, plc: id}
            });
            return false;
        }


        function view_settings_add() {
            $.ajax({
                type: 'POST',
                cache: false,
                url: "ajax/settings_view.php",
                beforeSend: function () {
                    $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#all_object').html(html);
                    $('.del_to, .add_to').click(function () {

                        if ($('#' + this.id).hasClass('add_to'))
                        {

                            $('#' + this.id).removeClass("btn-primary").addClass("btn-danger").removeClass("add_to").addClass("del_to").text("Удалить");
                            edit_object('insert', this.id);
                        } else
                        {

                            $('#' + this.id).removeClass("btn-danger").addClass("btn-primary").removeClass("del_to").addClass("add_to").text("Добавить");
                            edit_object('delete', this.id);
                        }
                    });

                }
            });
            return false;
        }

        $(document).ready(function () {
            $('.nav-sidebar li a').each(function () {
                var location = window.location.href;
                var link = this.href;
                if (location == link) {
                    $(this).parent('li').addClass("active");
                }

            });
            view_settings_add();

            $('.add_all').click(function () {
                add_all_object();
            });

            $('.del_all').click(function () {
                del_all_object();
            });

        })

    </script>

</html>
