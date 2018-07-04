<?php
session_destroy();

/*
 * 
 * CREATE TABLE "password_forgot"
  (
  id Serial,
  plc_name text,
  plc_address text,
  fio text,
  email text,
  status integer,
  CONSTRAINT id_pswd PRIMARY KEY (id)
  )
  WITH (
  OIDS=FALSE
  );
  ALTER TABLE "password_forgot"
  OWNER TO postgres;
 * 
 */
include 'db_config.php';
$flag = 0;
if (isset($_POST['submit'])) {

    if ($_POST['name'] != "" & $_POST['adress'] != "" & $_POST['fio'] != "" & $_POST['mail'] != "") {

        $_POST['name'] = pg_escape_string($_POST['name']);
        $_POST['name'] = strip_tags($_POST['name']);
        $_POST['name'] = htmlspecialchars($_POST['name']);
        $_POST['name'] = stripslashes($_POST['name']);
        $_POST['name'] = addslashes($_POST['name']);

        $_POST['adress'] = pg_escape_string($_POST['adress']);
        $_POST['adress'] = strip_tags($_POST['adress']);
        $_POST['adress'] = htmlspecialchars($_POST['adress']);
        $_POST['adress'] = stripslashes($_POST['adress']);
        $_POST['adress'] = addslashes($_POST['adress']);

        $_POST['fio'] = pg_escape_string($_POST['fio']);
        $_POST['fio'] = strip_tags($_POST['fio']);
        $_POST['fio'] = htmlspecialchars($_POST['fio']);
        $_POST['fio'] = stripslashes($_POST['fio']);
        $_POST['fio'] = addslashes($_POST['fio']);

        $data = array(
            'name' => $_POST['name'],
            'adress' => $_POST['adress'],
            'fio' => $_POST['fio'],
            'mail' => $_POST['mail']
        );
        $status = 0;
        $add_data = pg_query('INSERT INTO password_forgot(plc_name, plc_address, fio, email, status)
                         VALUES (\'' . $data['name'] . '\', \'' . $data['adress'] . '\', \'' . $data['fio'] . '\', \'' . $data['mail'] . '\', \'' . $status . '\')');
        $flag++;
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="Content-Style-Type" content="text/css"/>

        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>

        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/npm.js"></script>

    </head>
    <body>

        <div id="login_content" class="container" style="margin: 0px; padding: 0px;">

            <div class="row">
                <div class="col-lg-2 col-md-2">
                    <img src="img/keys.png" style="margin-top: 7px;" alt=""/>
                </div>
                <div class="col-lg-10 col-md-10">

                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <p id="login_name">ВОСТАНОВЛЕНИЕ ДОСТУПА К УЧЕТНОЙ ЗАПИСИ УЧЕРЕЖДЕНИЯ</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-7 col-md-7">
                            <form method="post" id="formLogin">
                                <h4>Наименование учереждения:</h4>
                                <input type="text" class="form-control" name="name" placeholder="Наименование учереждения" required autofocus  autocomplete="off">
                                <h4>Адрес учереждения:</h4>
                                <input type="text" class="form-control" name="adress" placeholder="Адрес учереждения" required autocomplete="off">
                                <h4>ФИО:</h4>
                                <input type="text" class="form-control" name="fio" placeholder="ФИО" required autocomplete="off">
                                <h4>Электронная почта:</h4>
                                <input type="email" class="form-control" name="mail" placeholder="Электронная почта" required autocomplete="off">
                                <label class="checkbox"></label>
                                <input class="btn btn-lg btn-primary btn-block" id="add" type="submit" name="submit" value="Отправить запрос"/>
                                <label class="checkbox"></label>
                                <p>Нажимая кнопку "Отправить запрос" Вы соглашаетесь <br>
                                    с нашей <a target="_blank" href="http://chelenergofond.ru/index.php?option=com_content&view=article&id=103&catid=10&Itemid=102 ">политикой обработки персональных данных</a></p>
                            </form>
                        </div>

                    </div>
                    <?php
                    if ($flag == 1) {
                        echo "<h4 class='text-center'>Ваш запрос передан администратору сайта, в течении суток на электронный адрес <b>" . $_POST['mail'] . "</b>, придут данные для входа в личный кабинет.</h4>";
                        unset($data);
                        unset($_POST['name']);
                        unset($_POST['adress']);
                        unset($_POST['fio']);
                        unset($_POST['mail']);
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="footer"></div>
        <script type="text/javascript">
            $(document).ready(function () {

            });
        </script>
    </body>
</html>




