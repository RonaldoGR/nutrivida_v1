<?php  
Class Connection {
    private static $connection = null;

    public function __construct() {}

    
    public static function getConnection() {
        if(!isset(self::$connection)) {
            try {
                self::$connection = new PDO("mysql:host=mysql;port=3306;dbname=nutrivida;charset=utf8", "root", "");                
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
            } catch (PDOException $e) {
               die('Erro de Conexão: ' . $e->getMessage());
            }
        }
        return self::$connection;
      } 
  }
