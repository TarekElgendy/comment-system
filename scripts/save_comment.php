<?php
require '../classes/Database.php';
require '../classes/Comment.php';

$database = new Database();
$comment = new Comment($database->getConnection());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'];
    $comment_text = $_POST['comment_text'];

    if ($comment->create($user_name, $comment_text)) {
        echo "Comment saved successfully!";
    } else {
        echo "Error saving comment.";
    }
}
?>