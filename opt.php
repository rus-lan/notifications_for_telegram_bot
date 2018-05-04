<?php
//формируем параметры
function my_getopt($defChat = null, $defDid = null){
    //параметры
    $params = array(
        "b" => "bags",    // без аргументов
        "t" => "tlgm",    // без аргументов
        "e" => "echo",    // без аргументов
        "n" => "nsql",    // без аргументов
        "c::" => "chats::",    // Необязательное значение
        "m::" => "mess::",    // Необязательное значение
        "d::" => "date::",    // Необязательное значение
        "h" => "help",    // без аргументов
    );
    //парсим
    $options = getopt( implode('', array_keys($params)), $params);
    //проверка режима вывода ошибок (дебаг режим)
    if( isset($options['b']) or isset($options['bags']) ){
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
    }else{
        error_reporting(0);
        ini_set("display_errors", 0);
    }
    //выводит в консоле сообщение или нет
    $echo = ( isset($options['e']) or isset($options['echo']) ) ? true : false;
    //выводит в консоле сообщение или нет
    $tlgm = ( isset($options['t']) or isset($options['tlgm']) ) ? true : false;
    //автономный режим
    $nosql = ( isset($options['n']) or isset($options['nsql']) ) ? false : true;
    //маска сообщения
    $mess = $options['mess'] ?? $options['m'] ?? null;
    //маска сообщения
    $date = $options['date'] ?? $options['d'] ?? (new DateTime())->modify('-1 day')->format('Y-m-d 00:00:00');
    //вывод помощи
    if( isset($options['help']) or isset($options['h']) ){
        echo "
Исполнение: 
        \033[1mphp index.php [-h|--help] [-m|--mess=message] [-c|--chats=chats_id] [-d|--date=date] [-e|--echo] [-t|--tlgm] [-n|--nsql] [-b|--bags]\033[0m
Опции:
        \033[1m-h  --help\033[0m      вывод подсказок
        \033[1m-m  --mess\033[0m      маска сообщения, по умолчанию: 'За %date% поступило %count% новых подписок.'
        \033[1m-c  --chats\033[0m     список id чатов в телеграмме через пробел
        \033[1m-c  --date\033[0m      дата начала подсчета данных в фомрате 'Y-m-d H:i:s'
        \033[1m-e  --echo\033[0m      выводить ли в консоле текстовый результат
        \033[1m-t  --tlgm\033[0m      выводить ли ответ телеграм сервера
        \033[1m-n  --nsql\033[0m      автономный режим, отключается от базы и вставляет тестовый результат
        \033[1m-b  --bags\033[0m      режим включения дебага
Пример: 
        php index.php --chats=\"1234 4321\" --dids=\"1234 4321\" --mess=\"Дополнительное сообщение\"

";
        die; //выход из скрипта
    }
    //собираем идентификаторы чатов;
    $TEMP = array();
    //получаем данные
    $chats = $options['chats'] ?? $options['c'] ?? $defChat ?? null;
    //преобразуем в массив данных
    $chats = $chats ? explode(' ', $chats) : array();
    //проходимся и обрабатываем данные
    foreach ($chats as $chat) {
        //убираем лишние символы
        if( $chat = trim(strip_tags($chat)) and $chat ){
            $TEMP[] = $chat;
        }
    }
    //обновляем данные
    $chats = $TEMP;

    //вывод поисковых значенией
    if($echo){
        echo "\033[1mChats для рассылки:\033[0m [ ".implode(', ', $chats)." ]
";
    }
    //вывод
    return [$chats, $date, $echo, $tlgm, $nosql, $mess];
}