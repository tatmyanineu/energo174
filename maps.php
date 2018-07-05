<?php

include 'db_config.php';
session_start();
$date = date('Y-m-d');
//print_r($_SESSION['err_plc']);

$sql_tickets = pg_query('SELECT DISTINCT
  public.ticket.plc_id
  FROM
  public.ticket
  WHERE
  public.ticket.status < 4');
while ($result = pg_fetch_row($sql_tickets)) {
    $tickets[] = $result[0];
}
unset($sql_tickets);
unset($result);

$sql_coordinats = pg_query('SELECT
  "Places_cnt1"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  "PropPlc_cnt2"."ValueProp",
  "Places_cnt1".plc_id
  FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt2" ON ("Places_cnt1".plc_id = "PropPlc_cnt2".plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Places_cnt1".plc_id = "Tepl"."PlaceGroupRelations".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."PlaceGroupRelations".grp_id = "Tepl"."GroupToUserRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
  WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND
  "PropPlc_cnt1".prop_id = 26 AND
  "PropPlc_cnt2".prop_id = 41 AND
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'');


while ($result = pg_fetch_row($sql_coordinats)) {
    $kl = array_search($result[4], $tickets);
    if ($kl !== false) {
        $arr1[] = array(
            'plc_id' => $result[4],
            'name' => $result[0],
            'adres' => '' . $result[1] . ' ' . $result[2] . '',
            'koord' => $result[3],
            'ticket' => '1'
        );
    } else {
        $arr1[] = array(
            'plc_id' => $result[4],
            'name' => $result[0],
            'adres' => '' . $result[1] . ' ' . $result[2] . '',
            'koord' => $result[3],
            'ticket' => null
        );
    }
}
$main_form = $_SESSION[main_form];

for ($i = 0; $i < count($main_form); $i++) {
    $k = array_search($main_form[$i]['plc_id'], array_column($arr1, 'plc_id'));
    if ($k !== false) {
        if ($arr1[$k][ticket] == null or $arr1[$k][ticket] < 4) {
            $koord[] = array(
                'plc_id' => $arr1[$k][plc_id],
                'name' => $arr1[$k][name],
                'adres' => $arr1[$k][adres],
                'koord' => $arr1[$k][koord],
                'ticket' => $arr1[$k][ticket],
                'marker' => $main_form[$i]['marker']
            );
        } else {
            $koord[] = array(
                'plc_id' => $arr1[$k][plc_id],
                'name' => $arr1[$k][name],
                'adres' => $arr1[$k][adres],
                'koord' => $arr1[$k][koord],
                'ticket' => null,
                'marker' => $main_form[$i]['marker']
            );
        }
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en"><head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta http-equiv="Content-Style-Type" content="text/css"/>
        <meta http-equiv="application-json" content="text/css"/>
        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="css/dashboard.css"/>
        <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script type="text/javascript" src="js/jquery.datetimepicker.js"></script>
        <script src="http://maps.api.2gis.ru/2.0/loader.js?skin=light" data-id="dgLoader"></script>
        <link rel="stylesheet" href="http://2gis.github.io/mapsapi/vendors/Leaflet.markerCluster/MarkerCluster.css" />
        <link rel="stylesheet" href="http://2gis.github.io/mapsapi/vendors/Leaflet.markerCluster/MarkerCluster.Default.css" />
    </head>
    <body>
        <div id="content">
            <!-- Верхний бар -->
            <div class="navbar navbar-default navbar-fixed-top" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">Измерительная система контроля и учета энергоресурсов</a>
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">

                            <li><a href="index.php">Выход</a></li>
                        </ul>
                        <?php
                        if ($_SESSION['privelege'] > 0) {
                            echo '  <form  class="navbar-form navbar-right">
                            <div class = "input-group">
                                    <input type="text" name="searchInput" class="form-control" style="z-index: 0;" autocomplete="off" id="search" placeholder="Поиск..." onkeyup="check();">
                                    <button id="clearSearchForm" type="button" class="close" data-dismiss="alert" onclick="reset1();" style="z-index: 3; margin-top: -27px; margin-right: 8px; visibility: hidden" aria-hidden="true">&times;</button>
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

            <!--Боковое меню -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-md-2 sidebar">
                        <?php include './include/menu.php'; ?> 

                        <ul class="nav nav-sidebar">
                            <li><a>Отфильтровать объекты:</a></li>

                            <li><a><input type="checkbox" name="optionsRadios" id="optionsRadios2" value="markers_enable" checked> Рабочие</a></li>
                            <li><a> <input type="checkbox" name="optionsRadios" id="optionsRadios3" value="markers_disable" checked> Нерабочие</a></li>
                            <li><a><input type="checkbox" name="optionsRadios" id="optionsRadios4" value="markers_warm" checked> Нет Тепла</a></li>
                            <li><a> <input type="checkbox" name="optionsRadios" id="optionsRadios5" value="markers_water" checked> Нет Воды</a></li>
                            <li><a> <input type="checkbox" name="optionsRadios" id="optionsRadios6" value="markers_tickets"> Заявки на обслуживание</a></li>
                            <li><a> <input type="checkbox" name="optionsRadios" id="optionsRadios7" value="marker_not_error" checked> Исключения</a></li>
                        </ul>

                        <ul id="checkIn" class="nav nav-sidebar">

                        </ul>
                    </div>


                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                        <div id="map"></div>


                    </div>
                </div>
            </div>

            <!--Баковое меню -->
    </body>
    <script>
        function frame_hieght() {
            if (parent.document.getElementById('blockrandom') != null) {
                parent.document.getElementById('blockrandom').style.height = '1000px';
                parent.document.getElementById('blockrandom').style.width = '1250px';

            }
        }



        function check() {
            X = document.getElementById('search'); // обращаемся к елементу страницы по ID
            if (X.value != '') { // проверяем поле по регулярному выражению, разрешаем ввод только цифр - \d
                //alert('буквы низя!'); 
                //inp.style.borderColor = 'red'; // краснеем
                document.getElementById('clearSearchForm').style.visibility = 'visible';
                //hideMarkers();
                return false;
            } else {
                //inp.style.borderColor = 'green'; // зеленеем
                document.getElementById('clearSearchForm').style.visibility = 'hidden';
                showMarkers(markers);
                return true;
            }
        }
        ;
        function reset1() {
            //alert('reset');
            markers_search[g].removeFrom(map);
            map.removeLayer(markers_search[g]);
            showMarkers(markers);
            document.getElementById('clearSearchForm').style.visibility = 'hidden';
            $("input[type=text]").val('');
            $('#checkIn').html('');
        }
        ;

        function object_search(search, g) {

            if (search != "") {
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax_search_map.php',
                    data: 'search=' + search,
                    success: function (data) {
                        //hideMarkers()
                        console.log(data);
                        markers_search[g] = DG.featureGroup();
                        for (var i = 0; i < data.length; i++) {
                            console.log(data[i]);
                            var title = "<a target='_blank' href=object.php?id_object='" + data[i].id + "'>" + data[i].name + "</a></br> " + data[i].addr;
                            var search_tochka = DG.marker([data[i].koord1, data[i].koord2], {icon: myIcon}, {title: title});
                            search_tochka.bindPopup(title);
                            search_tochka.addTo(markers_search[g]);
                            var li = document.createElement('li');
                            li.innerHTML = '<a><span onclick="clickObject(' + data[i].koord1 + ', ' + data[i].koord2 + ')">' + data[i].name + '</span></a>';
                            ul = document.getElementById('checkIn');
                            ul.appendChild(li);
                        }
                        markers_search[g].addTo(map);
                    }, error: function (error) {
                        console.log('error', error);
                    }
                });

                return false;
            }
        }


        $(document).ready(function () {
            frame_hieght();
            $('#reload_alarm').click(function () {
                $.ajax({
                    type: 'POST',
                    chase: false,
                    url: 'ajax_reload_error.php',
                    success: function (html) {
                        $('#reload_alarm').html(html);
                    }
                });
                return false;
            })



            DG.then(function () {
                g = 0;
                markers = DG.featureGroup();
                markers = DG.featureGroup();
                markers_enable = DG.featureGroup();
                markers_warm = DG.featureGroup();
                markers_water = DG.featureGroup();
                markers_disable = DG.featureGroup();
                marker_not_error = DG.featureGroup();
                markers_search = DG.featureGroup();
                markers_tickets = DG.featureGroup();
                map = DG.map('map', {
                    center: [55.157101, 61.401215],
                    zoom: 11
                });

                myIcon = DG.icon({
                    iconUrl: 'img/DGCustomization__marker.png',
                    iconSize: [22, 34]
                });
                myIconOk = DG.icon({
                    iconUrl: 'img/Green.png',
                    iconSize: [22, 34]
                });
                myIconError = DG.icon({
                    iconUrl: 'img/Black.png',
                    iconSize: [22, 34]
                });
                myIconNotError = DG.icon({
                    iconUrl: 'img/fiv.png',
                    iconSize: [22, 34]
                });
                myIconWarm = DG.icon({
                    iconUrl: 'img/Red.png',
                    iconSize: [22, 34]
                });
                myIconWater = DG.icon({
                    iconUrl: 'img/Orange.png',
                    iconSize: [22, 34]
                });
                myIconTickets = DG.icon({
                    iconUrl: 'img/tickets.png',
                    iconSize: [22, 34]
                });
                var data = <?php echo json_encode($koord, JSON_UNESCAPED_UNICODE); ?>;
                console.log(data);
                //console.log(data.length);

                for (var i = 0; i < data.length; i++) {
                    //console.log(data[i].koord);
                    var k = data[i].koord;
                    k = k.split(', ');
                    //console.log(k[0]+" -------------- c"+k[1]);
                    if (data[i].marker == 1) {
                        var title = "<b>" + data[i].name + "</b><br> <b>Адрес:</b>" + data[i].adres + "<br> <b><a id='goto_object' href='object.php?id_object=" + data[i].plc_id + "'>Посмотреть архивы</a> </b>";
                        var tochka = DG.marker([k[0], k[1]], {icon: myIconOk}, {title: title});
                        tochka.bindPopup(title, {
                            maxWidth: 350,
                            sprawling: true
                        });
                        tochka.addTo(markers_enable);
                    } else if (data[i].marker == 4) {
                        var title = "<b>" + data[i].name + "</b><br> <b>Адрес:</b>" + data[i].adres + "<br> <b><a id='goto_object' href='object.php?id_object=" + data[i].plc_id + "'>Посмотреть архивы</a> </b>";
                        var tochka = DG.marker([k[0], k[1]], {icon: myIconError}, {title: title});
                        tochka.bindPopup(title, {
                            maxWidth: 350,
                            sprawling: true
                        });
                        tochka.addTo(markers_disable);
                    } else if (data[i].marker == 3) {
                        var title = "<b>" + data[i].name + "</b><br> <b>Адрес:</b>" + data[i].adres + "<br> <b><a id='goto_object' href='object.php?id_object=" + data[i].plc_id + "'>Посмотреть архивы</a> </b>";
                        var tochka = DG.marker([k[0], k[1]], {icon: myIconWarm}, {title: title});
                        tochka.bindPopup(title, {
                            maxWidth: 350,
                            sprawling: true
                        });
                        tochka.addTo(markers_warm);
                    } else if (data[i].marker == 2) {
                        var title = "<b>" + data[i].name + "</b><br> <b>Адрес:</b>" + data[i].adres + "<br> <b><a id='goto_object' href='object.php?id_object=" + data[i].plc_id + "'>Посмотреть архивы</a> </b>";
                        var tochka = DG.marker([k[0], k[1]], {icon: myIconWater}, {title: title});
                        tochka.bindPopup(title, {
                            maxWidth: 350,
                            sprawling: true
                        });
                        tochka.addTo(markers_water);
                    } else if (data[i].marker == 8) {
                        var title = "<b>" + data[i].name + "</b><br> <b>Адрес:</b>" + data[i].adres + "<br> <b><a id='goto_object' href='object.php?id_object=" + data[i].plc_id + "'>Посмотреть архивы</a> </b>";
                        var tochka = DG.marker([k[0], k[1]], {icon: myIconNotError}, {title: title});
                        tochka.bindPopup(title, {
                            maxWidth: 350,
                            sprawling: true
                        });
                        tochka.addTo(marker_not_error);
                    }
                    if (data[i].ticket != null) {
                        var title = "<b>" + data[i].name + "</b><br> <b>Адрес:</b>" + data[i].adres + "<br> <b><a id='goto_object' href='object.php?id_object=" + data[i].plc_id + "'>Посмотреть архивы</a> </b>";
                        var tochka = DG.marker([k[0], k[1]], {icon: myIconTickets}, {title: title});
                        tochka.bindPopup(title, {
                            maxWidth: 350,
                            sprawling: true
                        });
                        tochka.addTo(markers_tickets);
                    }
                    tochka.addTo(markers);
                }

                markers_disable.addTo(map);
                markers_enable.addTo(map);
                markers_warm.addTo(map);
                markers_water.addTo(map);
                marker_not_error.addTo(map);

                $("input:checkbox").change(function () {
                    var $input = $(this);
                    var layer = $(this).val();
                    if ($input.prop("checked")) {
                        console.log('g= ' + g);
                        if (g != 0) {
                            $('#checkIn').html('');
                            markers_search[g].removeFrom(map);
                            map.removeLayer(markers_search[g]);

                        }
                        switch (layer) {
                            case "markers_disable":
                                markers_disable.addTo(map);
                                break;
                            case "markers_enable":
                                markers_enable.addTo(map);
                                break;
                            case "markers_warm":
                                markers_warm.addTo(map);
                                break;
                            case "markers_water":
                                markers_water.addTo(map);
                                break;
                            case "markers_tickets":
                                markers_tickets.addTo(map);
                                break;
                            case "marker_not_error":
                                marker_not_error.addTo(map);
                                break;
                        }
                    } else {
                        switch (layer) {
                            case "markers_disable":
                                markers_disable.removeFrom(map);
                                break;
                            case "markers_enable":
                                markers_enable.removeFrom(map);
                                break;
                            case "markers_warm":
                                markers_warm.removeFrom(map);
                                break;
                            case "markers_water":
                                markers_water.removeFrom(map);
                                break;
                            case "markers_tickets":
                                markers_tickets.removeFrom(map);
                                break;
                            case "marker_not_error":
                                marker_not_error.removeFrom(map);
                                break;
                        }
                    }
                });

                function showMarkers(Layers) {
                    Layers.addTo(map);
                    // map.fitBounds(markers.getBounds());
                }

                function hideMarkers() {
                    markers.removeFrom(map);
                    markers_disable.removeFrom(map);
                    markers_enable.removeFrom(map);
                    markers_search.removeFrom(map);
                    markers_warm.removeFrom(map);
                    markers_water.removeFrom(map);
                    markers_tickets.removeFrom(map);
                    marker_not_error.removeFrom(map);
                    map.removeLayer(markers_search);
                }
                /*
                 $("input[name=optionsRadios]:checkbox").change(function () {
                 if (jQuery("#optionsRadios1").prop('checked')) {
                 hideMarkers();
                 $('#checkIn').html('');
                 showMarkers(markers);
                 console.log('g= ' + g);
                 if (g != 0) {
                 
                 markers_search[g].removeFrom(map);
                 map.removeLayer(markers_search[g]);
                 
                 }
                 }
                 if (jQuery("#optionsRadios2").prop('checked')) {
                 hideMarkers();
                 $('#checkIn').html('');
                 showMarkers(markers_enable);
                 console.log('g= ' + g);
                 if (g != 0) {
                 
                 markers_search[g].removeFrom(map);
                 map.removeLayer(markers_search[g]);
                 
                 }
                 }
                 
                 if (jQuery("#optionsRadios3").prop('checked')) {
                 hideMarkers();
                 showMarkers(markers_disable);
                 console.log('g= ' + g);
                 var id_err = 4;
                 if (g != 0) {
                 
                 markers_search[g].removeFrom(map);
                 map.removeLayer(markers_search[g]);
                 
                 }
                 $.ajax({
                 type: 'POST',
                 chase: false,
                 url: 'ajax_maps_err.php',
                 data: 'id_err=' + id_err,
                 beforeSend: function () {
                 $('#checkIn').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                 },
                 success: function (html) {
                 $('#checkIn').html(html);
                 }
                 });
                 return false;
                 }
                 
                 if (jQuery("#optionsRadios4").prop('checked')) {
                 hideMarkers();
                 $('#checkIn').html('');
                 showMarkers(markers_warm);
                 console.log('g= ' + g);
                 var id_err = 3;
                 if (g != 0) {
                 
                 markers_search[g].removeFrom(map);
                 map.removeLayer(markers_search[g]);
                 
                 }
                 $.ajax({
                 type: 'POST',
                 chase: false,
                 url: 'ajax_maps_err.php',
                 data: 'id_err=' + id_err,
                 beforeSend: function () {
                 $('#checkIn').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                 },
                 success: function (html) {
                 $('#checkIn').html(html);
                 }
                 });
                 return false;
                 }
                 if (jQuery("#optionsRadios5").prop('checked')) {
                 hideMarkers();
                 $('#checkIn').html('');
                 showMarkers(markers_water);
                 console.log('g= ' + g);
                 var id_err = 2;
                 if (g != 0) {
                 
                 markers_search[g].removeFrom(map);
                 map.removeLayer(markers_search[g]);
                 
                 }
                 $.ajax({
                 type: 'POST',
                 chase: false,
                 url: 'ajax_maps_err.php',
                 data: 'id_err=' + id_err,
                 beforeSend: function () {
                 $('#checkIn').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                 },
                 success: function (html) {
                 $('#checkIn').html(html);
                 }
                 });
                 return false;
                 
                 }
                 
                 
                 if (jQuery("#optionsRadios6").prop('checked')) {
                 hideMarkers();
                 $('#checkIn').html('');
                 showMarkers(markers_tickets);
                 console.log('g= ' + g);
                 if (g != 0) {
                 
                 markers_search[g].removeFrom(map);
                 map.removeLayer(markers_search[g]);
                 
                 }
                 }
                 if (jQuery("#optionsRadios7").prop('checked')) {
                 hideMarkers();
                 $('#checkIn').html('');
                 showMarkers(marker_not_error);
                 console.log('g= ' + g);
                 if (g != 0) {
                 
                 markers_search[g].removeFrom(map);
                 map.removeLayer(markers_search[g]);
                 
                 }
                 }
                 });
                 */

                function enabled_layers() {
                    $("input[name=optionsRadios]:checkbox:checked").each(function () {
                        console.log(this.id);
                        var layer = $(this).val();
                        switch (layer) {
                            case "markers_disable":
                                markers_disable.addTo(map);
                                break;
                            case "markers_enable":
                                markers_enable.addTo(map);
                                break;
                            case "markers_warm":
                                markers_warm.addTo(map);
                                break;
                            case "markers_water":
                                markers_water.addTo(map);
                                break;
                            case "markers_tickets":
                                markers_tickets.addTo(map);
                                break;
                            case "marker_not_error":
                                marker_not_error.addTo(map);
                                break;
                        }
                    });
                }

                function disabled_layers() {
                    $("input[name=optionsRadios]:checkbox:checked").each(function () {
                        console.log(this.id);
                        var layer = $(this).val();
                        switch (layer) {
                            case "markers_disable":
                                markers_disable.removeFrom(map);
                                break;
                            case "markers_enable":
                                markers_enable.removeFrom(map);
                                break;
                            case "markers_warm":
                                markers_warm.removeFrom(map);
                                break;
                            case "markers_water":
                                markers_water.removeFrom(map);
                                break;
                            case "markers_tickets":
                                markers_tickets.removeFrom(map);
                                break;
                            case "marker_not_error":
                                marker_not_error.removeFrom(map);
                                break;
                        }
                    })
                }

                $('#formSearch').on("click", function () {
                    if (g != 0) {
                        markers_search[g].removeFrom(map);
                        map.removeLayer(markers_search[g]);
                    }
                    g++;
                    console.log('g= ' + g);
                    var str_search = $('#search').val();
                    $('#checkIn').html('');
                    disabled_layers();


                    object_search(str_search, g);
                    return false;
                });
                //console.log(n);
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
