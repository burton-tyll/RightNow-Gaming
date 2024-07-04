<?php

require_once(__DIR__ . '/../../Database.php');

class Comment extends Database {
    public function __construct() {
        parent::__construct();
        $this->conn = $this->connect();
    }

    public function getCommentsByGameId($id_game) {
      $query = '
          SELECT comment.content, comment.rating, comment.created_at, user.username 
          FROM comment
          JOIN user ON comment.id_user = user.id
          WHERE comment.id_game = :id_game
          ORDER BY comment.created_at DESC
      ';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id_game', $id_game, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  

    public function getAverageRatingForGame($id_game) {
        $query = 'SELECT AVG(rating) as average_rating FROM comment WHERE id_game = :id_game';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_game', $id_game, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['average_rating'] ?? 0;
    }

    public function addComment($id_game, $id_user, $rating, $content) {
        $query = 'INSERT INTO comment (id_game, id_user, rating, content, created_at) VALUES (:id_game, :id_user, :rating, :content, NOW())';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_game', $id_game, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->execute();

        $this->updateGameRating($id_game);
        $averageRating = $this->getAverageRatingForGame($id_game);
        return $averageRating;
    }

    private function updateGameRating($id_game) {
        $averageRating = $this->getAverageRatingForGame($id_game);
        $query = 'UPDATE game SET rate = :rate WHERE id = :id_game';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rate', $averageRating, PDO::PARAM_STR);
        $stmt->bindParam(':id_game', $id_game, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Code pour gérer les requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action']) && $input['action'] === 'add_comment') {
        $gameId = isset($input['id_game']) ? intval($input['id_game']) : null;
        $rating = isset($input['rating']) ? intval($input['rating']) : null;
        $content = isset($input['content']) ? trim($input['content']) : '';
        $userId = isset($input['id_user']) ? intval($input['id_user']) : null; // Récupération de l'ID utilisateur

        if ($gameId !== null && $rating !== null && $content !== '' && $userId !== null) {
            $comment = new Comment();
            $newAverageRating = $comment->addComment($gameId, $userId, $rating, $content);
            echo json_encode([
                'status' => 'success',
                'newAverageRating' => $newAverageRating
            ]);
        } else {
            $errors = [];
            if ($gameId === null) $errors[] = 'ID du jeu manquant';
            if ($content === '') $errors[] = 'Contenu du commentaire manquant';
            if ($userId === null) $errors[] = 'ID utilisateur manquant';

            echo json_encode(['status' => 'error', 'message' => implode(', ', $errors)]);
        }
        exit();
    }
}
?>
