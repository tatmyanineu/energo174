<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include 'db_config.php';

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

if (isset($_GET['month']) and isset($_GET['year'])) {

    if ($_GET['param'] == 0) {
        $p1 = 0;
        $p2 = 5;
    } elseif ($_GET['param'] == 1) {
        $p1 = 0;
        $p2 = 3;
    } elseif ($_GET['param'] == 2) {
        $p1 = 4;
        $p2 = 5;
    }

    $month = $_GET['month'];
    $year = $_GET['year'];
    $day = cal_days_in_month(CAL_GREGORIAN, $_GET['month'], $_GET['year']);


    $date1 = date('' . $year . '-' . $month . '-01');
    $date2 = date('' . $year . '-' . $month . '-' . $day . '');

    $sql_tickets = pg_query('SELECT 
    public.ticket.id,
    public.ticket.plc_id,
    public.ticket.date_ticket,
    public.ticket.text_ticket,
    public.ticket.status,
    public.ticket.close_date,
    public.ticket.close_text,
    public.korrect.date_time,
    public.korrect.old_value,
    public.korrect.new_value,
    public.korrect.name_prp
  FROM
    public.korrect
    RIGHT OUTER JOIN public.ticket ON (public.korrect.id_tick = public.ticket.id)
  WHERE
    ticket.status >=' . $p1 . ' and
    ticket.status <=' . $p2 . '  and
    ticket.date_ticket >= \'' . $date1 . '\' and
    ticket.date_ticket <= \'' . $date2 . '\'
  ORDER BY
    ticket.date_ticket,
    ticket.plc_id
  ');
} else {

    if ($_GET['param'] == 0) {
        $p1 = 0;
        $p2 = 5;
    } elseif ($_GET['param'] == 1) {
        $p1 = 0;
        $p2 = 3;
    } elseif ($_GET['param'] == 2) {
        $p1 = 4;
        $p2 = 5;
    }


    $sql_tickets = pg_query('SELECT 
        public.ticket.id,
        public.ticket.plc_id,
        public.ticket.date_ticket,
        public.ticket.text_ticket,
        public.ticket.status,
        public.ticket.close_date,
        public.ticket.close_text,
        public.ticket."user"
      FROM
        public.ticket
      WHERE
        public.ticket.status >= ' . $p1 . ' AND 
        public.ticket.status <= ' . $p2 . '
      ORDER BY
        public.ticket.status DESC,
        ticket.date_ticket,
        ticket.plc_id
  ');
}



// Подключаем класс для работы с excel
require_once('PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('PHPExcel/Writer/Excel5.php');

// Создаем объект класса PHPExcel
$xls = new PHPExcel();

// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();
// Подписываем лист
$sheet->setTitle('Сопроводительная');



//стиль для ячеек которые будут заголовками
$FontStyle11TNR = array(
    'font' => array(
        'bold' => true,
        'size' => 11,
        'name' => 'Times New Roman'
        ));

//стиль для ячеек с простым текстом
$FontStyle11TNRtext = array(
    'font' => array(
        'size' => 11,
        'name' => 'Times New Roman'
        ));
//стиль для оформления границ ячеек
$arrBorderStyle = array(
    'borders' => array(
        // внешняя рамка
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK,
            'color' => array(
                'rgb' => '00000000'
            )
        ),
        // внутренняя
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'rgb' => '00000000'
            )
        )
    )
);
//стиль для строки жирный 14 шрифт
$FontStyle14TNR = array(
    'font' => array(
        'bold' => true,
        'size' => 14,
        'name' => 'Times New Roman'
        ));




$sheet->setCellValue("A2", "№");
$sheet->setCellValue("B2", "Учереждение");

$sheet->setCellValue("C2", "Адрес");
$sheet->setCellValue("D2", "Пользователь");
$sheet->setCellValue("E2", "Дата открытия");
$sheet->setCellValue("F2", "Описание заявки");
$sheet->setCellValue("G2", "Результат выполенния");
$sheet->setCellValue("H2", "Срочность");
$sheet->setCellValue("I2", "Статус");
$sheet->setCellValue("J2", "Дата закрытия");

$sheet->getColumnDimension('A')->setWidth(6);
$sheet->getColumnDimension('B')->setWidth(35);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(15);

