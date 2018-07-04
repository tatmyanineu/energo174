<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">

        <title>Диспетчер mode</title>
        <!-- Latest compiled and minified CSS -->
        <script
            src="http://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>

        <link href="modules/DataTables-1.10.16/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="modules/css/style.css" rel="stylesheet" type="text/css"/>
        <script src="modules/DataTables-1.10.16/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="modules/DataTables-1.10.16/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="modules/DataTables-1.10.16/dataTables.buttons.min.js" type="text/javascript"></script>
        <script src="modules/DataTables-1.10.16/buttons.flash.min.js" type="text/javascript"></script>
        <script src="modules/DataTables-1.10.16/pdfmake.min.js" type="text/javascript"></script>
        <script src="modules/DataTables-1.10.16/buttons.html5.min.js" type="text/javascript"></script>
        <script src="modules/DataTables-1.10.16/jszip.min.js" type="text/javascript"></script>
        <link href="modules/DataTables-1.10.16/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
        <script src="modules/DataTables-1.10.16/dataTables.bootstrap.min.js" type="text/javascript"></script>
        <link href="modules/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="modules/bootstrap-3.3.7-dist/js/bootstrap.js" type="text/javascript"></script>

        <script src="modules/DataTables-1.10.16/date-de.js" type="text/javascript"></script>

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
                    <h1 class="page-header">
                        <div id="center_h1">Режим диспетчера
                        </div>
                    </h1>
                    <div id="example_wrapper" ><div class="row">

                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="example">
                                        <thead><tr role="row">
                                                <th>№</th>
                                                <th>Название</th>
                                                <th>Тип проверки</th>
                                                <th>Дата</th>
                                                <th>Коментаций ошибки</th>
                                                <th>Статус</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script type="text/javascript" charset="utf-8">

            $(document).ready(function () {

                var table = $('#example').DataTable({
                    paging: false,
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
                    columnDefs: [
                        {type: 'de_date', targets: 3}
                    ],
                    order: [[3, "desc"]],
                    ajax: {
                        type: "POST",
                        url: "ajax/controllers/incidents.php",
                    },
                    columns: [
                        {data: "id", searchable: false},
                        {data: "name"},
                        {data: "incedent"},
                        {data: "date", searchable: false},
                        {data: "comment", searchable: false},
                        {data: "view", searchable: false},
                        {data: null,
                            searchable: false,
                            fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).html("<a href='controller_log.php?plc=" + oData.plc_id + "&date=" + oData.date + "&inc=" + oData.inc_id + "'><span class='glyphicon glyphicon-eye-open'></span></a>");
                            }
                        },
                    ]
                });
                $('#example')
                        .removeClass('display')
                        .addClass('table table-striped table-bordered');

                $('.nav-sidebar li a').each(function () {
                    var location = window.location.href;
                    var link = this.href;
                    if (location == link) {
                        $(this).parent('li').addClass("active");
                    }

                });
            });
        </script>



    </body></html>