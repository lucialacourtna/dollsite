  <!DOCTYPE html>
  <html lang="en">

  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lucia's Dolls</title>
  <link rel="shortcut icon" href="images/favicon.ico">
  <link rel="stylesheet" href="styles/forum.css">
  </head>

  <body>
<?php session_start(); ?>
<header>
  <h1>Lucia's Dolls</h1>
  <?php if (isset($_SESSION['username'])): ?>
    <div class="logSec">
      <span>Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
      <a href="logout.php" class="login-btn">Log Out</a>
    </div>
  <?php else: ?>
    <div class="logSec">
    <a href="login.html" class="login-btn">Log In</a>
  </div>
  <?php endif; ?>
</header>

<div class="navbar">
  <a href="home.php">Home</a>
  <a href="db.php">Database</a>
  <a class="active" href="forum.php">Forum</a>
</div>

<?php
if (!isset($_SESSION["username"])) {
    echo "<script>
        alert('You must be logged in to access the forum');
        window.location.href='login.html';
    </script>";
    exit();
}
?>

<?php
include("connect.php");

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject'], $_POST['content'])) {
    $subject = trim($_POST['subject']);
    $content = trim($_POST['content']);

    if ($subject !== '' && $content !== '') {
        // Get next PostID as number of rows + 1
        $result = mysqli_query($con, "SELECT COUNT(*) as total FROM Posts");
        $row = mysqli_fetch_assoc($result);
        $nextPostID = $row['total'] + 1;

        // Insert new post
        $stmt = mysqli_prepare($con, "INSERT INTO Posts (PostID, Username, Email, Subject, Content) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issss", $nextPostID, $_SESSION['username'], $_SESSION['email'], $subject, $content);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Refresh page after posting
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error = "Subject and content cannot be empty.";
    }
}



// Fetch all posts in newest-first order
$result = mysqli_query($con, "SELECT Username, Subject, Content FROM Posts ORDER BY PostID DESC");
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
?>
<main>
<div class="new-post">
    <h2>Create New Post</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <p>
            <input type="text" name="subject" placeholder="Subject" maxlength="50" required>
        </p>
        <br>
        <p>
            <textarea name="content" placeholder="Your message..." maxlength="300" rows="5" cols="50" required></textarea>
        </p>
        <div class="post-btn-container">
            <button type="submit">Post</button>
        </div>
    </form>
</div>
<br>
<h2>All Posts</h2>
<?php
if ($posts) {
    foreach ($posts as $post) {
        echo "<div class='post'>";
        echo "<h3>" . htmlspecialchars($post['Subject']) . "</h3>";
        echo "<p><strong>" . htmlspecialchars($post['Username']) . ":</strong> " . nl2br(htmlspecialchars($post['Content'])) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>No posts yet.</p>";
}
?>


</main>
</body>
</html>