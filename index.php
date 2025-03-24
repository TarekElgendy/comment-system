<?php
require 'classes/Database.php';
require 'classes/Comment.php';
require 'classes/Reaction.php';
require 'classes/Reply.php';

$database = new Database();
$comment = new Comment($database->getConnection());
$reaction = new Reaction($database->getConnection());
$reply = new Reply($database->getConnection());
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment System</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        /* Add your CSS styles here */
    </style>
</head>

<body>
    <h1>Comment System</h1>
    

    <!-- Comment Form -->
    <form id="commentForm">
        <input type="text" name="user_name"  value="Tarke Gamal" placeholder="Your Name" required>
        <textarea name="comment_text"   id="comment-input"  placeholder="Write a comment..." required></textarea>
        <div class="emoji-container">
            <button type="button" class="emoji-button">😊</button>
            <div class="emoji-drawer">
                <div class="emoji" onclick="addEmoji(this.innerHTML)">👍</div>
                <div class="emoji" onclick="addEmoji(this.innerHTML)">👏</div>
                <div class="emoji" onclick="addEmoji(this.innerHTML)">💡</div>
                <div class="emoji" onclick="addEmoji(this.innerHTML)">❤️</div>
            </div>
            <button type="submit">Post Comment</button>
        </div>
    </form>

    <!-- Display Comments -->
    <div id="comments">
        <?php
        $comments = $comment->getAll();
        if ($comments->num_rows > 0) {
            while ($row = $comments->fetch_assoc()) {
                echo "<div class='comment' id='comment-{$row['id']}'>";
                echo "<strong>{$row['user_name']}</strong>";
                echo "<div class='comment-meta'>";
                echo "<span>Posted on: " . date('F j, Y, g:i a', strtotime($row['created_at'])) . "</span>";
                echo "<button class='delete-button' data-comment-id='{$row['id']}' onclick='deleteComment({$row['id']})'>Delete</button>";
                echo "</div>";
                echo "<p>{$row['comment_text']}</p>";
                echo "<div class='reactions'>";
                echo "<span class='reaction-icon' data-comment-id='{$row['id']}' data-reaction-type='thumbsUp'>👍 <span id='thumbsUp-count-{$row['id']}'>{$row['thumbsUp_count']}</span></span>";
                echo "<span class='reaction-icon' data-comment-id='{$row['id']}' data-reaction-type='clap'>👏 <span id='clap-count-{$row['id']}'>{$row['clap_count']}</span></span>";
                echo "<span class='reaction-icon' data-comment-id='{$row['id']}' data-reaction-type='love'>❤️ <span id='love-count-{$row['id']}'>{$row['love_count']}</span></span>";
                echo "<span class='reaction-icon' data-comment-id='{$row['id']}' data-reaction-type='smile'>😊 <span id='smile-count-{$row['id']}'>{$row['smile_count']}</span></span>";
                echo "</div>";

                // Display Replies
                $replies = $reply->getByCommentId($row['id']);
                if ($replies->num_rows > 0) {
                    echo "<div class='replies'>";
                    while ($reply_row = $replies->fetch_assoc()) {
                        echo "<div class='reply' id='reply-{$reply_row['id']}'>";
                        echo "<strong>{$reply_row['user_name']}</strong>";
                        echo "<div class='comment-meta'>";
                        echo "<span>Posted on: " . date('F j, Y, g:i a', strtotime($reply_row['created_at'])) . "</span>";
                        echo "<button class='delete-button' data-reply-id='{$reply_row['id']}' onclick='deleteReply({$reply_row['id']})'>Delete</button>";
                        echo "</div>";
                        echo "<p>{$reply_row['reply_text']}</p>";
                        echo "</div>";
                    }
                    echo "</div>";
                }

                // Reply Form
                echo "<form class='reply-form' data-comment-id='{$row['id']}'>";
                echo "<input type='text' name='user_name' placeholder='Your Name' required>";
                echo "<textarea name='reply_text' placeholder='Write a reply...' required></textarea>";
                echo "<button type='submit'>Reply</button>";
                echo "</form>";

                echo "</div>";
            }
        } else {
            echo "<p>No comments yet.</p>";
        }
        ?>
    </div>

    <script>
        // Handle Comment Form Submission
        document.getElementById('commentForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('scripts/save_comment.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.text())
                .then(data => {
                //    alert(data); // Show success or error message
                    location.reload(); // Refresh the page to show the new comment
                });
        });

        ///-----------------Handle Reaction Clicks
        // Handle Reaction Clicks
        document.querySelectorAll('.reaction-icon').forEach(icon => {
            icon.addEventListener('click', () => {
                const commentId = icon.getAttribute('data-comment-id');
                const reactionType = icon.getAttribute('data-reaction-type');

                fetch('scripts/save_reaction.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `comment_id=${commentId}&reaction_type=${reactionType}`,
                })
                    .then(response => response.text())
                    .then(data => {
                        //alert(data); // Show success or error message

                        // Fetch and update the reaction counts dynamically
                        fetch(`scripts/get_reaction_counts.php?comment_id=${commentId}`)
                            .then(response => response.json())
                            .then(counts => {
                                document.getElementById(`love-count-${commentId}`).innerText = counts.love;
                                document.getElementById(`thumbsUp-count-${commentId}`).innerText = counts.thumbsUp;
                                document.getElementById(`clap-count-${commentId}`).innerText = counts.clap;
                                document.getElementById(`smile-count-${commentId}`).innerText = counts.smile;
                            });
                    });
            });
        });

        // Handle Reply Form Submission
        document.querySelectorAll('.reply-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const commentId = this.getAttribute('data-comment-id');
                const formData = new FormData(this);
                formData.append('comment_id', commentId);

                fetch('scripts/save_reply.php', {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => response.text())
                    .then(data => {
                      //  alert(data); // Show success or error message
                        location.reload(); // Refresh the page to show the new reply
                    });
            });
        });

        // Delete Comment
