<?php
    require_once(__DIR__ . '/../../Database.php');
    class Game extends Database {
        //Définition du constructeur
        public function __construct() {
            // Appelle le constructeur de la classe parente pour initialiser la connexion
            parent::__construct();
            $this->conn = $this->connect();
        }
        
        public function getGameById($id) {
            $query = 'SELECT * FROM game WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }      

        public function addGame($image, $name, $description, $price, $special_offer, $studio, $quantity, $release_date, $rate){
            $query = 'INSERT INTO game (image, name, description, price, special_offer, studio, quantity, release_date, rate) VALUES (:image, :name, :description, :price, :special_offer, :studio, :quantity, :release_date, :rate)';
            $stmt = $this->conn->prepare($query);

            // Liaison des paramètres
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':special_offer', $special_offer);
            $stmt->bindParam(':studio', $studio);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':release_date', $release_date);
            $stmt->bindParam(':rate', $rate);

            $stmt->execute();
            $gameId = $this->conn->lastInsertId();

            return($gameId);
        }

        public function getAllGames(){
            $query = 'SELECT * FROM game';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getGamesBy($by, $byvalue){
            $query = 'SELECT * FROM game WHERE ' . $by . ' = :byvalue';
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['byvalue' => $byvalue]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getGamesOrderedByDate(){
            $query = 'SELECT * FROM game ORDER BY release_date DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getTheBestGame(){
            $query = 'SELECT * FROM game ORDER BY rate DESC LIMIT 1';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getGamesOrderedByRate(){
            $query = 'SELECT * FROM game ORDER BY rate DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function deleteGame($id_game){
            $query = 'DELETE * FROM game WHERE id = :id_game';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }

    }
?>