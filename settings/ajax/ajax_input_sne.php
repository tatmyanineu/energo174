<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../db_config.php';
session_start();

$js = $_POST['arr'];

$sql_all_sim = pg_query('SELECT 
  public."SimPlace_cnt".plc_id,
  "Tepl"."Places_cnt"."Name",
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  public."SimPlace_cnt".sim_number
FROM
  public."SimPlace_cnt"
  INNER JOIN "Tepl"."Places_cnt" ON (public."SimPlace_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON ("Tepl"."Places_cnt".plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON ("Tepl"."Places_cnt".plc_id = "PropPlc_cnt1".plc_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26
ORDER BY
  public."SimPlace_cnt".plc_id');

while ($row = pg_fetch_row($sql_all_sim)) {
    $sim[] = array(
        'plc_id' => $row[0],
        'numb' => $row[4]
    );
}

$sql_max_id = pg_query('SELECT max(id) FROM public."SimNotEroor"');
$max_id = pg_fetch_result($sql_max_id, 0, 0);
$col = 0;
for ($i = 0; $i < count($js); $i++) {
    $numb = preg_replace('~[^0-9]+~', '', $js[$i]);
    $k = array_search($numb, array_column($sim, 'numb'));
    if ($k !== false) {
        $max_id++;
        $col++;
        $search_numb = pg_query('SELECT sim_number FROM public."SimNotEroor" WHERE sim_number = \''.$sim[$k]['numb'].'\'');
        $number = pg_fetch_result($search_numb, 0, 0);

        if ($number != null) {
            echo "<h2 class='text-center'>Номер ".$sim[$k]['numb']." уже пристувует в списе исключений</h2>";
        } else {

            $add_sne = pg_query('INSERT INTO public."SimNotEroor"(id, sim_number) VALUES (' . $max_id . ', \'' . $sim[$k]['numb'] . '\')');

            $search_plc = pg_query('SELECT plc_id, text_alarm FROM public.alarm WHERE plc_id=' . $sim[$k]['plc_id'] . '');
            $plc = pg_fetch_result($search_plc, 0, 0);
            if ($plc != null) {
                $text = '<b>Исключения SIM</b>: заблокированна Sim-карта; ' . pg_fetch_result($search_plc, 0, 1);
                $edit_alarm = pg_query('UPDATE public.alarm SET text_alarm=\'' . $text . '\' , sim_number=\'' . $sim[$k]['numb'] . '\' WHERE plc_id=' . $sim[$k]['plc_id'] . '');
            } else {
                $text = '<b>Исключения SIM</b>: заблокированна Sim-карта';
                $add_alarm = pg_query('INSERT INTO public.alarm(plc_id, date_err, text_alarm, prp_id, sim_number) VALUES (' . $sim[$k]['plc_id'] . ', \'' . date('Y-m-d') . '\', \'' . $text . '\', \'\', \'' . $sim[$k]['numb'] . '\')');
            }
        }
    }
}
