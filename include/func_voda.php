<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function summ_voda($voda) {
    $val = array();
    for ($l = 0; $l < count($voda); $l++) {
        $n1 = count($voda[$l]) - 1;
        $z = 0;
        for ($n = 0; $n < count($voda[$l]); $n++) {

            if ($n == $n1) {
                $z = $z;
                // echo "n=" .$n." mas = ". $mass_voda[$l][$n]."    z=".$z."  <br>" ;
            }
            if ($n >= 0 and $n < $n1) {
                if ($voda[$l][$n]['value'])
                    $z = $z + $voda[$l][$n + 1]['value'] - $voda[$l][$n]['value'];
                //echo "n=" .$n." mas = ". $mass_voda[$l][$n]."   mas+1 =  ".$mass_voda[$l][$n+1]. "     z=".$z."  <br>" ;
            }
        }
        $val[$l] = $z;
        //echo "Z ====" . $val[$l] . "  <br>";
    }
    return $val;
}

function summ_voda_korrect($voda, $id_object, $date1, $date2) {

    $sql_korrect = pg_query('SELECT DISTINCT 
            public.korrect.old_value,
            public.korrect.new_value,
            public.korrect.date_record,
            "Tepl"."ParamResPlc_cnt"."ParamRes_id"
          FROM
            "Tepl"."ParamResPlc_cnt"
            INNER JOIN public.korrect ON ("Tepl"."ParamResPlc_cnt".prp_id = public.korrect.prp_id)
          WHERE
            public.korrect.plc_id = ' . $id_object . ' AND 
            public.korrect.date_time >= \'' . $date1 . '\' AND 
            public.korrect.date_time <= \'' . $date2 . '\'');
    $korrec_arr[] = array();
    while ($result = pg_fetch_row($sql_korrect)) {
        $korrec_arr[] = array(
            'date' => $result[2],
            'id_res' => $result[3]
        );
    }

    //print_r($voda);
    $val = array();
    for ($l = 0; $l < count($voda); $l++) {
        //print_r($voda[$l])."<br>";
        $n1 = count($voda[$l]) - 1;
        $z = 0;
        if ($l == 0) {
            $k_voda = count($voda[$l]);
        } else {
            if ($k_voda > count($voda[$l])) {
                $k_voda = count($voda[$l]);
            }
        }

        for ($n = 0; $n < count($voda[$l]); $n++) {

            $k = array_search($voda[$l][$n][date], array_column($korrec_arr, 'date'));
            $k_res = array_search($voda[$l][$n][res_id], array_column($korrec_arr, 'id_res'));
            if ($k !== false and $k_res !== false) {
                //echo $voda[$l][$n][value] . " Коррекция <br>";
            } else {
                if ($n == 0) {
                    //$z = $voda[$l][$n][value];
                    //echo $voda[$l][$n][value] . " z= " . $z . "<br>";
                } else {
                    $z = $z + ($voda[$l][$n][value] - $voda[$l][$n - 1][value]);
                    //echo " z= " . $z . "<br>";
                }
            }
        }
        $val[$l] = $z;
        //echo "Z ====".$z."  <br>";
        //echo "Конец<br>";
    }
    return $val;
}

function summ_voda_for_limit($voda) {
    $val = 0;
    for ($l = 0; $l < count($voda); $l++) {
        $n1 = count($voda[$l]) - 1;
        $z = 0;
        for ($n = 0; $n < count($voda[$l]); $n++) {

            if ($n == $n1) {
                $z = $z;
                // echo "n=" .$n." mas = ". $mass_voda[$l][$n]."    z=".$z."  <br>" ;
            }
            if ($n >= 0 and $n < $n1) {
                if ($voda[$l][$n]['value'])
                    $z = $z + $voda[$l][$n + 1]['value'] - $voda[$l][$n]['value'];
                //echo "n=" .$n." mas = ". $mass_voda[$l][$n]."   mas+1 =  ".$mass_voda[$l][$n+1]. "     z=".$z."  <br>" ;
            }
        }
        $val = $z;
        //echo "Z ====" . $val[$l] . "  <br>";
    }
    return $val;
}

function summ_voda_korrect_for_limit($voda, $id_object, $date1, $date2) {

    $sql_korrect = pg_query('SELECT DISTINCT 
            public.korrect.old_value,
            public.korrect.new_value,
            public.korrect.date_record,
            "Tepl"."ParamResPlc_cnt"."ParamRes_id"
          FROM
            "Tepl"."ParamResPlc_cnt"
            INNER JOIN public.korrect ON ("Tepl"."ParamResPlc_cnt".prp_id = public.korrect.prp_id)
          WHERE
            public.korrect.plc_id = ' . $id_object . ' AND 
            public.korrect.date_time >= \'' . $date1 . '\' AND 
            public.korrect.date_time <= \'' . $date2 . '\'');
    $korrec_arr[] = array();
    while ($result = pg_fetch_row($sql_korrect)) {
        $korrec_arr[] = array(
            'date' => $result[2],
            'id_res' => $result[3]
        );
    }

    //print_r($voda);
    $val = 0;
    for ($l = 0; $l < count($voda); $l++) {
        //print_r($voda[$l])."<br>";
        $n1 = count($voda[$l]) - 1;
        $z = 0;
        if ($l == 0) {
            $k_voda = count($voda[$l]);
        } else {
            if ($k_voda > count($voda[$l])) {
                $k_voda = count($voda[$l]);
            }
        }

        for ($n = 0; $n < count($voda[$l]); $n++) {

            $k = array_search($voda[$l][$n][date], array_column($korrec_arr, 'date'));
            $k_res = array_search($voda[$l][$n][res_id], array_column($korrec_arr, 'id_res'));
            if ($k !== false and $k_res !== false) {
                //echo $voda[$l][$n][value] . " Коррекция <br>";
            } else {
                if ($n == 0) {
                    //$z = $voda[$l][$n][value];
                    //echo $voda[$l][$n][value] . " z= " . $z . "<br>";
                } else {
                    $z = $z + ($voda[$l][$n][value] - $voda[$l][$n - 1][value]);
                    //echo " z= " . $z . "<br>";
                }
            }
        }
        $val = $z;
        //echo "Z ====".$z."  <br>";
        //echo "Конец<br>";
    }
    return $val;
}
