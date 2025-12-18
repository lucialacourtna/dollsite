  <!DOCTYPE html>
  <html lang="en">

  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lucia's Dolls</title>
  <link rel="shortcut icon" href="images/favicon.ico">
  <link rel="stylesheet" href="styles/db.css">
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


<?php
include("connect.php");

/* -------------------------
   Get filter/sort inputs
------------------------- */
$brand = $_GET['brand'] ?? '';
$year = $_GET['year'] ?? '';
$location = $_GET['location'] ?? '';
$sort = $_GET['sort'] ?? 'Brand';
$search = trim($_GET['search'] ?? '');


/* -------------------------
   Allowed sort columns
------------------------- */
$allowedSorts = ['DollName', 'Brand', 'ReleaseYear', 'PurchasePrice', 'PurchaseDate'];
if (!in_array($sort, $allowedSorts)) {
    $sort = 'Brand';
}

/* -------------------------
   Dropdown data
------------------------- */
$brands = mysqli_query($con, "SELECT DISTINCT Brand FROM Dolls ORDER BY Brand");
$years = mysqli_query($con, "SELECT DISTINCT ReleaseYear FROM Dolls ORDER BY ReleaseYear DESC");
$locations = mysqli_query($con, "SELECT DISTINCT PurchaseLocation FROM Dolls ORDER BY PurchaseLocation");

/* -------------------------
   Build main query
------------------------- */
$sql = "SELECT * FROM Dolls WHERE 1=1";
$params = [];
$types = "";

if ($brand !== '') {
    $sql .= " AND Brand = ?";
    $params[] = $brand;
    $types .= "s";
}

if ($year !== '') {
    $sql .= " AND ReleaseYear = ?";
    $params[] = $year;
    $types .= "i";
}

if ($location !== '') {
    $sql .= " AND PurchaseLocation = ?";
    $params[] = $location;
    $types .= "s";
}
if ($search !== '') {
    $sql .= " AND (
        Line LIKE ?
        OR DollName LIKE ?
        OR Brand LIKE ?
        OR PurchaseLocation LIKE ?
        OR CAST(ReleaseYear AS CHAR) LIKE ?
        OR CAST(PurchasePrice AS CHAR) LIKE ?
        OR CAST(PurchaseDate AS CHAR) LIKE ?
    )";

    $searchTerm = "%$search%";
    $params = array_merge($params, [
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    ]);
    $types .= "sssssss";
}


$sql .= " ORDER BY $sort";

$stmt = $con->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$rowCount = $result->num_rows;


/* -------------------------
   Sum calculation (same filters)
------------------------- */
$sumSql = "SELECT SUM(PurchasePrice) AS total FROM Dolls WHERE PurchasePrice IS NOT NULL";
$sumParams = [];
$sumTypes = "";

if ($brand !== '') {
    $sumSql .= " AND Brand = ?";
    $sumParams[] = $brand;
    $sumTypes .= "s";
}

if ($year !== '') {
    $sumSql .= " AND ReleaseYear = ?";
    $sumParams[] = $year;
    $sumTypes .= "i";
}

if ($location !== '') {
    $sumSql .= " AND PurchaseLocation = ?";
    $sumParams[] = $location;
    $sumTypes .= "s";
}
if ($search !== '') {
    $sumSql .= " AND (
        Line LIKE ?
        OR DollName LIKE ?
        OR Brand LIKE ?
        OR PurchaseLocation LIKE ?
        OR CAST(ReleaseYear AS CHAR) LIKE ?
        OR CAST(PurchasePrice AS CHAR) LIKE ?
        OR CAST(PurchaseDate AS CHAR) LIKE ?
    )";

    $searchTerm = "%$search%";
    $sumParams = array_merge($sumParams, [
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    ]);
    $sumTypes .= "sssssss";
}


$sumStmt = $con->prepare($sumSql);
if (!empty($sumParams)) {
    $sumStmt->bind_param($sumTypes, ...$sumParams);
}
$sumStmt->execute();
$total = $sumStmt->get_result()->fetch_assoc()['total'] ?? 0;
?>


<form method="GET" class="filters">
<strong>Search:</strong>
<input
    type="text"
    name="search"
    value="<?= htmlspecialchars($search) ?>"
    placeholder="Search all fields...">
    <br>    <br>


    <strong>Brand:</strong>
    <select name="brand">
        <option value="">All</option>
        <?php while ($b = mysqli_fetch_assoc($brands)): ?>
            <option value="<?= $b['Brand'] ?>" <?= $brand === $b['Brand'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['Brand']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <strong>Release Year:</strong>
    <select name="year">
        <option value="">All</option>
        <?php while ($y = mysqli_fetch_assoc($years)): ?>
            <option value="<?= $y['ReleaseYear'] ?>" <?= $year == $y['ReleaseYear'] ? 'selected' : '' ?>>
                <?= $y['ReleaseYear'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <strong>Purchase Location:</strong>
    <select name="location">
        <option value="">All</option>
        <?php while ($l = mysqli_fetch_assoc($locations)): ?>
            <option value="<?= $l['PurchaseLocation'] ?>" <?= $location === $l['PurchaseLocation'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($l['PurchaseLocation']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <strong>Sort By:</strong>
    <select name="sort">
        <option value="Brand" <?= $sort === 'Brand' ? 'selected' : '' ?>>Brand</option>
        <option value="DollName" <?= $sort === 'DollName' ? 'selected' : '' ?>>Doll Name</option>
        <option value="ReleaseYear" <?= $sort === 'ReleaseYear' ? 'selected' : '' ?>>Release Year</option>
        <option value="PurchasePrice" <?= $sort === 'PurchasePrice' ? 'selected' : '' ?>>Purchase Price</option>
        <option value="PurchaseDate" <?= $sort === 'PurchaseDate' ? 'selected' : '' ?>>Purchase Date</option>
    </select>

    <button type="submit">Apply</button>

</form>

<strong>Total Purchase Price:</strong>$<?= number_format($total, 2) ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<strong>Dolls Returned:</strong> <?= $rowCount ?>
<br><br>
<table>
<tr>
    <th>Line</th>
    <th>Doll Name</th>
    <th>Brand</th>
    <th>Release Year</th>
    <th>Purchase Price</th>
    <th>Purchase Date</th>
    <th>Purchase Location</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['Line']) ?></td>
    <td><?= htmlspecialchars($row['DollName']) ?></td>
    <td><?= htmlspecialchars($row['Brand']) ?></td>
    <td><?= $row['ReleaseYear'] ?></td>
    <td>
        <?= $row['PurchasePrice'] !== null
            ? "$" . number_format($row['PurchasePrice'], 2)
            : "—" ?>
    </td>
    <td><?= $row['PurchaseDate'] ?? "—" ?></td>
    <td><?= htmlspecialchars($row['PurchaseLocation']) ?></td>
</tr>
<?php endwhile; ?>

</table>

</main>


</body>
</html>


