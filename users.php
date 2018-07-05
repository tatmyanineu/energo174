<?php

include 'db_config.php';
session_start();
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
                        <ul  class="nav nav-sidebar">
                        </ul>


                    </div>
                    <!--Боковое меню -->

                    <!--Контент -->
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1>Настройка пользователей</h1>
                            </div>
                        </h1>
                        <div class="modal fade" id="addGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-plus"></span>Добавление новой группы пользователей</h4>
                                    </div>

                                    <div class="modal-body">
                                        <form id="formWorkUsers">
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-5 col-md-5 col-xs-12">Название</div>
                                                <div class="col-lg-7 col-md-7 col-xs-12"><input type="text" id="AddName" class="form-control"/></div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-5 col-md-5 col-xs-12">Комментарий</div>
                                                <div class="col-lg-7 col-md-7 col-xs-12"><input type="text" id="AddComment" class="form-control"/></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                        <button type="button" id="addGroupButton" class="btn btn-primary">Добавить</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="search_view">
                            <div class="row" style="margin-bottom:  20px;">
                                <div class="col-lg-4 col-md-4 col-xs-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Поиск по группе</span>
                                        <input type="text" id="search-group" class="form-control" placeholder="Название группы">
                                    </div>    
                                </div>
                                <div class="col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 col-xs-12">
                                    <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#addGroup"><span class="glyphicon glyphicon-plus"></span> Добавить группу</button>
                                </div> 
                            </div>
                        </div>


                        <div id="view" class="table-responsive">
                        </div>
                    </div>
                </div>
            </div>

            <!--Баковое меню -->

    </body>

    <script type="text/javascript">

        function read_logs() {

        }

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

        function delete_group(id) {
            alert(id);
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/users_deleteGroup.php',
                data: {id: id},
                success: function () {
                    refresh_table()
                }
            });
            return false;
        }

        function refresh_table() {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/users_ajax.php',
                data: 'key=' + $('#search-group').val(),
                beforeSend: function () {
                    $('#view').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#view').html(html);
                    $('.deleteGroup').click(function () {
                        delete_group(this.id);
                    });
                    $('tbody td[data-href]').addClass('clickable').click(function () {
                        window.location = $(this).attr('data-href');
                    })
                    frame_hieght();
                }
            });
            return false;
        }


        $(document).ready(function () {
            priveleg = <?php echo $_SESSION['privelege']; ?>;
            frame_hieght();
            refresh_table();

            $("#search-group").keyup(function () {
                refresh_table();
            });

            $('#addGroupButton').click(function () {
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax/users_addGroup.php',
                    data: {name: $('#AddName').val(), comment: $('#AddComment').val()},
                    beforeSend: function () {
                        $('#view').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                    },
                    success: function (html) {
                        //$('#view').html(html);
                        $('tbody tr[data-href]').addClass('clickable').click(function () {
                            window.location = $(this).attr('data-href');
                        })

                        $('#addGroup .modal-body').html(html);
                        $("#addGroupButton").remove();

                        frame_hieght();
                        refresh_table();
                    }
                });
                return false;
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
