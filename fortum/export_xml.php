<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include './include/db_config.php';

if (isset($_POST['date1']) and isset($_POST['date2'])) {
    $date1 = date('Y-m-d', strtotime($_POST['date1'])); //подогнать формат даты по yyyy-mm-dd hh:mm:ss TZD
    $date2 = date('Y-m-d', strtotime($_POST['date2']));
} else {
    $date1 = date('Y-m-d', strtotime('-1 day')); //подогнать формат даты по yyyy-mm-dd hh:mm:ss TZD
    $date2 = date('Y-m-d');
}


/*
 * Список параметров передаваемых в фортум(дополнить 3 трубой по необходимости)
 * 775 - ВНР
 * 282 - Энергия 1
 * 283 - Энергия 2
 * 9 - Теп Энергия 1
 * 17 - Теп Энергия 3
 * 3 - Объем 1
 * 4 - Объем 2 
 * 10 - Объем 3
 * 19 - масса 1
 * 20 - масса 2
 * 21 - масса 3
 * 5 - температура 1
 * 6 - температура 2
 * 12 - температура 3
 * 13 - температура 4
 */

$param = array(775, 9, 282, 17, 283, 19, 20, 21, 3, 4, 10, 5, 6, 12, 13);



//запрос всех обьектов нужно убдет переделать на те что в справочнике для фортума
$sql_object = pg_query('SELECT DISTINCT 
  "Tepl"."Places_cnt"."Name",
  "Places_cnt1"."Name",
  "Places_cnt2"."Name",
  public.fortum_plc.plc_id,
  "Tepl"."PropPlc_cnt"."ValueProp",
  "PropPlc_cnt1"."ValueProp",
  public.fortum_places_cnt.frt_plc
FROM
  "Tepl"."Places_cnt" "Places_cnt1"
  INNER JOIN "Tepl"."Places_cnt" ON ("Places_cnt1".place_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."Places_cnt" "Places_cnt2" ON ("Places_cnt1".plc_id = "Places_cnt2".place_id)
  INNER JOIN public.fortum_plc ON ("Places_cnt2".plc_id = public.fortum_plc.plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" ON (public.fortum_plc.plc_id = "Tepl"."PropPlc_cnt".plc_id)
  INNER JOIN "Tepl"."PropPlc_cnt" "PropPlc_cnt1" ON (public.fortum_plc.plc_id = "PropPlc_cnt1".plc_id)
  INNER JOIN public.fortum_places_cnt ON ("Places_cnt2".plc_id = public.fortum_places_cnt.plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Places_cnt".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."GroupToUserRelations".usr_id = "Tepl"."User_cnt".usr_id)
WHERE
  "Tepl"."PropPlc_cnt".prop_id = 27 AND 
  "PropPlc_cnt1".prop_id = 26 AND 
  "Tepl"."User_cnt"."Login" = \'ФОРТУМ\'
ORDER BY
  public.fortum_plc.plc_id
  ');

while ($row = pg_fetch_row($sql_object)) {
    $plc_id[] = $row[3];
    $plc_array[] = array(
        'id' => $row[3],
        'name' => $row[2],
        'adr' => $row[0] . ', ' . $row[4] . ', ' . $row[5],
        'f_id' => $row[6]
    );
}
//запрос всех приборо учета тепла на обьектах с их серийниками и как бы датой установки которой нету
$sql_devices = pg_query('SELECT DISTINCT 
  "Tepl"."Device_cnt".dev_id,
  "Tepl"."TypeDevices"."Name",
  "Tepl"."Device_Property"."Propert_Value",
  "Tepl"."Device_Property".id_type_property,
  public.fortum_plc.plc_id
FROM
  "Tepl"."Device_cnt"
  INNER JOIN "Tepl"."TypeDevices" ON ("Tepl"."Device_cnt".dev_typ_id = "Tepl"."TypeDevices".dev_typ_id)
  LEFT OUTER JOIN "Tepl"."Device_Property" ON ("Tepl"."Device_cnt".dev_id = "Tepl"."Device_Property".dev_id)
  INNER JOIN public.fortum_plc ON ("Tepl"."Device_cnt".plc_id = public.fortum_plc.plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."Device_cnt".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
WHERE
  "Tepl"."TypeDevices"."Name" NOT LIKE \'%Пульсар%\' AND 
  "Tepl"."User_cnt"."Login" = \'ФОРТУМ\'
ORDER BY
  public.fortum_plc.plc_id,
  "Tepl"."Device_Property".id_type_property');

while ($row = pg_fetch_row($sql_devices)) {
    if ($row[3] == 0) {
        $plc_dev[] = $row[4];
        $dev_array[] = array(
            'id' => $row[0],
            'name' => $row[1],
            'numb' => $row[2],
            'plc_id' => $row[4]
        );
    }
}

$sql_param = pg_query('SELECT DISTINCT 
  ("Tepl"."ParametrResourse"."Name") AS "FIELD_1",
  "Tepl"."ParamResPlc_cnt"."NameGroup",
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."ParamResPlc_cnt".prp_id,
  public.fortum_plc.plc_id
FROM
  "Tepl"."ParametrResourse"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."ParametrResourse"."ParamRes_id" = "Tepl"."ParamResPlc_cnt"."ParamRes_id")
  INNER JOIN "Tepl"."Resourse_cnt" ON ("Tepl"."ParametrResourse".res_id = "Tepl"."Resourse_cnt".res_id)
  INNER JOIN public.fortum_plc ON ("Tepl"."ParamResPlc_cnt".plc_id = public.fortum_plc.plc_id)
WHERE
  "Tepl"."Resourse_cnt"."Name" = \'Тепло\'
ORDER BY
  public.fortum_plc.plc_id,
  "Tepl"."Resourse_cnt"."Name",
  "Tepl"."ParamResPlc_cnt"."NameGroup"');




while ($row = pg_fetch_row($sql_param)) {
    $k = array_search($row[3], $param);
    if ($k !== false) {
        $plc_param[] = $row[5];
        $ed = "";
        $name = "";
        switch ($row[3]) {
//            case 775:
//                $ed = 'Час';
//                $name = 'Время работы прибора учета';
//                break;
//            case 5:
//                $ed = 'Градус';
//                $name = 'Температура теплоносителя на подающем трубопроводе';
//                break;
//            case 6:
//                $ed = 'Градус';
//                $name = 'Температура теплоносителя на обратном трубопроводе';
//                break;
//            case 12:
//                $ed = 'Градус';
//                $name = 'Температура теплоносителя на подающем трубопроводе';
//                break;
//            case 13:
//                $ed = 'Градус';
//                $name = 'Температура теплоносителя на обратном трубопроводе';
//                break;
//            case 19:
//                $name ='Масса теплоносителя в подающем трубопроводе';
//                $ed = 'Тонна';
//                break;
//            case 20:
//                $name ='Масса теплоносителя в обратном трубопроводе';
//                $ed = 'Тонна';
//                break;
//            case 21:
//                $ed = 'Тонна';
//                break;
//            case 282:
//                $ed = 'Гигакалория';
//                break;
//            case 283:
//                $ed = 'Гигакалория';
//                break;
//            case 9:
//                $name = 'Тепловая энергия';
//                $ed = 'Гигакалория';
//                break;
//            case 17:
//                $ed = 'Гигакалория';
//                break;
//            case 3:
//                $name = 'Объем теплоносителя в подающем трубопроводе';
//                $ed = 'Кубометр';
//                break;
//            case 4:
//                $name = 'Объем теплоносителя в обратном трубопроводе';
//                $ed = 'Кубометр';
//                break;
//            case 10:
//
//                $ed = 'Кубометр';
//                break;\

            case 775: $ed = 'Час';
                break;
            case 5:
            case 6:
            case 12:$ed = 'Градус';
                break;
            case 3:
            case 4:
            case 10: $ed = 'Кубометр';
                break;
            case 19:
            case 20:
            case 21: $ed = 'Тонна';
                break;
            case 282:
            case 283:
            case 9:
            case 17:$ed = 'Гигакалория';
                break;
        }

        $param_array[] = array(
            'id' => $row[3],
            'name' => $row[0],
            'name_res' => $row[2],
            'ed' => $ed
        );
    }
}

$sql_archive = pg_query('SELECT DISTINCT 
  public.fortum_plc.plc_id,
  "Tepl"."ParamResPlc_cnt".prp_id,
  "Tepl"."Arhiv_cnt"."DateValue",
  "Tepl"."Arhiv_cnt"."DataValue",
  "Tepl"."ParamResPlc_cnt"."ParamRes_id",
  "Tepl"."ParametrResourse"."Name"
FROM
  "Tepl"."Arhiv_cnt"
  INNER JOIN "Tepl"."ParamResPlc_cnt" ON ("Tepl"."Arhiv_cnt".pr_id = "Tepl"."ParamResPlc_cnt".prp_id)
  INNER JOIN "Tepl"."ParametrResourse" ON ("Tepl"."ParamResPlc_cnt"."ParamRes_id" = "Tepl"."ParametrResourse"."ParamRes_id")
  INNER JOIN public.fortum_plc ON ("Tepl"."ParamResPlc_cnt".plc_id = public.fortum_plc.plc_id)
  INNER JOIN "Tepl"."PlaceGroupRelations" ON ("Tepl"."PlaceGroupRelations".plc_id = "Tepl"."ParamResPlc_cnt".plc_id)
  INNER JOIN "Tepl"."GroupToUserRelations" ON ("Tepl"."GroupToUserRelations".grp_id = "Tepl"."PlaceGroupRelations".grp_id)
  INNER JOIN "Tepl"."User_cnt" ON ("Tepl"."User_cnt".usr_id = "Tepl"."GroupToUserRelations".usr_id)
WHERE
  "Tepl"."Arhiv_cnt".typ_arh = 2 AND 
  "Tepl"."Arhiv_cnt"."DateValue" > \'' . $date1 . '\' AND 
  "Tepl"."Arhiv_cnt"."DateValue" <= \'' . $date2 . '\' AND
  "Tepl"."User_cnt"."Login" = \'ФОРТУМ\'
ORDER BY
  public.fortum_plc.plc_id,
  "Tepl"."ParametrResourse"."Name"

');

while ($row = pg_fetch_row($sql_archive)) {
    $archive_array[] = array(
        'plc' => $row[0],
        'id' => $row[1],
        'date' => $row[2],
        'value' => $row[3],
        'res_id' => $row[4]
    );
}

//Создает XML-строку и XML-документ при помощи DOM 
$dom = new DomDocument('1.0', 'UTF-8');

//добавление корня - <books> 
$enkrug = $dom->appendChild($dom->createElement('energykrug'));

//добавление элемента <book> в <books> 
$period = $enkrug->appendChild($dom->createElement('period'));

$from = $period->appendChild($dom->createElement('from'));
$from->appendChild($dom->createTextNode(date('d.m.Y', strtotime($date1))));

$to = $period->appendChild($dom->createElement('to'));
$to->appendChild($dom->createTextNode(date('d.m.Y', strtotime($date2))));

$result_tag = $enkrug->appendChild($dom->createElement('result'));
$code = $result_tag->appendChild($dom->createElement('code'));
$code->appendChild($dom->createTextNode('0'));
$descr = $result_tag->appendChild($dom->createElement('description'));
$descr->appendChild($dom->createTextNode('Выгрузка сформирована успешно.'));

$places = $enkrug->appendChild($dom->createElement('places'));
for ($i = 0; $i < count($plc_array); $i++) {
    $place = $places->appendChild($dom->createElement('place'));
    // описание обьекта установки
    $id = $place->appendChild($dom->createElement('id'));
    $id->appendChild($dom->createTextNode($plc_array[$i]['f_id']));
    $name = $place->appendChild($dom->createElement('name'));
    $name->appendChild($dom->createTextNode($plc_array[$i]['name']));
    $address = $place->appendChild($dom->createElement('address'));
    $address->appendChild($dom->createTextNode($plc_array[$i]['adr']));
    //описание прибора учета
    $devices = $place->appendChild($dom->createElement('devices'));
    $device = $devices->appendChild($dom->createElement('device'));
    //нахождение ключей 
    $kd = array_search($plc_array[$i]['id'], array_column($dev_array, 'plc_id'));
    $dev_id = $device->appendChild($dom->createElement('id'));
    $dev_id->appendChild($dom->createTextNode($dev_array[$kd]['id']));
    $dev_name = $device->appendChild($dom->createElement('name'));
    $dev_name->appendChild($dom->createTextNode($dev_array[$kd]['name']));
    $dev_serial = $device->appendChild($dom->createElement('serial'));
    $dev_serial->appendChild($dom->createTextNode($dev_array[$kd]['numb']));
    $dev_date = $device->appendChild($dom->createElement('installDate'));
    $dev_date->appendChild($dom->createTextNode('01.11.2017'));

    $devParams = $device->appendChild($dom->createElement('deviceParams'));

    $keys = array_keys($plc_param, $plc_array[$i]['id']);

    for ($j = 0; $j < count($keys); $j++) {
        $devParam = $devParams->appendChild($dom->createElement('deviceParam'));
        $param_id = $devParam->appendChild($dom->createElement('Id'));
        $param_id->appendChild($dom->createTextNode($param_array[$keys[$j]]['id']));
        $param_type = $devParam->appendChild($dom->createElement('type'));
        $param_type->appendChild($dom->createTextNode('4'));
        $param_name = $devParam->appendChild($dom->createElement('name'));
        $param_name->appendChild($dom->createTextNode($param_array[$keys[$j]]['name']));
        $param_unit = $devParam->appendChild($dom->createElement('unit'));
        $param_unit->appendChild($dom->createTextNode($param_array[$keys[$j]]['ed']));
        $param_resource = $devParam->appendChild($dom->createElement('resource'));
        $param_resource->appendChild($dom->createTextNode($param_array[$keys[$j]]['name_res']));
        $param_tariff = $devParam->appendChild($dom->createElement('tariff'));
        $param_tariff->appendChild($dom->createTextNode(''));
    }
}

$deletedPlaces = $enkrug->appendChild($dom->createElement('deletedPlaces'));
$deletedDevices = $enkrug->appendChild($dom->createElement('deletedDevices'));

$paramValues = $enkrug->appendChild($dom->createElement('paramValues'));

for ($i = 0; $i < count($archive_array); $i++) {

    $prp_k = array_search($archive_array[$i]['res_id'], $param);
    if ($prp_k !== false) {
        $paramValue = $paramValues->appendChild($dom->createElement('paramValue'));
        $kd = array_search($archive_array[$i]['plc'], array_column($dev_array, 'plc_id'));
        $date = date('d.m.Y', strtotime('-1 day', strtotime($archive_array[$i]['date'])));
        $deviceId = $paramValue->appendChild($dom->createElement('deviceId'));
        $deviceId->appendChild($dom->createTextNode($dev_array[$kd]['id']));

        $prp_id = $paramValue->appendChild($dom->createElement('id'));
        $prp_id->appendChild($dom->createTextNode($archive_array[$i]['res_id']));

        $timestamp = $paramValue->appendChild($dom->createElement('timestamp'));
        $timestamp->appendChild($dom->createTextNode($date));

        $coefficient = $paramValue->appendChild($dom->createElement('coefficient'));
        $coefficient->appendChild($dom->createTextNode('1'));

        $value = $paramValue->appendChild($dom->createElement('value'));
        $value->appendChild($dom->createTextNode($archive_array[$i]['value']));

        $quality = $paramValue->appendChild($dom->createElement('quality'));
        $quality->appendChild($dom->createTextNode(''));
    }
}

//генерация xml 
$dom->formatOutput = true; // установка атрибута formatOutput
// domDocument в значение true 
// save XML as string or file 
$test1 = $dom->saveXML(); // передача строки в test1 
$dom->save('archive/export_' . $date1 . '-' . $date2 . '.xml'); // сохранение файла 

class SendMailSmtpClass {

    /**
     * 
     * @var string $smtp_username - логин
     * @var string $smtp_password - пароль
     * @var string $smtp_host - хост
     * @var string $smtp_from - от кого
     * @var integer $smtp_port - порт
     * @var string $smtp_charset - кодировка
     *
     */
    public $smtp_username;
    public $smtp_password;
    public $smtp_host;
    public $smtp_from;
    public $smtp_port;
    public $smtp_charset;

    public function __construct($smtp_username, $smtp_password, $smtp_host, $smtp_from, $smtp_port = 25, $smtp_charset = "utf-8") {
        $this->smtp_username = $smtp_username;
        $this->smtp_password = $smtp_password;
        $this->smtp_host = $smtp_host;
        $this->smtp_from = $smtp_from;
        $this->smtp_port = $smtp_port;
        $this->smtp_charset = $smtp_charset;
    }

    /**
     * Отправка письма
     * 
     * @param string $mailTo - получатель письма
     * @param string $subject - тема письма
     * @param string $message - тело письма
     * @param string $headers - заголовки письма
     *
     * @return bool|string В случаи отправки вернет true, иначе текст ошибки    *
     */
    function send($mailTo, $subject, $message, $headers) {
        $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $contentMail .= 'Subject: =?' . $this->smtp_charset . '?B?' . base64_encode($subject) . "=?=\r\n";
        $contentMail .= $headers . "\r\n";
        $contentMail .= $message . "\r\n";

        try {
            if (!$socket = @fsockopen($this->smtp_host, $this->smtp_port, $errorNumber, $errorDescription, 30)) {
                throw new Exception($errorNumber . "." . $errorDescription);
            }
            if (!$this->_parseServer($socket, "220")) {
                throw new Exception('Connection error');
            }

            //$server_name = $_SERVER["SERVER_NAME"];
            $server_name = "vsbt174.ru";
            fputs($socket, "HELO $server_name\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: HELO');
            }

            fputs($socket, "AUTH LOGIN\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }



            fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }

            fputs($socket, base64_encode($this->smtp_password) . "\r\n");
            if (!$this->_parseServer($socket, "235")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }

            fputs($socket, "MAIL FROM: <" . $this->smtp_username . ">\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: MAIL FROM');
            }

            $mailTo = ltrim($mailTo, '<');
            $mailTo = rtrim($mailTo, '>');
            fputs($socket, "RCPT TO: <" . $mailTo . ">\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: RCPT TO');
            }

            fputs($socket, "DATA\r\n");
            if (!$this->_parseServer($socket, "354")) {
                fclose($socket);
                throw new Exception('Error of command sending: DATA');
            }

            fputs($socket, $contentMail . "\r\n.\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception("E-mail didn't sent");
            }

            fputs($socket, "QUIT\r\n");
            fclose($socket);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    private function _parseServer($socket, $response) {
        while (@substr($responseServer, 3, 1) != ' ') {
            if (!($responseServer = fgets($socket, 256))) {
                return false;
            }
        }
        if (!(substr($responseServer, 0, 3) == $response)) {
            return false;
        }
        return true;
    }

}

if (!isset($_POST['date1']) and !isset($_POST['date2'])) {
    $sql_mail = pg_query('SELECT id, mail_name, mail_pass, mail_smtp, mail_port, contacts
  FROM mail_settings');
    while ($row = pg_fetch_row($sql_mail)) {
        $arr = array(
            'mail' => $row[1],
            'pass' => $row[2],
            'smtp' => $row[3],
            'port' => $row[4],
            'contact' => $row[5]
        );
    }


    $file1 = "archive/export_" . $date1 . "-" . $date2 . ".xml";
    $fp1 = fopen($file1, "r");
    if ($fp1) {
        echo "файл прикреплен к письму</br>";
    };
    $code_file1 = chunk_split(base64_encode(fread($fp1, filesize($file1))));
    fclose($fp1);


    $text = "------------A4D921C2D10D7DB
Content-Type: text/plain; charset=windows-1251
Content-Transfer-Encoding: 8bit


------------A4D921C2D10D7DB
Content-Type: application/octet-stream; name=\"export_" . $date1 . "-" . $date2 . ".xml\"
Content-transfer-encoding: base64
Content-Disposition: attachment; filename=\"export_" . $date1 . "-" . $date2 . ".xml\"

" . $code_file1 . "
------------A4D921C2D10D7DB
";


    $mailSMTP = new SendMailSmtpClass('' . $arr['mail'] . '', '' . $arr['pass'] . '', '' . $arr['smtp'] . '', 'XML file', '' . $arr['port'] . ''); // создаем экземпляр класса
//$header = "Date: " . date("D, j M Y G:i:s") . " +0700\r\n";
//$header .= "From: =?windows-1251?Q?" . str_replace("+", "_", str_replace("%", "=", urlencode(''.$arr['mail'].''))) . "?= <".$arr['mail'].">\r\n";
//$header .= "X-Mailer: The Bat! (v3.99.3) Professional\r\n";
//$header .= "Reply-To: =?windows-1251?Q?" . str_replace("+", "_", str_replace("%", "=", urlencode(''.$arr['mail'].''))) . "?= <".$arr['mail'].">\r\n";
//$header .= "X-Priority: 3 (Normal)\r\n";
//$header .= "Message-ID: <172562218." . date("YmjHis") . "@fmail.com>\r\n";
//$header .= "To: =?windows-1251?Q?" . str_replace("+", "_", str_replace("%", "=", urlencode('УТСК'))) . "?= <".$arr['contact'].">\r\n";
//$header .= "Subject: =?windows-1251?Q?" . str_replace("+", "_", str_replace("%", "=", urlencode(''))) . "?=\r\n";
//$header .= "MIME-Version: 1.0\r\n";
//$header .= "Content-Type: multipart/mixed; boundary=\"----------A4D921C2D10D7DB\"\r\n";

    $subject = 'Выгрузка для УТСК показаний потребления тепловой энергии за ' . date($date1) . ' по муниципальным учреждениям г. Челябинска';

    $headers = "MIME-Version: 1.0\r\n";


    $headers .= "From: Chgfeeit <" . $arr['mail'] . ">\r\n"; // от кого письмо
    $header .= "From: =?windows-1251?Q?" . str_replace("+", "_", str_replace("%", "=", urlencode('' . $arr['mail'] . ''))) . "?= <" . $arr['mail'] . ">\r\n";
    $header .= "From: =?windows-1251?Q?" . str_replace("+", "_", str_replace("%", "=", urlencode('' . $arr['mail'] . ''))) . "?= <" . $arr['mail'] . ">\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"----------A4D921C2D10D7DB\"\r\n";

    $result = $mailSMTP->send($arr['contact'], $subject, $text, $header); // отправляем письмо
    if ($result === true) {
        echo "Письмо успешно отправлено </br>";
    } else {
        echo "Письмо не отправлено. Ошибка: " . $result . "</br>";
    }
}