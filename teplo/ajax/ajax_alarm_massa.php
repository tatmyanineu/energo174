  <?php
include '../../db_config.php';
session_start();
$date = $_POST['date2'];
$time = strtotime("-10 day");
$after_day = $_POST['date1'];
$id=$_POST['id'];
$pogr = $_POST['pogr'];
                   echo '<h3 class="text-center">Проверка за период с '. date("d.m.Y", strtotime($after_day)).' по '. date("d.m.Y", strtotime($date)).' </h3>';
                            $sql_archive = pg_query('SELECT DISTINCT 
                                            ("Tepl"."Arhiv_cnt"."DateValue") AS "FIELD_1",
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id",
                                            "Tepl"."Places_cnt".plc_id,
                                            "Tepl"."Arhiv_cnt"."DataValue",
                                            "Tepl"."Places_cnt"."Name"
                                          FROM
                                            "Tepl"."ParamResPlc_cnt"
                                            INNER JOIN "Tepl"."Arhiv_cnt" ON ("Tepl"."ParamResPlc_cnt".prp_id = "Tepl"."Arhiv_cnt".pr_id)
                                            INNER JOIN "Tepl"."Places_cnt" ON ("Tepl"."ParamResPlc_cnt".plc_id = "Tepl"."Places_cnt".plc_id)
                                            INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
                                            INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
                                            INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
                                          WHERE
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                                             "Tepl"."User_cnt".usr_id = '.$id.' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 5 OR 
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                                             "Tepl"."User_cnt".usr_id = '.$id.' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 6 OR 
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                                            "Tepl"."User_cnt".usr_id = '.$id.' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 12 OR 
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                                             "Tepl"."User_cnt".usr_id = '.$id.' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 19 OR 
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                                             "Tepl"."User_cnt".usr_id = '.$id.' AND 
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 20 OR 
                                            "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" >= \'' . $after_day . '\' AND 
                                            "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date . '\' AND 
                                             "Tepl"."User_cnt".usr_id = '.$id.' AND  
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 21
                                          ORDER BY
                                            "Tepl"."Places_cnt".plc_id,
                                            "Tepl"."Arhiv_cnt"."DateValue",
                                            "Tepl"."ParamResPlc_cnt"."ParamRes_id"');

                            //echo pg_num_rows($sql_archive) . "<br>";
                            $z = 0;
                            $kol = 0;
                            $n = 0;
                            while ($resusl_archive = pg_fetch_row($sql_archive)) {
                                $arr_id[$z] = $resusl_archive[2];
                                $arr_name[$z] = $resusl_archive[4];
                                $arr_param[$z] = $resusl_archive[1];
                                $arr_value[$z] = $resusl_archive[3];
                                $arr_date[$z] = $resusl_archive[0];

                                if ($resusl_archive[2] == 39 or $resusl_archive[2] == 40 or $resusl_archive[2] == 54) {
                                    if ($z != 0) {
                                        $i++;
                                    }
                                } else {

                                    if ($z != 0) {

                                        if ($arr_id[$z - 1] == $resusl_archive[2]) {
                                            if (strtotime($arr_date[$z - 1]) == strtotime($resusl_archive[0])) {
                                                if ($arr_param[$z - 1] == 5 and $arr_param[$z] == 6) {
                                                    $temp = 0;

                                                    if ($arr_value[$z] > $arr_value[$z - 1]) {

                                                        $sql_massa = pg_query('SELECT DISTINCT
                                                      "Tepl"."Arhiv_cnt"."DataValue",
                                                      "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                                                      FROM
                                                      "Tepl"."Arhiv_cnt"
                                                      INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."Arhiv_cnt".pr_id = "Tepl"."ParamResPlc_cnt".prp_id)
                                                      WHERE
                                                      "Tepl"."Arhiv_cnt"."DateValue" = \'' . $arr_date[$z] . '\' AND
                                                      "Tepl"."ParamResPlc_cnt".plc_id = ' . $arr_id[$z] . ' AND
                                                      "Tepl"."Arhiv_cnt".typ_arh = 2 AND
                                                      "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 19 OR
                                                      "Tepl"."Arhiv_cnt"."DateValue" = \'' . $arr_date[$z] . '\'AND
                                                      "Tepl"."ParamResPlc_cnt".plc_id = ' . $arr_id[$z] . ' AND
                                                      "Tepl"."Arhiv_cnt".typ_arh = 2 AND
                                                      "Tepl"."ParamResPlc_cnt"."ParamRes_id" = 20
                                                      ORDER BY
                                                      "Tepl"."ParamResPlc_cnt"."ParamRes_id"
                                                      ');

                                                        if (pg_num_rows($sql_massa) > 1) {
                                                            $m++;

                                                            $array_temper[$m] = array(
                                                                'id_object' => '' . $arr_id[$z] . '',
                                                                'name' => '' . $arr_name[$z] . '',
                                                                'date' => '' . $arr_date[$z] . '',
                                                                'massa1' => '' . number_format(pg_fetch_result($sql_massa, 0, 0), 2) . '',
                                                                'massa2' => '' . number_format(pg_fetch_result($sql_massa, 1, 0), 2) . '',
                                                                'temper1' => '' . $arr_value[$z - 1] . '',
                                                                'temper2' => '' . $arr_value[$z] . ''
                                                            );
                                                        }
                                                    }
                                                }
                                                if ($arr_param[$z - 1] == 19 and $arr_param[$z] == 20) {
                                                    if ($arr_value[$z - 1] == 0) {
                                                        $raz = ((str_replace(',', '.', $arr_value[$z]) / 0.00001) * 100) - 100;
                                                    } else {
                                                        $raz = ((str_replace(',', '.', $arr_value[$z]) / str_replace(',', '.', $arr_value[$z - 1])) * 100) - 100;
                                                    }
                                                    if ($raz > $pogr) {
                                                        $raz_temp = ((str_replace(',', '.', $arr_value[$z - 2]) / str_replace(',', '.', $arr_value[$z - 3])) * 100) - 100;
                                                        $n++;

                                                        $array_name_massa[$n] = $arr_id[$z];

                                                        $array_massa[$n] = array(
                                                            'id_object' => '' . $arr_id[$z] . '',
                                                            'name' => '' . $arr_name[$z] . '',
                                                            'date' => '' . $arr_date[$z] . '',
                                                            'massa1' => '' . $arr_value[$z - 1] . '',
                                                            'massa2' => '' . $arr_value[$z] . '',
                                                            'temper1' => '' . $arr_value[$z - 3] . '',
                                                            'temper2' => '' . $arr_value[$z - 2] . ''
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $z++;
                                }
                            }

if($array_massa != NULL and $array_temper != NULL){
   $array_massa = array_merge($array_massa, $array_temper);
}elseif($array_temper != NULL and $array_temper == NULL){
   $array_massa = $array_massa; 
}elseif($array_temper == NULL and $array_temper != NULL){
    $array_massa = $array_temper; 
}

                            

                            $tmp1 = Array();
                            foreach ($array_massa as &$ma) {
                                $tmp1[] = &$ma["name"];
                            }
                            array_multisort($tmp1, $array_massa);

                            echo "<table id='main_table' class='table table-bordered'>"
                            . "<thead id='thead'>"
                            . "<tr id='warning'>"
                            . "<td rowspan=2><b>№</b></td>"
                            . "<td rowspan=2><b>Название</b></td>"
                            . "<td rowspan=2><b>Дата</b></td>"
                            . "<td colspan=3><b>Масса</b></td>"
                            . "<td colspan=3><b>Температура</b></td>"
                            . "</tr>"
                            . "<tr id='warning'>"
                            . "<td><b>Подача (м1)</b></td>"
                            . "<td><b>Обратка (м2)</b></td>"
                            . "<td><b>Разность (%)</b></td>"
                            . "<td><b>Подача (т1)</b></td>"
                            . "<td><b>Обратка (т2)</b></td>"
                            . "<td><b>Разность (%)</b></td>"
                            . "</tr>"
                            . "</thead>"
                            . "<tbody>";

                            for ($i = 0; $i < count($array_massa); $i++) {
                                if ($array_massa[$i]['name'] == $array_massa[$i + 1]['name'] and strtotime($array_massa[$i]['date']) != strtotime($array_massa[$i + 1]['date'])) {
                                    $kol++;
                                }

                                if ($array_massa[$i]['name'] != $array_massa[$i + 1]['name']) {
                                    if ($kol > 1) {
                                        $f++;
                                        $kol++;

                                        echo "<tr class='for_click warning' id='" . $array_massa[$i]['id_object'] . "'>"
                                        . "<td>" . $f . "</td>"
                                        . "<td><b>" . $array_massa[$i]['name'] . "</b></td>"
                                        . "<td colspan=7><b>" . $kol . "</b></td>"
                                        . "</tr>";
                                        for ($j = 0; $j < count($array_massa); $j++) {
                                            if ($array_massa[$i]['name'] == $array_massa[$j]['name'] and strtotime($array_massa[$j]['date']) != strtotime($array_massa[$j + 1]['date'])) {
                                                //echo "<tr><td></td> <td>" . $array_massa[$j]['name'] . "</td><td>" . date("d.m.Y", strtotime($array_massa[$j]['date'])) . "</td><td>" . $array_massa[$j]['massa1'] . " </td> <td>" . $array_massa[$i]['massa2'] . "</td</tr>";
                                                $raznost_mass = 0;
                                                $raznost_temp = 0;
                                                $raz_massa = 0;
                                                $raznost_temp = ((str_replace(',', '.', $array_massa[$j]['temper2']) / str_replace(',', '.', $array_massa[$j]['temper1'])) * 100) - 100;
                                                if ($array_massa[$j]['temper1'] < $array_massa[$j]['temper2']) {
                                                    $raznost_temp = "<td class = 'danger'>" . number_format($raznost_temp, 3) . "</td>";
                                                } else {
                                                    $raznost_temp = "<td> - </td>";
                                                }


                                                $raznost_mass = ((str_replace(',', '.', $array_massa[$j]['massa2']) / str_replace(',', '.', $array_massa[$j]['massa1'])) * 100) - 100;

                                                if ($array_massa[$j]['massa1'] != 0) {
                                                    if ($raznost_mass > $pogr) {
                                                        $raz_massa = "<td class = 'danger'>" . number_format($raznost_mass, 3) . "</td>";
                                                    } else {
                                                        $raz_massa = "<td> - </td>";
                                                    }
                                                } else {
                                                    if ($array_massa[$j]['massa2'] != 0) {
                                                        $raznost_mass = $array_massa[$j]['massa2'] * 100;
                                                        $raz_massa = "<td class = 'danger'> " . number_format($raznost_mass, 3) . " </td>";
                                                    } elseif ($array_massa[$j]['massa2'] == 0 and $array_massa[$j]['massa1'] == 0) {
                                                        $raz_massa = "<td> - </td>";
                                                    }
                                                }
                                                echo "<tr data-href='object.php?id_object=" . $array_massa[$i]['id_object'] . "' id='hide_" . $array_massa[$i]['id_object'] . "' style= 'display: none;'>"
                                                . "<td></td>"
                                                . "<td>" . $array_massa[$j]['name'] . "</td>"
                                                . "<td>" . date("d.m.Y", strtotime("-1 day", strtotime($array_massa[$j]['date']))) . "</td>"
                                                . "<td>" . number_format($array_massa[$j]['massa1'], 3) . "</td>"
                                                . "<td>" . number_format($array_massa[$j]['massa2'], 3) . "</td>"
                                                . "" . $raz_massa . ""
                                                . "<td>" . number_format($array_massa[$j]['temper1'], 3) . "</td>"
                                                . "<td>" . number_format($array_massa[$j]['temper2'], 3) . "</td>"
                                                . "" . $raznost_temp . ""
                                                . "</tr>";
                                            }
                                        }
                                    } else {
                                        $f++;
                                        $raznost_mass = 0;
                                        $raznost_temp = 0;
                                        $raz_massa = 0;
                                        $raznost_temp = ((str_replace(',', '.', $array_massa[$i]['temper2']) / str_replace(',', '.', $array_massa[$i]['temper1'])) * 100) - 100;
                                        if ($array_massa[$i]['temper1'] < $array_massa[$i]['temper2']) {
                                            $raznost_temp = "<td class = 'danger'>" . number_format($raznost_temp, 3) . "</td>";
                                        } else {
                                            $raznost_temp = "<td> - </td>";
                                        }


                                        $raznost_mass = ((str_replace(',', '.', $array_massa[$i]['massa2']) / str_replace(',', '.', $array_massa[$i]['massa1'])) * 100) - 100;

                                        if ($array_massa[$i]['massa1'] != 0) {
                                            if ($raznost_mass > $pogr) {
                                                $raz_massa = "<td class = 'danger'>" . number_format($raznost_mass, 3) . "</td>";
                                            } else {
                                                $raz_massa = "<td> - </td>";
                                            }
                                        } else {
                                            if ($array_massa[$i]['massa2'] != 0) {
                                                $raznost_mass = $array_massa[$i]['massa2'] * 100;
                                                $raz_massa = "<td class = 'danger'> " . number_format($raznost_mass, 3) . " </td>";
                                            } elseif ($array_massa[$i]['massa2'] == 0 and $array_massa[$i]['massa1'] == 0) {
                                                $raz_massa = "<td> - </td>";
                                            }
                                        }

                                        echo "<tr  data-href='object.php?id_object=" . $array_massa[$i]['id_object'] . "' >"
                                        . "<td>" . $f . "</td>"
                                        . "<td>" . $array_massa[$i]['name'] . "</td>"
                                        . "<td>" . date("d.m.Y", strtotime("-1 day", strtotime($array_massa[$i]['date']))) . "</td>"
                                        . "<td>" . number_format($array_massa[$i]['massa1'], 3) . "</td>"
                                        . "<td>" . number_format($array_massa[$i]['massa2'], 3) . "</td>"
                                        . "" . $raz_massa . ""
                                        . "<td>" . number_format($array_massa[$i]['temper1'], 3) . "</td>"
                                        . "<td>" . number_format($array_massa[$i]['temper2'], 3) . "</td>"
                                        . "" . $raznost_temp . ""
                                        . "</tr>";
                                    }
                                    $kol = 0;
                                }
                            }
                            ?>