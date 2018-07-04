<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include '../../../db_config.php';
$sql = pg_query('SELECT fias, cdog
  FROM fias_cnt WHERE plc = ' . $_POST['plc'] . '');
$arr = pg_fetch_all($sql);
if (pg_num_rows($sql) > 0) {
    echo '<form id = "fiasDataForm">
    <div class = "row" style = "margin-bottom: 15px;">
    <div class = "col-lg-3 col-md-4 col-xs-12">ФИАС</div>
    <div class = "col-lg-3 col-md-4 col-xs-12"><input type = "text" id = "fias" class = "form-control" value="' . $arr[0]['fias'] . '"></div>
    </div>
    <div class = "row" style = "margin-bottom: 15px;">
    <div class = "col-lg-3 col-md-4 col-xs-12">Договор</div>
    <div class = "col-lg-3 col-md-4 col-xs-12"><input type = "text" id = "cdog" class = "form-control" value="' . $arr[0]['cdog'] . '"> <a href="#" class="link_cid" id="' . $_POST['plc'] . '">Ссылка из конфигуратора</a></div>
    </div>
    </form>';
} else {
    echo '<form id = "fiasDataForm">
    <div class = "row" style = "margin-bottom: 15px;">
    <div class = "col-lg-3 col-md-4 col-xs-12">ФИАС</div>
    <div class = "col-lg-3 col-md-4 col-xs-12"><input type = "text" id = "fias" class = "form-control"></div>
    </div>
    <div class = "row" style = "margin-bottom: 15px;">
    <div class = "col-lg-3 col-md-4 col-xs-12">Договор</div>
    <div class = "col-lg-3 col-md-4 col-xs-12"><input type = "text" id = "cdog" class = "form-control" value=""> <a href="#" class="link_cid" id="' . $_POST['plc'] . '">Ссылка из конфигуратора</a></div>
   </div>
    </form>';
}

echo '<div class="row" style="margin-bottom:  20px;">
          <div class="col-lg-3 col-lg-offset-3 col-md-3 col-md-offset-3 col-xs-12">
            <button class="btn btn-primary btn-lg">Сохранить</button>
          </div> 
      </div>';
