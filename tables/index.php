<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">

        <title>Справочник ФИАС объектов</title>
        <!-- Latest compiled and minified CSS -->
        <script
            src="http://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>


        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">
        <link href="../modules/css/style.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">


        <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
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
                    <?php include '../include/menu.php'; ?>

                    <ul class = "nav nav-sidebar">
                        <li><a href="#" id="load_dog"><span class="glyphicon glyphicon-import"></span> Связать договор МУП  </a></li>
                        <li><a href="#" id="load_fias"><span class="glyphicon glyphicon-export"></span> Связать ФИАС(api)   </a></li>

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

                    <div id="example_wrapper" ><div class="row">

                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="example">
                                        <thead><tr role="row">
                                                <th>№</th>
                                                <th>plc_id</th>
                                                <th>Название</th>
                                                <th>Район</th>
                                                <th>Адрес</th>
                                                <th>ФИАС</th>
                                                <th>Договор</th>
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

            var data_property = function (id) {
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'view_property.php',
                    data: {id: id},
                    success: function (html) {
                        $('#view_prop').html(html);
                    }
                });
                return false;
            }


            $(document).ready(function () {

                $('#load_dog').click(function () {
                    $.ajax({
                        type: 'POST',
                        chashe: false,
                        url: 'ajax/fias_cdog.php',
                        success: function (html) {
                            alert(html);
                            table.ajax.reload();
                        }
                    });
                    return false;
                });


                var table = $('#example').DataTable({
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
                    "ajax": {
                        type: "POST",
                        url: "fias_table.php",
                    },
                    columns: [
                        {data: "num", searchable: false},
                        {data: "plc_id", searchable: false},
                        {data: "name",
                            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).html("<a href='../object.php?id_object=" + oData.plc_id + "'>" + oData.name + "</a>");
                            }
                        },
                        {data: "dist"},
                        {data: "adr"},
                        {data: "fias", searchable: false},
                        {data: "cdog"},
                        {data: null,
                            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).html("<button id='" + oData.plc_id + "' class='btn btn-primary btn fias_id'><span class='glyphicon glyphicon-pencil'></span>...</button>");
                            }
                        }

                    ]
                });
                $('#example').on('click', 'button', function (e) {
                    window.open('edit_property.php?plc_id=' + this.id + '');
//                    data_property(this.id);
//                    $('#editProprtyModal').modal('show');
                    //alert(this.id);
                });
//                $('a.toggle-vis').on('click', function (e) {
//                    e.preventDefault();
//                    // Get the column API object
//                    var column = table.column($(this).attr('data-column'));
//                    // Toggle the visibility
//                    column.visible(!column.visible());
//                });

                $('#example')
                        .removeClass('display')
                        .addClass('table table-striped table-bordered');
            });
        </script>

        <script type="text/javascript">
            // For demo to fit into DataTables site builder...

        </script>

    </body></html>