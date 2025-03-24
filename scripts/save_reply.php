<?php
// Include the Database and Reply classes
require '../classes/Database.php';
require '../classes/Reply.php';

// Create a new Database instance
$database = new Database();

// Create a new Reply instance using the database connection
$reply = new Reply($database->getConnection());

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $comment_id = $_POST['comment_id'];
    $user_name = $_POST['user_name'];
    $reply_text = $_POST['reply_text'];

    // Save the reply using the Reply class
    if ($reply->create($comment_id, $user_name, $reply_text)) {
        echo "Reply saved successfully!";
    } else {
        echo "Error saving reply.";
    }
}
?>