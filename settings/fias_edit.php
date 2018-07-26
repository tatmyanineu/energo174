<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">

        <title>Справочник ФИАС объектов</title>
        <!-- Latest compiled and minified CSS -->
        <script
            src="http://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>

        <link href="../modules/DataTables-1.10.16/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../modules/css/style.css" rel="stylesheet" type="text/css"/>
        <script src="../modules/DataTables-1.10.16/jquery.dataTables.min.js" type="text/javascript"></script>

        <script src="../modules/DataTables-1.10.16/dataTables.buttons.min.js" type="text/javascript"></script>
        <script src="../modules/DataTables-1.10.16/buttons.flash.min.js" type="text/javascript"></script>
        <script src="../modules/DataTables-1.10.16/pdfmake.min.js" type="text/javascript"></script>
        <script src="../modules/DataTables-1.10.16/buttons.html5.min.js" type="text/javascript"></script>
        <script src="../modules/DataTables-1.10.16/jszip.min.js" type="text/javascript"></script>
        <link href="../modules/DataTables-1.10.16/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
        <script src="../modules/DataTables-1.10.16/dataTables.bootstrap.min.js" type="text/javascript"></script>
        <link href="../modules/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="../modules/bootstrap-3.3.7-dist/js/bootstrap.js" type="text/javascript"></script>

        <link href="../modules/css/dashboard.css" rel="stylesheet" type="text/css"/>
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
                            <li><a id="forBrand" href="#">Пользователь</a></li>
                            <li><a id="forBrand" href="index.php">Выход</a></li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <!--Боковое меню -->
                <div class="col-sm-3 col-md-2 sidebar">
                    <?php include 'include/menu.php'; ?>

                    <ul class = "nav nav-sidebar">
                        <li><a href="#" id="load_dog"><span class="glyphicon glyphicon-import"></span> Связать договор МУП  </a></li>
                        <li><a href="#" id="load_fias" class="disabled"><span class="glyphicon glyphicon-export"></span> Связать ФИАС(api)   </a></li>
                        <li><a href="#" id="del_duble"><span class="glyphicon glyphicon-remove"></span> Удалить дубликаты   </a></li>

                    </ul>

                    <!--                    <ul class = "nav nav-sidebar">
                                            <li><a href="#"><span class="glyphicon glyphicon-th-list"></span> Представление таблицы  </a></li>
                                            <li><a href="#" class="toggle-vis" data-column="1"><span class=""></span> plc_id  </a></li>
                                            <li><a href="#" class="toggle-vis" data-column="2"><span class=""></span> Название  </a></li>
                                            <li><a href="#" class="toggle-vis" data-column="3"><span class=""></span> Район  </a></li>
                                            <li><a href="#" class="toggle-vis" data-column="4"><span class=""></span> Адрес  </a></li>
                                            <li><a href="#" class="toggle-vis" data-column="5"><span class=""></span> ФИАС  </a></li>
                                            <li><a href="#" class="toggle-vis" data-column="6"><span class=""></span> Договор  </a></li>
                                        </ul>-->
                </div>


                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">  


                    <div class="modal fade bs-example-modal-md" id="editProprtyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Редактировать свойства</h4>
                                </div>
                                <div class="modal-body" >
                                    <div id="view_prop">

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                    <button type="button" class="btn btn-primary" id="editPropertySubmit">Сохранить</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#home">Обьекты: Справочник - ФИАС</a></li>
                                    <li><a data-toggle="tab" href="#menu1">Обьекты: Сводная таблица</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="home" class="tab-pane fade in active" style="margin-top: 30px;">
                                        <table id="example"></table>
                                    </div>
                                    <div id="menu1" class="tab-pane fade" style="margin-top: 30px;">
                                        <table id="example2"></table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div id="tableDiv"></div>
                </div>
            </div>
        </div>


        <script type="text/javascript" charset="utf-8">



            $(document).ready(function () {

                $.ajax({
                    "url": "ajax/fias_prop/fias_table.php",
                    "dataType": "json",
                    "success": function (json) {
                        console.log(json.columns);
                        console.log(json.data);


                        var table1 = $('#example').DataTable({
                            dom: 'Bfrtip',
                            buttons: [
                                'excel'
                            ],
                            paging: false,
                            "oLanguage": {
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
                            data: json.data,
                            columnDefs: [
                                {
                                    targets: [2],
                                    render: function (data, type, columns, meta) {
                                        data = "<a href='../object.php?id_object=" + columns.plc + "'>" + columns.name + "<a>";
                                        return data;
                                    }

                                },
                                {

                                    targets: [7],
                                    render: function (data, type, columns, meta) {
                                        data = "<button id='" + columns.plc + "' class='btn btn-primary btn fias_id'><span class='glyphicon glyphicon-pencil'></span>...</button>";
                                        return data;
                                    },
                                }
                            ]
                        });
                    }

                })

                $.ajax({
                    "url": "ajax/fias_prop/fias_table_prop.php",
                    "dataType": "json",
                    "success": function (json) {
                        console.log(json.columns);
                        console.log(json.data);


                        var table2 = $('#example2').DataTable({
                            dom: 'Bfrtip',
                            buttons: [
                                'excel'
                            ],
                            paging: false,
                            "oLanguage": {
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
                            data: json.data,
                            columnDefs: [
                                {
                                    targets: [2],
                                    render: function (data, type, columns, meta) {
                                        data = "<a href='../object.php?id_object=" + columns.plc + "'>" + columns.name + "<a>";
                                        return data;
                                    }

                                }
                            ]
                        });
                    }

                })



                $('#load_dog').click(function () {
                    $.ajax({
                        type: 'POST',
                        chashe: false,
                        url: 'ajax/fias_prop/fias_cdog.php',
                        success: function (html) {
                            alert(html);
                        }
                    });
                    return false;
                });

                $('#del_duble').click(function () {
                    $.ajax({
                        type: 'POST',
                        chashe: false,
                        url: 'ajax/fias_prop/fias_delete_duble.php',
                        success: function (html) {
                            alert(html);
                        }
                    });
                    return false;
                });


                $('.reload_table').click(function () {
                    if (this.id == "table_first") {
                        this.id = "table_second";
                        table.hide();
                        $(this).text("Таблица Объект -> Параметры");
                        $('#example').DataTable().destroy();
                        ;
                        //second_table();
                    } else {
                        this.id = "table_first";

                        $(this).text("Таблица Объект -> ФИАС");
                        //table_first();
                    }

                });
//                var table2 = $('#table2').DataTable({
//                    "ajax": "ajax/fias_prop/fias_table.php",
//                    "columns": column
//                });




//
//                var table = $('#example').DataTable({
//                    dom: 'Bfrtip',
//                    buttons: [
//                        'excel'
//                    ],
//                    paging: false,
//                    "oLanguage": {
//                        "sLengthMenu": "Отображено _MENU_ записей на страницу",
//                        "sSearch": "Поиск:",
//                        "sZeroRecords": "Ничего не найдено - извините",
//                        "sInfo": "Показано с _START_ по _END_ из _TOTAL_ записей",
//                        "sInfoEmpty": "Показано с 0 по 0 из 0 записей",
//                        "sInfoFiltered": "(filtered from _MAX_ total records)",
//                        "oPaginate": {
//                            "sFirst": "Первая",
//                            "sLast": "Посл.",
//                            "sNext": "След.",
//                            "sPrevious": "Пред.",
//                        }
//                    },
//                    "ajax": {
//                        type: "POST",
//                        url: "ajax/fias_prop/fias_table.php",
//                    },
//                    columns: [
//                        {data: "num", searchable: false},
//                        {data: "plc_id"},
//                        {data: "name",
//                            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
//                                $(nTd).html("<a href='../object.php?id_object=" + oData.plc_id + "'>" + oData.name + "</a>");
//                            }
//                        },
//                        {data: "dist"},
//                        {data: "adr"},
//                        {data: "fias", searchable: false},
//                        {data: "cdog"},
//                        {data: null,
//                            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
//                                $(nTd).html("<button id='" + oData.plc_id + "' class='btn btn-primary btn fias_id'><span class='glyphicon glyphicon-pencil'></span>...</button>");
//                            }
//                        }
//
//                    ]
//                });
                $('#example').on('click', 'button', function (e) {
                    window.open('edit_property.php?plc_id=' + this.id + '');
//                    data_property(this.id);
//                    $('#editProprtyModal').modal('show');
                    //alert(this.id);
                });
////                $('a.toggle-vis').on('click', function (e) {
////                    e.preventDefault();
////                    // Get the column API object
////                    var column = table.column($(this).attr('data-column'));
////                    // Toggle the visibility
////                    column.visible(!column.visible());
////                });

                $('#example')
                        .removeClass('display')
                        .addClass('table table-striped table-bordered');
                $('#example2')
                        .removeClass('display')
                        .addClass('table table-striped table-bordered');
            });
        </script>

        <script type="text/javascript">
            // For demo to fit into DataTables site builder...

        </script>

    </body></html>