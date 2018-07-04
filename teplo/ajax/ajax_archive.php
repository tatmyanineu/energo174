<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../db_config.php';
session_start();
$id_object = $_POST['id_object'];

$date1 = $_POST['date1'];
$date2 = $_POST['date2'];

$sql_device = pg_query('SELECT 
                                    MAX("Tepl"."Device_cnt".dev_typ_id) AS field_1
                                  FROM
                                    "Tepl"."Places_cnt" "Places_cnt1"
                                    INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                    INNER JOIN "Tepl"."Device_cnt" ON ("Places_cnt1".plc_id = "Tepl"."Device_cnt".plc_id)
                                    INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
                                  WHERE
                                    "Places_cnt1".plc_id = ' . $id_object . '');
$row_device = pg_fetch_row($sql_device);


$sql_resurse = pg_query('SELECT DISTINCT 
  ("Tepl"."ParametrResourse"."Name") AS "FIELD_1",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' 
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup"');



$i = 0;
$j = 0;
while ($row_resours = pg_fetch_row($sql_resurse)) {
    $array_res[] = array(
        'param_name' => $row_resours[0],
        'group_name' => $row_resours[1],
        'res_name' => $row_resours[2],
        'param_id' => $row_resours[3]
    );
}

$sql_kol_vvod = pg_query('SELECT DISTINCT 
  "Tepl"."ParamResPlc_cnt"."NameGroup"
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
  "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
  "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\' AND
  "Tepl"."ParamResPlc_cnt"."NameGroup" LIKE \'%Тр%\'');


$array_sort = array('775', '3', '19', '5', '10', '21', '12', '4', '20', '6', '12', '285', '9', '16');
//$array_sort = array('775', '3', '19', '5', '4', '20', '6', '285', '9');

$g = 0;
if (pg_num_rows($sql_kol_vvod) == 3) {


    $array_resourse [] = array(
        'name_res' => 'Время<br> исправной<br> работы',
        'name_res_row' => 'ВНР',
        'name_group' => 'Время<br> исправной<br> работы',
        'name_gr_row' => 'ВНР',
        'name_param' => 'Время<br> исправной<br> работы',
        'name_param_row' => 'ВНР',
        'id_param' => 775,
        'ed_izmer' => 'ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'V1',
        'name_param_row' => 'V1 Объемный расход',
        'id_param' => 3,
        'ed_izmer' => 'м3/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'М1',
        'name_param_row' => 'М1 Масса1',
        'id_param' => 19,
        'ed_izmer' => 'т/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'Т1',
        'name_param_row' => 'Т1 Температура1',
        'id_param' => 5,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба3',
        'name_param' => 'V3',
        'name_param_row' => 'V3 Объемный расход',
        'id_param' => 4,
        'ed_izmer' => 'м3/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба3',
        'name_param' => 'М3',
        'name_param_row' => 'М3 Масса3',
        'id_param' => 20,
        'ed_izmer' => 'т/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба3',
        'name_param' => 'Т3',
        'name_param_row' => 'Т3 Температура3',
        'id_param' => 6,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Vгвс',
        'name_param_row' => 'Vгвс Объемный расход2',
        'id_param' => 10,
        'ed_izmer' => 'м3/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Мгвс',
        'name_param_row' => 'Мгвс Масса2',
        'id_param' => 21,
        'ed_izmer' => 'т/ч'
    );

    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Тгвс',
        'name_param_row' => 'Тгвс Температура1',
        'id_param' => 12,
        'ed_izmer' => '°С'
    );


    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС1',
        'name_group' => 'Обратный<br> трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Тгвс',
        'name_param_row' => 'Тгвс Температура2',
        'id_param' => 13,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'dt',
        'name_gr_row' => 'delt',
        'name_param' => 'dt',
        'name_param_row' => 'delt',
        'id_param' => 285,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло2',
        'name_group' => 'Q',
        'name_gr_row' => 'Q1',
        'name_param' => 'Q',
        'name_param_row' => 'Q1',
        'id_param' => 9,
        'ed_izmer' => 'ГКал'
    );
    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС2',
        'name_group' => 'dt',
        'name_gr_row' => 'delt',
        'name_param' => 'dt',
        'name_param_row' => 'delt',
        'id_param' => 286,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'ГВС',
        'name_res_row' => 'ГВС2',
        'name_group' => 'Qгвс',
        'name_gr_row' => 'Q2',
        'name_param' => 'Qгвс',
        'name_param_row' => 'Q2',
        'id_param' => 16,
        'ed_izmer' => 'ГКал'
    );
} else {

    $array_resourse [] = array(
        'name_res' => 'h',
        'name_res_row' => 'ВНР',
        'name_group' => 'h',
        'name_gr_row' => 'ВНР',
        'name_param' => 'h',
        'name_param_row' => 'ВНР',
        'id_param' => 775,
        'ed_izmer' => 'ч'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'V1',
        'name_param_row' => 'V1 Объемный расход',
        'id_param' => 3,
        'ed_izmer' => 'м3'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'М1',
        'name_param_row' => 'М1 Масса1',
        'id_param' => 19,
        'ed_izmer' => 'т'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Подающий трубопровод',
        'name_gr_row' => 'Труба1',
        'name_param' => 'Т1',
        'name_param_row' => 'Т1 Температура1',
        'id_param' => 5,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'V2',
        'name_param_row' => 'V2 Объемный расход2',
        'id_param' => 4,
        'ed_izmer' => 'м3'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'М2',
        'name_param_row' => 'М2 Масса2',
        'id_param' => 20,
        'ed_izmer' => 'т'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Обратный трубопровод',
        'name_gr_row' => 'Труба2',
        'name_param' => 'Т2',
        'name_param_row' => 'Т2 Температура1',
        'id_param' => 6,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'dt',
        'name_gr_row' => 'delt',
        'name_param' => 'dt',
        'name_param_row' => 'delt',
        'id_param' => 285,
        'ed_izmer' => '°С'
    );

    $array_resourse [] = array(
        'name_res' => 'Тепло',
        'name_res_row' => 'Тепло1',
        'name_group' => 'Q',
        'name_gr_row' => 'Q1',
        'name_param' => 'Q',
        'name_param_row' => 'Q1',
        'id_param' => 9,
        'ed_izmer' => 'ГКал'
    );

}
//var_dump($array_resourse);
echo '<table id="main_table" style="text-align: center;" class="table table-responsive table-bordered">
    <thead  id="thead">
        <tr id = "warning">
            <td rowspan = 4><b>№</b></td>
            <td rowspan = 4><b>Дата</b></td>';
