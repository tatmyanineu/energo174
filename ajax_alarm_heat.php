<?php

include 'db_config.php';
session_start();
$date = $_POST['date_afte'];
$time = strtotime("-10 day");
$after_day = $_POST['date_now'];
//echo '<h3 class="text-center">Проверка за период с ' . date("d.m.Y", strtotime($after_day)) . ' по ' . date("d.m.Y", strtotime($date)) . ' </h3>';


$sql_not_error = pg_query('SELECT DISTINCT 
  public.alarm.plc_id
FROM
  public.alarm
WHERE
  public.alarm.text_alarm LIKE \'%Поверка тепло%\' OR
  public.alarm.text_alarm LIKE \'%Интерф%\' OR
  public.alarm.text_alarm LIKE \'%Наводка%\' OR
  public.alarm.text_alarm LIKE \'%Технические работы%\' OR
  public.alarm.text_alarm LIKE \'%Заблокированная Sim-карта%\'');


while ($result = pg_fetch_row($sql_not_error)) {
    $not_error[] = $result[0];
}

$_SESSION['arr_id'] = '';
$_SESSION['arr_name'] = '';
$_SESSION['arr_addr'] = '';
$_SESSION['arr_date'] = '';
$_SESSION['arr_stat'] = '';
$_SESSION['arr_plc_id'] = '';
echo "<table id='main_table' class='table table-bordered'>
                            <thead id='thead'>
                                <tr id='warning'>
                                <td rowspan=2 data-query='0'><b>№</b></td>
                                <td rowspan=2 data-query='1'><b>Учереждение</b></td>
                                <td rowspan=2 data-query='2'><b>Адрес</b></td>
                                <td colspan=2 ><b>Передача данных</b></td>
                                </tr>
                                <tr id='warning'>
                                    <td data-query='3'><b>Дата обновления</b></td>
                                    <td data-query='4'><b>Статус</b></td>
                                </tr>
                            </thead><tbody>";


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



$m = 1;
for ($i = 0; $i < count($table); $i++) {
    $k = array_search($table[$i][plc_id], $not_error);
    if ($k === false) {
        if ($table[$i]['error_t'] > 0 and $table[$i]['error_t'] < 4) {
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
                echo "<td class='text-center'> - </td>";
                $_SESSION['arr_date_t'][] = "-";
            } elseif ($table[$i]['error_t'] == 0) {
                echo "<td>" . date("d.m.Y", strtotime($table[$i]['date_t'])) . "</td>";
                $_SESSION['arr_date_t'][] = date("d.m.Y", strtotime($table[$i]['date_t']));
            }
            $m++;
            echo "<td>Отсутствует: Тепло</td>"
            . "</tr>";
        }
    }
}


/*
  for ($i = 0; $i < count($array_school); $i++) {
  $k = array_search($array_school[$i][plc_id], array_column($main_array, 'plc_id'));
  if ($k !== false) {
  if ($main_array[$k]['marker'] == 3) {


  echo "<tr data-href='object.php?id_object=" . $array_school[$i][plc_id] . "' id ='hover'>";
  echo "<td>" . $n . "</td>"
  . "<td>" . $array_school[$i][name] . "</td>"
  . "<td>" . $array_school[$i][addres] . "</td>"
  . "<td>" . date("d.m.Y", strtotime($main_array[$k]['date_warm'])) . "</td>"
  . "<td>Отсутствует: Тепло</td>"
  . "</tr>";
  //echo "n= " . $n++ . " s= " . $school_name[$j] . " d=" . $water_date[$w] . " <br>";

  $n++;
  }
  } else {
  echo "<tr data-href='object.php?id_object=" . $array_school[$i][plc_id] . "' id ='hover'>";
  echo "<td>" . $n . "</td>"
  . "<td>" . $array_school[$i][name] . "</td>"
  . "<td>" . $array_school[$i][addres] . "</td>"
  . "<td> - </td>"
  . "<td>Отсутствует: Тепло</td>"
  . "</tr>";
  //echo "n= " . $n++ . " s= " . $school_name[$j] . " d=" . $water_date[$w] . " <br>";

  $n++;
  }
  }

  /*

  for ($j = 0; $j < count($school_id); $j++) {
  $plc_err = 1;
  for ($i = 0; $i < count($_SESSION['err_plc']); $i++) {
  if ($school_id[$j] == $_SESSION['err_plc'][$i]) {
  $plc_err = 1;
  }
  }
  if ($plc_err == 1) {

  //echo "n= " . $n++ . "  " . $school_name[$j] . " <br>";
  for ($p = 0; $p < count($water_param_id); $p++) {
  if ($school_id[$j] == $water_param_id[$p]) {
  //echo "n= " . $n++ . " s= " . $school_name[$j] . " <br>";
  $water_error = 1;
  for ($w = 0; $w < count($water_id); $w++) {
  if ($school_id[$j] == $water_id[$w]) {
  if ($water_id[$w] != $water_id[$w + 1]) {
  $water_error = 0;
  if (strtotime($water_date[$w]) != strtotime($date)) {
  echo "<tr data-href='object.php?id_object=" . $school_id[$j] . "' id ='hover'>";
  echo "<td>" . $n . "</td><td>" . $school_name[$j] . "</td><td>" . $school_str[$j] . " " . $school_hs[$j] . "</td><td>" . date("d.m.Y", strtotime($water_date[$w])) . "</td><td>Отсутствует: Тепло</td>";
  //echo "n= " . $n++ . " s= " . $school_name[$j] . " d=" . $water_date[$w] . " <br>";
  $_SESSION['arr_id'][] = $n;
  $_SESSION['arr_name'][] = $school_name[$j];
  $_SESSION['arr_addr'][] = $school_str[$j] . ' ' . $school_hs[$j];
  $_SESSION['arr_date'][] = date("d.m.Y", strtotime($water_date[$w]));
  $_SESSION['arr_stat'][] = 'Отсутствует: Тепло';
  $_SESSION['arr_plc_id'][] = $school_id[$j];
  $n++;
  }
  }
  }
  }
  if ($water_error == 1) {
  echo "<tr data-href='object.php?id_object=" . $school_id[$j] . "' id ='hover'>";
  echo "<td>" . $n . "</td><td>" . $school_name[$j] . "</td><td>" . $school_str[$j] . " " . $school_hs[$j] . "</td><td> Нет данных</td><td>Отсутствует: Тепло</td>";
  $_SESSION['arr_id'][] = $n;
  $_SESSION['arr_name'][] = $school_name[$j];
  $_SESSION['arr_addr'][] = $school_str[$j] . ' ' . $school_hs[$j];
  $_SESSION['arr_date'][] = 'Нет данных';
  $_SESSION['arr_stat'][] = 'Отсутствует: Тепло';
  $_SESSION['arr_plc_id'][] = $school_id[$j];
  //echo "n= " . $n++ . " s= " . $school_name[$j] . " d= нет данных <br>";
  $n++;
  }
  }
  }
  }
  }
  echo "</tbody></table>";
 * */
?>