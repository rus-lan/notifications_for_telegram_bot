<?php
    require_once "controller.php";
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Пример Telegram уведомлений</title>
</head>
<body>
<form method="post">
    <fieldset>
        <legend>Подпишитесь на новости</legend>
        <?=(isset($message) and $message)?"<p>$message</p>":"";?>
        <p><label for="email">E-mail</label><input type="email" name="email" id="email" placeholder="info@tarsy.club"></p> </fieldset>
    <p><button type="submit" name="send">Отправить</button></p>
    </fieldset>
</form>
</body>
</html>