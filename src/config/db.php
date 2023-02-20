<?php

class Db{
    private $dbhost = "localhost";
    private $dbuser = "root";
    private $dbpass = "root";
    private $dbname = "abone_takip";

    public function connect(){
        $mysql_connection = "mysql:host=$this->dbhost;dbname=$this->dbname;charset=utf8";
        $connection = new PDO($mysql_connection,$this->dbuser,
    $this->dbpass);
        $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //*Bu tanım pdo da herhangi bir sorun olursa bize hataları fırlatması için tanım yaptık
        return $connection;
    }

}