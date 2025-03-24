<?php
// Include the Database and Reaction classes
require '../classes/Database.php';
require '../classes/Reaction.php';

// Create a new Database instance
$database = new Database();

// Create a new Reaction instance using the database connection
$reaction = new Reaction($database->getConnection());

// Check if the comment_id is provided in the query string
if (isset($_GET['comment_id'])) {
    $comment_id = $_GET['comment_id'];

    // Get the reaction counts for the comment
    $counts = $reaction->getCounts($comment_id);

    // Return the counts as JSON
    echo json_encode($counts);
}
?>