<?php
require_once __DIR__ . '/../models/UserModel.php';

class AdminController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    public function dashboard(): array {
        return $this->userModel->getAllUsers();
    }
}
?>
