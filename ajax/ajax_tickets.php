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
  "Places_cnt1".plc_id,
  "PropPlc_cnt1"."ValueProp",
  "Tepl"."PropPlc_cnt"."ValueProp"
FROM
  "Tepl"."PropPlc_cnt"
  INNER JOIN "Tepl"."Places_cnt" "Places_cnt1" ON ("Tepl"."PropPlc_cnt".plc_id = "Places_cnt1".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Places_cnt1".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Places_cnt1".typ_id = 17 AND 
  "Tepl"."PropPlc_cnt".prop_id = 26 AND 
  "PropPlc_cnt1".prop_id = 27');
while ($result = pg_fetch_row($sql_all_school)) {
    $array_school[] = array(
        'plc_id' => $result[1],
        'adres' => '' . $result[2] . ' ' . $result[3] . '',
        'name' => $result[0]
    );
}

$sql_text = 'SELECT DISTINCT 
        public.ticket.id,
        public.ticket.plc_id,
        public.ticket.date_ticket,
        public.ticket.text_ticket,
        public.ticket.status,
        public.ticket.date_close,
        public.ticket.close_text,
        public.ticket."user"
      FROM
        public.ticket
      WHERE ';

if (isset($_POST['month']) and isset($_POST['year'])) {

    $month = $_POST['month'];
    $year = $_POST['year'];
    $day = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);


    $date1 = date('' . $year . '-' . $month . '-01');
    $date2 = date('' . $year . '-' . $month . '-' . $day . '');


    if ($_POST['param'] == 0) {
        $p1 = 0;
        $p2 = 4;
        $sql_text .= ' public.ticket.status >= ' . $p1 . ' AND 
        public.ticket.status <= ' . $p2 . '  AND
        public.ticket.date_ticket >= \'' . $date1 . '\' and
        public.ticket.date_ticket <= \'' . $date2 . '\'
        ORDER BY
        public.ticket.status DESC,
        ticket.date_ticket,
        ticket.plc_id';
    } elseif ($_POST['param'] == 1) {
        $p1 = 0;
        $p2 = 3;
        $sql_text .= 'public.ticket.status >= ' . $p1 . ' AND 
        public.ticket.status <= ' . $p2 . '  AND
        public.ticket.date_ticket >= \'' . $date1 . '\' and
        public.ticket.date_ticket <= \'' . $date2 . '\'
        ORDER BY
        public.ticket.status DESC,
        ticket.date_ticket DESC,
        ticket.plc_id';
    } elseif ($_POST['param'] == 2) {
        $p1 = 4;
        $p2 = 4;
        $sql_text .= 'public.ticket.status >= ' . $p1 . ' AND 
        public.ticket.status <= ' . $p2 . '  AND
        public.ticket.date_close >= \'' . $date1 . '\' and
        public.ticket.date_close <= \'' . $date2 . '\'
            ORDER BY
        ticket.date_close DESC';
    } elseif ($_POST['param'] == 3) {
        $p1 = 5;
        $p2 = 5;
        $sql_text .= ' public.ticket.status >= ' . $p1 . ' AND 
        public.ticket.status <= ' . $p2 . '  AND
        public.ticket.date_close >= \'' . $date1 . '\' and
        public.ticket.date_close <= \'' . $date2 . '\'
        ORDER BY
        ticket.date_close DESC';
    }



    $sql_tickets = pg_query($sql_text);
} else {



    if ($_POST['param'] == 0) {
        $p1 = 0;
        $p2 = 5;
        $sql_text .= ' public.ticket.status >= ' . $p1 . ' AND 
        public.ticket.status <= ' . $p2 . ' 
        ORDER BY
        public.ticket.status DESC,
        ticket.date_ticket,
        ticket.plc_id';
    } elseif ($_POST['param'] == 1) {
        $p1 = 0;
        $p2 = 3;
        $sql_text .= 'public.ticket.status >= ' . $p1 . ' AND 
        public.ticket.status <= ' . $p2 . '
        ORDER BY
        public.ticket.status DESC,
        ticket.date_ticket DESC,
        ticket.plc_id';
    } elseif ($_POST['param'] == 2) {
        $p1 = 4;
        $p2 = 5;
        $sql_text .= 'public.ticket.status >= ' . $p1 . ' AND 
        public.ticket.status <= ' . $p2 . '
            ORDER BY
        ticket.date_close DESC';
    } elseif ($_POST['param'] == 3) {
        $p1 = 5;
        $p2 = 5;
        $sql_text .= 'public.ticket.status >= ' . $p1 . ' AND 
        public.ticket.status <= ' . $p2 . '
            ORDER BY
        ticket.date_close DESC';
    }


    $sql_tickets = pg_query($sql_text);
}



