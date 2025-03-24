<?php
require '../classes/Database.php';
require '../classes/Comment.php';

$database = new Database();
$comment = new Comment($database->getConnection());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $user_name = $_POST['user_name'];
    $page_id = 1;
    $user_id = 2;
    $comment_text = $_POST['comment_text'];
    // var_dump($page_id);
    // die();
    if ($comment->create($page_id,$user_id,$user_name, $comment_text)) {
        echo "Comment saved successfully!";
    } else {
        echo "Error saving comment.";
    }
}
?>