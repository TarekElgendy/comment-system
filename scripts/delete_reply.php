<?php
// Include the Database and Reply classes
require '../classes/Database.php';
require '../classes/Reply.php';

// Create a new Database instance
$database = new Database();

// Create a new Reply instance using the database connection
$reply = new Reply($database->getConnection());

// Check if the reply_id is provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply_id = $_POST['reply_id'];

    // Delete the reply using the Reply class
    if ($reply->delete($reply_id)) {
        echo "Reply deleted successfully!";
    } else {
        echo "Error deleting reply.";
    }
}
?>