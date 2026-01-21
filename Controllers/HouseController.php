<?php
require_once __DIR__ . '/../Model/HouseModel.php';

class HouseController {
    private $model;

    public function __construct($conn) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new HouseModel($conn);
    }

    public function handleRequest() {
        $currentUser = $_SESSION['username'] ?? null;


        if (!$currentUser) {
            header("Location: ../Log_Res/login.php");
            exit();
        }

        $message = '';


        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_house'])) {
            if ($this->model->addHouse($_POST['house_id'], $_POST['house_name'], $_POST['location'], $currentUser)) {
                $message = "House added successfully!";
            }
        }


        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            if ($this->model->deleteHouse($_GET['id'], $currentUser)) {
                header("Location: admin_houses.php?msg=deleted");
                exit();
            }
        }


        $houses = $this->model->getAllHouses($currentUser);

        return [
            'houses' => $houses,
            'message' => $message,
            'username' => $currentUser
        ];
    }
}