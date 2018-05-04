<?php
//конфиг БД
$db = [
    'hostname' => 'localhost', // расположение БД
    'database' => 'tutorial', //имя БД
    'username' => 'usr', //имя пользователя
    'password' => 'pass', // пароль пользователя
    'dbcollat' => 'utf8', // кодировка
];
//конфиг telegram
$telegram = [
    'token' => '123456:QWER1234QWER1234', // ключ для бота
    'mask' => 'С %date% поступило %count% новых подписок.', // маска сообщения в Telegram
    'chats' => '123123', // чат Telegram куда прийдет отчет
    'proxy' => null, // сервер для отправки через прокси (socks5) - '127.0.0.1:8888'
    'auth' => null, // аккаунт для подключения к прокси серверу - 'user:pass'
];