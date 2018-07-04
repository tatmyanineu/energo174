<?php
include 'setting.php';
session_start();
$id_object=$_POST['id_object'];
echo $id_object."</br>";
$type_arch=$_POST['type_arch'];
echo $type_arch."</br>";
$_SESSION['type_archive'] = $_POST['type_arch'];
$num = 40;
$page = $_POST['page_num'];
echo $page."</br>";
if(isset($_POST['date_now']) && isset($_POST['date_afte'])){
echo $page."</br>";
if($type_arch==1){
    if($_POST['date_now']>$_POST['date_afte']){
        $date_now = date('Y-m-d H:00:00', strtotime($_POST['date_now']));
        $date_afte =date('Y-m-d H:00:00', strtotime($_POST['date_afte']));
    }else{
        $date_afte = date('Y-m-d H:00:00', strtotime($_POST['date_now']));
        $date_now =date('Y-m-d H:00:00', strtotime($_POST['date_afte']));
    }
}
if($type_arch==2){
    if($_POST['date_now']>$_POST['date_afte']){
        $date_now = date('Y-m-d H:00:00', strtotime($_POST['date_now']));
        $date_afte =date('Y-m-d H:00:00', strtotime($_POST['date_afte']));
    }else{
        $date_afte = date('Y-m-d H:00:00', strtotime($_POST['date_now']));
        $date_now =date('Y-m-d H:00:00', strtotime($_POST['date_afte']));
    }
}    

    
$sql_count_row=pg_query('SELECT  
                          COUNT(DISTINCT("Tepl"."Arhiv_cnt"."DateValue")) AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = '.$id_object.' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = '.$type_arch .' AND
                          "Tepl"."Arhiv_cnt"."DateValue" >= \''.$date_afte.'\' AND 
                          "Tepl"."Arhiv_cnt"."DateValue" <= \''.$date_now.'\'
                          
                        ');    
}else{
 echo $page."123</br>";
//Запрос на вывод количества данным по часовому архиву
$sql_count_row = pg_query('SELECT  
                          COUNT(DISTINCT("Tepl"."Arhiv_cnt"."DateValue")) AS "FIELD_1"
                        FROM
                          "Tepl"."ParamResPlc_cnt"
                          INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                          INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                        WHERE
                          "Tepl"."Places_cnt".plc_id = '.$id_object.' AND 
                          "Tepl"."Arhiv_cnt".typ_arh = '.$type_arch .'
                        ');
                        
}
//Разбиваем по 30 показаний на одну страницу

$posts = pg_num_rows($sql_count_row);
echo $posts;
$total = intval(($posts[0] - 1) / $num) + 1;
$page = intval($page); 
if(empty($page) or $page < 0) $page = 1; 
  if($page > $total) $page = $total; 
$start = $page * $num - $num; 
// Проверяем нужны ли стрелки назад 
if ($page != 1) $pervpage = '<input type="submit" class="pagination-number previous" id="1" value="Начало"> 
                               <input type="submit" class="pagination-number previous" id="'. ($page - 1) .'"value="Назад"> '; 
// Проверяем нужны ли стрелки вперед 
if ($page != $total) $nextpage = ' <input type="submit" class="pagination-number previous" id="'. ($page + 1) .'" value="Вперед"> 
                                   <input type="submit" class="pagination-number previous" id="' .$total. '" value="Конец">'; 

// Находим две ближайшие станицы с обоих краев, если они есть 
if($page - 2 > 0) $page2left = '<input type="submit" class="pagination-number" id="'. ($page - 2) .'" value="'. ($page - 2) .'">'; 
if($page - 1 > 0) $page1left = '<input type="submit" class="pagination-number" id="'. ($page - 1) .'" value="'. ($page - 1) .'">'; 
if($page + 2 <= $total) $page2right = '<input type="submit" class="pagination-number" id="'. ($page + 2) .'" value="'. ($page + 2) .'">'; 
if($page + 1 <= $total) $page1right = '<input type="submit" class="pagination-number" id="'. ($page + 1) .'" value="'. ($page + 1) .'">'; 

// Вывод меню 
echo ''.$pervpage.$page2left.$page1left.'<b class="pagination-number current">'.$page.'</b>'.$page1right.$page2right.$nextpage.''; 

?>