function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        fetch('scripts/delete_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `comment_id=${commentId}`,
        })
        .then(response => response.text())
        .then(data => {
            //alert(data); // Show success or error message
            location.reload(); // Refresh the page to reflect the deletion
        });
    }
}

// Delete Reply
function deleteReply(replyId) {
    if (confirm('Are you sure you want to delete this reply?')) {
        fetch('scripts/delete_reply.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `reply_id=${replyId}`,
        })
        .then(response => response.text())
        .then(data => {
         //   alert(data); // Show success or error message
            location.reload(); // Refresh the page to reflect the deletion
        });
    }
}

    </script>

<script>
        function addEmoji(emoji) {
            let inputEle = document.getElementById('comment-input');
            inputEle.value += emoji;
        }

        function submitComment() {
            let nameInput = document.getElementById('commenter-name');
            let input = document.getElementById('comment-input');
            let commentText = input.value.trim();
            let commenterName = nameInput.value.trim() || "Anonymous";

            if (commentText === "") return;

            let commentId = "comment-" + Date.now();
            let timestamp = new Date().toLocaleString();
            let commentHTML = `
                <div class="comment" id="${commentId}">
                    <div class="comment-header">
                        <span><strong>${commenterName}</strong> • ${timestamp}</span>
                        <button class="delete-btn" onclick="deleteComment('${commentId}')">🗑 Delete</button>
                    </div>
                    <p class="comment-text">${commentText}</p>
                    <div class="reactions">
                        <span onclick="reactToComment('${commentId}', '👍')">👍</span>
                        <span onclick="reactToComment('${commentId}', '❤️')">❤️</span>
                        <span onclick="reactToComment('${commentId}', '👏')">👏</span>
                        <span onclick="reactToComment('${commentId}', '💡')">💡</span>
                    </div>
                    <button class="reply-btn" onclick="toggleReplySection('${commentId}')">Reply</button>
                    <div class="reply-section" id="reply-${commentId}">
                        <input type="text" placeholder="Your Name" id="reply-name-${commentId}">
                        <textarea rows="2" placeholder="Write a reply..."></textarea>
                        <button onclick="submitReply('${commentId}')">Reply</button>
                    </div>
                </div>
            `;

            document.getElementById("comments-list").innerHTML += commentHTML;
            input.value = "";
            nameInput.value = "";
        }

        function reactToComment(commentId, emoji) {
            let comment = document.getElementById(commentId);
            let reactionsDiv = comment.querySelector(".reactions");

            let existingReaction = reactionsDiv.querySelector(`span[data-reaction="${emoji}"]`);
            if (existingReaction) {
                let count = parseInt(existingReaction.getAttribute("data-count")) || 0;
                count++;
                existingReaction.innerHTML = `${emoji} (${count})`;
                existingReaction.setAttribute("data-count", count);
            } else {
                let newReaction = document.createElement("span");
                newReaction.innerHTML = `${emoji} (1)`;
                newReaction.setAttribute("data-reaction", emoji);
                newReaction.setAttribute("data-count", "1");
                reactionsDiv.appendChild(newReaction);
            }
        }

        function toggleReplySection(commentId) {
            let replySection = document.getElementById(`reply-${commentId}`);
            replySection.style.display = replySection.style.display === "block" ? "none" : "block";
        }

        function submitReply(commentId) {
            let replySection = document.getElementById(`reply-${commentId}`);
            let replyNameInput = document.getElementById(`reply-name-${commentId}`);
            let replyInput = replySection.querySelector("textarea");
            let replyText = replyInput.value.trim();
            let replierName = replyNameInput.value.trim() || "Anonymous";

            if (replyText === "") return;

            let replyHTML = `
                <div class="comment reply">
                    <div class="comment-header">
                        <span><strong>${replierName}</strong> • ${new Date().toLocaleString()}</span>
                    </div>
                    <p class="comment-text">${replyText}</p>
                </div>
            `;

            replySection.innerHTML += replyHTML;
            replyInput.value = "";
            replyNameInput.value = "";
        }

        function deleteComment(commentId) {
            let comment = document.getElementById(commentId);
            comment.remove();
        }
    </script>
</body>

</html>