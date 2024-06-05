<?php
class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllUsers(): array {
        $query = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($query);

        if ($result === false) {
            error_log("Query failed: " . $this->conn->error);
            return [];
        }

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        if (empty($users)) {
            error_log("No users found in the database.");
        }

        return $users;
    }
}
?>
