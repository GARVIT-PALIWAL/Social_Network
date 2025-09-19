<?php
require_once 'Database.php';

class Post {
    private $conn;
    private $table_name = "posts";

    public $id;
    public $user_id;
    public $content;
    public $image_path;
    public $created_at;
    public $author_username;
    public $author_first_name;
    public $author_last_name;
    public $author_profile_picture;
    public $likes_count;
    public $comments_count;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create new post
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, content=:content, image_path=:image_path";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":image_path", $this->image_path);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Get all posts with author info
    public function getAllPosts($limit = 20, $offset = 0) {
        $query = "SELECT p.*, u.username as author_username, u.first_name as author_first_name, 
                         u.last_name as author_last_name, u.profile_picture as author_profile_picture,
                         COUNT(DISTINCT l.id) as likes_count,
                         COUNT(DISTINCT c.id) as comments_count
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN likes l ON p.id = l.post_id
                  LEFT JOIN comments c ON p.id = c.post_id
                  GROUP BY p.id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get posts by user ID
    public function getPostsByUserId($user_id, $limit = 20, $offset = 0) {
        $query = "SELECT p.*, u.username as author_username, u.first_name as author_first_name, 
                         u.last_name as author_last_name, u.profile_picture as author_profile_picture,
                         COUNT(DISTINCT l.id) as likes_count,
                         COUNT(DISTINCT c.id) as comments_count
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN likes l ON p.id = l.post_id
                  LEFT JOIN comments c ON p.id = c.post_id
                  WHERE p.user_id = :user_id
                  GROUP BY p.id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get single post by ID
    public function getPostById($id) {
        $query = "SELECT p.*, u.username as author_username, u.first_name as author_first_name, 
                         u.last_name as author_last_name, u.profile_picture as author_profile_picture,
                         COUNT(DISTINCT l.id) as likes_count,
                         COUNT(DISTINCT c.id) as comments_count
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN likes l ON p.id = l.post_id
                  LEFT JOIN comments c ON p.id = c.post_id
                  WHERE p.id = :id
                  GROUP BY p.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->content = $row['content'];
            $this->image_path = $row['image_path'];
            $this->created_at = $row['created_at'];
            $this->author_username = $row['author_username'];
            $this->author_first_name = $row['author_first_name'];
            $this->author_last_name = $row['author_last_name'];
            $this->author_profile_picture = $row['author_profile_picture'];
            $this->likes_count = $row['likes_count'];
            $this->comments_count = $row['comments_count'];
            return true;
        }
        return false;
    }

    // Like a post
    public function likePost($user_id, $post_id) {
        $query = "INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":post_id", $post_id);
        
        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            // If duplicate entry, user already liked this post
            return false;
        }
    }

    // Unlike a post
    public function unlikePost($user_id, $post_id) {
        $query = "DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":post_id", $post_id);
        return $stmt->execute();
    }

    // Check if user liked post
    public function isLikedByUser($user_id, $post_id) {
        $query = "SELECT id FROM likes WHERE user_id = :user_id AND post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Add comment
    public function addComment($user_id, $post_id, $content) {
        $query = "INSERT INTO comments (user_id, post_id, content) VALUES (:user_id, :post_id, :content)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":content", $content);
        return $stmt->execute();
    }

    // Get comments for a post
    public function getComments($post_id) {
        $query = "SELECT c.*, u.username, u.first_name, u.last_name, u.profile_picture
                  FROM comments c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = :post_id
                  ORDER BY c.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Delete post
    public function deletePost($post_id, $user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :post_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }
}
?>
