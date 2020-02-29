<?php

class EasyPdo{

    const DEFAULT_PORT = 3306;

    private $connection = NULL;

    public function __construct(array $config){
        $host = $config['host'];
        $port = !empty($config['port']) ? $config['port'] : self::DEFAULT_PORT;
        $userName = $config['userName'];
        $password = $config['password'];
        $database = $config['database'];
        $options = !empty($config['options']) && is_array($config['options']) ? $config['options'] : [];
        $procotol = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';charset=UTF8';
        try{
            $this->connection = new PDO($procotol, $userName, $password, $options);
        }catch(Exception $exception){
            echo 'ErrorCode: ' . $exception->getCode(), PHP_EOL;
            echo 'ErrorMessage: ' . $exception->getMessage(), PHP_EOL;
            echo '连接数据库失败.', PHP_EOL;
            exit -1;
        }
    }

    public function __call($name, $parameters){
        return call_user_func_array([$this->connection, $name], $parameters);
    }

    public function __destruct(){
        $this->connection = NULL;
        unset($this->connection);
    }
}
