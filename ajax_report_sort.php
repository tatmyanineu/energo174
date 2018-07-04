<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_config.php';
$start = microtime(true);
session_start();
switch ($_POST['id_sort']) {
    case 0:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr1;
        asort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='10'><b>№</b> <span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    case 10:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr1;
        arsort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b> <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    //второй столбец
    case 1:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr2;
        asort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='11'><b>Учереждение</b> <span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    case 11:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr2;
        arsort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b> <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    //третий столбец
    case 2:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr3;
        asort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b> </td>
            <td rowspan=2 data-query='12'><b>Адрес</b> <span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    case 12:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr3;
        arsort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b>  <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
         </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    //четвертый столбец
    case 3:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr4;
        asort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
        </tr>    <tr id='warning'>
                <td data-query='13'><b>Дата обновления</b>  <span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    case 13:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr4;
        arsort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b> <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    //пятый столбец
    case 4:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr5;
        asort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='14'><b>Статус</b>  <span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    case 14:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr5;
        arsort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b> <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    //шестой столбец
    case 5:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr6;
        asort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='15'><b>Данные</b>  <span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    case 15:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr6;
        arsort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
        </tr>    <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b> <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    //седьмой столбец
    case 6:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr7;
        asort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='16'><b>Лимит</b>  <span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    case 16:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr7;
        arsort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b> <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    //восьмой столбец
    case 7:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr9;
        asort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='17'><b>Данные</b>  <span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    case 17:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr9;
        arsort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b>  <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
                <td data-query='8'><b>Лимит</b></td>
            </tr>
        </thead>
        <tbody>";
        break;
    //девятыйстолбец
    case 8:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr9;
        asort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='18'><b>Лимит</b>  <span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
            </tr>
        </thead>
        <tbody>";
        break;
    case 18:
        $arr0 = $_SESSION['rep_id'];
        $arr1 = $_SESSION['rep_m'];
        $arr2 = $_SESSION['rep_name'];
        $arr3 = $_SESSION['rep_addr'];
        $arr4 = $_SESSION['rep_date'];
        $arr5 = $_SESSION['rep_stat'];

        $arr6 = $_SESSION['rep_tep_val'];
        $arr7 = $_SESSION['rep_tep_lim'];
        $arr8 = $_SESSION['rep_tep_col'];

        $arr9 = $_SESSION['rep_vod_val'];
        $arr10 = $_SESSION['rep_vod_lim'];
        $arr11 = $_SESSION['rep_vod_col'];

        $arr12 = $_SESSION['rep_plc_err'];
        $sort_arr = $arr10;
        arsort($sort_arr);

        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2><b>Передача данных</b></td>
            <td colspan=2><b>Тепло (Г.кал.)</b></td>
            <td colspan=2><b>Вода (Куб.м.)</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>Дата обновления</b></td>
                <td data-query='4'><b>Статус</b></td>
                <td data-query='5'><b>Данные</b></td>
                <td data-query='6'><b>Лимит</b></td>
                <td data-query='7'><b>Данные</b></td>
                <td data-query='8'><b>Лимит</b>  <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
            </tr>
        </thead>
        <tbody>";
        break;
}




foreach ($sort_arr as $key => $val) {
    //echo $key." = ". $val ."<br>";
    echo "<tr id='hover' data-href='object.php?id_object=$arr0[$key]'>";
    echo "<td>" . $arr1[$key] . "</td>";
    echo "<td>" . $arr2[$key] . "</td>";
    echo "<td>" . $arr3[$key] . "</td>";

    if ($arr12[$key] == 1) {
        echo "<td>" . $arr4[$key] . "</td>";
        echo "<td>" . $arr5[$key] . "</td>";
    } else {
        echo "<td class='danger'>" . $arr4[$key] . "</td>";
        echo "<td class='danger'>" . $arr5[$key] . "</td>";
    }

    if ($arr8[$key] == 0) {
        echo "<td class='danger'>" . str_replace('.', ',', $arr6[$key]) . "</td>";
        echo "<td class='danger'>" . str_replace('.', ',', $arr7[$key]) . "</td>";
    }
    if ($arr8[$key] == 1) {
        echo "<td class='warning'>" .  str_replace('.', ',', $arr6[$key]). "</td>";
        echo "<td class='warning'>" . str_replace('.', ',', $arr7[$key]) . "</td>";
    }
    if ($arr8[$key] == 2) {
        echo "<td class='success'>" .  str_replace('.', ',', $arr6[$key]) . "</td>";
        echo "<td class='success'>" . str_replace('.', ',', $arr7[$key]) . "</td>";
    }
    if ($arr8[$key] == 3) {
        echo "<td>" .  str_replace('.', ',', $arr6[$key]) . "</td>";
        echo "<td>" . str_replace('.', ',', $arr7[$key]). "</td>";
    }

    if ($arr11[$key] == 0) {
        echo "<td class='danger'>" . $arr9[$key] . "</td>";
        echo "<td class='danger'>" . $arr10[$key] . "</td>";
    }
    if ($arr11[$key] == 1) {
        echo "<td class='warning'>" . $arr9[$key] . "</td>";
        echo "<td class='warning'>" . $arr10[$key] . "</td>";
    }
    if ($arr11[$key] == 2) {
        echo "<td class='success'>" . $arr9[$key] . "</td>";
        echo "<td class='success'>" . $arr10[$key] . "</td>";
    }
    if ($arr11[$key] == 3) {
        echo "<td>" . $arr9[$key] . "</td>";
        echo "<td>" . $arr10[$key] . "</td>";
    }
    echo "</tr>";
}