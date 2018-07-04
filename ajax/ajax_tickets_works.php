<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../db_config.php';

$sql_all_school = pg_query('SELECT 
  "Places_cnt1"."Name",
  "Places_cnt1".plc_id
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
WHERE
  "Places_cnt1".typ_id = 17');
while ($result = pg_fetch_row($sql_all_school)) {
    $array_school[] = array(
        'plc_id' => $result[1],
        'name' => $result[0]
    );
}


$id_object = $_POST['id_object'];
$param = $_POST['param'];
$month = $_POST['month'];
$year = $_POST['year'];
$day = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);


$date1 = date('' . $year . '-' . $month . '-01');
$date2 = date('' . $year . '-' . $month . '-' . $day . '');

$sql_tickets = pg_query('SELECT 
    ticket.id, 
    ticket.plc_id, 
    ticket.date_ticket, 
    ticket.text_ticket, 
    ticket.status, 
    ticket.close_date, 
    ticket.close_text
  FROM 
    public.ticket
  WHERE
    ticket.status =4 and
    ticket.date_ticket >= \'' . $date1 . '\' and
    ticket.date_ticket <= \'' . $date2 . '\'
  ORDER BY
    ticket.status DESC,
    ticket.plc_id
  ');

echo '<table class = "table table-responsive table-bordered" >'
 . '<thead id = "thead">'
 . '<tr id = "warning">'
 . '<td><b>№</b></td>'
 . '<td><b>Учереждение</b></td>'
 . '<td><b>Дата</b></td>'
 . '<td><b>Описание заявки</b></td>'
 . '<td><b>Дата обработки</b></td>'
 . '<td><b>Результат</b></td>'
 . '<td><b>Статус заявки</b></td>'
 . '</tr>'
 . '</thead>'
 . '<tbody>';
$n = 1;
while ($result = pg_fetch_row($sql_tickets)) {
    $key = array_search($result[1], array_column($array_school, 'plc_id'));

    switch ($result[4]) {
        case 0:
            $status = "Обычная";
            echo '<tr>';
            break;
        case 1:
            $status = "Срочная";
            echo '<tr class="warning">';
            break;
        case 2:
            $status = "Критическая";
            echo '<tr class="danger">';
            break;
        case 4:
            $status = "Закрыта";
            echo '<tr class="">';
            break;
        case 5:
            $status = "Закрыта";
            echo '<tr class="success">';
            break;
    }


    echo '<td>' . $n . '</td>'
    . '<td>' . $array_school[$key][name] . '</td>'
    . '<td>' . date('d.m.Y', strtotime($result[2])) . '</td>'
    . '<td>' . $result[3] . '</td>'
    . '<td>' . $result[5] . '</td>'
    . '<td>' . $result[6] . '</td>'
    . '<td>' . $status . '</td>'
   
    . '</tr>';
    $n++;
}
echo '</tbody></table>';
