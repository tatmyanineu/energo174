<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include '../../../db_config.php';



$sql = pg_query('SELECT 
  "Tepl"."ParametrResourse"."Name",
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Resourse_cnt"."Name"
FROM
  "Tepl"."ParamResPlc_cnt"
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
WHERE
  "Tepl"."ParamResPlc_cnt".plc_id = ' . $_POST['plc'] . ' AND 
  "Tepl"."Resourse_cnt"."Name" LIKE \'%ВС%\'
ORDER BY
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParametrResourse"."Name"');

$n = 1;
while ($row = pg_fetch_row($sql)) {

    $sql_prop = pg_query('SELECT 
            public.prop_connect.id_connect,
            public.prop_connect.date,
            public.prop_connect.cnt_numb,
            public.prop_connect.prp_id
          FROM
            public.prop_connect
          WHERE
            public.prop_connect.prp_id = ' . $row[1]);

    $p = pg_fetch_all($sql_prop);
    if ($p != null) {
        $date = date("d.m.Y", strtotime($p[0]['date']));
        $numb = $p[0]['cnt_numb'];
        $con = $p[0]['id_connect'];
    }else{
         $date = "";
        $numb = "";
        $con = "";
    }
    echo '
    <h2 class="prp_id" id="' . $row[1] . '">' . $row[2] . ': ' . $row[0] . '</h2>    
    <form id = "fiasPrpForm">
    <div class = "row" style = "margin-bottom: 15px;">
    <div class = "col-lg-3 col-md-4 col-xs-12">№ подключения (prp_id=' . $row[1] . ')</div>
    <div class = "col-lg-3 col-md-4 col-xs-12"><input type = "text" id = "id_connect_' . $row[1] . '" class = "form-control" value="'.$con.'"></div>
    </div>
    <div class = "row" style = "margin-bottom: 15px;">
    <div class = "col-lg-3 col-md-4 col-xs-12">№ счетчика</div>
    <div class = "col-lg-3 col-md-4 col-xs-12"><input type = "text" id = "counter_numb_' . $row[1] . '" class = "form-control" value="'.$numb.'"><a href="#" class="link_prp" id="' . $row[1] . '">Ссылка из конфигуратора</a></div>
    </div>
     <div class = "row" style = "margin-bottom: 15px;">
    <div class = "col-lg-3 col-md-4 col-xs-12">Дата установки</div>
    <div class = "col-lg-3 col-md-4 col-xs-12"><input type = "text" id="date_' . $row[1] . '" class = "form-control date" value="'.$date.'"></div>
    </div>
    </form>';
}

echo '<div class="row" style="margin-bottom:  20px;">
          <div class="col-lg-3 col-lg-offset-3 col-md-3 col-md-offset-3 col-xs-12">
            <button class="btn btn-primary btn-lg btn-save-prop">Сохранить</button>
          </div> 
      </div>';