echo '<table class = "table table-responsive table-bordered" >'
 . '<thead id = "thead">'
 . '<tr id = "warning">'
 . '<td><b>№</b></td>'
 . '<td><b>Учереждение</b></td>'
 . '<td><b>Пользователь</b></td>'
 . '<td><b>Дата<br>открытия<br>заявки</b></td>'
 . '<td><b>Описание заявки</b></td>'
 . '<td><b>Срочночть заявки</b></td>'
 . '<td><b>Статус заявки</b></td>'
 . '<td><b>Дата<br>закрытия<br>заявки</b></td>'
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
            $status = "<b>Закрыта</b>";
            echo '<tr>';
            break;
        case 5:
            $status = "Удалена";
            echo '<tr class="success">';
            break;
    }

    if ($result[4] < 4) {
        echo '<td>' . $n . '</td>'
        . '<td><a class="object" id ="' . $result[1] . '">' . $array_school[$key][name] . '</a><br> ' . $array_school[$key][adres] . '</td>'
        . '<td>' . $result[7] . '</td>'
        . '<td>' . date('d.m.Y', strtotime($result[2])) . '</td>'
        . '<td>' . $result[3] . '</td>'
        . '<td>' . $status . '</td>'
        . '<td> <a href="#" class="ticket" id ="' . $result[0] . '">Редактировать...</a></td>'
        . '<td></td>'
        . '</tr>';
        $n++;
    } elseif ($result[4] == 5) {
        echo '<td>' . $n . '</td>'
        . '<td><a class="object" id ="' . $result[1] . '">' . $array_school[$key][name] . '</a><br> ' . $array_school[$key][adres] . '</td>'
        . '<td>' . $result[7] . '</td>'
        . '<td>' . date('d.m.Y', strtotime($result[2])) . '</td>'
        . '<td>' . $result[3] . '</td>'
        . '<td></td>'
        . '<td> Удалена</td>'
        . '<td>' . date('d.m.Y', strtotime($result[5])) . '</td>'
        . '</tr>';
        $n++;
    } else {
        $string = $result[6];
        $searching = "Коррекция";
        $kor = strpos($string, $searching);
        if ($kor === false) {
            $search = "Подключение счетчика ХВ к систем";
            $pod = strpos($string, $search);
            if ($pod === false) {
                echo '<td>' . $n . '</td>'
                . '<td><a class="object" id ="' . $result[1] . '">' . $array_school[$key][name] . '</a><br> ' . $array_school[$key][adres] . '</td>'
                . '<td>' . $result[7] . '</td>'
                . '<td>' . date('d.m.Y', strtotime($result[2])) . '</td>'
                . '<td>' . $result[3] . '<br> Результат: ' . $result[6] . ' </td>'
                . '<td> - </td>'
                . '<td>' . $status . '</td>'
                . '<td>' . date('d.m.Y', strtotime($result[5])) . '</td>'
                . '</tr>';
            } else {
                echo '<td>' . $n . '</td>'
                . '<td><a class="object" id ="' . $result[0] . '">' . $array_school[$key][name] . '</a><br> ' . $array_school[$key][adres] . '</td>'
                . '<td>' . $result[7] . '</td>'
                . '<td>' . date('d.m.Y', strtotime($result[2])) . '</td>';

                $sql_pod = pg_query('SELECT 
                    public.korrect.id,
                    public.korrect.plc_id,
                    public.korrect.prp_id,
                    public.korrect.id_tick,
                    public.korrect.date_time,
                    public.korrect.old_value,
                    public.korrect.new_value,
                    public.korrect.name_prp,
                    public.korrect.date_record
                  FROM
                    public.korrect
                  WHERE
                    public.korrect.id_tick=' . $result[0] . '
                  ORDER BY
                    public.korrect.name_prp');
                $str = '';
                if (pg_num_rows($sql_pod) != 0) {
                    while ($result_pod = pg_fetch_row($sql_pod)) {
                        $str = $str . " <b>Результат подключения счечтика:</b>  " . $result_pod[7] . ". Дата: " . date("d.m.Y", strtotime($result_pod[4])) . " Нач. показания: " . $result_pod[6] . " <br>";
                    }
                }

                echo '<td><b>' . $result[3] . '</b><br> <b>Результат:</b> ' . $result[6] . '<br> ' . $str . ' </td>';

                echo '<td> - </td>'
                . '<td>' . $status . '</td>'
                . '<td>' . date('d.m.Y', strtotime($result[5])) . '</td>'
                . '</tr>';
            }
        } else {
            echo '<td>' . $n . '</td>'
            . '<td><a class="object" id ="' . $result[1] . '">' . $array_school[$key][name] . '</a><br> ' . $array_school[$key][adres] . '</td>'
            . '<td>' . $result[7] . '</td>'
            . '<td>' . date('d.m.Y', strtotime($result[2])) . '</td>';
            $sql_pod = pg_query('SELECT 
                    public.korrect.id,
                    public.korrect.plc_id,
                    public.korrect.prp_id,
                    public.korrect.id_tick,
                    public.korrect.date_time,
                    public.korrect.old_value,
                    public.korrect.new_value,
                    public.korrect.name_prp,
                    public.korrect.date_record
                  FROM
                    public.korrect
                  WHERE
                    public.korrect.id_tick=' . $result[0] . '
                  ORDER BY
                    public.korrect.name_prp');
            $str = '';
            if (pg_num_rows($sql_pod) != 0) {
                while ($result_pod = pg_fetch_row($sql_pod)) {
                    $str = $str . " <b>Результат коррекции счечтика:</b>  " . $result_pod[7] . ". Дата: " . date("d.m.Y", strtotime($result_pod[4])) . " Нач. показания: " . $result_pod[5] . " Кон. показания:  " . $result_pod[6] . " <br>";
                }
            }

            echo '<td><b>' . $result[3] . '</b><br> <b>Результат:</b> ' . $result[6] . '<br> ' . $str . ' </td>';
            echo '<td> - </td>'
            . '<td>' . $status . '</td>'
            . '<td>' . date('d.m.Y', strtotime($result[5])) . '</td>'
            . '</tr>';
        }
        $n++;
    }
}
echo '</tbody></table>';
