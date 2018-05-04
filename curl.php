<?php
//отправка get запроса
function my_file_get_contents(string $url = '', string $proxy = null, string $proxyauth = null): string {
    //возвращаемые данные
    $data = null;
    try{
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url, //url отправки данных
            CURLOPT_ENCODING => "UTF-8", //кодировка данных
            CURLOPT_HEADER => false, //не возвращать заголовки
            CURLOPT_RETURNTRANSFER => true, //необрабатывать ответ
            CURLOPT_AUTOREFERER => true, //автоматическая установка поля Referer
            CURLOPT_FOLLOWLOCATION => true, //автопереходы при редиректах
            CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', //передаем клиента
            CURLOPT_TIMEOUT => 15, //время ожидание в секундах
            CURLOPT_CONNECTTIMEOUT => 5, //время конекта в секундах
        ]);
        //проверка параметров для прокси соединения
        if($proxy){
            //прокси сервер
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            //используемый драйвер
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
            //необходимо ли авторизироваться
            if($proxyauth){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
            }
        }
        //получам данные
        $data = curl_exec($ch);
        //проверка на ошибку
        $data = $data ? $data : curl_error($ch);
        //закрываем соединение
        curl_close($ch);
    } catch (\Exception $e) {
        $data = null;
    }
    return $data;
}