<?php
include 'db_config.php';
session_start();
$id_object = $_GET['id_object'];
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
        <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script src="http://maps.api.2gis.ru/2.0/loader.js?pkg=full" data-id="dgLoader"></script>
        <script type="text/javascript" src="js/npm.js"></script>
        <script type="text/javascript" src="js/jquery.datetimepicker.js"></script>
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
                        <?php include './include/menu.php';?>

                        <ul class="nav nav-sidebar">
                            <li><a href="object_device.php?id_object=<?php echo $id_object; ?>" ><span class="glyphicon glyphicon-chevron-left"></span> Назад  </a></li>
                        </ul>
                    </div>
                    <!--Боковое меню -->

                    <!--Контент -->
                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

                        <?php
                        $sql_cootd = pg_query('SELECT 
                                  "Tepl"."Places_cnt"."Name",
                                  "Places_cnt1"."Name",
                                  "Places_cnt2"."Name",
                                  "Tepl"."PropPlc_cnt"."ValueProp",
                                  "PropPlc_cnt1"."ValueProp",
                                  "PropPlc_cnt2"."ValueProp"
                                FROM
                                  "Tepl"."Places_cnt" "Places_cnt1"
                                  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                  INNER JOIN "Tepl"."Places_cnt" "Places_cnt2" ON ("Places_cnt1".plc_id = "Places_cnt2".place_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt2".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt2".plc_id = "PropPlc_cnt1".plc_id)
                                  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt2" ON ("Places_cnt2".plc_id = "PropPlc_cnt2".plc_id)
                                WHERE
                                  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                                  "PropPlc_cnt1".prop_id = 26 AND 
                                  "Places_cnt2".plc_id =' . $_SESSION['id_object'] . '');

                        $result_cootd = pg_fetch_row($sql_cootd);
                        echo '<ol class="breadcrumb">
                        <li>' . $result_cootd[1] . '</a></li>
                        <li>' . $result_cootd[2] . '</a></li>
                        <li> ул. ' . $result_cootd[3] . '</li>
                        <li> д. ' . $result_cootd[4] . '</li>
                      </ol>'
                        ?>  
                        <h4 class=""></h4>
                        <label class="checkbox"></label><label class="checkbox"></label>
                        <h2 class="text-center page-header"><span class="glyphicon glyphicon-pencil"></span> Редактирование узла учета воды</h2>
                        <div id="center_h1"></div>
                        <div  id="view_archive" class="row ">
                            <div class="col-lg-12 col-md-12 col-xs-12">
                                <div id="resours" style="margin-top: 30px">
                                    <?php
                                    $sql_sens_name = pg_query('SELECT 
                                        "Tepl"."TypeSensor".sen_id,
                                        "Tepl"."TypeSensor"."Name"
                                      FROM
                                        "Tepl"."TypeSensor"');
                                    while ($result = pg_fetch_row($sql_sens_name)) {
                                        $sens_cnt[] = array(
                                            'sens_id' => $result[0],
                                            'sens_name' => $result[1]
                                        );
                                    }

                                    $sql_max_id_log = pg_query('SELECT 
                                        MAX(public.logs.id) AS field_1
                                      FROM
                                        public.logs');

                                    $id_log = pg_fetch_result($sql_max_id_log, 0, 0);


                                    $sql_res_plc = pg_query('SELECT DISTINCT 
                                            ("Tepl"."ParamResPlc_cnt"."ParamRes_id") AS "FIELD_1",
                                            "Tepl"."ParametrResourse"."Name",
                                            "Tepl"."Resourse_cnt"."Name",
                                            "Tepl"."ParamResPlc_cnt".prp_id,
                                            "Tepl"."ParamResPlc_cnt"."Comment",
                                            "Tepl"."Device_cnt"."Numbe"
                                          FROM
                                            "Tepl"."ParamResPlc_cnt"
                                            INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                            INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
                                            INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                                            INNER JOIN "Tepl"."PointRead" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."PointRead".prp_id)
                                            INNER JOIN "Tepl"."Device_cnt" ON ("Tepl"."PointRead".dev_id = "Tepl"."Device_cnt".dev_id)
                                          WHERE
                                            "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND 
                                            "Tepl"."Resourse_cnt"."Name" = \'ХВС\' OR 
                                            "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND 
                                            "Tepl"."Resourse_cnt"."Name" = \'ГВС\'
                                          ORDER BY
                                            "Tepl"."ParametrResourse"."Name"');

                                    while ($res_resours = pg_fetch_row($sql_res_plc)) {
                                        $resours[] = array(
                                            'prp_id' => $res_resours[3],
                                            'name_res' => $res_resours[2],
                                            'name_group' => $res_resours[1],
                                            'diametr' => $res_resours[4],
                                            'number'=>$res_resours[5]
                                        );
                                    }


                                    unset($_SESSION['array_edit']);
                                    for ($i = 0; $i < count($resours); $i++) {
                                        echo "<div class = 'row'>"
                                        . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                        . "<h4><b>Водосчетчик (" . $resours[$i][name_res] . ": " . $resours[$i][name_group] . ")<br> Регистратор: ".$resours[$i][number]."</b></h4>"
                                        . "</div>";

                                        $sql_prp = pg_query('SELECT 
                                                "Tepl"."Sensor_cnt".sen_id,
                                                "Tepl"."Sensor_cnt".s_id,
                                                "Tepl"."Sensor_cnt"."Comment"
                                              FROM
                                                "Tepl"."Sensor_cnt"
                                              WHERE
                                                "Tepl"."Sensor_cnt".prp_id = ' . $resours[$i][prp_id] . '');
                                        $s = pg_fetch_result($sql_prp, 0, 0);
                                        $p = pg_fetch_result($sql_prp, 0, 2);
                                        echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                        . "<select id='sens_name" . $i . "' class='form-control'>"
                                        . "<option></option>";
                                        for ($j = 0; $j < count($sens_cnt); $j++) {
                                            if ($sens_cnt[$j][sens_id] == pg_fetch_result($sql_prp, 0, 0)) {
                                                echo "<option value=" . $sens_cnt[$j][sens_id] . " selected>" . $sens_cnt[$j][sens_name] . "</option>";
                                            } else {
                                                echo "<option value=" . $sens_cnt[$j][sens_id] . ">" . $sens_cnt[$j][sens_name] . "</option>";
                                            }
                                        }
                                        echo "</select></div></div>";

                                        echo "<div class = 'row'>"
                                        . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                        . "<h4><b>Диаметр</b></h4>"
                                        . "</div>"
                                        . "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                        . "<input type='text' id='diametr" . $i . "' class='form-control'placeholder='Диаметр трубы' value='" . $resours[$i][diametr] . "'>"
                                        . "</div></div>"
                                        . "<div class = 'row'>"
                                        . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                        . "<h4><b>Заводской номер</b></h4>"
                                        . "</div>";
                                        if ($s == NULL) {
                                            echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<input type='text' id='number" . $i . "'  class='form-control'placeholder='Заводской номер' value=''>"
                                            . "</div>";
                                        } else {
                                            $sql_sens_number = pg_query('SELECT 
                                                        "Tepl"."Sensor_Property"."Propert_Value"
                                                      FROM
                                                        "Tepl"."Sensor_Property"
                                                      WHERE
                                                        "Tepl"."Sensor_Property".s_id = ' . pg_fetch_result($sql_prp, 0, 1) . ' AND 
                                                        "Tepl"."Sensor_Property".id_type_property = 0');

                                            echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<input type='text' id='number" . $i . "' class='form-control'placeholder='Заводской номер' value='" . pg_fetch_result($sql_sens_number, 0, 0) . "'>"
                                            . "</div>";
                                        }

                                        echo "</div>";

                                        echo "<div class = 'row'>"
                                        . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                        . "<h4><b>Местоположение</b></h4>"
                                        . "</div>";
                                        if ($p == NULL) {
                                            echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<input type='text' id='place" . $i . "'  class='form-control'placeholder='Местоположение' value=''>"
                                            . "</div>";
                                        } else {
                                            echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<input type='text' id='place" . $i . "' class='form-control'placeholder='Местоположение' value='" . $p . "'>"
                                            . "</div>";
                                        }

                                        echo "</div>";

                                        $sql_sens_prop = pg_query('SELECT
                                            "Tepl"."Sensor_Property"."Propert_Value"
                                            FROM
                                            "Tepl"."Sensor_Property"
                                            INNER JOIN "Tepl"."Sensor_cnt" ON ("Tepl"."Sensor_Property".s_id = "Tepl"."Sensor_cnt".s_id)
                                            WHERE
                                            "Tepl"."Sensor_cnt".prp_id = ' . $resours[$i][prp_id] . ' AND
                                            "Tepl"."Sensor_Property".id_type_property = 2
                                            ORDER BY
                                            "Tepl"."Sensor_Property".id_type_property');
                                        if (pg_fetch_result($sql_sens_prop, 0, 0) != '') {
                                            echo '<div class="row">'
                                            . '<div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Дата поверки комплекта</b></h4></div>'
                                            . '<div class="col-lg-4 col-md-4 col-xs-8"><input id="datetimepicker1" type="text" class="form-control" name="number" placeholder="Дата поверки" required autofocus style="width: 100%;" value = "' . date('d.m.Y', strtotime(pg_fetch_result($sql_sens_prop, 0, 0))) . '"></div>'
                                            . '</div>';
                                        } else {
                                            echo '<div class="row">'
                                            . '<div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Дата поверки комплекта</b></h4></div>'
                                            . '<div class="col-lg-4 col-md-4 col-xs-8"><input id="datetimepicker1" type="text" class="form-control" name="number" placeholder="Дата поверки" required autofocus style="width: 100%;" value = ""></div>'
                                            . '</div>';
                                        }

                                        echo "<div class='row'><div id='' class=' col-lg-4 col-md-4 col-xs-6 col-lg-offset-5 col-md-offset-5 col-xs-offset-2'><a id='" . $i . "' data-prp_id='" . $resours[$i][prp_id] . "' class='save_voda btn btn-md btn-primary btn-block'>Сохранить</a></div></div><br><br>";
                                        $id_log = $id_log + 1;
                                        $sql_add_sens = pg_query('INSERT INTO  "public"."logs" VALUES (' . $id_log . ',' . $id_object . ', \'' . $_SESSION['login'] . '\' , ' . $resours[$i][prp_id] . ', \'' . $resours[$i][diametr] . '\', \'' . pg_fetch_result($sql_prp, 0, 0) . '\', \'' . $p . '\', \'' . pg_fetch_result($sql_sens_number, 0, 0) . '\', \'READ\' , \'' . date("Y-m-d") . '\')');
                                    }



                                    /*

                                     * запрос на максимальный элемент в таблице Sensor_cnt для добавления нового
                                     * SELECT 
                                      MAX("Tepl"."Sensor_cnt".s_id) AS field_1
                                      FROM
                                      "Tepl"."Sensor_cnt"
                                     * 
                                     * 
                                     *                                      */
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Баковое меню -->

                </body>

                <script type="text/javascript">

                    $('#datetimepicker1').datetimepicker({
                        format: 'd.m.Y',
                        lang: 'ru'
                    });


                    function edit_sens(prp_id, sen_id, diametr, number, plc_id, place, date) {
                        $.ajax({
                            type: 'POST',
                            chase: false,
                            url: 'ajax/ajax_edit_sensWater.php',
                            data: 'prp_id=' + prp_id + '&sen_id=' + sen_id + '&diametr=' + diametr + '&number=' + number + '&plc_id=' + plc_id + '&place=' + place + '&date=' + date,
                            beforeSend: function () {
                            },
                            success: function (html) {
                                alert('ok');
                            }
                        });
                        return false;
                    }




                    $(document).ready(function () {
                        var priveleg = <?php echo $_SESSION['privelege']; ?>;
                        //$('#dis').removeAttr("disabled");

                        $('a[class ^="save_voda"').click(function () {
                            if ($('#sens_name').val() != "") {
                                var ident = $(this).attr("id");
                                var plc_id = <?php echo $id_object ?>;
                                var prp_id = $(this).data("prp_id");
                                var sen_id = $('#sens_name' + ident).val();
                                var diametr = $('#diametr' + ident).val();
                                var number = $('#number' + ident).val();
                                var place = $('#place' + ident).val();
                                var date = $('#datetimepicker1').val();
                                console.log(ident + ' ' + sen_id + ' ' + diametr + ' ' + number + ' ' + place + ' ' + plc_id + ' ' + prp_id);
                                edit_sens(prp_id, sen_id, diametr, number, plc_id, place, date);

                            }
                            console.log($(this).attr("id"));
                        });





                        $('#reload_alarm').click(function () {
                            //alert('okokokok');
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


                        $('#formSearch').click(function () {
                            var str_search = $('#search').val();
                            if (str_search != "") {
                                $.ajax({
                                    type: 'POST',
                                    chase: false,
                                    url: 'ajax_serach_object.php',
                                    data: 'search=' + $('#search').val(),
                                    beforeSend: function () {
                                        $('.main').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
                                    },
                                    success: function (html) {
                                        $('.main').html(html);
                                        $('tbody tr[data-href]').addClass('clickable').click(function () {

                                            if (priveleg > 0) {
                                                window.open($(this).attr('data-href'));
                                            } else {
                                                window.location = $(this).attr('data-href');
                                            }

                                        })

                                    }
                                });
                                return false;
                            }
                        });
                    });
                </script>

                </html>
