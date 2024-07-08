<?php

require_once(__DIR__ . '/../../Database.php');
class Delivery extends Database{
    private $db;

    public function __construct() {
        // Appelle le constructeur de la classe parente pour initialiser la connexion
        parent::__construct();
        $this->conn = $this->connect();
    }

    public function getDeliveryByUser($userId) {
        $query = "SELECT delivery.*, game.name as game_name
                  FROM delivery
                  JOIN game_delivery ON delivery.id = game_delivery.id_delivery
                  JOIN game ON game_delivery.id_game = game.id
                  WHERE delivery.id_user = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function postDelivery($date, $status, $total_price, $userid){
        $query = "INSERT INTO delivery (created_at, statut, total_price, id_user) VALUES (:date, :status, :total_price, :userid)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['date' => $date, 'status' => $status, 'total_price' => $total_price, 'userid' => $userid]);
        $deliveryId = $this->conn->lastInsertId();

        return($deliveryId);
    }

    public function addGamesToDelivery($deliveryId, $gameId){
        $query = "INSERT INTO game_delivery (id_delivery, id_game) VALUES (:deliveryId, :gameId)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['deliveryId' => $deliveryId, 'gameId' => $gameId]);
    }
}
?>