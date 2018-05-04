<?php
//переменная уведомления
$message = null;
//функция проверка валидности
function validEmail($email){
    return preg_match( "/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", $email );
}
//была ли отправлена форма
if( isset($_POST['email']) ){
    //проверяем валидность пришедшего email
    if ( validEmail( $_POST['email'] ) ) {//email указан верно
        //подключаем файл конфигураций
        include_once "config.php";
        //подключаем файл для работы с БД
        include_once "db.php";
        //инициализируем базу данных
        DB::getInstance( $db ?? array() );
        //добавляем новый элемент в таблицу
        $count = DB::insert("INSERT IGNORE INTO `emails` (`email_value`) VALUES (?)", [
            $_POST['email']
        ]);
        //проверяем добавлена ли запись
        $message = $count ? "Email адрес успешно добавлен" : "Email адрес не добавлен, повторите попытку позднее.";
    }else{
        //выводим сообщение об не корректном email
        $message = "Email адрес указан не правильно.";
    }
}