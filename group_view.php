<?php
//Добавить шифрование пароля для передачи в скрипте ajax


include 'db_config.php';
session_start();

$sqlGroup = pg_query('SELECT DISTINCT 
  "Tepl"."UserGroups"."Name"
FROM
  "Tepl"."UserGroups"
WHERE
  "Tepl"."UserGroups".grp_id = ' . $_GET['gr_id'] . '');

$name = pg_fetch_result($sqlGroup, 0, 0);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>


        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="css/dashboard.css"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script type="text/javascript" src="js/jquery.livequery.js"></script>
        <!--        <link href="css/jquery.tagsinput.css" rel="stylesheet" type="text/css"/>
                <script src="js/jquery.tagsinput.js" type="text/javascript"></script>-->


        <script src="js/jQuery-tagEditor-master/jQuery-tagEditor-master/jquery.tag-editor.js" type="text/javascript"></script>
        <link href="js/jQuery-tagEditor-master/jQuery-tagEditor-master/jquery.tag-editor.css" rel="stylesheet" type="text/css"/>
        <script src="js/jQuery-tagEditor-master/jQuery-tagEditor-master/jquery.caret.min.js" type="text/javascript"></script>

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

                        <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-user"></span>Добавление нового пользователя</h4>
                                    </div>

                                    <div class="modal-body">
                                        <form id="formWorkUsers">
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-5 col-md-5 col-xs-12">Логин</div>
                                                <div class="col-lg-7 col-md-7 col-xs-12"><input type="text" id="AddLogin" class="form-control"/></div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-5 col-md-5 col-xs-12">Пароль</div>
                                                <div class="col-lg-7 col-md-7 col-xs-12"><input type="text" id="AddPasswd" class="form-control"/></div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-5 col-md-5 col-xs-12">Придумать название</div>
                                                <div class="col-lg-7 col-md-7 col-xs-12"><input type="text" id="AddSurname" class="form-control"/></div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-5 col-md-5 col-xs-12">Придумать название</div>
                                                <div class="col-lg-7 col-md-7 col-xs-12"><input type="text" id="AddName" class="form-control"/></div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-5 col-md-5 col-xs-12">Права</div>
                                                <div class="col-lg-7 col-md-7 col-xs-12"><input type="text" id="AddRole" class="form-control"/></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                        <button type="button" id="addUserButton" class="btn btn-primary">Добавить</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal fade bs-example-modal-lg" id="addUserGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel">Добавление пользователей в группу</h4>
                                    </div>
                                    <div class="modal-body" >
                                        <div class="input-group">
                                            <span class="input-group-addon">Поиск по пользователям </span>
                                            <input type="text" id="search-users" class="form-control" placeholder="логин">
                                        </div> 
                                        <br>
                                        <div class=" table-responsive text-center" id="viewUsers"></div>
                                    </div>
                                    <div class="modal-footer">
<!--                                        <input name="tags" class="form-control" id="tags" value="" placeholder="">-->
                                        <textarea id="demo1" class="tag-editor-hidden-src" readonly="readonly" style="display: block;"></textarea>
                                        <br>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                        <button type="button" class="btn btn-primary" id="addUserGroupButton">Добавить</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal fade bs-example-modal-lg" id="addObjectGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel">Добавление обьектов в группу</h4>
                                    </div>
                                    <div class="modal-body" >
                                        <form class="form-inline" role="form">

                                            <div class="input-group">
                                                <span class="input-group-addon">Поиск по обьектам </span>
                                                <input type="text" id="searchObjectModal" class="form-control" placeholder="Название объекта">
                                            </div> 
                                            <div class="input-group">
                                                <span class="input-group-addon">Районы </span>
                                                <?php
                                                $sql_disitnct = pg_query('SELECT DISTINCT 
                                                                    "Tepl"."Places_cnt"."Name",
                                                                    "Tepl"."Places_cnt".plc_id
                                                                  FROM
                                                                    "Tepl"."Places_cnt"
                                                                  WHERE
                                                                    "Tepl"."Places_cnt".typ_id = 10');
                                                echo '<select class="form-control">'
                                                . '<option id="0"></option>';
                                                while ($row = pg_fetch_row($sql_disitnct)) {
                                                    echo '<option id="' . $row[1] . '">' . $row[0] . '</option>';
                                                }
                                                echo '</select>';
                                                ?>

                                            </div> 
                                        </form>
                                        <br>
                                        <div class=" table-responsive text-center" id="viewObjects"></div>
                                    </div>
                                    <div class="modal-footer">
<!--                                        <input name="tags" class="form-control" id="tags" value="" placeholder="">-->
                                        <textarea id="demo2" class="tag-editor-hidden-src" readonly="readonly" style="display: block;"></textarea>
                                        <br>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Очистить список</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                        <button type="button" class="btn btn-primary" id="addOBjectGroupButton">Добавить</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1>Группа: <?php echo $name; ?></h1>
                            </div>
                        </h1>

                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1>Настройка пользователей</h1>
                            </div>
                        </h1>
                        <div id="search_view">
                            <div class="row" style="margin-bottom:  20px;">
                                <div class="col-lg-3 col-md-3 col-xs-12">
                                    <!--                                    <div class="input-group">
                                                                            <span class="input-group-addon">Поиск по группе</span>
                                                                            <input type="text" id="search-group" class="form-control" placeholder="Название группы">
                                                                        </div>    -->
                                </div>
                                <div class="col-lg-3 col-lg-offset-3 col-md-3 col-md-offset-3 col-xs-12">
                                    <button class="btn btn-info btn-md" data-toggle="modal" data-target=".bs-example-modal-lg" id="addUserGroupBtn"><span class="glyphicon glyphicon-ok"></span> Добавить пользователя к группе</button>
                                </div> 
                                <div class="col-lg-3 col-md-3 col-xs-12">
                                    <button class="btn btn-success btn-md" data-toggle="modal" data-target="#addUser"><span class="glyphicon glyphicon-plus"></span> Создать пользователя</button>
                                </div> 
                            </div>
                        </div>
                        <div id="view_user" class="">
                        </div>

                        <h1 class="page-header">
                            <div id="center_h1">
                                <h1>Настройка объектов</h1>
                            </div>
                        </h1>
                        <div id="search_view">
                            <div class="row" style="margin-bottom:  20px;">
                                <div class="col-lg-3 col-md-3 col-xs-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Поиск по объектам</span>
                                        <input type="text" id="search-object" class="form-control" placeholder="Название объекта">
                                    </div>    
                                </div>
                                <div class="col-lg-3 col-lg-offset-3 col-md-3 col-md-offset-3 col-xs-12">
                                    <button class="btn btn-info btn-md" data-toggle="modal" data-target=".bs-example-modal-lg" id="addObjectGroupBtn"><span class="glyphicon glyphicon-ok"></span> Добавить объекты к группе</button>
                                </div> 

                            </div>
                        </div>
                        <div id="view_object" class="">
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

        function delUserFunc(id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/users_delete.php',
                data: {group: group, id: id},
                beforeSend: function () {
                    $('#view_user').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#view_user').html(html);
                    frame_hieght();
                    refresh_table_user(group);
                }
            });
            return false;
        }

        function delUserGroupFunc(id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/users_delFromGroup.php',
                data: {group: group, id: id},
                beforeSend: function () {
                    $('#view_user').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#view_user').html(html);
                    frame_hieght();
                    refresh_table_user(group);
                }
            });
            return false;
        }

        function editUserFunc(id) {

            return false;
        }

        function refresh_table_user(group) {
            $.ajax({
                type: 'POST',
                chase: false,
                data: {group: group},
                url: 'ajax/users_info.php',
                beforeSend: function () {
                    $('#view_user').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#view_user').html(html);
                    $('.editUserMenu').click(function () {
                        var id = this.id;
                        editUserFunc(id);
                    });
                    $('.deleteUserGroupMenu').click(function () {
                        var id = this.id;
                        delUserGroupFunc(id);
                    });
                    $('.deleteUserMenu').click(function () {
                        var id = this.id;
                        delUserFunc(id);
                    });
                    frame_hieght();
                }
            });
            return false;
        }

        function  delete_object_group(group, id) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/users/delete_objectGroup.php',
                data: {group_id: group, plc_id: id},
                success: function (html) {
                    refresh_table_object(group);
                }
            });
            return false;
        }

        function refresh_table_object(group) {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/users_object.php',
                data: {group: group, key: $('#search-object').val()},
                beforeSend: function () {
                    $('#view_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                },
                success: function (html) {
                    $('#view_object').html(html);

                    $('.deleteObject').click(function () {
                        alert(this.id);
                        delete_object_group(group, this.id)
                    });
                    frame_hieght();
                }
            });
            return false;
        }

        function refresh_object_search() {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/users/object_view.php',
                data: {key: $('#searchObjectModal').val()},
                success: function (html) {
                    $('#viewObjects').html(html);
                    $('.addTag').click(function () {
                        var name = $('#name_' + this.id).text();
                        //$('#tags').addTag($('#name_' + this.id).text());
                        $('#demo2').tagEditor('addTag', '' + name + '');

                        if (objectID.hasOwnProperty(name)) {
                            console.log('Элемент уже добавлен');
                        } else {
                            objectID[name.toUpperCase()] = this.id;
                            console.log(objectID);
                        }
                    });
                }
            });
            return false;
        }

        function refresh_user_search() {
            $.ajax({
                type: 'POST',
                chase: false,
                url: 'ajax/users/users_view.php',
                data: {key: $('#search-users').val()},
                success: function (html) {
                    $('#viewUsers').html(html);
                    $('.addTag').click(function () {
                        var name = $('#name_' + this.id).text();
                        //$('#tags').addTag($('#name_' + this.id).text());
                        $('#demo1').tagEditor('addTag', '' + name + '');

                        if (userId.hasOwnProperty(name)) {
                            console.log('Элемент уже добавлен');
                        } else {
                            userId[name.toUpperCase()] = this.id;
                            console.log(userId);
                        }


                    });
                }
            });
            return false;
        }

