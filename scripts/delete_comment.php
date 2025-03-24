<?php
// Include the Database and Comment classes
require '../classes/Database.php';
require '../classes/Comment.php';

// Create a new Database instance
$database = new Database();

// Create a new Comment instance using the database connection
$comment = new Comment($database->getConnection());

// Check if the comment_id is provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = $_POST['comment_id'];

    // Delete the comment using the Comment class
    if ($comment->delete($comment_id)) {
        echo "Comment deleted successfully!";
    } else {
        echo "Error deleting comment.";
    }
}
?>