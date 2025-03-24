
<?php
class Comment {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function create($page_id, $user_id, $user_name, $comment_text) {
        $stmt = $this->db->prepare("INSERT INTO comments (page_id, user_id, user_name, comment_text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $page_id, $user_id, $user_name, $comment_text);
        return $stmt->execute();
    }

    public function delete($comment_id) {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        return $stmt->execute();
    }

    public function getAll() {
        $sql = "
            SELECT c.id, c.user_name, c.comment_text, c.created_at, 
                   SUM(r.reaction_type = 'love') AS love_count,
                   SUM(r.reaction_type = 'clap') AS clap_count,
                   SUM(r.reaction_type = 'thumbsUp') AS thumbsUp_count,
                   SUM(r.reaction_type = 'smile') AS smile_count
            FROM comments c
            LEFT JOIN reactions r ON c.id = r.comment_id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ";
        return $this->db->query($sql);
    }
}
?>