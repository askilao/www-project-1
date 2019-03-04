<?php
class DB {

  private static $db=null;
  private $dsn, $dbname, $user, $password;
  private $dbh = null;

/* Database gets all parameters from env variables sent in docker compose*/
  private function __construct() {
  	$this->user = getenv('MYSQL_ROOT_USER');
  	$this->dbname = getenv('MYSQL_DATABASE');
  	$this->password = getenv('MYSQL_ROOT_PASSWORD');
  	$this->dsn = "mysql:dbname={$this->dbname};host=docker_db_1";

    try {
        $this->dbh = new PDO($this->dsn, $this->user, $this->password);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
  }
  /* If no database connection already exsist, one is created and returned */
  public static function getDBConnection() {
      if (DB::$db==null) {
        DB::$db = new self();
      }
      return DB::$db->dbh;
  }
}