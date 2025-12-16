<?php
include("connect.php");

/* -------------------------
   Get filter/sort inputs
------------------------- */
$brand = $_GET['brand'] ?? '';
$year = $_GET['year'] ?? '';
$location = $_GET['location'] ?? '';
$sort = $_GET['sort'] ?? 'DollName';

/* -------------------------
   Allowed sort columns
------------------------- */
$allowedSorts = ['DollName', 'Brand', 'ReleaseYear', 'PurchasePrice'];
if (!in_array($sort, $allowedSorts)) {
    $sort = 'DollName';
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

$sql .= " ORDER BY $sort";

$stmt = $con->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

/* -------------------------
   Sum calculation
------------------------- */
$sumSql = "SELECT SUM(PurchasePrice) AS total FROM Dolls WHERE PurchasePrice IS NOT NULL";
$sumParams = [];
$sumTypes = "";

if ($location !== '') {
    $sumSql .= " AND PurchaseLocation = ?";
    $sumParams[] = $location;
    $sumTypes .= "s";
}

$sumStmt = $con->prepare($sumSql);
if (!empty($sumParams)) {
    $sumStmt->bind_param($sumTypes, ...$sumParams);
}
$sumStmt->execute();
$total = $sumStmt->get_result()->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Doll Collection</title>
<link rel="stylesheet" href="styles/dolls.css">
</head>
<body>

<h2>Doll Collection</h2>

<form method="GET" class="filters">

    Brand:
    <select name="brand">
        <option value="">All</option>
        <?php while ($b = mysqli_fetch_assoc($brands)): ?>
            <option value="<?= $b['Brand'] ?>" <?= $brand === $b['Brand'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['Brand']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    Release Year:
    <select name="year">
        <option value="">All</option>
        <?php while ($y = mysqli_fetch_assoc($years)): ?>
            <option value="<?= $y['ReleaseYear'] ?>" <?= $year == $y['ReleaseYear'] ? 'selected' : '' ?>>
                <?= $y['ReleaseYear'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    Purchase Location:
    <select name="location">
        <option value="">All</option>
        <?php while ($l = mysqli_fetch_assoc($locations)): ?>
            <option value="<?= $l['PurchaseLocation'] ?>" <?= $location === $l['PurchaseLocation'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($l['PurchaseLocation']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    Sort By:
    <select name="sort">
        <option value="DollName" <?= $sort === 'DollName' ? 'selected' : '' ?>>Name</option>
        <option value="Brand" <?= $sort === 'Brand' ? 'selected' : '' ?>>Brand</option>
        <option value="ReleaseYear" <?= $sort === 'ReleaseYear' ? 'selected' : '' ?>>Release Year</option>
        <option value="PurchasePrice" <?= $sort === 'PurchasePrice' ? 'selected' : '' ?>>Price</option>
    </select>

    <button type="submit">Apply</button>

</form>

<p>
<strong>
Total Purchase Price<?= $location ? " (from " . htmlspecialchars($location) . ")" : "" ?>:
</strong>
$<?= number_format($total, 2) ?>
</p>

<table>
<tr>
    <th>Line</th>
    <th>Doll Name</th>
    <th>Brand</th>
    <th>Purchase Price</th>
    <th>Release Year</th>
    <th>Purchase Date</th>
    <th>Purchase Location</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['Line']) ?></td>
    <td><?= htmlspecialchars($row['DollName']) ?></td>
    <td><?= htmlspecialchars($row['Brand']) ?></td>
    <td>
        <?= $row['PurchasePrice'] !== null
            ? "$" . number_format($row['PurchasePrice'], 2)
            : "—" ?>
    </td>
    <td><?= $row['ReleaseYear'] ?></td>
    <td><?= $row['PurchaseDate'] ?? "—" ?></td>
    <td><?= htmlspecialchars($row['PurchaseLocation']) ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
