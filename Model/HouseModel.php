<?php
class HouseModel {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }


    public function getAllHouses($username) {
        $stmt = $this->db->prepare("SELECT * FROM houses WHERE created_by = :user ORDER BY house_id ASC");
        $stmt->execute([':user' => $username]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addHouse($id, $name, $loc, $username) {
        $sql = "INSERT INTO houses (house_id, house_name, location, created_by) VALUES (:id, :name, :loc, :user)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':name' => $name, ':loc' => $loc, ':user' => $username]);
    }


    public function deleteHouse($id, $username) {
        $stmt = $this->db->prepare("DELETE FROM houses WHERE house_id = :id AND created_by = :user");
        return $stmt->execute([':id' => $id, ':user' => $username]);
    }
}