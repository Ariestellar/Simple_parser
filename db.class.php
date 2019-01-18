<?php

class DB{

  protected $connection;
  //подключение к базе
  public function __construct($host,$user,$password,$db_name){
    $this->connection =new mysqli($host,$user,$password,$db_name);
    $this->query('SET NAMES UTF8');

    if(mysqli_connect_error()){
      throw new Exception('Could not connect to DB');
    }

  }

  //выполняем запросы
  public function query($sql){
      if(!$this->connection){
        return false;
      }

      $result =$this->connection->query($sql);

      if(mysqli_error($this->connection)){
        throw new Exception(mysqli_error($this->connection));
      }
      return $result;
    }

  public function escape($str){//экранирование строк
    return mysqli_escape_string($this->connection,$str);
  }

//Получить последний вставленный авто инкремент
  public function insertId(){
    return mysqli_insert_id($this->connection);
  }
}

?>
