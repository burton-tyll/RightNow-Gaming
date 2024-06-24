<?php
    require_once(__DIR__ . '/../../Database.php');
    class Game extends Database {
        //Définition du constructeur
        public function __construct() {
            // Appelle le constructeur de la classe parente pour initialiser la connexion
            parent::__construct();
            $this->conn = $this->connect();
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

        //Playstation Games

        public function getAllGamesByPlateform($id_platform){
            $query = 'SELECT id_game FROM game_platform WHERE id_platform = :id_platform';
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id_platform' => $id_platform]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAllGamesByPlatformOrderedByDate($id_platform) {
            $query = 'SELECT * FROM game JOIN game_platform ON game.id = game_platform.id_game WHERE game_platform.id_platform = :id_platform ORDER BY game.release_date DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id_platform' => $id_platform]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAllGamesByPlatformOrderedByRate($id_platform) {
            $query = 'SELECT * FROM game JOIN game_platform ON game.id = game_platform.id_game WHERE game_platform.id_platform = :id_platform ORDER BY game.rate DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id_platform' => $id_platform]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getTheBestGameByPlateform($id_platform){
            $query = 'SELECT * FROM game JOIN game_platform ON game.id = game_platform.id_game WHERE game_platform.id_platform = :id_platform ORDER BY game.rate DESC LIMIT 1';
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id_platform' => $id_platform]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>