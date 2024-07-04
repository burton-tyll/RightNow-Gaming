<?php
class Delivery {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getDeliveryByUser($userId) {
        $query = "SELECT delivery.*, game_delivery.id_game
                  FROM delivery
                  JOIN game_delivery ON delivery.id = game_delivery.id_delivery
                  WHERE delivery.id_user = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>