$col = 0;
$row = 1;

for ($i = 0; $i < count($array_resourse); $i++) {
    if ($array_resourse[$i]['name_res_row'] == $array_resourse[$i + 1]['name_res_row']) {
        $col++;
    }
    if ($array_resourse[$i]['name_res_row'] != $array_resourse[$i + 1]['name_res_row']) {

        if ($array_resourse[$i]['name_res_row'] == $array_resourse[$i]['name_gr_row']) {
            $row++;
        }
        if ($array_resourse[$i]['name_res_row'] == $array_resourse[$i]['name_param_row']) {
            $row++;
        }

        $col++;
        //echo $row . " " . $col . " " . $array_resourse[$i]['name_res'] . "<br>";
        echo "<td colspan='" . $col . "' rowspan='" . $row . "'><b>" . $array_resourse[$i]['name_res'] . "</b></td>";
        $col = 0;
        $row = 1;
    }
}
echo "</tr>";
echo "<tr id = 'warning'>";
$col = 0;
$row = 1;
for ($i = 0; $i < count($array_resourse); $i++) {

    if ($array_resourse[$i]['name_group'] == $array_resourse[$i + 1]['name_group']) {
        $col++;
    }

    if ($array_resourse[$i]['name_group'] != $array_resourse[$i + 1]['name_group']) {
        if ($array_resourse[$i]['name_res_row'] != $array_resourse[$i]['name_gr_row']) {
            if ($array_resourse[$i]['name_res_row'] != $array_resourse[$i]['name_gr_row'] and $array_resourse[$i]['name_gr_row'] == $array_resourse[$i]['name_param_row']) {
                $row++;
            }
            $col++;
            echo "<td colspan='" . $col . "' rowspan='" . $row . "'><b>" . $array_resourse[$i]['name_group'] . "</b></td>";
            //echo $row . " " . $col . " " . $array_resourse[$i]['name_group'] . "<br>";
            $col = 0;
            $row = 1;
        }
    }
}
echo "</tr>";

