<?php

include 'db_config.php';
session_start();
$date = date();
$id = $_POST['id'];
$dist = $_POST['dist'];


//$num = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);
//$date1 = date('' . $_POST['year'] . '-' . $_POST['month'] . '-01');
//$date1 = date('Y-m-d', strtotime("+1 day", strtotime($date1)));



$date1 = date('Y-m-d', strtotime('+1 day', strtotime($_POST['date1'])));
//$date1 = date('Y-m-d', strtotime('-1 month', strtotime($date1)));


$date2 = date('Y-m-d', strtotime('+1 day', strtotime($_POST['date2'])));

//$date2 = date('' . $_POST['year'] . '-' . $_POST['month'] . '-' . $num);
//$date2 = date('Y-m-d', strtotime("+1 day", strtotime($date2)));



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
                        "Tepl"."User_cnt".usr_id = ' . $id . ' AND
                        "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $date1 . '\' AND 
                        "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\'  
                      ORDER BY
                        "Tepl"."ParamResPlc_cnt".plc_id');




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
                                                "Tepl"."User_cnt".usr_id = ' . $id . ' AND
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



while ($row_archive = pg_fetch_row($sql_archive)) {
    $array_archive[] = array(
        'plc_id' => $row_archive[0],
        'param_id' => $row_archive[2],
        'date' => $row_archive[1]
    );
}

