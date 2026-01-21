<?php
require_once dirname(__FILE__) . '/../Model/ApartmentModel.php';

class ApartmentController {
    private $model;

    public function __construct($conn) {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->model = new ApartmentModel($conn);
    }

    public function handleRequest() {
        $currentUser = $_SESSION['username'] ?? null;
        if (!$currentUser) { header("Location: login.php"); exit; }

        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_unit'])) {
            $unit = trim($_POST['unit_no']);
            $house = $_POST['house_id'];
            $rent = $_POST['rent'];

            if ($unit && $house && $rent) {
                if ($this->model->addUnit($unit, $house, $rent)) {
                    $message = "Apartment unit $unit added successfully!";
                }
            }
        }

        if (isset($_GET['delete_id'])) {
            if ($this->model->deleteUnit($_GET['delete_id'])) {
                header("Location: admin_apartments.php?msg=deleted");
                exit;
            }
        }

        return [
            'houses'   => $this->model->getHouses($currentUser), // Filtered
            'units'    => $this->model->getAllUnits($currentUser), // Filtered
            'message'  => $message,
            'username' => $currentUser
        ];
    }
}
?>