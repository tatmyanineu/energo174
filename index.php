<?php
/*
 * 
  -- Table: table_tpm

  -- DROP TABLE table_tpm;

  CREATE TABLE table_tpm
  (
  id serial NOT NULL,
  plc_id integer,
  warm integer,
  date_warm timestamp without time zone,
  error_warm integer,
  water integer,
  date_water timestamp without time zone,
  error_water integer,
  marker integer,
  CONSTRAINT id_table PRIMARY KEY (id)
  )
  WITH (
  OIDS=FALSE
  );
  ALTER TABLE table_tpm
  OWNER TO postgres;
 * 
 * 
  -- Table: logs_user

  -- DROP TABLE logs_user;

  CREATE TABLE logs_user
  (
  id serial NOT NULL,
  id_user integer,
  ip_adr text,
  date_con timestamp without time zone,
  CONSTRAINT id_usr PRIMARY KEY (id)
  )
  WITH (
  OIDS=FALSE
  );
  ALTER TABLE logs_user
  OWNER TO postgres;

 * 
 */


include 'db_config.php';
$date2 = date('Y-m-d');
$d = date('Y-m-d h:i');
$date1 = date('Y-m-d', strtotime("-40 day"));
session_start();
$fall = 0;
unset($_SESSION['err_plc']);
unset($_SESSION['main_form']);

