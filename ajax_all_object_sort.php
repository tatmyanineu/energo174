<?php

include 'db_config.php';
$start = microtime(true);
session_start();

switch ($_POST['id_sort']) {
    case 0:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];

       
        
        $sort_arr = $arr0;
        asort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
        <tr id='warning'>
            <td rowspan=2 data-query='5'><b>№</b> <span class='glyphicon glyphicon-sort-by-alphabet'></span> </td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b></td>
                <td data-query='4'><b>ХВС</b></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;
    case 5:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];
        $sort_arr = $arr0;
        arsort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
         <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b> <span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b></td>
                <td data-query='4'><b>ХВС</b></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;



    case 1:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];
        $sort_arr = $arr1;
        asort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
         <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='6'><b>Учереждение</b><span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b></td>
                <td data-query='4'><b>ХВС</b></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;

    case 6:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];
        $sort_arr = $arr1;
        arsort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
         <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b><span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b></td>
                <td data-query='4'><b>ХВС</b></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;

    case 2:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];
        $sort_arr = $arr2;
        asort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
         <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='7'><b>Адрес</b><span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
         </tr>   <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b></td>
                <td data-query='4'><b>ХВС</b></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;

    case 7:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];
        $sort_arr = $arr2;
        arsort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
         <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b><span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b></td>
                <td data-query='4'><b>ХВС</b></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;

    case 3:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];
        $sort_arr = $arr3;
        asort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
         <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
         </tr>   <tr id='warning'>
                <td data-query='8'><b>ТЕПЛО</b><span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
                <td data-query='4'><b>ХВС</b></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;

    case 8:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];
        $sort_arr = $arr3;
        arsort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
         <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b><span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
                <td data-query='4'><b>ХВС</b></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;
    case 4:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];
        $sort_arr = $arr4;
        asort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
         <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
          </tr>  <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b></td>
                <td data-query='9'><b>ХВС</b><span class='glyphicon glyphicon-sort-by-alphabet'></span></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;

    case 9:
        $arr0 = $_SESSION['arr_id'];
        $arr1 = $_SESSION['arr_name'];
        $arr2 = $_SESSION['arr_addr'];
        $arr3 = $_SESSION['arr_date_t'];
        $arr4 = $_SESSION['arr_date_w'];
        $arr5 = $_SESSION['arr_plc_id'];
        $arr6 = $_SESSION['arr_error_t'];
        $arr7 = $_SESSION['arr_error_w'];
        $sort_arr = $arr4;
        arsort($sort_arr);
        echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
         <tr id='warning'>
            <td rowspan=2 data-query='0'><b>№</b></td>
            <td rowspan=2 data-query='1'><b>Учереждение</b></td>
            <td rowspan=2 data-query='2'><b>Адрес</b></td>
            <td colspan=2 ><b>Дата последней передачи</b></td>
           </tr> <tr id='warning'>
                <td data-query='3'><b>ТЕПЛО</b></td>
                <td data-query='4'><b>ХВС</b><span class='glyphicon glyphicon-sort-by-alphabet-alt'></span></td>
            </tr>
        </thead>";
        echo "<tbody>";
        break;
}
//print_r($sort_arr);

foreach ($sort_arr as $key => $val) {
    //echo $key." = ". $val ."<br>";
    echo "<tr id='hover' data-href='object.php?id_object=$arr5[$key]'>";
    echo "<td>" . $arr0[$key] . "</td>";
    echo "<td>" . $arr1[$key] . "</td>";
    echo "<td>" . $arr2[$key] . "</td>";
    if ($arr6[$key] == 0) {
        echo "<td>" . $arr3[$key] . "</td>";
    } elseif($arr6[$key] == 1) {
        echo "<td class='warning'>" . $arr3[$key] . "</td>";
    }elseif($arr6[$key] == 3){
        echo "<td class='danger'>" . $arr3[$key] . "</td>";
    }elseif($arr6[$key] == 4){
        echo "<td>" . $arr3[$key] . "</td>";
    }
    if ($arr7[$key] == 0) {
        echo "<td>" . $arr4[$key] . "</td>";
    } elseif($arr7[$key] == 1) {
        echo "<td class='warning'>" . $arr4[$key] . "</td>";
    }elseif($arr7[$key] == 3){
        echo "<td class='danger'>" . $arr4[$key] . "</td>";
    }elseif($arr7[$key] == 4){
        echo "<td>" . $arr4[$key] . "</td>";
    }
    echo "</tr>";
}
?>