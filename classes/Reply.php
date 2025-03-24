<?php
class Reply {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($comment_id, $user_name, $reply_text) {
        $stmt = $this->db->prepare("INSERT INTO replies (comment_id, user_name, reply_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $comment_id, $user_name, $reply_text);
        return $stmt->execute();
    }

    public function delete($reply_id) {
        $stmt = $this->db->prepare("DELETE FROM replies WHERE id = ?");
        $stmt->bind_param("i", $reply_id);
        return $stmt->execute();
    }

    public function getByCommentId($comment_id) {
        $stmt = $this->db->prepare("SELECT * FROM replies WHERE comment_id = ? ORDER BY created_at ASC");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>