//        $('.tagsinput .tag').on('click', 'a.removeTags', function () {
//            alert(this.id);
//        });

        $(document).ready(function () {
            $('#demo1').tagEditor({
                initialTags: [],
                placeholder: 'Пользователи',
                onChange: function (field, editor, tags) {
                    $('#response').prepend('Tags changed to: <i>' + (tags.length ? tags.join(', ') : '----') + '</i><hr>');
                },
                beforeTagDelete: function (field, editor, tags, val) {
                    var v = val.toUpperCase();
//                    var q = confirm('Remove tag "' + val + '"?');
//                    if (q)
//                        $('#response').prepend('Tag <i>' + val + '</i> deleted.<hr>');
//                    else
//                        $('#response').prepend('Removal of <i>' + val + '</i> discarded.<hr>');
//                    return q;
                    delete userId[v];
                    console.log(userId);
                }
            });

            $('#demo2').tagEditor({
                initialTags: [],
                placeholder: 'Обьекты',
                onChange: function (field, editor, tags) {
                    $('#response').prepend('Tags changed to: <i>' + (tags.length ? tags.join(', ') : '----') + '</i><hr>');
                },
                beforeTagDelete: function (field, editor, tags, val) {
//                    var q = confirm('Remove tag "' + val.toUpperCase() + '"?');
                    var v = val.toUpperCase();
//                    if (q)
//                        $('#response').prepend('Tag <i>' + val.toUpperCase() + '</i> deleted.<hr>');
//                    else
//                        $('#response').prepend('Removal of <i>' + val.toUpperCase() + '</i> discarded.<hr>');
//                    return q;
                    delete objectID[v];
                    console.log(objectID);
                }
            });

//            $('#tags').tagsInput({width: 'auto'});
            userId = new Object();
            objectID = new Object();
            priveleg = <?php echo $_SESSION['privelege']; ?>;
            group = <?php echo $_GET['gr_id']; ?>;
            frame_hieght();
            refresh_table_user(group);
            refresh_table_object(group);

            $("#addUserGroupButton").click(function () {
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax/users/usersAddGroup.php',
                    data: {users: userId, id: group},
                    success: function (html) {
                        alert(html);
                        refresh_table_user(group);
                        $('#addUserGroup').modal('hide');
                        var tags = $('#demo1').tagEditor('getTags')[0].tags;
                        for (i = 0; i < tags.length; i++) {
                            $('#demo1').tagEditor('removeTag', tags[i]);
                        }
                        $('#search-users').val('');
                        delete userID;
                        console.log(userId);
                    }
                });
                return false;
            });


            $("#addOBjectGroupButton").click(function () {
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax/users/objectsAddGroup.php',
                    data: {objects: objectID, id: group},
                    success: function (html) {
                        alert(html);
                        refresh_table_object(group);
                        $('#addObjectGroup').modal('hide');
                        var tags = $('#demo2').tagEditor('getTags')[0].tags;
                        for (i = 0; i < tags.length; i++) {
                            $('#demo2').tagEditor('removeTag', tags[i]);
                        }
                        $('#searchObjectModal').val('');
                        delete objectID;
                        objectID = new Object();
                        console.log(objectID);
                    }
                });
                return false;
            });



            $("#search-object").keyup(function () {
                refresh_table_object(group);
            });

            $("#search-users").keyup(function () {
                refresh_user_search();
            });

            $('#searchObjectModal').keyup(function () {
                refresh_object_search();
            });

            $('#addUserGroupBtn').click(function () {
                $('#addUserGroup').modal('show');
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax/users/users_view.php',
                    data: {key: $('#search-users').val()},
                    success: function (html) {
                        $('#viewUsers').html(html);
                        $('.addTag').click(function () {
                            var name = $('#name_' + this.id).text();
                            //$('#tags').addTag($('#name_' + this.id).text());
                            $('#demo1').tagEditor('addTag', '' + name + '');

                            if (userId.hasOwnProperty(name)) {
                                console.log('Элемент уже добавлен');
                            } else {
                                userId[name.toUpperCase()] = this.id;
                                console.log(userId);
                            }
                        });
                    }
                });
                return false;
            });

            $('#addObjectGroupBtn').click(function () {
                $('#addObjectGroup').modal('show');
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax/users/object_view.php',
                    data: {key: $('#searchObjectModal').val()},
                    success: function (html) {
                        $('#viewObjects').html(html);
                        $('.addTag').click(function () {
                            var name = $('#name_' + this.id).text();
                            //$('#tags').addTag($('#name_' + this.id).text());
                            $('#demo2').tagEditor('addTag', '' + name + '');

                            if (objectID.hasOwnProperty(name)) {
                                console.log('Элемент уже добавлен');
                            } else {
                                objectID[name.toUpperCase()] = this.id;
                                console.log(objectID);
                            }
                        });
                    }
                });
                return false;
            });

            $('#addUserButton').click(function () {
                var login = $('#AddLogin').val();
                var passwd = $('#AddPasswd').val();

                if (login == '' || passwd == '') {
                    alert("Не заполенено обяаельное поле");
                } else {
                    $.ajax({
                        type: 'POST',
                        chase: false,
                        url: 'ajax/users_addNew.php',
                        data: {login: login, passwd: passwd, group: group, name: $('#AddName').val(), surname: $('#AddSurname').val(), role: $('#AddRole').val()},
                        beforeSend: function () {
                            $('#view_user').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                        },
                        success: function (html) {
                            refresh_table_user(group);
                            $('#addUser').modal('hide');
                            $('#formWorkUsers').trigger('reset');

                        }
                    });
                    return false;
                }


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