$sheet->getColumnDimension('E')->setWidth(14);
$sheet->getColumnDimension('F')->setWidth(40);
$sheet->getColumnDimension('G')->setWidth(60);
$sheet->getColumnDimension('H')->setWidth(13);
$sheet->getColumnDimension('I')->setWidth(10);
$sheet->getColumnDimension('J')->setWidth(14);
$n = 0;
while ($result = pg_fetch_row($sql_tickets)) {
    $key = array_search($result[1], array_column($array_school, 'plc_id'));

    switch ($result[4]) {
        case 0:
            $status = "Обычная";
            break;
        case 1:
            $status = "Срочная";
            break;
        case 2:
            $status = "Критическая";
            break;
        case 4:
            $status = "";
            break;
    }

    if ($result[4] != 4) {
        /* echo '<td>' . $n . '</td>'
          . '<td><a class="object" id ="' . $result[1] . '">' . $array_school[$key][name] . '</a><br> ' . $array_school[$key][adres] . '</td>'
          . '<td>' . $result[11] . '</td>'
          . '<td>' . date('d.m.Y', strtotime($result[2])) . '</td>'
          . '<td>' . $result[3] . '</td>'
          . '<td>' . $status . '</td>'
          . '<td> <a href="#" class="ticket" id ="' . $result[0] . '">Редактировать...</a></td>'
          . '<td>' . $result[5] . '</td>'
          . '</tr>';

         * 
         */

        $sheet->setCellValueByColumnAndRow(0, 3 + $n, "" . $n . "");
        $sheet->setCellValueByColumnAndRow(1, 3 + $n, "" . $array_school[$key][name] . "");
        $sheet->setCellValueByColumnAndRow(2, 3 + $n, "" . $array_school[$key][adres] . "");
        $sheet->getStyleByColumnAndRow(1, 3 + $n)->getAlignment()->setWrapText(true);
        $sheet->getStyleByColumnAndRow(2, 3 + $n)->getAlignment()->setWrapText(true);
        $sheet->setCellValueByColumnAndRow(3, 3 + $n, "" . $result[7] . "");
        $sheet->setCellValueByColumnAndRow(4, 3 + $n, "" . date('d.m.Y', strtotime($result[2])) . "");
        $sheet->setCellValueByColumnAndRow(5, 3 + $n, "" . $result[3] . "");
        $sheet->getStyleByColumnAndRow(5, 3 + $n)->getAlignment()->setWrapText(true);
        $sheet->setCellValueByColumnAndRow(6, 3 + $n, "");
        $sheet->setCellValueByColumnAndRow(7, 3 + $n, "" . $status . "");
        $sheet->setCellValueByColumnAndRow(8, 3 + $n, "Открыта");
        $sheet->setCellValueByColumnAndRow(9, 3 + $n, "" . $result[5] . "");
        $n++;
    } else {
        $string = $result[6];
        $searching = "Коррекция";
        $kor = strpos($string, $searching);
        if ($kor === false) {
            $search = "Подключение счетчика ХВ к систем";
            $pod = strpos($string, $search);
            if ($pod === false) {


                $sheet->setCellValueByColumnAndRow(0, 3 + $n, "" . $n . "");
                $sheet->setCellValueByColumnAndRow(1, 3 + $n, "" . $array_school[$key][name] . "");
                $sheet->setCellValueByColumnAndRow(2, 3 + $n, "" . $array_school[$key][adres] . "");
                $sheet->getStyleByColumnAndRow(1, 3 + $n)->getAlignment()->setWrapText(true);
                $sheet->getStyleByColumnAndRow(2, 3 + $n)->getAlignment()->setWrapText(true);
                $sheet->setCellValueByColumnAndRow(3, 3 + $n, "" . $result[7] . "");
                $sheet->setCellValueByColumnAndRow(4, 3 + $n, "" . date('d.m.Y', strtotime($result[2])) . "");
                $sheet->setCellValueByColumnAndRow(5, 3 + $n, "" . $result[3] . "");
                $sheet->getStyleByColumnAndRow(5, 3 + $n)->getAlignment()->setWrapText(true);
                $sheet->setCellValueByColumnAndRow(6, 3 + $n, "Результат:  " . $result[6] . "");
                $sheet->getStyleByColumnAndRow(6, 3 + $n)->getAlignment()->setWrapText(true);
                $sheet->setCellValueByColumnAndRow(7, 3 + $n, "" . $status . "");
                $sheet->setCellValueByColumnAndRow(8, 3 + $n, "Закрыта");
                $sheet->setCellValueByColumnAndRow(9, 3 + $n, "" . $result[5] . "");
            } else {
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
                        $str = $str . " Результат подключения счечтика:  " . $result_pod[7] . ". Дата: " . date("d.m.Y", strtotime($result_pod[4])) . " Нач. показания: " . $result_pod[6] . " ";
                    }
                }

                $sheet->setCellValueByColumnAndRow(0, 3 + $n, "" . $n . "");
                $sheet->setCellValueByColumnAndRow(1, 3 + $n, "" . $array_school[$key][name] . "");
                $sheet->setCellValueByColumnAndRow(2, 3 + $n, "" . $array_school[$key][adres] . "");
                $sheet->getStyleByColumnAndRow(1, 3 + $n)->getAlignment()->setWrapText(true);
                $sheet->getStyleByColumnAndRow(2, 3 + $n)->getAlignment()->setWrapText(true);
                $sheet->setCellValueByColumnAndRow(3, 3 + $n, "" . $result[7] . "");
                $sheet->setCellValueByColumnAndRow(4, 3 + $n, "" . date('d.m.Y', strtotime($result[2])) . "");
                $sheet->setCellValueByColumnAndRow(5, 3 + $n, "" . $result[3] . "");
                $sheet->getStyleByColumnAndRow(5, 3 + $n)->getAlignment()->setWrapText(true);
                $sheet->setCellValueByColumnAndRow(6, 3 + $n, " Результат:  " . $result[6] . ".  " . $str . "");
                $sheet->getStyleByColumnAndRow(6, 3 + $n)->getAlignment()->setWrapText(true);
                $sheet->setCellValueByColumnAndRow(7, 3 + $n, "" . $status . "");
                $sheet->setCellValueByColumnAndRow(8, 3 + $n, "Закрыта");
                $sheet->setCellValueByColumnAndRow(9, 3 + $n, "" . $result[5] . "");
            }
        } else {
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
                    $str = $str . " Результат коррекции счечтика:  " . $result_pod[7] . ". Дата: " . date("d.m.Y", strtotime($result_pod[4])) . " Нач. показания: " . $result_pod[5] . " Кон. показания:  " . $result_pod[6] . " ";
                }
            }
            $sheet->setCellValueByColumnAndRow(0, 3 + $n, "" . $n . "");
            $sheet->setCellValueByColumnAndRow(1, 3 + $n, "" . $array_school[$key][name] . "");
            $sheet->setCellValueByColumnAndRow(2, 3 + $n, "" . $array_school[$key][adres] . "");
            $sheet->getStyleByColumnAndRow(1, 3 + $n)->getAlignment()->setWrapText(true);
            $sheet->getStyleByColumnAndRow(2, 3 + $n)->getAlignment()->setWrapText(true);
            $sheet->setCellValueByColumnAndRow(3, 3 + $n, "" . $result[7] . "");
            $sheet->setCellValueByColumnAndRow(4, 3 + $n, "" . date('d.m.Y', strtotime($result[2])) . "");
            $sheet->setCellValueByColumnAndRow(5, 3 + $n, "" . $result[3] . " ");
            $sheet->getStyleByColumnAndRow(5, 3 + $n)->getAlignment()->setWrapText(true);
            $sheet->setCellValueByColumnAndRow(6, 3 + $n, "Результат:  " . $result[6] . ".  " . $str . "");
            $sheet->getStyleByColumnAndRow(6, 3 + $n)->getAlignment()->setWrapText(true);
            $sheet->setCellValueByColumnAndRow(7, 3 + $n, "" . $status . "");
            $sheet->setCellValueByColumnAndRow(8, 3 + $n, "Закрыта");
            $sheet->setCellValueByColumnAndRow(9, 3 + $n, "" . $result[5] . "");
        }
        $n++;
    }
}

$sheet->getStyle('A3:J300')->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A3:J300')->getAlignment()->setVertical(
        PHPExcel_Style_Alignment::VERTICAL_CENTER);

$symb1 = 65;
$symb2 = 74;
$val = $n + 2;


$sheet->getStyle('' . chr($symb1) . '2:' . chr($symb2) . '' . $val)->applyFromArray($arrBorderStyle);
$sheet->getStyle('' . chr($symb1) . '2:' . chr($symb2) . '' . $val)->applyFromArray($FontStyle11TNRtext);


header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Заявки.xls");

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
