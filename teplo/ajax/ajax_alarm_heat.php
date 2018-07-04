<?php

include '../../db_config.php';
session_start();

$date1 = $_POST['date1'];
$date2 = $_POST['date2'];
$id= $_POST['id'];
echo '<h3 class="text-center">Проверка за период с ' . date("d.m.Y", strtotime($date1)) . ' по ' . date("d.m.Y", strtotime($date2)) . ' </h3>';

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
                                    <td data-query='4'><b>Ресурс</b></td>
                                </tr>
                            </thead><tbody>";


$school_name = '';
$school_hs = '';
$school_str = '';
$school_id = '';
$sql_object_info = pg_query('SELECT 
                                "Places_cnt1"."Name",
                                "Tepl"."PropPlc_cnt"."ValueProp",
                                "PropPlc_cnt1"."ValueProp",
                                "Places_cnt1".plc_id
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
                                "Tepl"."User_cnt".usr_id = '.$id.'
                              ORDER BY
                                "Tepl"."Places_cnt".plc_id');

while ($result_school_info = pg_fetch_row($sql_object_info)) {
    $school[] = array(
        'plc_id' => $result_school_info[3],
        'name' => $result_school_info[0],
        'addres' => "" . $result_school_info[2] . " " . $result_school_info[1] . ""
    );
}



$sql_heat = pg_query('SELECT DISTINCT 
  "Tepl"."Arhiv_cnt"."DateValue",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."ParamResPlc_cnt".plc_id,
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup"
FROM
  "Tepl"."Arhiv_cnt"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."Arhiv_cnt".pr_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN "Tepl"."ParamResGroupRelations" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."ParamResGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
WHERE
  "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
  "Tepl"."User_cnt".usr_id = '.$id.' AND 
  "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\'
ORDER BY
  "Tepl"."ParamResPlc_cnt".plc_id,
  "Tepl"."ParamResPlc_cnt"."ParamRes_id"');


while ($result_heat_info = pg_fetch_row($sql_heat)) {
    $heat[] = array(
        'plc_id' => $result_heat_info[2],
        'date' => $result_heat_info[0],
        'param_id' => $result_heat_info[1],
        'res_name' => $result_heat_info[3],
        'gr_name' => $result_heat_info[4]
    );
}
//var_dump($heat);

$n = 1;

for ($i = 0; $i < count($school); $i++) {
    $no_plc = 1;
    for ($j = 0; $j < count($heat); $j++) {
        if ($school[$i]['plc_id'] == $heat[$j]['plc_id']) {
            if ($heat[$j]['param_id'] != $heat[$j + 1]['param_id']) {
                if (strtotime($heat[$j]['date']) < strtotime($date2)) {
                    //echo $school[$i]['name'] . " " . $heat[$j]['plc_id'] . " " . $heat[$j]['date'] . " " . $heat[$j]['param_id'] . "<br>";
                    echo "<tr>"
                    . "<td>" . $n . "</td>"
                    . "<td>" . $school[$i]['name'] . "</td>"
                    . "<td>" . $school[$i]['addres'] . "</td>"
                    . "<td>" . date("d.m.Y", strtotime($heat[$j]['date'])) . "</td>"
                    . "<td>" . $heat[$j]['gr_name'] . ": " . $heat[$j]['res_name'] . "</td>"
                    . "</tr>";
                    $n++;
                }
            }
            $no_plc = 0;
        }
    }
    if ($no_plc == 1) {

        echo "<tr>"
        . "<td>" . $n . "</td>"
        . "<td>" . $school[$i]['name'] . "</td>"
        . "<td>" . $school[$i]['addres'] . "</td>"
        . "<td> Нет данных </td>"
        . "<td> Нет данных </td>"
        . "</tr>";
        $n++;
        //echo $school[$i]['name'] . " " . $school[$i]['plc_id'] . "нет данных<br>";
    }
}






echo "</tbody></table>";
?>