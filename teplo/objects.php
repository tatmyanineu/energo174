<?php
/*
  Доделать сортировку по районам!
 */
include '../db_config.php';
$date1 = date('Y-m-d', strtotime("-1 month"));
$date2 = date('Y-m-d');
session_start();
$date = date("Y-m-d");
if (isset($_SESSION['login']) and isset($_SESSION['password'])) {

    $sql_disitnct = pg_query('SELECT DISTINCT 
  "Places_cnt1"."Name",
  "Places_cnt1".plc_id
FROM
  "Tepl"."GroupToUserRelations"
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
WHERE
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
  "Places_cnt1".typ_id = 10  
ORDER BY
  "Places_cnt1"."Name"');
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>

        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="../css/dashboard.css"/>
        <link href="../css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style id="holderjs-style" type="text/css"></style></head>

    <body id="top">
        <div id="content">
            <!-- Верхний бар -->
            <div class="navbar navbar-default navbar-fixed-top" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a id="forBrand" class="navbar-brand" href="#">Измерительная система контроля и учета энергоресурсов</a>
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">

                            <li><a id="forBrand" href="../index.php">Выход</a></li>
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

            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-md-2 sidebar">
                        <ul class="nav nav-sidebar">
                            <li class="active"><a href="objects.php"><span class="glyphicon glyphicon-home"></span>Обьекты</a></li>
                        </ul>

                    </div>

                    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">

                        <div id="center_h1">

                            <div class="btn-group" style="margin-bottom: 10px">
                                <button class="btn btn-default distinct" type="submit" id="0">Все</button>
                                <?php
                                while ($row_disitinct = pg_fetch_row($sql_disitnct)) {
                                    echo '<button class="btn btn-default distinct" type="submit" id="' . $row_disitinct[1] . '" >' . $row_disitinct[0] . '</button>';
                                }
                                ?>
                            </div>
                        </div>                       
                        <div class="col-lg-12 col-md-12 col-xs-12">
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-12 col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 ">
                                        <div id="all_object">

                                            <?php
                                            $sql_school = pg_query('SELECT DISTINCT 
                                                "Tepl"."Places_cnt"."Name",
                                                "Tepl"."Places_cnt".plc_id,
                                                "PropPlc_cnt1"."ValueProp",
                                                "Tepl"."PropPlc_cnt"."ValueProp",
                                                "Places_cnt1".plc_id,
                                                "Places_cnt1"."Name"
                                              FROM
                                                "Tepl"."GroupToUserRelations"
                                                INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                                INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                                INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                                INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."Places_cnt".place_id = "Places_cnt1".plc_id)
                                                INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
                                                INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                              WHERE
                                                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
                                                "Tepl"."Places_cnt".typ_id = 17 AND 
                                                "Tepl"."PropPlc_cnt".prop_id = 26 AND 
                                                "PropPlc_cnt1".prop_id = 27');

                                            while ($row_school = pg_fetch_row($sql_school)) {
                                                $array_school[] = array(
                                                    'plc_id' => $row_school[1],
                                                    'id_dist' => $row_school[4],
                                                    'name' => $row_school[0],
                                                    'addres' => '' . $row_school[2] . ' ' . $row_school[3] . '',
                                                    'dist' => $row_school[5]
                                                );
                                            }


                                            $sql_archive = pg_query('SELECT DISTINCT 
                                                "Tepl"."ParamResPlc_cnt".plc_id,
                                                "Tepl"."Arhiv_cnt"."DateValue",
                                                "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                                              FROM
                                                "Tepl"."User_cnt"
                                                INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
                                                INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
                                                INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
                                                INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                              WHERE
                                                "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                                                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
                                                "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                                                "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\'  
                                              ORDER BY
                                                "Tepl"."ParamResPlc_cnt".plc_id');

                                            while ($row_archive = pg_fetch_row($sql_archive)) {
                                                $array_archive[] = array(
                                                    'plc_id' => $row_archive[0],
                                                    'param_id' => $row_archive[2],
                                                    'date' => $row_archive[1]
                                                );
                                            }


                                            for ($i = 0; $i < count($array_archive); $i++) {
                                                if ($array_archive[$i]['plc_id'] != $array_archive[$i + 1]['plc_id']) {
                                                    $key = array_search($array_archive[$i]['plc_id'], array_column($array_school, 'plc_id'));
                                                    if ($key !== false) {

                                                        $array[] = array(
                                                            'plc_id' => $array_archive[$i]['plc_id'],
                                                            'id_dist' => $array_school[$key]['id_dist'],
                                                            'dist' => $array_school[$key]['dist'],
                                                            'name' => $array_school[$key]['name'],
                                                            'addres' => $array_school[$key]['addres'],
                                                            'date' => $array_archive[$i]['date']
                                                        );
                                                    }
                                                }
                                            }

                                            for ($i = 0; $i < count($array_school); $i++) {
                                                $key = array_search($array_school[$i]['plc_id'], array_column($array, 'plc_id'));
                                                if ($key === false) {
                                                    //echo $array_school[$i]['name']." ".$array_school[$i]['addres']."<br>";
                                                    $array[] = array(
                                                        'plc_id' => $array_school[$i]['plc_id'],
                                                        'id_dist' => $array_school[$i]['id_dist'],
                                                        'dist' => $array_school[$i]['dist'],
                                                        'name' => $array_school[$i]['name'],
                                                        'addres' => $array_school[$i]['addres'],
                                                        'date' => '1970-01-01'
                                                    );
                                                }
                                            }

                                            $tmp1 = Array();
                                            foreach ($array as &$ma) {
                                                $tmp1[] = &$ma["dist"];
                                            }
                                            $tmp2 = Array();

                                            foreach ($array as &$ma) {
                                                $tmp2[] = &$ma["name"];
                                            }
                                            $tmp3 = Array();

                                            foreach ($array as &$ma) {
                                                $tmp3[] = &$ma["addres"];
                                            }
                                            $tmp4 = Array();



                                            array_multisort($tmp1, $tmp2, $tmp3, $array);


                                            //var_dump($array);

                                            echo '<table class="table table-bordered">'
                                            . '<thead id="thead">'
                                            . '<tr id="warning">'
                                            . '<td>№</td>'
                                            . '<td>Название</td>'
                                            . '<td>Адрес</td>'
                                            . '<td>Дата</td>'
                                            . '<td>Статус</td></tr>'
                                            . '</thead><tbody>';



                                            $n = 1;
                                            for ($i = 0; $i < count($array); $i++) {

                                                if (strtotime($array[$i]['date']) == strtotime('1970-01-01')) {
                                                    $date = "<td class='danger'>Нет данных</td>";
                                                    $status = "<td class='danger'>Нет связи</td>";
                                                } else {

                                                    $kol_day = (strtotime($date2) - strtotime(date("Y-m-d", strtotime($array[$i]['date'])))) / (60 * 60 * 24);
                                                    if ($kol_day > 3) {
                                                        $date = "<td class='danger'>" . date("d.m.Y", strtotime($array[$i]['date'])) . "</td>";
                                                        $status = "<td class='danger'>Нет связи</td>";
                                                    } else {
                                                        $date = "<td>" . date("d.m.Y", strtotime($array[$i]['date'])) . "</td>";
                                                        $status = "<td>ОК</td>";
                                                    }
                                                    $kol_day = 0;
                                                }

                                                echo "<tr id='houver'  data-href='object.php?id_object=" . $array[$i]['plc_id'] . "'>"
                                                . "<td>" . $n++ . "</td>"
                                                . "<td>" . $array[$i]['name'] . "</td>"
                                                . "<td>" . $array[$i]['addres'] . "</td>"
                                                . "" . $date . ""
                                                . "" . $status . ""
                                                . "</tr>";
                                            }
                                            echo "</tbody></table>";
                                            ?>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap core JavaScript
            ================================================== -->
            <!-- Placed at the end of the document so the pages load faster -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
            <script src="../js/bootstrap.js" type="text/javascript"></script>
            <script src="../js/npm.js" type="text/javascript"></script>
            <script src="../js/jquery.datetimepicker.js" type="text/javascript"></script>
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

                $(document).ready(function () {
                    frame_hieght();

                    priveleg = <?php echo $_SESSION['privelege']; ?>;

                    $('tbody tr[data-href]').addClass('clickable').click(function () {

                        if (priveleg > 1) {
                            window.open($(this).attr('data-href'));
                        } else {
                            window.location = $(this).attr('data-href');
                        }

                    });
                });
            </script>

    </body></html>