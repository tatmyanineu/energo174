<?php
include '../db_config.php';
$date = date('Y-m-d');
session_start();

$sql = pg_query('SELECT 
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp"
FROM
  "Tepl"."PropPlc_cnt" "PropPlc_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("PropPlc_cnt1".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
WHERE
  "Tepl"."Places_cnt".plc_id = ' . $_GET['plc_id'] . ' AND 
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26');
$name = pg_fetch_row($sql);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>
    <a href="../modules/bootstrap-3.3.7-dist/fonts/glyphicons-halflings-regular.woff2"></a>
    <link href="../modules/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="../modules/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
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
                <?php include '../include/menu.php'; ?>
            </div>
            <!--Боковое меню -->

            <!--Контент -->
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <h1 class="page-header">
                    <div id="center_h1"><?php echo '<h2>' . $name[0] . ' ( ' . $name[1] . ', ' . $name[2] . ' )</h2>' ?>
                    </div>
                </h1>
                <div id="fias_data"></div>

                <div id="prp_data" ></div>
            </div>
        </div>
    </div>

    <!--Баковое меню -->

</body>

<script src="http://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
crossorigin="anonymous"></script>
<script src="../modules/bootstrap-3.3.7-dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../js/jquery.datetimepicker.js" type="text/javascript"></script>
<script src="../modules/js/jquery.maskedinput.min.js" type="text/javascript"></script>
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

    var view_coutProp = function (prp, e) {
        e.preventDefault()
        $.ajax({
            type: 'POST',
            chashe: false,
            url: 'ajax/fias_prop/fias_link_number.php',
            dataType: "json",
            data: {prp: prp},
            success: function (html) {
                //(html == "") ? alert("Ссылки в поле CONTRAKT_ID не найдено") : $('#cdog').val(html);

                console.log(html.numb);
                $("#counter_numb_" + prp).val(html.numb);
                console.log(html.date);
                $("#date_" + prp).val(html.date);
                $('#' + prp).append(" " + html.name);
            }
        });
        return false;
    }

    var view_cdog = function (plc) {

        $.ajax({
            type: 'POST',
            chashe: false,
            url: 'ajax/fias_prop/fias_link_cdog.php',
            data: {plc: plc},
            success: function (html) {
                (html == "") ? alert("Ссылки в поле CONTRAKT_ID не найдено") : $('#cdog').val(html);

            }
        });
        return false;
    }

    var prp_data_refresh = function (plc) {
        $.ajax({
            type: 'POST',
            chashe: false,
            url: 'ajax/fias_prop/fias_prp.php',
            data: {plc: plc},
            success: function (html) {
                $('#prp_data').html(html);
                $('.date').mask('99.99.9999');
                $('.link_prp').click(function () {
                    //alert(this.id);
                    view_coutProp(this.id, event);
                });
            }
        });
        return false;
    }

    var fias_data_refresh = function (plc) {
        $.ajax({
            type: 'POST',
            chashe: false,
            url: 'ajax/fias_prop/fias_data.php',
            data: {plc: plc},
            success: function (html) {
                $('#fias_data').html(html);

                $('.link_cid').click(function () {
                    view_cdog(this.id);
                });
            }
        });
        return false;
    }

    $(document).ready(function () {
        var plc_id = <?php echo $_GET['plc_id']; ?>;
        fias_data_refresh(plc_id);

        prp_data_refresh(plc_id);

        $(document).on('click', '.btn-save-prop', function () {
            var i = 0;
            var jsonObj = [];
            var value = $('h2').each(function () {
                var array = new Object();
                console.log(this.id);
                array.prp = this.id;
                array.id_connect = $('#id_connect_' + this.id).val();
                array.numb = $('#counter_numb_' + this.id).val();
                array.date = $('#date_' + this.id).val();
                array.plc = plc_id;
                jsonObj.push(array);
                i++;
            })

            $.ajax({
                type: 'POST',
                chashe: false,
                url: 'ajax/fias_prop/fias_add_prp_prop.php',
                data: {json: jsonObj},
                success: function (html) {
                    alert(html);
                }
            });

            console.log(jsonObj);
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