echo "<tr id = 'warning'>";
$col = 0;
$row = 1;
for ($i = 0; $i < count($array_resourse); $i++) {
    if ($array_resourse[$i]['name_gr_row'] != $array_resourse[$i]['name_param_row']) {
        $col++;
        echo "<td colspan='" . $col . "' rowspan='" . $row . "'><b>" . $array_resourse[$i]['name_param'] . "</b></td>";
        //echo $row . " " . $col . " " . $array_resourse[$i]['name_param'] . "<br>";
        $col = 0;
        $row = 1;
    }
}
echo "</tr>";

echo "<tr id = 'warning'>";
$col = 0;
$row = 1;
for ($i = 0; $i < count($array_resourse); $i++) {

    echo "<td><b>" . $array_resourse[$i]['ed_izmer']."</b></td>";
}
echo "</tr>";

echo "</thead><tbody>";




$sql_date = pg_query('SELECT DISTINCT 
                          ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = ' . $id_object . ' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = 2  AND
                          "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                          "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\'
                        ORDER BY
                          "Tepl"."Arhiv_cnt"."DateValue"');

while ($row_date = pg_fetch_row($sql_date)) {
    echo '<tr id="hover">';
    $s++;
    echo "<td>" . $s . "</td>";
    echo '<td>' . date("d.m.Y", strtotime($row_date[0])) . '</td>';
     $sql_archive = pg_query('SELECT 
                "Tepl"."Arhiv_cnt"."DataValue",
                "Tepl"."ParamResPlc_cnt"."ParamRes_id"
              FROM
                "Tepl"."ParametrResourse"
                INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
                INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
                INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
                INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."ParamResGroupRelations".grp_id = "Tepl"."GroupToUserRelations".grp_id)
                INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
              WHERE
                "Tepl"."ParamResPlc_cnt".plc_id = ' . $id_object . ' AND 
                "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                "Tepl"."Arhiv_cnt"."DateValue" = \'' . $row_date[0] . '\' AND
                "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
              ORDER BY
                "Tepl"."Arhiv_cnt"."DateValue",
                "Tepl"."Resourse_cnt"."Name",
                "Tepl"."ParamResPlc_cnt"."NameGroup",
                "Tepl"."ParametrResourse"."Name"
                                              ');
    unset($archive);
    while ($row_archive = pg_fetch_row($sql_archive)) {
        $archive[] = array(
            'value' => $row_archive[0],
            'id_param' => $row_archive[1]
        );
    }

    $t1 = 0;
    $t2 = 0;
    $t3 = 0;
    $t = 0;
    for ($i = 0; $i < count($array_resourse); $i++) {
        $key = array_search($array_resourse[$i]['id_param'], array_column($archive, 'id_param'));
        if ($key !== false) {
            echo "<td>" . number_format($archive[$key]['value'], 2, ',', '') . "</td>";
            $array_summ[$i] += $archive[$key]['value'];

            if (pg_num_rows($sql_kol_vvod) == 2) {
                if ($array_resourse[$i]['id_param'] == 5) {
                    $t1 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 6) {
                    $t2 = $archive[$key]['value'];
                }
            } else {
                if ($array_resourse[$i]['id_param'] == 5) {
                    $t1 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 6) {
                    $t2 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 12) {
                    $t3 = $archive[$key]['value'];
                }
                if ($array_resourse[$i]['id_param'] == 13) {
                    $t4 = $archive[$key]['value'];
                }
            }

            if ($array_resourse[$i]['id_param'] == 9 or $array_resourse[$i]['id_param'] == 16) {
                if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
                    $mass_arch[$i][] = $archive[$key]['value'];
                } else {
                    $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                }
            } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
                if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
                    if ($archive[$key]['value'] != "NaN") {
                        $mass_arch[$i][] = $archive[$key]['value'];
                    }
                } else {
                    $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
                }
            } elseif ($array_resourse[$i]['id_param'] == 775 or $array_resourse[$i]['id_param'] == 3 or $array_resourse[$i]['id_param'] == 4 or $array_resourse[$i]['id_param'] == 10) {
                $mass_arch[$i][] = $archive[$key]['value'];
            } else {
                $mass_arch[$i] = $mass_arch[$i] + $archive[$key]['value'];
            }
        }
        if ($key === false) {
            if ($array_resourse[$i]['id_param'] == 285) {
                $t = $t1 - $t2;
                $mass_arch[$i] = $mass_arch[$i] + $t;

                echo "<td> " . number_format($t, 2) . " </td>";
            }elseif ($array_resourse[$i]['id_param'] == 286) {
                $t = $t1 - $t2;
                $mass_arch[$i] = $mass_arch[$i] + $t;
                echo "<td> " . number_format($t, 2) . " </td>";
            } else {
                echo "<td> - </td>";
            }
        }
    }
    echo "</tr>";
}



