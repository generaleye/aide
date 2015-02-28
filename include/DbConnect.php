<?php
/**
 * Handling database connection
 * Author: Generaleye
 */
class DbConnect {

    private $conn;

    function __construct() {        
    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        try {
            require_once dirname(__FILE__) . '/Config.php';
            $this->conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USERNAME, DB_PASSWORD);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
        return $this->conn;
    }

    function __destruct() {
        $this->conn = null;
    }

}

?>
