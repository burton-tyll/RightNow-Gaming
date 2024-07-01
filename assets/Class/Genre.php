<?php

    require_once(__DIR__ . '/../../Database.php');

    class Genre extends Database{
        //Définition du constructeur
        public function __construct() {
            // Appelle le constructeur de la classe parente pour initialiser la connexion
            parent::__construct();
            $this->conn = $this->connect();
        }

        public function getAllGenres() {
            $query = 'SELECT * FROM genre';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function addGameToGenre($id_game, $id_genre) {
            $query = 'INSERT INTO game_genre (id_game, id_genre) VALUES (:id_game, :id_genre)';
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':id_game' => $id_game,
                ':id_genre' => $id_genre
            ]);

            return(true);
        }

        public function getAllGamesByGenreOrderedByDate($id_genre) {
            $query = 'SELECT * FROM game JOIN game_genre ON game.id = game_genre.id_game WHERE game_genre.id_genre = :id_genre ORDER BY game.release_date DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id_genre' => $id_genre]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAllGamesByGenreOrderedByRate($id_genre) {
            $query = 'SELECT * FROM game JOIN game_genre ON game.id = game_genre.id_game WHERE game_genre.id_genre = :id_genre ORDER BY game.rate DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id_genre' => $id_genre]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getTheBestGameByGenre($id_genre){
            $query = 'SELECT * FROM game JOIN game_genre ON game.id = game_genre.id_game WHERE game_genre.id_genre = :id_genre ORDER BY game.rate DESC LIMIT 1';
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id_genre' => $id_genre]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getGameGenre($id_game){
            $query = 'SELECT genre.name FROM genre JOIN game_genre ON genre.id = game_genre.id_genre WHERE game_genre.id_game = :id_game';
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id_game' => $id_game]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        
    }
?>