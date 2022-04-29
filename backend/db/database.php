<?php

class Database {

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
    public function select(string $query, array $params = null, string $param_types = null, bool $singleRow = null) : array{

        $stmt = $this->connection->prepare($query);
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
    public function insert(string $query, array $params, string $param_types): ?int {

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param($param_types, ...$params);

        if ($stmt->execute()){
            $stmt->close();
            return $this->connection->insert_id;
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
    public function update(string $query, array $params = null, string $param_types = null): bool {

        $stmt = $this->connection->prepare($query);

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


    //TODO: move to user class
    public function checkUserNameAvailable($username) {
        if (!isset($username) || $username == "") {
            return false;
        }

        $result = $this->getUserData($username);
        if (isset($result)) {
            return false;
        }

        return true;
    }


    //TODO: move to user class
    function getUserData($username) {
        $stmt = $this->connection->prepare("SELECT * FROM user WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    //TODO: move to user class
    function registerUser($username, $password, $first_name, $last_name, $user_type) {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->connection->prepare("INSERT INTO user (fk_user_type, first_name, last_name, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_type, $first_name, $last_name, $username, $password_hash);
        $stmt->execute();
        $stmt->close();
    }

}

?>