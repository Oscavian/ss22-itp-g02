<?php

class Database {

    private static $db;
    private $connection;

    public function __construct() {
        require_once "dbaccess.php";

        $this->connection = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

        if (isset($this->connection->connect_error)) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }


    /**
     * @param string $query
     * @param array|null $params
     * @param string|null $param_types - for bind_param(), e.g. "ii" for 2 int params
     * @param bool|null $singleRow - if explicitly one row is expected, returns a single assoc arr if true
     * @return array|bool
     */
    public static function select(string $query, array $params = null, string $param_types = null, bool $singleRow = null){

        if (self::$db == null) {
            self::$db = new Database();
        }

        $stmt = self::$db->connection->prepare($query);
        if (isset($params)) {
            $stmt->bind_param($param_types, ...$params);
        }

        if (!$stmt->execute()){
            return false;
        }

        $result = $stmt->get_result();
        $rows = array();
        $stmt->close();

        if (isset($singleRow) && $singleRow){
            return $result->fetch_assoc();
        } else if ($result->num_rows == 0){
            return [];
        } else {
            while ($row = $result->fetch_assoc()){
                $rows[] = $row;
            }

            return $rows;
        }
    }

    /**
     * @param string $query
     * @param array|null $params
     * @param string|null $param_types
     * @return int|null - returns the id of the inserted row as int, returns null if stmt failed
     */
    public static function insert(string $query, array $params, string $param_types): ?int {

        if (self::$db == null) {
            self::$db = new Database();
        }
       
        $stmt = self::$db->connection->prepare($query);
        $stmt->bind_param($param_types, ...$params);

        if ($stmt->execute()){
            $stmt->close();
            return self::$db->connection->insert_id;
        } else {
            $stmt->close();
            return null;
        }
    }

    /**
     * @param string $query
     * @param array|null $params
     * @param string|null $param_types
     * @return bool
     */
    public static function update(string $query, array $params = null, string $param_types = null): bool {

        if (self::$db == null) {
            self::$db = new Database();
        }
        
        $stmt = self::$db->connection->prepare($query);

        if (isset($params)) {
            $stmt->bind_param($param_types, ...$params);
        }

        if ($stmt->execute()){
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
}

?>