<?php

include 'db_config.php';
$start = microtime(true);
session_start();

$time = strtotime("-0 day");
$date_now = date('Y-m-d', $time);

$id_distinct = $_POST['id_dist'];
//print_r($id_distinct);
//выстраиваем иерархию
$sql_type_plc_num = pg_query('
SELECT DISTINCT 
  "Tepl"."Places_cnt".typ_id
FROM
  "Tepl"."Places_cnt"');
$num_row_type = pg_num_rows($sql_type_plc_num);
$sql_type_plc = pg_query('SELECT DISTINCT 
                          "Tepl"."Places_cnt".typ_id,
                          "Places_cnt1".typ_id,
                          "Places_cnt2".typ_id
                        FROM
                          "Tepl"."Places_cnt" "Places_cnt1"
                          INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                          INNER JOIN "Tepl"."Places_cnt" "Places_cnt2" ON ("Places_cnt1".plc_id = "Places_cnt2".place_id)
                            ');

$rs = pg_fetch_row($sql_type_plc);

//выстраиваем иерархию

echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
            <tr id='warning'>
                <td rowspan=2 data-query='0'><b>№</b></td>
                <td rowspan=2 data-query='1'><b>Учереждение</b></td>
                <td rowspan=2 data-query='2'><b>Адрес</b></td>
                <td colspan=2 ><b>Дата последней передачи</b></td>
            </tr>
            <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b></td>
                <td data-query='4'><b>ХВС</b></td>
            </tr>
        </thead>";

$m = 1;

$sql_school_info = pg_query('SELECT 
                                "Places_cnt1"."Name",
                                "Tepl"."PropPlc_cnt"."ValueProp",
                                "PropPlc_cnt1"."ValueProp",
                                "Places_cnt1".plc_id,
                                "Tepl"."Places_cnt"."Name",
                                "Tepl"."Places_cnt".plc_id
                              FROM
                                "Tepl"."Places_cnt" "Places_cnt1"
                                INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
                                INNER JOIN "Tepl"."PropPlc_cnt" ON ("Places_cnt1".plc_id = "Tepl"."PropPlc_cnt".plc_id)
                                INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
                                INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Places_cnt1".plc_id)
                                INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                              WHERE
                                "Tepl"."PropPlc_cnt".prop_id = 27 AND 
                                "PropPlc_cnt1".prop_id = 26 AND 
                              "Tepl"."User_cnt"."Login" = \'' . $_SESSION['login'] . '\' AND 
                              "Tepl"."User_cnt"."Password" = \'' . $_SESSION['password'] . '\'
                              ORDER BY
                                "Tepl"."Places_cnt".plc_id');

while ($result_school_info = pg_fetch_row($sql_school_info)) {
    $school_info[] = array(
        'plc_id' => $result_school_info[3],
        'distinct' => $result_school_info[4],
        'id_dist' => $result_school_info[5],
        'name' => $result_school_info[0],
        'addres' => '' . $result_school_info[1] . ' ' . $result_school_info[2] . ''
    );
}

//print_r($school_info);



for ($i = 0; $i < count($school_info); $i++) {
    $key = array_search($school_info[$i]['plc_id'], array_column($_SESSION['main_form'], 'plc_id'));
    if ($key !== false) {
        $table[] = array(
            'plc_id' => $school_info[$i]['plc_id'],
            'distinct' => $school_info[$i]['distinct'],
            'id_dist' => $school_info[$i]['id_dist'],
            'name' => $school_info[$i]['name'],
            'addres' => $school_info[$i]['addres'],
            'date_t' => $_SESSION['main_form'][$key]['date_warm'],
            'error_t' => $_SESSION['main_form'][$key]['error_warm'],
            'date_w' => $_SESSION['main_form'][$key]['date_water'],
            'error_w' => $_SESSION['main_form'][$key]['error_water'],
        );
    } elseif ($key === false) {
        $table[] = array(
            'plc_id' => $school_info[$i]['plc_id'],
            'distinct' => $school_info[$i]['distinct'],
            'id_dist' => $school_info[$i]['id_dist'],
            'name' => $school_info[$i]['name'],
            'addres' => $school_info[$i]['addres'],
            'date_t' => 'Нет данных',
            'error_t' => 3,
            'date_w' => 'Нет данных',
            'error_w' => 3,
        );
    }
}

$tmp1 = Array();
foreach ($table as &$ma) {
    $tmp1[] = &$ma["distinct"];
}
$tmp2 = Array();

foreach ($table as &$ma) {
    $tmp2[] = &$ma["name"];
}
$tmp3 = Array();

foreach ($table as &$ma) {
    $tmp3[] = &$ma["addres"];
}
array_multisort($tmp1, $tmp2, $tmp3, $table);


unset($_SESSION['arr_plc_id']);
unset($_SESSION['arr_id']);
unset($_SESSION['arr_name']);
unset($_SESSION['arr_addr']);
unset($_SESSION['arr_date_t']);
unset($_SESSION['arr_error_t']);
unset($_SESSION['arr_date_w']);
unset($_SESSION['arr_error_w']);

if ($id_distinct == 0) {
    for ($i = 0; $i < count($table); $i++) {
        echo "<tr data-href='object.php?id_object=" . $table[$i]['plc_id'] . "' id='hover' >"
        . "<td>" . $m . "</td>"
        . "<td>" . $table[$i]['name'] . "</td>"
        . "<td>" . $table[$i]['addres'] . "</td>";
        if ($table[$i]['error_t'] == 1) {
            echo "<td class='warning'>" . date("d.m.Y", strtotime($table[$i]['date_t'])) . "</td>";
            $_SESSION['arr_date_t'][] = date("d.m.Y", strtotime($table[$i]['date_t']));
        } elseif ($table[$i]['error_t'] == 3) {
            echo "<td class='danger'> Нет данных </td>";
            $_SESSION['arr_date_t'][] = "Нет данных";
        } elseif ($table[$i]['error_t'] == 4) {
            echo "<td class='text-center'> -</td>";
            $_SESSION['arr_date_t'][] = "-";
        } elseif ($table[$i]['error_t'] == 0) {
            echo "<td>" . date("d.m.Y", strtotime($table[$i]['date_t'])) . "</td>";
            $_SESSION['arr_date_t'][] = date("d.m.Y", strtotime($table[$i]['date_t']));
        } elseif ($table[$i]['error_t'] == 8) {
            if (strtotime(date('1970-01-01')) == strtotime($table[$i]['date_t']) or $table[$i]['date_t'] == "") {
                echo "<td class='success'>Нет данных</td>";
                $_SESSION['arr_date_t'][] = "Нет данных";
            } else {
                echo "<td class='success'>" . date("d.m.Y", strtotime($table[$i]['date_t'])) . "</td>";
                $_SESSION['arr_date_t'][] = date("d.m.Y", strtotime($table[$i]['date_t']));
            }
        }

        if ($table[$i]['error_w'] == 1) {
            echo "<td class='warning'>" . date("d.m.Y", strtotime($table[$i]['date_w'])) . "</td>";
            $_SESSION['arr_date_w'][] = date("d.m.Y", strtotime($table[$i]['date_w']));
        } elseif ($table[$i]['error_w'] == 3) {
            echo "<td class='danger'> Нет данных </td>";
            $_SESSION['arr_date_w'][] = "Нет данных";
        } elseif ($table[$i]['error_w'] == 4) {
            echo "<td class='text-center'> - </td>";
            $_SESSION['arr_date_w'][] = "-";
        } elseif ($table[$i]['error_w'] == 0) {
            echo "<td>" . date("d.m.Y", strtotime($table[$i]['date_w'])) . "</td>";
            $_SESSION['arr_date_w'][] = date("d.m.Y", strtotime($table[$i]['date_w']));
        } elseif ($table[$i]['error_w'] == 8) {
            if (strtotime(date('1970-01-01')) == strtotime($table[$i]['date_w']) or $table[$i]['date_w'] == "") {
                echo "<td class='success'>Нет данных</td>";
                $_SESSION['arr_date_w'][] = "Нет данных";
            } else {
                echo "<td class='success'>" . date("d.m.Y", strtotime($table[$i]['date_w'])) . "</td>";
                $_SESSION['arr_date_w'][] = date("d.m.Y", strtotime($table[$i]['date_w']));
            }
        }

        $_SESSION['arr_plc_id'][] = $table[$i]['plc_id'];
        $_SESSION['arr_id'][] = $m;
        $_SESSION['arr_name'][] = $table[$i]['name'];
        $_SESSION['arr_addr'][] = $table[$i]['addres'];
        $_SESSION['arr_error_t'][] = $table[$i]['error_t'];

        $_SESSION['arr_error_w'][] = $table[$i]['error_w'];

        $m++;
        echo "</tr>";
    }
} else {
    for ($i = 0; $i < count($table); $i++) {
        if ($table[$i]['id_dist'] == $id_distinct) {
            echo "<tr data-href='object.php?id_object=" . $table[$i]['plc_id'] . "' id='hover' >"
            . "<td>" . $m . "</td>"
            . "<td>" . $table[$i]['name'] . "</td>"
            . "<td>" . $table[$i]['addres'] . "</td>";

            if ($table[$i]['error_t'] == 1) {
                echo "<td class='warning'>" . date("d.m.Y", strtotime($table[$i]['date_t'])) . "</td>";
                $_SESSION['arr_date_t'][] = date("d.m.Y", strtotime($table[$i]['date_t']));
            } elseif ($table[$i]['error_t'] == 3) {
                echo "<td class='danger'> Нет данных </td>";
                $_SESSION['arr_date_t'][] = "Нет данных";
            } elseif ($table[$i]['error_t'] == 4) {
                echo "<td class='text-center'> -</td>";
                $_SESSION['arr_date_t'][] = "-";
            } elseif ($table[$i]['error_t'] == 0) {
                echo "<td>" . date("d.m.Y", strtotime($table[$i]['date_t'])) . "</td>";
                $_SESSION['arr_date_t'][] = date("d.m.Y", strtotime($table[$i]['date_t']));
            } elseif ($table[$i]['error_t'] == 8) {
                if (strtotime(date('1970-01-01')) == strtotime($table[$i]['date_t']) or $table[$i]['date_t'] == "") {
                    echo "<td class='success'>Нет данных</td>";
                    $_SESSION['arr_date_t'][] = "Нет данных";
                } else {
                    echo "<td class='success'>" . date("d.m.Y", strtotime($table[$i]['date_t'])) . "</td>";
                    $_SESSION['arr_date_t'][] = date("d.m.Y", strtotime($table[$i]['date_t']));
                }
            }

            if ($table[$i]['error_w'] == 1) {
                echo "<td class='warning'>" . date("d.m.Y", strtotime($table[$i]['date_w'])) . "</td>";
                $_SESSION['arr_date_w'][] = date("d.m.Y", strtotime($table[$i]['date_w']));
            } elseif ($table[$i]['error_w'] == 3) {
                echo "<td class='danger'> Нет данных </td>";
                $_SESSION['arr_date_w'][] = "Нет данных";
            } elseif ($table[$i]['error_w'] == 4) {
                echo "<td class='text-center'> - </td>";
                $_SESSION['arr_date_w'][] = "-";
            } elseif ($table[$i]['error_w'] == 0) {
                echo "<td>" . date("d.m.Y", strtotime($table[$i]['date_w'])) . "</td>";
                $_SESSION['arr_date_w'][] = date("d.m.Y", strtotime($table[$i]['date_w']));
            } elseif ($table[$i]['error_w'] == 8) {
                if (strtotime(date('1970-01-01')) == strtotime($table[$i]['date_w']) or $table[$i]['date_w'] == "") {
                    echo "<td class='success'>Нет данных</td>";
                    $_SESSION['arr_date_w'][] = "Нет данных";
                } else {
                    echo "<td class='success'>" . date("d.m.Y", strtotime($table[$i]['date_w'])) . "</td>";
                    $_SESSION['arr_date_w'][] = date("d.m.Y", strtotime($table[$i]['date_w']));
                }
            }

            $_SESSION['arr_plc_id'][] = $table[$i]['plc_id'];
            $_SESSION['arr_id'][] = $m;
            $_SESSION['arr_name'][] = $table[$i]['name'];
            $_SESSION['arr_addr'][] = $table[$i]['addres'];

            $_SESSION['arr_error_t'][] = $table[$i]['error_t'];

            $_SESSION['arr_error_w'][] = $table[$i]['error_w'];

            $m++;
            echo "</tr>";
        }
    }
}

echo "</tbody>";
echo "</table>";
echo 'Время выполнения скрипта: ' . (microtime(true) - $start) . ' сек.';





