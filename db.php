<?php
interface DBInterface{
    //старт файла
    public function __construct();
    //запрещаем клонирование объекта
    public function __clone();
    //запрещаем восстановление
    public function __wakeup();
    //инициализация обьекта
    public static function getInstance( array $config = array() );
    //запрос с возвратом ответа
    public static function select(string $query, array $val = null): array;
    //добавление материала
    public static function insert(string $query, array $val = null): int;
    //обновление материала
    public static function update(string $query, array $val = null): int;
    //удаление материала
    public static function delete(string $query, array $val = null): int;
    //удаление материала
    public static function error(): string;
    //Закрытие соединения
    public function __destruct();
}
class DB implements DBInterface{
    //сам бьект
    protected static $_instance = [
        'pdo' => null,
        'error' => null,
        'config' => null,
    ];
    //старт файла
    public function __construct(){}
    //запрещаем клонирование объекта
    public function __clone() {}
    //запрещаем восстановление
    public function __wakeup() {}
    public static function getInstance( array $config = array() ) {
        $error = (!isset(
            $config['hostname'],
            $config['database'],
            $config['username'],
            $config['password'],
            $config['dbcollat'])
        ) ? "Error connection to database." : false;
        //инициалезируем обьект
        if (self::$_instance === null) self::$_instance = new self;
        self::$_instance['error'] = $error;
        self::$_instance['config'] = $config;
        //проверка ошибок
        if( !self::$_instance['error'] ){
            //подключение к БД
            try{
                //драйвер подключения
                $dbconn = "mysql:host=$config[hostname];dbname=$config[database]";
                //процесс подключения
                self::$_instance['pdo'] = new PDO( $dbconn, $config['username'], $config['password'] );
                //установка параметров
                self::$_instance['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //переводим кодировку
                self::$_instance['pdo']->exec("SET NAMES '$config[dbcollat]'");
            }catch(PDOException $e){ //вывод ошибки подключения
                self::$_instance['error'] = $e->getMessage();
            }
        }
        //возвращаем подключение
        return self::$_instance;
    }
    //запрос с возвратом ответа
    private static function query(string $query, array $val): PDOStatement {
        $stmt = null;
        try{
            //проверка наличия ошибок
            if(self::$_instance['error']){
                return null;
            }
            //проверка передаваемых параметров
            if(!$val){
                $stmt = self::$_instance['pdo']->query($query);
                $stmt->execute();
            }else{
                $stmt = self::$_instance['pdo']->prepare($query);
                $stmt->execute($val);
            }
        }catch(PDOException $e){ }
        //возврат результата
        return $stmt;
    }
    //запрос с возвратом ответа
    public static function select(string $query = '', array $val = null): array{
        //отправка результата
        $query = self::query($query, $val);
        //проверка ответа
        if( $query === null ){
            return array();
        }
        //возврат результата
        return (isset($query->errorInfo()[0]) and $query->errorInfo()[0] == 00000) ?
            $query->fetchAll(PDO::FETCH_OBJ) : array();
    }
    //добавление материала
    public static function insert(string $query = '', array $val = null): int {
        //отправка результата
        $query = self::query($query, $val);
        //проверка ответа
        if( $query === null ){
            return 0;
        }
        //возврат результата
        return self::$_instance['pdo']->lastInsertId();
    }
    //обновление материала
    public static function update(string $query = '', array $val = null): int {
        //отправка результата
        $query = self::query($query, $val);
        //проверка ответа
        if( $query === null ){
            return 0;
        }
        //возврат результата
        return (isset($query->errorInfo()[0]) and $query->errorInfo()[0] == 00000)?
            $query->rowCount() : 0;
    }
    //удаление материала
    public static function delete(string $query = '', array $val = null): int {
        //отправка результата
        $query = self::query($query, $val);
        //проверка ответа
        if( $query === null ){
            return 0;
        }
        //возврат результата
        return (isset($query->errorInfo()[0]) and $query->errorInfo()[0] == 00000)?
            $query->rowCount() : 0;
    }
    //удаление материала
    public static function error(): string {
        return self::$_instance['error'] ?? '';
    }
    //Закрытие соединения
    public function __destruct(){
        self::$_instance['pdo'] = null;
    }
}