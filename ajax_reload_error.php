<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include 'db_config.php';
$date = date('Y-m-d');

$date1 = date("Y-m-d", strtotime("-40 day"));
$date2 = date("Y-m-d");

unset($_SESSION['main_form']);
unset($_SESSION['data_oshibki']);
unset($_SESSION['alarm']);

$sql_not_alarm = pg_query('SELECT plc_id FROM public.alarm');



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
            "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
            "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
        ORDER BY
            "Tepl"."Places_cnt".plc_id');

while ($resul_all_school = pg_fetch_row($sql_all_school)) {
    $all_school[] = array(
        'plc_id' => $resul_all_school[1],
        'name' => $resul_all_school[0]
    );
}
unset($resul_all_school);
unset($sql_all_school);

$sql_school_archive = pg_query('SELECT DISTINCT 
    "Tepl"."Arhiv_cnt"."DateValue",
    "Tepl"."Places_cnt".plc_id,
    "Tepl"."ParamResPlc_cnt"."ParamRes_id"
  FROM
    "Tepl"."GroupToUserRelations"
    INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
    INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
    INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
    INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
    INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
 WHERE
    "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
    "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'  AND 
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
        'id_param' => $result_school[2]
    );
}
unset($result_school);
unset($sql_school_archive);

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
                        "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                        "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND 
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
        }else{
            $marker=8;
            $error_warm=8;
            $error_water=8;
        }

        $main_form[] = array(
            'plc_id' => $array_school[$a]['plc_id'],
            'warm' => $res_warm,
            'date_warm' => $date_arch_warm,
            'error_warm' => $error_warm,
            'water' => $res_water,
            'date_water' => $date_arch_water,
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

//print_r($_SESSION['data_oshibki']);
//print_r($_SESSION['err_plc']);
$_SESSION['main_form'] = $main_form;
$_SESSION['alarm'] = $er;
echo $_SESSION['alarm'];