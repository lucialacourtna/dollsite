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
  <a class="active" href="db.php">Database</a>
  <a href="forum.php">Forum</a>
</div>

<main>
ok so its gonna show the full database and then theres gonna be sort and filter sections at the top 
<br>
<label for="sort">Sort by:</label>

<select name="sort" id="sort">
  <option value="Brand">Brand</option>
  <option value="Purchase Location">Purchase Location</option>
  <option value="DateNew">Date - Newest to Oldest</option>
  <option value="DateOld">Date - Oldest to Newest</option>
  <option value="PriceAsc">Price - Low to High</option>
    <option value="PriceDesc">Price - High to Low</option>


</select>

</main>


</body>
</html>