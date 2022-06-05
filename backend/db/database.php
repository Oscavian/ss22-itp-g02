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
     * @throws InvalidArgumentException - if $query is not a SELECT query
     */
    public static function select(string $query, array $params = null, string $param_types = null, bool $singleRow = null){

        if (self::$db == null) {
            self::$db = new Database();
        }

        if (!preg_match("/(select|SELECT) .+ (from|FROM) .+/", $query)){
            throw new InvalidArgumentException("SQL Query invalid - only SELECT queries allowed for method 'select'!");
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
     * @throws InvalidArgumentException - if $query is not an INSERT query
     */
    public static function insert(string $query, array $params, string $param_types): ?int {

        if (self::$db == null) {
            self::$db = new Database();
        }


        if (!preg_match("/(insert|INSERT) (INTO|into) .+ (VALUES|values) \(.*\)/", $query)){
            throw new InvalidArgumentException("SQL Query invalid - only INSERT queries allowed for method 'insert'!");
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
     * @throws InvalidArgumentException - if $query is not an UPDATE query
     */
    public static function update(string $query, array $params = null, string $param_types = null): bool {

        if (self::$db == null) {
            self::$db = new Database();
        }

        if (!preg_match("/(update|UPDATE) .+ (SET|set) .+/", $query)){
            throw new InvalidArgumentException("SQL Query invalid - only UPDATE queries allowed for method 'update'!");
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

    /**
     * @param string $query
     * @param array|null $params
     * @param string|null $param_types
     * @return bool
     * @throws InvalidArgumentException - if $query is not a DELETE query
     */
    public static function delete(string $query, array $params = null, string $param_types = null): bool {

        if (self::$db == null) {
            self::$db = new Database();
        }

        if (!preg_match("/(delete|DELETE) (FROM|from) .+/", $query)){
            throw new InvalidArgumentException("SQL Query invalid - only DELETE queries allowed for method 'delete'!");
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