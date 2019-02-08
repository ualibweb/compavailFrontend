<?php 

class MysqlPDO{
  protected static $user = "acs-read";
  protected static $pass = "ACSacs!22A33C44S$!";
  protected static $database = "acs";
  protected static $host = 'localhost';
  
  public static function connect(){
    $connection_string = "mysql:host=".self::$host.";dbname=".self::$database;
    $dbh = null;
    try{
      $dbh = new PDO($connection_string, self::$user, self::$pass);
    } catch (PDOException $e){
      print $e->getMessage();
    }
    return $dbh;
  }
  
  public function close(){
    $dhb = null;
  }
  
}
