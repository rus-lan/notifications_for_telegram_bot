<?php
//подключаем файл обработки передаваемых параметров
include_once "opt.php";
//подключаем файл конфигураций
include_once "config.php";
//проверка данных
if( !isset($telegram) ){
    echo "Конфигурационный файл заполнен не корректно
";
    die;
}
//получаем чат telegram для отправки. в параметрах передаем значения по умолчанию
list(
    $chats, //чат куда отсылать сообщение
    $date, // дата начала проверки
    $echo, //необходимо ли выводить системные сообщения
    $tlgm, //необходимо ли выводить json ответа telegram
    $nosql, //автономная обработка, без подключения к базе
    $mess, //маска сообщения
) = my_getopt($telegram['chats'] ?? array() );
//проверка пришедших параметров
if( empty($chats) ){
    echo "Чаты куда паресылать сообщения не найдены
";
    die;
}
//проверка пришедших параметров
if( !isset($telegram['token']) ) {
    echo "Токен бота для отправки не задан
";
    die;
}
$token = $telegram['token'];
//замена маски
$mess = $mess ? $mess : $telegram['mask'];
//проверка необходимости подключения
if($nosql) {
    //подключам файл работы с БД
    include_once "db.php";
    //инициализируем базу данных
    DB::getInstance($db ?? array());
    //получаем количество подписавшихся
    $count = DB::select("SELECT COUNT(`email_id`) AS `count` FROM `emails` WHERE `email_date` >= ? ", [
        $date
    ]);
    //проверка данных
    $count = empty($count) ? 0 : $count[0]->count;
}else{
    //подставляем количество
    $count = 0;
}
//подставляем дату и количество в маску
$mess = str_replace( ['%date%', '%count%'], [$date, $count], $mess );
//выводим ответ если необходимо
echo $echo ? $mess.'
': '';
//преобразуем данные для отправки в телеграм
$mess = urlencode($mess);
//подключаем файл отправки данных по curl
include_once "curl.php";
//если несколько получателей
$res = [];
if(is_array($chats)){
    for ($i=0; isset($chats[$i]); $i++) {
        $res[] = my_file_get_contents( "https://api.telegram.org/bot{$token}/sendMessage?chat_id=".$chats[$i]."&parse_mode=html&text=$mess", $telegram['proxy'] ?? null, $telegram['auth'] ?? null );
    }
}else{
    $res[] = my_file_get_contents( "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chats}&parse_mode=html&text=$mess", $telegram['proxy'] ?? null, $telegram['auth'] ?? null );
}
//вывод json от telegram
if( $tlgm ){
    echo '['.implode(',', $res).']';
}