$sql_tickets_reports = pg_query('SELECT 
  public.ticket.id,
  public.ticket.plc_id,
  public.ticket.date_ticket,
  public.ticket.text_ticket,
  public.ticket.status,
  public.ticket.close_date,
  public.ticket.close_text
FROM
  public.ticket
WHERE
  public.ticket.date_ticket >= \'' . $date1 . '\' AND 
  public.ticket.date_ticket <= \'' . $date2 . '\' AND 
  public.ticket.status > 2');

$ticket = array();
while ($result = pg_fetch_row($sql_tickets_reports)) {
    $ticket[] = array(
        'plc_id' => $result[1],
        'tick_id' => $result[0]
    );
}

$prp_array = array(775, 3, 4, 10, 9, 16, 282, 283);
$vnr = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
$v1 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
$v2 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
$v3 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
$q1 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
$q2 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
$q3 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
$q4 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";

for ($i = 0; $i < count($array_archive); $i++) {
    if ($array_archive[$i]['plc_id'] != $array_archive[$i + 1]['plc_id']) {
        $key = array_search($array_archive[$i]['plc_id'], array_column($array_school, 'plc_id'));
        if ($key !== false) {

            if (strtotime($array_archive[$i]['date']) <= strtotime($date2)) {
                $k = array_search($array_archive[$i]['param_id'], $prp_array);
                if ($k !== false) {
                    if ($prp_array[$k] == 775) {
                        $vnr = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                    } elseif ($prp_array[$k] == 3) {
                        $v1 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                    } elseif ($prp_array[$k] == 4) {
                        $v2 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                    } elseif ($prp_array[$k] == 10) {
                        $v3 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                    } elseif ($prp_array[$k] == 9) {
                        $q1 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                    } elseif ($prp_array[$k] == 16) {
                        $q2 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                    } elseif ($prp_array[$k] == 282) {
                        $q3 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                    } elseif ($prp_array[$k] == 283) {
                        $q4 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                    }
                }
            }


            $tick_key = array_search($array_archive[$i]['plc_id'], array_column($ticket, 'plc_id'));
            if ($tick_key !== false) {
                $array[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'id_dist' => $array_school[$key]['id_dist'],
                    'dist' => $array_school[$key]['dist'],
                    'name' => $array_school[$key]['name'],
                    'addres' => $array_school[$key]['addres'],
                    'date' => $array_archive[$i]['date'],
                    'vnr' => $vnr,
                    'v1' => $v1,
                    'v2' => $v2,
                    'v3' => $v3,
                    'q1' => $q1,
                    'q2' => $q2,
                    'q3' => $q3,
                    'q4' => $q4,
                    'ticket' => 1
                );
            } else {
                $array[] = array(
                    'plc_id' => $array_archive[$i]['plc_id'],
                    'id_dist' => $array_school[$key]['id_dist'],
                    'dist' => $array_school[$key]['dist'],
                    'name' => $array_school[$key]['name'],
                    'addres' => $array_school[$key]['addres'],
                    'date' => $array_archive[$i]['date'],
                    'vnr' => $vnr,
                    'v1' => $v1,
                    'v2' => $v2,
                    'v3' => $v3,
                    'q1' => $q1,
                    'q2' => $q2,
                    'q3' => $q3,
                    'q4' => $q4,
                    'ticket' => 0
                );
            }

            $vnr = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
            $v1 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
            $v2 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
            $v3 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
            $q1 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
            $q2 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
            $q3 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
            $q4 = "<span class='glyphicon glyphicon-remove' style='color: red;'></span>";
        }
    } else {
        if (strtotime($array_archive[$i]['date']) <= strtotime($date2)) {
            $k = array_search($array_archive[$i]['param_id'], $prp_array);
            if ($k !== false) {
                if ($prp_array[$k] == 775) {
                    $vnr = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                } elseif ($prp_array[$k] == 3) {
                    $v1 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                } elseif ($prp_array[$k] == 4) {
                    $v2 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                } elseif ($prp_array[$k] == 10) {
                    $v3 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                } elseif ($prp_array[$k] == 9) {
                    $q1 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                } elseif ($prp_array[$k] == 16) {
                    $q2 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                } elseif ($prp_array[$k] == 282) {
                    $q3 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                } elseif ($prp_array[$k] == 283) {
                    $q4 = "<span class='glyphicon glyphicon-ok' style='color: green;'></span>";
                }
            }
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
            'dist' => $array_school[$i]['dist'], 'name' => $array_school[$i]['name'],
            'addres' => $array_school[$i]['addres'],
            'date' => '1970-01-01',
            'vnr' => '<span class="glyphicon glyphicon-minus"></span>',
            'v1' => '<span class="glyphicon glyphicon-minus"></span>',
            'v2' => '<span class="glyphicon glyphicon-minus"></span>',
            'v3' => '<span class="glyphicon glyphicon-minus"></span>',
            'q1' => '<span class="glyphicon glyphicon-minus"></span>',
            'q2' => '<span class="glyphicon glyphicon-minus"></span>',
            'q3' => '<span class="glyphicon glyphicon-minus"></span>',
            'q4' => '<span class="glyphicon glyphicon-minus"></span>',
            'ticket' => 0
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

$sql_device_report = pg_query('SELECT DISTINCT 
  "Tepl"."ParamResPlc_cnt".plc_id,
  "Tepl"."TypeDevices"."Name",
  "Tepl"."TypeDevices".dev_typ_id
FROM
  "Tepl"."ParamResGroupRelations"
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."ParamResGroupRelations".grp_id = "Tepl"."GroupToUserRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParamResGroupRelations".prp_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PointRead" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."PointRead".prp_id)
  INNER JOIN "Tepl"."Device_cnt" ON ("Tepl"."PointRead".dev_id = "Tepl"."Device_cnt".dev_id)
  INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
WHERE
  "Tepl"."User_cnt".usr_id = ' . $id . '');

while ($result = pg_fetch_row($sql_device_report)) {
    $dev_report[] = array(
        'plc_id' => $result[0],
        'device' => $result[1],
        'dev_type_id' => $result[2]
    );
}

echo '<table class="table table-bordered">'
 . '<thead id="thead">'
 . '<tr id="warning">'
 . '<td rowspan =3>№</td>'
 . '<td  rowspan =3>Название</td>'
 . '<td  rowspan =3>Адрес</td>'
 . '<td  rowspan =3>Прибор</td>'
 . '<td colspan=8>Показания</td>'
 . '<td  rowspan =3>Дата</td>'
 . '<td  rowspan =3>Статус</td>'
 . '<td  rowspan =3>С.О.</td></tr>'
 . '<tr id="warning">'
 . '<td>ВНР</td>'
 . '<td colspan=3>Объем</td>'
 . '<td colspan=4>Тепл.Энерг</td>'
 . '</tr>'
 . '<tr  id="warning">'
 . '<td>ВНР</td>'
 . '<td>V1</td>'
 . '<td>V2</td>'
 . '<td>V3</td>'
 . '<td>Q1</td>'
 . '<td>Q2</td>'
 . '<td>Q3(Сумм)</td>'
 . '<td>Q4(Сумм)</td>'
 . '</tr>'
 . '</thead><tbody>';

if ($dist == 0) {
    $n = 1;
    for ($i = 0; $i < count($array); $i++) {
        $key_d = array_search($array[$i]['plc_id'], array_column($dev_report, 'plc_id'));

        if ($key_d !== false) {
            $dev = $dev_report[$key_d]['device'];
        } else {
            $dev = "";
        }
        if (strtotime($array[$i]['date']) == strtotime('1970-01-01')) {
            $date = "<td class='danger'>Нет данных</td>";
            $status = "<td class='danger'>Нет связи</td>";
            if ($array[$i]['ticket'] != 0) {
                $ticket = "<td class='text-center'><a href='#' class='go_ticket' id='".$array[$i]['plc_id']."'><span class='glyphicon glyphicon-wrench'></span></a></td>";
            } else {
                $ticket = "<td></td>";
            }
        } else {

            if (strtotime($date2) > strtotime(date('Y-m-d'))) {
                $kol_day = (strtotime(date('Y-m-d')) - strtotime(date("Y-m-d", strtotime($array[$i]['date'])))) / (60 * 60 * 24);
            } else {
                $kol_day = (strtotime($date2) - strtotime(date("Y-m-d", strtotime($array[$i]['date'])))) / (60 * 60 * 24);
            }


            if ($array[$i]['ticket'] != 0) {
                $ticket = "<td class='text-center'><a href='#' class='go_ticket' id='".$array[$i]['plc_id']."'><span class='glyphicon glyphicon-wrench'></span></a></td>";
            } else {
                $ticket = "<td></td>";
            }

            if ($kol_day > 3) {
                $date = "<td class='danger'>" . date("d.m.Y", strtotime($array[$i]['date'])) . "</td>";
                $status = "<td class='danger'>Нет связи</td>";
            } else {
                $date = "<td>" . date("d.m.Y", strtotime($array[$i]['date'])) . "</td>";
                $status = "<td><a id='link' href ='export_reports_teplo.php?id_object=" . $array[$i]['plc_id'] . "&id=" . $id . "&date1=" . date("Y-m-d", strtotime("-1 day", strtotime($date1))) . "&date2=" . date("Y-m-d", strtotime($date2)) . "'>Скачать</a></td>";
            }
            $kol_day = 0;
        }
        if ($dev_report[$key_d]['dev_type_id'] == 217) {
            $array[$i]['v1'] = '<span class="glyphicon glyphicon-minus"></span>';
            $array[$i]['v2'] = '<span class="glyphicon glyphicon-minus"></span>';
            $array[$i]['v3'] = '<span class="glyphicon glyphicon-minus"></span>';
            $array[$i]['q3'] = '<span class="glyphicon glyphicon-minus"></span>';
            $array[$i]['q4'] = '<span class="glyphicon glyphicon-minus"></span>';
        }

        echo "<tr id='houver'  data-href='object.php?id_object=" . $array[$i]['plc_id'] . "'>"
        . "<td>" . $n++ . "</td>"
        . "<td><a  href='#' class='go_object' id='".$array[$i]['plc_id']."'>" . $array[$i]['name'] . "</a></td>"
        . "<td>" . $array[$i]['addres'] . "</td>"
        . "<td>" . $dev . "</td>"
        . "<td class='text-center'>" . $array[$i]['vnr'] . "</td>"
        . "<td class='text-center'>" . $array[$i]['v1'] . "</td>"
        . "<td class='text-center'>" . $array[$i]['v2'] . "</td>"
        . "<td class='text-center'>" . $array[$i]['v3'] . "</td>"
        . "<td class='text-center'>" . $array[$i]['q1'] . "</td>"
        . "<td class='text-center'>" . $array[$i]['q2'] . "</td>"
        . "<td class='text-center'>" . $array[$i]['q3'] . "</td>"
        . "<td class='text-center'>" . $array[$i]['q4'] . "</td>"
        . "" . $date . ""
        . "" . $status . ""
        . "" . $ticket . ""
        . "</tr>";

        $ticket = '';
    }
} else {
    $n = 1;
    for ($i = 0; $i < count($array); $i++) {
        if ($dist == $array[$i]['id_dist']) {
            $key_d = array_search($array[$i]['plc_id'], array_column($dev_report, 'plc_id'));

            if ($key_d !== false) {
                $dev = $dev_report[$key_d]['device'];
            } else {
                $dev = "";
            }


            if (strtotime($array[$i]['date']) == strtotime('1970-01-01')) {
                $date = "<td class='danger'>Нет данных</td>";
                $status = "<td class='danger'>Нет связи</td>";
                if ($array[$i]['ticket'] != 0) {
                    $ticket = "<td class='text-center'><a href='#' class='go_ticket' id='".$array[$i]['plc_id']."'><span class='glyphicon glyphicon-wrench'></span></a></td>";
                } else {
                    $ticket = "<td></td>";
                }
            } else {



                if ($array[$i]['ticket'] != 0) {
                    $ticket = "<td class='text-center'><a href='#' class='go_ticket' id='".$array[$i]['plc_id']."'><span class='glyphicon glyphicon-wrench'></span></a></td>";
                } else {
                    $ticket = "<td></td>";
                }

                $kol_day = (strtotime($date2) - strtotime(date("Y-m-d", strtotime($array[$i]['date'])))) / (60 * 60 * 24);
                if ($kol_day > 3) {
                    $date = "<td class='danger'>" . date("d.m.Y", strtotime($array[$i]['date'])) . "</td>";
                    $status = "<td class='danger'>Нет связи</td>";
                } else {
                    $date = "<td>" . date("d.m.Y", strtotime($array[$i]['date'])) . "</td>";
                    $status = "<td><a  id='link_" . $array[$i]['plc_id'] . "' href ='export_reports_teplo.php?id_object=" . $array[$i]['plc_id'] . "&id=" . $id . "&date1=" . date("Y-m-d", strtotime("-1 day", strtotime($date1))) . "&date2=" . $date2 . "'>Скачать</a></td>";
                }
                $kol_day = 0;
            }
            if ($dev_report[$key_d]['dev_type_id'] == 217) {
                $array[$i]['v1'] = '<span class="glyphicon glyphicon-minus"></span>';
                $array[$i]['v2'] = '<span class="glyphicon glyphicon-minus"></span>';
                $array[$i]['v3'] = '<span class="glyphicon glyphicon-minus"></span>';
                $array[$i]['q3'] = '<span class="glyphicon glyphicon-minus"></span>';
                $array[$i]['q4'] = '<span class="glyphicon glyphicon-minus"></span>';
            }

            echo "<tr id='houver'  data-href='object.php?id_object=" . $array[$i]['plc_id'] . "'>"
            . "<td>" . $n++ . "</td>"
            . "<td><a  href='#' class='go_object' id='".$array[$i]['plc_id']."'>" . $array[$i]['name'] . "</a></td>"
            . "<td>" . $array[$i]['addres'] . "</td>"
            . "<td>" . $dev . "</td>"
            . "<td class='text-center'>" . $array[$i]['vnr'] . "</td>"
            . "<td class='text-center'>" . $array[$i]['v1'] . "</td>"
            . "<td class='text-center'>" . $array[$i]['v2'] . "</td>"
            . "<td class='text-center'>" . $array[$i]['v3'] . "</td>"
            . "<td class='text-center'>" . $array[$i]['q1'] . "</td>"
            . "<td class='text-center'>" . $array[$i]['q2'] . "</td>"
            . "<td class='text-center'>" . $array[$i]['q3'] . "</td>"
            . "<td class='text-center'>" . $array[$i]['q4'] . "</td>"
            . "" . $date . ""
            . "" . $status . ""
            . "" . $ticket . ""
            . "</tr>";

            $ticket = '';
        }
    }
}
echo "</tbody></table>";
?>