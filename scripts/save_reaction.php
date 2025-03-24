<?php
// Include the Database and Reaction classes
require '../classes/Database.php';
require '../classes/Reaction.php';

// Create a new Database instance
$database = new Database();

// Create a new Reaction instance using the database connection
$reaction = new Reaction($database->getConnection());

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $comment_id = $_POST['comment_id'];
    $reaction_type = $_POST['reaction_type'];

    // Save the reaction using the Reaction class
    if ($reaction->add($comment_id, $reaction_type)) {
        echo "Reaction saved successfully!";
    } else {
        echo "Error saving reaction.";
    }
}
?>