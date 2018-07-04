<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$dir = 'C:\inetpub\wwwroot\pulsar_form\fortum\archive';
$f = scandir($dir, 1);


echo "<table id='main_table' class='table table-bordered'>
        <thead id='thead'>
            <tr id='warning'>
                <td rowspan=2 data-query='0'><b>№</b></td>
                <td rowspan=2 data-query='1'><b>Название</b></td>
                <td rowspan=2 data-query='2'><b>Дата создания</b></td>
                <td rowspan=2 data-query='2'><b>Размер файла</b></td>
                <td colspan=2 ><b>Ссылка</b></td>
            </tr>
        </thead>";
$i = 1;
foreach ($f as $file) {
    if (preg_match('/\.(xml)/', $file)) {
        $x = filesize($dir."\\".$file)/(1024*1024);
        echo '<tr>'
        . '<td>' . $i . '</td>'
        . '<td>' . $file . '</td>'
        . '<td>' . date("d.m.Y h:s", filectime($dir . "\\" . $file)) . '</td>'
        . '<td>' . substr($x, 0,4) . ' mb</td>'
        . '<td><a href="http:\\"' . $_SERVER['SERVER_ADDR'].'\\archive\\' . $file . '" id="file">Скачать</a></td>'
        . '</tr>';
        $i++;
//        // здесь условие
//        if (basename($file) == 'этот файл не трогать')
//            continue;
//        echo $file . '<br/>';
//        echo '<a href="' . $dir . '\\' . $file . '">Скачать</a>';
//        echo "Файл $file в последний раз был изменен: " . date("d.m.Y", filectime($dir . "\\" . $file)) . " <br>";
    }
}