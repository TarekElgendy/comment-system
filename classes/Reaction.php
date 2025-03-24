<?php
class Reaction {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function add($comment_id, $reaction_type) {
        $stmt = $this->db->prepare("INSERT INTO reactions (comment_id, reaction_type) VALUES (?, ?)");
        $stmt->bind_param("is", $comment_id, $reaction_type);
        return $stmt->execute();
    }

    public function getCounts($comment_id) {
        $sql = "
            SELECT 
                SUM(reaction_type = 'love') AS love,
                SUM(reaction_type = 'clap') AS clap,
                SUM(reaction_type = 'thumbsUp') AS thumbsUp,
                SUM(reaction_type = 'smile') AS smile
            FROM reactions
            WHERE comment_id = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>