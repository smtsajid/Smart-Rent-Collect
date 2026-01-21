<?php
class ApartmentModel {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }


    public function getHouses($username) {
        $stmt = $this->db->prepare("SELECT house_id, house_name FROM houses WHERE created_by = :user ORDER BY house_name ASC");
        $stmt->execute([':user' => $username]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAllUnits($username) {
        $sql = "SELECT a.*, h.house_name 
                FROM apartments a 
                JOIN houses h ON a.house_id = h.house_id 
                WHERE h.created_by = :user
                ORDER BY a.unit_id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user' => $username]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUnit($unit, $house, $rent) {
        $sql = "INSERT INTO apartments (unit_id, house_id, rent, status) VALUES (:u, :h, :r, 'Vacant')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':u' => $unit, ':h' => $house, ':r' => $rent]);
    }

    public function deleteUnit($id) {
        $stmt = $this->db->prepare("DELETE FROM apartments WHERE unit_id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>