echo '<tr id = "warning">';
echo '<td colspan=2><b>Среднее:</b></td>';
$m = 0;
$h = 0;
for ($i = 0; $i < count($array_resourse); $i++) {
    if ($array_resourse[$i]['id_param'] == 9 or $array_resourse[$i]['id_param'] == 16) {
        if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
                //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
                if ($l - 1 >= 0) {
                    $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o / $s;
        } else {
            $teplo = $mass_arch[$i] / $s;
        }

        echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
        if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
                //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
                if ($l - 1 >= 0) {
                    $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o / $s;
        } else {
            $teplo = $mass_arch[$i] / $s;
        }


        echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 5 or $array_resourse[$i]['id_param'] == 6 or $array_resourse[$i]['id_param'] == 12) {
        $temp_s = $mass_arch[$i] / $s;
        echo '<td><b>' . number_format($temp_s, 2, ',', '') . '</b></td>';
        // echo '<td></td>';
    } elseif ($array_resourse[$i]['id_param'] == 775) {
        echo "<td></td>";
    } elseif ($array_resourse[$i]['id_param'] == 3) {
        echo "<td></td>";
    } elseif ($array_resourse[$i]['id_param'] == 4) {
        echo "<td></td>";
    } elseif ($array_resourse[$i]['id_param'] == 10) {
        echo "<td></td>";
    } else {
        $temp_s = $mass_arch[$i] / $s;
        echo '<td><b>' . number_format($temp_s, 2, ',', '') . '</b></td>';
    }
}
echo '</tr>';



echo '<tr id = "warning">';
echo '<td colspan=2><b>Итого:</b></td>';
$m = 0;
$h = 0;
for ($i = 0; $i < count($array_resourse); $i++) {
    if ($array_resourse[$i]['id_param'] == 9 or $array_resourse[$i]['id_param'] == 16) {
        if ($row_device[0] == 214 or $id_object == 314 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
                //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
                if ($l - 1 >= 0) {
                    $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        } else {
            $teplo = $mass_arch[$i];
        }

        echo '<td><b>' . number_format($teplo, 2, '.', '') . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 19 or $array_resourse[$i]['id_param'] == 20 or $array_resourse[$i]['id_param'] == 21) {
        if ($row_device[0] == 214 or $id_object == 251 or $id_object == 316 or $id_object == 318) {
            $z = 0;
            $o = 0;
            $p = 0;
            for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
                //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
                if ($l - 1 >= 0) {
                    $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
                }
                $o = $o + $p;

                //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
                $p = 0;
            }
            $teplo = $o;
        } else {
            $teplo = $mass_arch[$i];
        }


        echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } elseif ($array_resourse[$i]['id_param'] == 5 or $array_resourse[$i]['id_param'] == 6 or $array_resourse[$i]['id_param'] == 12) {
        $temp_s = $mass_arch[$i] / $s;
        //echo '<td><b>' .number_format($temp_s, 2, '.', ''). '</b></td>';
        echo '<td></td>';
    } elseif ($array_resourse[$i]['id_param'] == 775 or $array_resourse[$i]['id_param'] == 3 or $array_resourse[$i]['id_param'] == 4 or $array_resourse[$i]['id_param'] == 10) {
        $z = 0;
        $o = 0;
        $p = 0;
        for ($l = count($mass_arch[$i]) - 1; $l >= 0; $l--) {
            //echo  "     l ==   " . $l . "  val ==  " . $mass_arch[$i][$l];
            if ($l - 1 >= 0) {
                $p = $mass_arch[$i][$l] - $mass_arch[$i][$l - 1];
            }
            $o = $o + $p;

            //echo   "  p== ".  $p  .  "    o== ". $o."<br>";
            $p = 0;
        }
        $teplo = $o;

        echo '<td><b>' . number_format($teplo, 2, ',', '') . '</td>';
    } else {
        $temp_s = $mass_arch[$i];
        echo "<td></td>";
        //echo '<td><b>' . number_format($temp_s, 2, '.', '') . '</b></td>';
    }
}
echo '</tr>';