if (isset($_POST['submit'])) {
    if (isset($_POST['login']) and isset($_POST['password'])) {

        $sql_user = pg_query('SELECT DISTINCT 
                                      "Tepl"."User_cnt".usr_id,
                                      "Tepl"."User_cnt"."Login",
                                      "Tepl"."User_cnt"."Password",
                                      "Tepl"."User_cnt"."SurName",
                                      "Tepl"."User_cnt"."PatronName",
                                      "Tepl"."User_cnt"."Comment",
                                      "Tepl"."User_cnt"."Privileges"
                                    FROM
                                      "Tepl"."User_cnt"
                                    WHERE
                                       "Tepl"."User_cnt"."Login" = \'' . $_POST['login'] . '\' AND
                                       "Tepl"."User_cnt"."Password" =  \'' . $_POST['password'] . '\' ');
        $resul_login = pg_fetch_row($sql_user);
        //echo $resul_login[2];
        if ($resul_login[2] == $_POST['password']) {

            $_SESSION['login'] = $_POST['login'];
            $_SESSION['password'] = $_POST['password'];
            $_SESSION['privelege'] = $resul_login[6];
            $_SESSION['err_plc'] = "";

            $sql_tickets = pg_query('SELECT DISTINCT 
                                public.ticket.plc_id
                              FROM
                                public.ticket
                              WHERE
                                public.ticket.status < 4');
            $_SESSION['count_ticiket'] = pg_num_rows($sql_tickets);

            $sql_not_alarm = pg_query('SELECT plc_id FROM public.alarm');

            $sql_add_log = pg_query('INSERT INTO logs_user(id_user, ip_adr, date_con)
                                    VALUES (' . $resul_login[0] . ', \'' . $_SERVER['REMOTE_ADDR'] . '\', \'' . $d . '\')');

            while ($result = pg_fetch_row($sql_not_alarm)) {
                $not_alarm[] = $result[0];
            }
            unset($result);
            unset($sql_not_alarm);

            $sql_all_school = pg_query('SELECT 
                    "Places_cnt1"."Name",
                    "Places_cnt1".plc_id
                  FROM
                    "Tepl"."Places_cnt" "Places_cnt1"
                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                    INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                    INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
                    INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
                    INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
                  WHERE
                    "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                    "PropPlc_cnt1".prop_id = 26 AND 
                "Tepl"."User_cnt"."Login" = \'' . $_POST['login'] . '\' AND
                "Tepl"."User_cnt"."Password" =  \'' . $_POST['password'] . '\' 
                  ORDER BY
                    "Tepl"."Places_cnt".plc_id');

            while ($resul_all_school = pg_fetch_row($sql_all_school)) {
                $all_school[] = array(
                    'plc_id' => $resul_all_school[1],
                    'name' => $resul_all_school[0]
                );
            }


            if ($_POST['lite'] != "ON" && !isset($_POST['lite'])) {


                $sql_school_archive = pg_query('SELECT DISTINCT 
                    "Tepl"."Arhiv_cnt"."DateValue",
                    "Tepl"."Places_cnt".plc_id,
                    "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                    "Tepl"."Resourse_cnt"."Name"
                  FROM
                    "Tepl"."GroupToUserRelations"
                    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                    INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
                    INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
                    INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                    INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
                    INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                  WHERE
                "Tepl"."User_cnt"."Login" = \'' . $_POST['login'] . '\' AND
                "Tepl"."User_cnt"."Password" =  \'' . $_POST['password'] . '\' AND
                    "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                    "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                    "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\'
                  ORDER BY
                    "Tepl"."Places_cnt".plc_id,
                    "Tepl"."Arhiv_cnt"."DateValue",
                    "Tepl"."ParamResPlc_cnt"."ParamRes_id"');


                $er = 0;
                while ($result_school = pg_fetch_row($sql_school_archive)) {
                    $array_school[] = array(
                        'date_val' => $result_school[0],
                        'plc_id' => $result_school[1],
                        'id_param' => $result_school[2],
                        'res_name' => $result_school[3]
                    );
                }



                for ($i = 0; $i < count($all_school); $i++) {
                    $key_id = array_search($all_school[$i][plc_id], array_column($array_school, 'plc_id'));
                    if ($key_id === false) {
                        $array_school[] = array(
                            'date_val' => '1970-01-01',
                            'plc_id' => $all_school[$i][plc_id],
                            'id_param' => 1,
                            'res_name' => 'ХВС'
                        );
                        $array_school[] = array(
                            'date_val' => '1970-01-01',
                            'plc_id' => $all_school[$i][plc_id],
                            'id_param' => 9,
                            'res_name' => 'Тепло'
                        );
                    }
                }


                $warm = array(775, 3, 19, 5, 4, 20, 6, 10, 21, 12, 13, 285, 9, 16);
                $water = array(1, 308, 310, 414, 420, 436, 787, 2, 44, 377, 442, 402, 408, 922);
                $m = 1;
                $error_warm = 0;
                $error_water = 0;
                $max_date = '';
                $date_arch_water = 0;
                $date_arch_warm = 0;
                $er = 0;

                for ($a = 0; $a < count($array_school); $a++) {
                    if (strtotime($max_date) < strtotime($array_school[$a]['date_val'])) {
                        $max_date = $array_school[$a]['date_val'];
                    }
                    if ($array_school[$a]['plc_id'] == $array_school[$a + 1]['plc_id']) {
                        $key_warm = array_search($array_school[$a]['id_param'], $warm);
                        $key_water = array_search($array_school[$a]['id_param'], $water);

                        if ($key_warm !== false) {
                            if (strtotime($array_school[$a]['date_val']) >= strtotime($max_date)) {
                                $date_arch_warm = $array_school[$a]['date_val'];
                                $max_date = '';
                            } else {
                                $date_arch_warm = $max_date;
                                $max_date = '';
                            }
                            //$max_date = '';
                        } elseif ($key_water !== false) {
                            if (strtotime($array_school[$a]['date_val']) >= strtotime($max_date)) {
                                $date_arch_water = $array_school[$a]['date_val'];
                                $max_date = '';
                            } else {
                                $date_arch_water = $max_date;
                                $max_date = '';
                            }
                            //$max_date = '';
                        }
                        //$max_date = '';
                    }
                    if ($array_school[$a]['plc_id'] != $array_school[$a + 1]['plc_id']) {
                        $id = $array_school[$a][plc_id];
                        $kol_day_warm = (strtotime(date("Y-m-d")) - strtotime(date("Y-m-d", strtotime($date_arch_warm)))) / (60 * 60 * 24);
                        $kol_day_water = (strtotime(date("Y-m-d")) - strtotime(date("Y-m-d", strtotime($date_arch_water)))) / (60 * 60 * 24);


                        $sql_school_res = pg_query('SELECT DISTINCT 
                        "Tepl"."Resourse_cnt"."Name",
                        "Tepl"."ParamResPlc_cnt".plc_id
                      FROM
                        "Tepl"."User_cnt"
                        INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
                        INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
                        INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
                        INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
                        INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                      WHERE
                        "Tepl"."User_cnt"."Login" = \'' . $_POST['login'] . '\' AND
                        "Tepl"."User_cnt"."Password" =  \'' . $_POST['password'] . '\' AND 
                        "Tepl"."ParamResPlc_cnt".plc_id = ' . $id . '
                      ORDER BY
                        "Tepl"."ParamResPlc_cnt".plc_id,
                        "Tepl"."Resourse_cnt"."Name"');
                        $res_warm = 0;
                        $res_water = 0;
                        while ($result_res = pg_fetch_row($sql_school_res)) {
                            if ($result_res[0] == "ХВС") {
                                $res_water = 1;
                            } elseif ($result_res[0] == "Тепло") {
                                $res_warm = 1;
                            }
                        }


                        if ($kol_day_warm > 7000) {
                            if ($res_warm == 1) {
                                $error_warm = 3;
                            } else {
                                $error_warm = 4;
                            }
                            //$_SESSION['data_oshibki'][] = "Нет данных";
                        } elseif ($kol_day_warm > 3) {
                            $error_warm = 1;
                            //$_SESSION['data_oshibki'][] = $date_arch_warm;
                        }
                        if ($kol_day_water > 7000) {
                            if ($res_water == 1) {
                                $error_water = 3;
                            } else {
                                $error_water = 4;
                            }
                        } elseif ($kol_day_water > 3) {
                            $error_water = 1;
                        }
                        $kl = array_search($array_school[$a][plc_id], $not_alarm);
                        if ($kl === false) {
                            if ($error_warm == 1 and $error_water == 1) {
                                //черный маркер 
                                $marker = 4;
                            } elseif ($error_warm == 1 and $error_water == 3) {
                                //черный маркер 
                                $marker = 4;
                            } elseif ($error_warm == 1 and $error_water == 4) {
                                //черный маркер 
                                $marker = 4;
                            } elseif ($error_warm == 3 and $error_water == 1) {
                                //черный маркер 
                                $marker = 4;
                            } elseif ($error_warm == 3 and $error_water == 3) {
                                //черный маркер 
                                $marker = 4;
                            } elseif ($error_warm == 3 and $error_water == 4) {
                                //черный маркер 
                                $marker = 4;
                            } elseif ($error_warm == 4 and $error_water == 1) {
                                //черный маркер 
                                $marker = 4;
                            } elseif ($error_warm == 4 and $error_water == 3) {
                                //черный маркер 
                                $marker = 4;
                            } elseif ($error_warm == 0 and $error_water == 0) {
                                //зеленый маркер 
                                $marker = 1;
                            } elseif ($error_warm == 0 and $error_water == 4) {
                                //зеленый маркер 
                                $marker = 1;
                            } elseif ($error_warm == 4 and $error_water == 0) {
                                //зеленый маркер 
                                $marker = 1;
                            } elseif ($error_warm == 1 and $error_water == 0) {
                                //Красный маркер 
                                $marker = 3;
                            } elseif ($error_warm == 3 and $error_water == 0) {
                                //Красный маркер 
                                $marker = 3;
                            } elseif ($error_warm == 0 and $error_water == 1) {
                                //Оранжевый маркер 
                                $marker = 2;
                            } elseif ($error_warm == 0 and $error_water == 3) {
                                //Оранжевый маркер 
                                $marker = 2;
                            }
                        } else {
                            $marker = 8;
                            $error_warm = 8;
                            $error_water = 8;
                        }

                        $main_form[] = array(
                            'plc_id' => $array_school[$a]['plc_id'],
                            'warm' => $res_warm,
                            'date_warm' => date('Y-m-d', strtotime($date_arch_warm)),
                            'error_warm' => $error_warm,
                            'water' => $res_water,
                            'date_water' => date('Y-m-d', strtotime($date_arch_water)),
                            'error_water' => $error_water,
                            'marker' => $marker
                        );

                        if ($error_warm == 3 or $error_warm == 1 or $error_water == 3 or $error_water == 1) {
                            $_SESSION['err_plc'][] = $array_school[$a]['plc_id'];

                            $er++;
                        }

                        //echo "№" . $m . " id=" . $id . " d_w" . $date_arch_warm . " k_d" . $kol_day_warm . " e_w" . $error_warm . " d_v" . $date_arch_water . " k_v" . $kol_day_water . " e_v" . $error_water . " res_warm=" . $res_warm . " res_water=" . $res_water . "<br>";
                        $m++;
                        $date_arch_warm = '';
                        $date_arch_water = '';
                        $error_warm = 0;
                        $error_water = 0;
                        $max_date = '';
                    }
                }
                if ($_SESSION['login'] == 'adm') {
                    $sql_delete_tmd = pg_query('TRUNCATE public.table_tpm');
                    for ($i = 0; $i < count($main_form); $i++) {
                        $sql_add_tmp = pg_query('INSERT INTO table_tpm(plc_id, warm, date_warm, error_warm, water, date_water, error_water, marker)
                         VALUES ( ' . $main_form[$i]['plc_id'] . ', ' . $main_form[$i]['warm'] . ', \'' . $main_form[$i]['date_warm'] . '\', ' . $main_form[$i]['error_warm'] . ', ' . $main_form[$i]['water'] . ', \'' . $main_form[$i]['date_water'] . '\', ' . $main_form[$i]['error_water'] . ', ' . $main_form[$i]['marker'] . ')');
                    }
                }
                $_SESSION['main_form'] = $main_form;
                $_SESSION['alarm'] = $er;



                $p = ($er / count($all_school)) * 100;
                $_SESSION['proc'] = $p;

                $sql_all_fp = pg_query('SELECT * FROM password_forgot where status =0');
                $_SESSION['reports_passord'] = pg_num_rows($sql_all_fp);
            } else {
                $sql_main_form = pg_query('SELECT id, plc_id, warm, date_warm, error_warm, water, date_water, error_water, marker FROM table_tpm');
                while ($result = pg_fetch_row($sql_main_form)) {
                    $key_plc_id = array_search($result[1], array_column($all_school, 'plc_id'));
                    if ($key_plc_id !== false) {
                        if ($result[4] == 3 or $result[4] == 1 or $result[7] == 3 or $result[7] == 1) {
                            $_SESSION['err_plc'][] = $result[1];
                            $er++;
                        }

                        $main_form[] = array(
                            'plc_id' => $result[1],
                            'warm' => $result[2],
                            'date_warm' => $result[3],
                            'error_warm' => $result[4],
                            'water' => $result[5],
                            'date_water' => $result[6],
                            'error_water' => $result[7],
                            'marker' => $result[8]
                        );
                    }
                }
                $_SESSION['main_form'] = $main_form;
                $_SESSION['alarm'] = $er;
                $p = ($er / count($all_school)) * 100;
                $_SESSION['proc'] = $p;

                $sql_all_fp = pg_query('SELECT * FROM password_forgot where status =0');
                $_SESSION['reports_passord'] = pg_num_rows($sql_all_fp);
            }
        }
        header('location: objects.php');
        //echo $er;
    } else {
        $fall = 1;
    }
    //var_dump($main_form);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>

        <!--
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css"/>
        <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css.map"/>
        <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css"/>
        -->
        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>

        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/npm.js"></script>

    </head>
    <body>
        <div id="login_content" class="container" style="margin: 0px; padding: 0px;">

            <div class="row">
                <div class="col-lg-2 col-md-2">
                    <img src="img/keys.png" style="margin-top: 7px;" alt=""/>
                </div>
                <div class="col-lg-10 col-md-10">

                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <p id="login_name">СИТУАЦИОННЫЙ ЦЕНТР КОНТРОЛЯ И УЧЕТА ЭНЕРГОРЕСУРСОВ</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-md-5">
                            <form method="post" id="formLogin">
                                <h4>Логин:</h4>
                                <input type="text" class="form-control" name="login" placeholder="Имя пользователя" required autofocus>
                                <h4>Пароль:</h4>
                                <input type="password" class="form-control" name="password" placeholder="Пароль" required>
                                <label class="checkbox"></label>
                                <label class="checkbox"></label>
                                <input type="checkbox" name="lite" value="ON"> Облегченная версия
                                <label class="checkbox"></label>
                                <input class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="Войти"/>
                                <label class="checkbox"></label>
                                <br>
                                <br>
                            </form>
                            <?php
                            if ($fall == 1) {
                                echo "<h4>Не верное имя пользователя или пароль</h4>";
                            }
//echo '<pre>';
//    print_r($_SESSION['main_form']);
//    echo '</pre>';
                            ?>
                        </div>
                        <div class="col-lg-6 col-md-6" style="margin-top: 20px; margin-left: 30px" >

                            <div class="row">
                                <div class="col-lg-3 col-md-3"><a href="../Documents/МБУ Фонд энергоэффективности и инновационных технологий - Инструкция пользователя телеметрии.docx"><img src="img/info.png" alt=""></a></div>
                                <div class="col-lg-8 col-md-8 " style="margin-top: 12px; margin-left: -20px"><h4><i><a id="link_help" href="../Documents/МБУ Фонд энергоэффективности и инновационных технологий - Инструкция пользователя телеметрии.docx">Инструкция</a>  </i></h4></div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-3 col-md-3"><a href="forgot_password.php"><img src="img/key.png" alt=""></a></div>
                                <div class="col-lg-8 col-md-8 " style="margin-left: -20px"><h4><i><a id="link_help" href="forgot_password.php">Заявка на восстановление пароля</a>  </i></h4></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer"></div>
        <script type="text/javascript">
            $(document).ready(function () {

            });
        </script>
    </body>
</html>



