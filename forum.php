  <!DOCTYPE html>
  <html lang="en">

  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lucia's Dolls</title>
  <link rel="shortcut icon" href="images/favicon.ico">
  <link rel="stylesheet" href="styles/style.css">
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

<main>
  <!-- page content -->
<?php
if (!isset($_SESSION["username"])) {
    echo "<script>
        alert('You must be logged in to access the forum');
        window.location.href='login.html';
    </script>";
    exit();
}
?>

you made it to the forum :)

</main>


</body>
</html>