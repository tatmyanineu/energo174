<?php
include 'db_config.php';
session_start();
$id_object = $_GET['id_object'];

//$conn_log = pg_connect("host=localhost port=5432 dbname=Base user=postgres password=postgres");
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
                        <h2 class="text-center page-header"><span class="glyphicon glyphicon-pencil"></span> Редактирование узла учета тепла</h2>
                        <div id="center_h1"></div>
                        <div  id="view_archive" class="row ">
                            <div class="col-lg-12 col-md-12 col-xs-12">
                                <div>
                                    <?php
                                    $sql_device = pg_query('SELECT 
                                "Places_cnt1".plc_id,
                                "Tepl"."TypeDevices"."Name",
                                "Tepl"."Device_cnt".dev_typ_id,
                                "Tepl"."Device_cnt"."Comment",
                                "Tepl"."Device_cnt".dev_id
                              FROM
                                "Tepl"."Places_cnt" "Places_cnt1"
                                INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                              WHERE
                                "Places_cnt1".plc_id = ' . $id_object . ' AND 
                                "Tepl"."TypeDevices"."Name" NOT LIKE \'%Пульс%\'
                              ORDER BY
                                "Tepl"."TypeDevices"."Name"');

                                    while ($res_device = pg_fetch_row($sql_device)) {
                                        $dev_teplo[] = array(
                                            'name' => $res_device[1],
                                            'dev_typ_id' => $res_device[2],
                                            'comment' => $res_device[3],
                                            'dev_id' => $res_device[4]
                                        );
                                    }

                                    for ($i = 0; $i < count($dev_teplo); $i++) {
                                        echo '<div class="row">'
                                        . '<div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Тепловычислитель</b></h4></div>'
                                        . '<div class="col-lg-4 col-md-4 col-xs-12"><h4>' . $dev_teplo[$i][name] . '</h4></div>'
                                        . '</div>';
                                        echo '<div class="row">'
                                        . '<div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Комментарий</b></h3></div>';
                                        if ($dev_teplo[$i][comment] == "") {
                                            echo '<div class="col-lg-4 col-md-4 col-xs-8"><input id="value_comm_teplo"  type="text" class="form-control" name="komment" placeholder="Схема измерения" required autofocus value = "Схема измерений"></div>';
                                        } else {
                                            echo '<div class="col-lg-4 col-md-4 col-xs-8"><input id="value_comm_teplo"  type="text" class="form-control" name="komment" placeholder="Схема измерения" required autofocus value = "' . $dev_teplo[$i][comment] . '"></div>';
                                        }

                                        echo '</div>';

                                        $sql_dev_prop = pg_query('SELECT
                                        "Tepl"."Device_Property"."Propert_Value"
                                        FROM
                                        "Tepl"."Places_cnt" "Places_cnt1"
                                        INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                        INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                        INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                        INNER JOIN "Tepl"."Device_Property" ON ("Tepl"."Device_cnt".dev_id = "Tepl"."Device_Property".dev_id)
                                        WHERE
                                        "Places_cnt1".plc_id = ' . $id_object . ' AND
                                        "Tepl"."Device_Property".id_type_property = 0 AND
                                        "Tepl"."Device_cnt".dev_typ_id = ' . $dev_teplo[$i][dev_typ_id] . '
                                        ORDER BY
                                        "Tepl"."TypeDevices"."Name",
                                        "Tepl"."Device_Property".id_type_property');

                                        echo '<div class="row">'
                                        . '<div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Заводской номер</b></h4></div>'
                                        . '<div class="col-lg-4 col-md-4 col-xs-8"><input id="value_number_teplo"  type="text" class="form-control" name="number" placeholder="Заводский номер" required autofocus value = "' . pg_fetch_result($sql_dev_prop, 0, 0) . '"></div>'
                                        . '</div>';

                                        $sql_date_pov = pg_query('SELECT
                                        "Tepl"."Device_Property"."Propert_Value",
                                        "Tepl"."Device_cnt"."Numbe"
                                        FROM
                                        "Tepl"."Places_cnt" "Places_cnt1"
                                        INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                        INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                        INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                        INNER JOIN "Tepl"."Device_Property" ON ("Tepl"."Device_cnt".dev_id = "Tepl"."Device_Property".dev_id)
                                        WHERE
                                        "Places_cnt1".plc_id = ' . $id_object . ' AND
                                        "Tepl"."Device_Property".id_type_property = 2
                                        ORDER BY
                                        "Tepl"."TypeDevices"."Name",
                                        "Tepl"."Device_Property".id_type_property');
                                        if (pg_fetch_result($sql_date_pov, 0, 0) != '') {
                                            echo '<div class="row">'
                                            . '<div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Дата поверки комплекта</b></h4></div>'
                                            . '<div class="col-lg-4 col-md-4 col-xs-8"><input id="datetimepicker1" type="text" class="form-control" name="number" placeholder="Дата поверки" required autofocus style="width: 100%;" value = "' . date('d.m.Y', strtotime(pg_fetch_result($sql_date_pov, 0, 0))) . '"></div>'
                                            . '</div>';
                                        }else{
                                            echo '<div class="row">'
                                            . '<div class="col-lg-5 col-md-5 col-xs-12"><h4><b>Дата поверки комплекта</b></h4></div>'
                                            . '<div class="col-lg-4 col-md-4 col-xs-8"><input id="datetimepicker1" type="text" class="form-control" name="number" placeholder="Дата поверки" required autofocus style="width: 100%;" value = ""></div>'
                                            . '</div>';
                                        }

                                        echo "<div class='row'><div class='col-lg-4 col-md-4 col-xs-6 col-lg-offset-5 col-md-offset-5 col-xs-offset-2'><a data-dev_id = '" . $dev_teplo[$i][dev_id] . "' data-dev_type_id='" . $dev_teplo[$i][dev_typ_id] . "' id='teplo' class='btn btn-md btn-primary btn-block'>Сохранить</a></div></div>";
                                    }
                                    ?>
                                </div>
                                <div id="resours" style="margin-top: 30px">
                                    <?php
                                    $sql_sens_name = pg_query($conn, 'SELECT 
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
                                    if (pg_num_rows($sql_max_id_log) != 0) {
                                        $id_log = pg_fetch_result($sql_max_id_log, 0, 0);
                                    } else {
                                        $id_log = 1;
                                    }
                                    if ($dev_teplo[0][dev_typ_id] != 175) {
                                        $sql_res_plc = pg_query('SELECT 
                                            "Tepl"."ParamResPlc_cnt".prp_id,
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                            "Tepl"."ParamResPlc_cnt"."NameGroup",
                                            "Tepl"."ParamResPlc_cnt"."Comment"
                                          FROM
                                            "Tepl"."ParamResPlc_cnt"
                                          WHERE
                                            "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 19 OR 
                                            "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 20 OR 
                                            "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 21
                                           ORDER BY
                                            "Tepl"."ParamResPlc_cnt"."NameGroup"');

                                        while ($res_resours = pg_fetch_row($sql_res_plc)) {
                                            $resours[] = array(
                                                'prp_id' => $res_resours[0],
                                                'param_res' => $res_resours[1],
                                                'name_group' => $res_resours[2],
                                                'diametr' => $res_resours[3]
                                            );
                                        }
                                    } else {
                                        $sql_res_plc = pg_query('SELECT 
                                            "Tepl"."ParamResPlc_cnt".prp_id,
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                            "Tepl"."ParamResPlc_cnt"."NameGroup",
                                            "Tepl"."ParamResPlc_cnt"."Comment"
                                          FROM
                                            "Tepl"."ParamResPlc_cnt"
                                          WHERE
                                            "Tepl"."ParamResPlc_cnt".plc_id =  ' . $id_object . ' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 3 OR
                                              "Tepl"."ParamResPlc_cnt".plc_id =  ' . $id_object . ' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 4
                                          ORDER BY
                                            "Tepl"."ParamResPlc_cnt"."NameGroup"');

                                        while ($res_resours = pg_fetch_row($sql_res_plc)) {
                                            $resours[] = array(
                                                'prp_id' => $res_resours[0],
                                                'param_res' => $res_resours[1],
                                                'name_group' => $res_resours[2],
                                                'diametr' => $res_resours[3]
                                            );
                                        }
                                    }

                                    for ($i = 0; $i < count($resours); $i++) {
                                        unset($dia);
                                        if ($resours[$i][param_res] == 19 or $resours[$i][param_res] == 3) {
                                            echo "<div class = 'row'>"
                                            . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                            . "<h4><b>Расходомер подача(" . $resours[$i][name_group] . ")</b></h4>"
                                            . "</div>";

                                            $sql_prp = pg_query('SELECT 
                                                "Tepl"."Sensor_cnt".sen_id,
                                                "Tepl"."Sensor_cnt".s_id
                                              FROM
                                                "Tepl"."Sensor_cnt"
                                              WHERE
                                                "Tepl"."Sensor_cnt".prp_id = ' . $resours[$i][prp_id] . '');
                                            $s = pg_fetch_result($sql_prp, 0, 0);
                                            echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<select id='podacha1' class='form-control'>"
                                            . "<option></option>";
                                            for ($j = 0; $j < count($sens_cnt); $j++) {
                                                if ($sens_cnt[$j][sens_id] == pg_fetch_result($sql_prp, 0, 0)) {
                                                    echo "<option value=" . $sens_cnt[$j][sens_id] . " selected>" . $sens_cnt[$j][sens_name] . "</option>";
                                                } else {
                                                    echo "<option value=" . $sens_cnt[$j][sens_id] . ">" . $sens_cnt[$j][sens_name] . "</option>";
                                                }
                                            }
                                            echo "</select></div></div>";
                                            $dia = explode(';', $resours[$i][diametr]);
                                            echo "<div class = 'row'>"
                                            . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                            . "<h4><b>Диаметр</b></h4>"
                                            . "</div>"
                                            . "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<input type='text' id='comm_pod1' class='form-control'placeholder='Диаметр трубы' value='" . $dia[0] . "'>"
                                            . "</div></div>"
                                            . "<div class = 'row'>"
                                            . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                            . "<h4><b>Заводской номер</b></h4>"
                                            . "</div>";

                                            if ($s == NULL) {
                                                echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                                . "<input type='text' id='dis_pod1' disabled class='form-control'placeholder='Заводской номер' value=''>"
                                                . "</div>";
                                            } else {
                                                $sql_sens_number = pg_query($conn, 'SELECT 
                                                        "Tepl"."Sensor_Property"."Propert_Value"
                                                      FROM
                                                        "Tepl"."Sensor_Property"
                                                      WHERE
                                                        "Tepl"."Sensor_Property".s_id = ' . pg_fetch_result($sql_prp, 0, 1) . ' AND 
                                                        "Tepl"."Sensor_Property".id_type_property = 0');

                                                echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                                . "<input type='text' id='dis_pod1' class='form-control'placeholder='Заводской номер' value='" . pg_fetch_result($sql_sens_number, 0, 0) . "'>"
                                                . "</div>";
                                            }
                                            $place = "";
                                            $id_log = $id_log + 1;
                                            $sql_add_sens = pg_query('INSERT INTO  "public"."logs" VALUES (' . $id_log . ',' . $id_object . ', \'' . $_SESSION['login'] . '\' , ' . $resours[$i][prp_id] . ', \'' . $dia[0] . '\', \'' . pg_fetch_result($sql_prp, 0, 0) . '\', \'\', \'' . pg_fetch_result($sql_sens_number, 0, 0) . '\', \'READ\' , \'' . date("Y-m-d") . '\')');

                                            /*

                                              if (($fp = fopen("ajax/log.csv", 'a')) !== FALSE) {
                                              fputs($fp, "" . $id_object . ""); //id учереждения
                                              fputs($fp, ";");
                                              fputs($fp, "" . $_SESSION['login'] . ""); //user
                                              fputs($fp, ";");
                                              fputs($fp, "" . $resours[$i][prp_id] . ""); // id ресурса
                                              fputs($fp, ";");
                                              fputs($fp, "" . $dia[0] . ""); //диаметр
                                              fputs($fp, ";");
                                              fputs($fp, "" . pg_fetch_result($sql_prp, 0, 0) . ""); // id расходомера
                                              fputs($fp, ";");
                                              fputs($fp, "" . $place . ""); //местоположение
                                              fputs($fp, ";");
                                              fputs($fp, "" . pg_fetch_result($sql_sens_number, 0, 0) . ""); //заводской номер
                                              fputs($fp, ";");
                                              fputs($fp, "read"); //действие
                                              fputs($fp, ";");
                                              fputs($fp, "" . date("d.m.Y") . ""); //Дата
                                              fputs($fp, ";");
                                              fputs($fp, "\r\n");
                                              }
                                              fclose($fp);

                                             */


                                            echo "</div>"
                                            . "<div class='row'><div class='col-lg-4 col-md-4 col-xs-6 col-lg-offset-5 col-md-offset-5 col-xs-offset-2'><a id='podacha_button1' data-prp_id='" . $resours[$i][prp_id] . "' class='btn btn-md btn-primary btn-block'>Сохранить</a></div></div><br><br>";
                                        } elseif ($resours[$i][param_res] == 20 or $resours[$i][param_res] == 4) {
                                            echo "<div class = 'row'>"
                                            . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                            . "<h4><b>Расходомер обратка(" . $resours[$i][name_group] . ")</b></h4>"
                                            . "</div>";

                                            $sql_prp = pg_query('SELECT 
                                                "Tepl"."Sensor_cnt".sen_id,
                                                "Tepl"."Sensor_cnt".s_id
                                              FROM
                                                "Tepl"."Sensor_cnt"
                                              WHERE
                                                "Tepl"."Sensor_cnt".prp_id = ' . $resours[$i][prp_id] . '');
                                            $s = pg_fetch_result($sql_prp, 0, 0);
                                            echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<select placeholder='Расходомер' id='obratka' class='form-control'>"
                                            . "<option></option>";
                                            for ($j = 0; $j < count($sens_cnt); $j++) {
                                                if ($sens_cnt[$j][sens_id] == pg_fetch_result($sql_prp, 0, 0)) {
                                                    echo "<option value=" . $sens_cnt[$j][sens_id] . " selected>" . $sens_cnt[$j][sens_name] . "</option>";
                                                } else {
                                                    echo "<option value=" . $sens_cnt[$j][sens_id] . ">" . $sens_cnt[$j][sens_name] . "</option>";
                                                }
                                            }
                                            $dia = explode(';', $resours[$i][diametr]);
                                            echo "</select></div></div>";
                                            echo "<div class = 'row'>"
                                            . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                            . "<h4><b>Диаметр</b></h4>"
                                            . "</div>"
                                            . "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<input type='text' id='comm_obr' class='form-control'placeholder='Диаметр трубы' value='" . $dia[0] . "'>"
                                            . "</div></div>"
                                            . "<div class = 'row'>"
                                            . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                            . "<h4><b>Заводской номер</b></h4>"
                                            . "</div>";
                                            if ($s == NULL) {
                                                echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                                . "<input type='text' id='dis_obr' disabled class='form-control'placeholder='Заводской номер' value=''>"
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
                                                . "<input type='text' id='dis_obr' class='form-control'placeholder='Заводской номер' value='" . pg_fetch_result($sql_sens_number, 0, 0) . "'>"
                                                . "</div>";
                                            }

                                            $id_log = $id_log + 1;
                                            $sql_add_sens = pg_query('INSERT INTO  "public"."logs" VALUES (' . $id_log . ',' . $id_object . ', \'' . $_SESSION['login'] . '\' , ' . $resours[$i][prp_id] . ', \'' . $dia[0] . '\', \'' . pg_fetch_result($sql_prp, 0, 0) . '\', \'\', \'' . pg_fetch_result($sql_sens_number, 0, 0) . '\', \'READ\' , \'' . date("Y-m-d") . '\')');
                                            /*
                                              if (($fp = fopen("ajax/log.csv", 'a')) !== FALSE) {
                                              fputs($fp, "" . $id_object . ""); //id учереждения
                                              fputs($fp, ";");
                                              fputs($fp, "" . $_SESSION['login'] . ""); //user
                                              fputs($fp, ";");
                                              fputs($fp, "" . $resours[$i][prp_id] . ""); // id ресурса
                                              fputs($fp, ";");
                                              fputs($fp, "" . $dia[0] . ""); //диаметр
                                              fputs($fp, ";");
                                              fputs($fp, "" . pg_fetch_result($sql_prp, 0, 0) . ""); // id расходомера
                                              fputs($fp, ";");
                                              fputs($fp, "" . $place . ""); //местоположение
                                              fputs($fp, ";");
                                              fputs($fp, "" . pg_fetch_result($sql_sens_number, 0, 0) . ""); //заводской номер
                                              fputs($fp, ";");
                                              fputs($fp, "read"); //действие
                                              fputs($fp, ";");
                                              fputs($fp, "" . date("d.m.Y") . ""); //Дата
                                              fputs($fp, ";");
                                              fputs($fp, "\r\n");
                                              }
                                              fclose($fp);
                                             */

                                            echo "</div>"
                                            . "<div class='row'><div class='col-lg-4 col-md-4 col-xs-6 col-lg-offset-5 col-md-offset-5 col-xs-offset-2'><a id='obratka_button' data-prp_id='" . $resours[$i][prp_id] . "'  class='btn btn-md btn-primary btn-block'>Сохранить</a></div></div>";
                                        } elseif ($resours[$i][param_res] == 21) {
                                            echo "<div class = 'row'>"
                                            . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                            . "<h4><b>Расходомер подача ГВС(" . $resours[$i][name_group] . ")</b></h4>"
                                            . "</div>";

                                            $sql_prp = pg_query('SELECT 
                                                "Tepl"."Sensor_cnt".sen_id,
                                                "Tepl"."Sensor_cnt".s_id
                                              FROM
                                                "Tepl"."Sensor_cnt"
                                              WHERE
                                                "Tepl"."Sensor_cnt".prp_id = ' . $resours[$i][prp_id] . '');
                                            $s = pg_fetch_result($sql_prp, 0, 0);
                                            echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<select id='podacha2' class='form-control'>"
                                            . "<option></option>";
                                            for ($j = 0; $j < count($sens_cnt); $j++) {
                                                if ($sens_cnt[$j][sens_id] == pg_fetch_result($sql_prp, 0, 0)) {
                                                    echo "<option value=" . $sens_cnt[$j][sens_id] . " selected>" . $sens_cnt[$j][sens_name] . "</option>";
                                                } else {
                                                    echo "<option value=" . $sens_cnt[$j][sens_id] . ">" . $sens_cnt[$j][sens_name] . "</option>";
                                                }
                                            }
                                            echo "</select></div></div>";
                                            $dia = explode(';', $resours[$i][diametr]);

                                            echo "<div class = 'row'>"
                                            . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                            . "<h4><b>Диаметр</b></h4>"
                                            . "</div>"
                                            . "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                            . "<input type='text' id='comm_pod2' class='form-control'placeholder='Диаметр трубы' value='" . $dia[0] . "'>"
                                            . "</div></div>"
                                            . "<div class = 'row'>"
                                            . "<div class ='col-lg-5 col-md-5 col-xs-12'>"
                                            . "<h4><b>Заводской номер</b></h4>"
                                            . "</div>";
                                            if ($s == NULL) {
                                                echo "<div class='col-lg-4 col-md-4 col-xs-12'>"
                                                . "<input type='text' id='dis_pod2' disabled class='form-control'placeholder='Заводской номер' value=''>"
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
                                                . "<input type='text' id='dis_pod2' class='form-control'placeholder='Заводской номер' value='" . pg_fetch_result($sql_sens_number, 0, 0) . "'>"
                                                . "</div>";
                                            }

                                            $id_log = $id_log + 1;
                                            $sql_add_sens = pg_query('INSERT INTO  "public"."logs" VALUES (' . $id_log . ',' . $id_object . ', \'' . $_SESSION['login'] . '\' , ' . $resours[$i][prp_id] . ', \'' . $dia[0] . '\', \'' . pg_fetch_result($sql_prp, 0, 0) . '\', \'\', \'' . pg_fetch_result($sql_sens_number, 0, 0) . '\', \'READ\' , \'' . date("Y-m-d") . '\')');

                                            /*
                                              if (($fp = fopen("ajax/log.csv", 'a')) !== FALSE) {
                                              fputs($fp, "" . $id_object . ""); //id учереждения
                                              fputs($fp, ";");
                                              fputs($fp, "" . $_SESSION['login'] . ""); //user
                                              fputs($fp, ";");
                                              fputs($fp, "" . $resours[$i][prp_id] . ""); // id ресурса
                                              fputs($fp, ";");
                                              fputs($fp, "" . $dia[0] . ""); //диаметр
                                              fputs($fp, ";");
                                              fputs($fp, "" . pg_fetch_result($sql_prp, 0, 0) . ""); // id расходомера
                                              fputs($fp, ";");
                                              fputs($fp, "" . $place . ""); //местоположение
                                              fputs($fp, ";");
                                              fputs($fp, "" . pg_fetch_result($sql_sens_number, 0, 0) . ""); //заводской номер
                                              fputs($fp, ";");
                                              fputs($fp, "read"); //действие
                                              fputs($fp, ";");
                                              fputs($fp, "" . date("d.m.Y") . ""); //Дата
                                              fputs($fp, ";");
                                              fputs($fp, "\r\n");
                                              }
                                              fclose($fp);
                                             */

                                            echo "</div>"
                                            . "<div class='row'><div class='col-lg-4 col-md-4 col-xs-6 col-lg-offset-5 col-md-offset-5 col-xs-offset-2'><a id='podacha_button2' data-prp_id='" . $resours[$i][prp_id] . "' class='btn btn-md btn-primary btn-block'>Сохранить</a></div></div><br><br>";
                                        }
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
                    $('#datetimepicker2').datetimepicker({
                        format: 'd.m.Y',
                        lang: 'ru'
                    });
                    function edit_teplo(plc_id, comm, dev_id, dev_type_id, number, date) {
                        $.ajax({
                            type: 'POST',
                            chase: false,
                            url: 'ajax/ajax_edit_devise.php',
                            data: 'plc_id=' + plc_id + '&comm=' + comm + '&dev_id=' + dev_id + '&dev_type_id=' + dev_type_id + '&number=' + number + '&date=' + date,
                            beforeSend: function () {
                            },
                            success: function (html) {
                                alert('ok');
                            }
                        });
                        return false;
                    }

                    function edit_sens(prp_id, sen_id, comm, number, plc_id) {
                        $.ajax({
                            type: 'POST',
                            chase: false,
                            url: 'ajax/ajax_edit_sens.php',
                            data: 'prp_id=' + prp_id + '&sen_id=' + sen_id + '&comm=' + comm + '&number=' + number + '&plc_id=' + plc_id,
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


                        $('#teplo').click(function () {
                            var plc_id = <?php echo $id_object ?>;
                            var comm = $('#value_comm_teplo').val();
                            var number = $('#value_number_teplo').val();
                            var date = $('#datetimepicker1').val();
                            var dev_id = $('#teplo').data('dev_id');
                            var dev_type_id = $('#teplo').data('dev_type_id');
                            console.log(plc_id + ' ' + comm + ' ' + dev_id + ' ' + dev_type_id + ' ' + number);
                            edit_teplo(plc_id, comm, dev_id, dev_type_id, number, date)
                        })


                        $('#podacha_button1').click(function () {
                            if ($('#podacha1').val() != "") {
                                var prp_id = $('#podacha_button1').data('prp_id');
                                var sen_id = $('#podacha1').val();
                                var comm = $('#comm_pod1').val();
                                var number = $('#dis_pod1').val();
                                var plc_id = <?php echo $id_object ?>;
                                console.log(prp_id + ' ' + sen_id + ' ' + comm + ' ' + number);
                                edit_sens(prp_id, sen_id, comm, number, plc_id);
                            }
                        });


                        $('#podacha_button2').click(function () {
                            if ($('#podacha1').val() != "") {
                                var prp_id = $('#podacha_button2').data('prp_id');
                                var sen_id = $('#podacha2').val();
                                var comm = $('#comm_pod2').val();
                                var number = $('#dis_pod2').val();
                                var plc_id = <?php echo $id_object ?>;
                                console.log(prp_id + ' ' + sen_id + ' ' + comm + ' ' + number);
                                edit_sens(prp_id, sen_id, comm, number, plc_id);
                            }
                        });


                        $('#obratka_button').click(function () {
                            if ($('#obratka').val() != "") {
                                var prp_id = $('#obratka_button').data('prp_id');
                                var sen_id = $('#obratka').val();
                                var comm = $('#comm_obr').val();
                                var number = $('#dis_obr').val();
                                var plc_id = <?php echo $id_object ?>;
                                console.log(prp_id + ' ' + sen_id + ' ' + comm + ' ' + number);
                                edit_sens(prp_id, sen_id, comm, number, plc_id);
                            }
                        });



                        $('#obratka').on("change", function () {

                            if ($('#obratka').val() != "") {
                                console.log($('#obratka').val());
                                document.getElementById('dis_obr').disabled = false;
                            } else {
                                document.getElementById('dis_obr').disabled = true;
                            }
                        })

                        $('#podacha1').on("change", function () {
                            if ($('#podacha1').val() != "") {
                                document.getElementById('dis_pod1').disabled = false;
                            } else {
                                document.getElementById('dis_pod1').disabled = true;
                            }
                        })

                        $('#podacha2').on("change", function () {
                            if ($('#podacha2').val() != "") {
                                document.getElementById('dis_pod2').disabled = false;
                            } else {
                                document.getElementById('dis_pod2').disabled = true;
                            }
                        })

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
