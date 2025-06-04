<?php
require_once 'config.inc.php';

$minAccountID = $_POST['minAccountID'] ?? '';
$maxAccountID = $_POST['maxAccountID'] ?? '';
$usernameFilter = $_POST['usernameFilter'] ?? '';

$sql = "SELECT * FROM Accounts WHERE 1=1";
$params = [];
$types = '';

if (!empty($minAccountID)) {
    $sql .= " AND AccountID >= ?";
    $params[] = $minAccountID;
    $types .= 'i';
}

if (!empty($maxAccountID)) {
    $sql .= " AND AccountID <= ?";
    $params[] = $maxAccountID;
    $types .= 'i';
}

if (!empty($usernameFilter)) {
    $sql .= " AND Username LIKE ?";
    $params[] = '%' . $usernameFilter . '%';
    $types .= 's';
}

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$results = $stmt->get_result();
?>

<html>
<head>
    <title>Filter Accounts</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php require_once 'header.inc.php'; ?>
<div>
    <h2>Filter Accounts</h2>
    <form method="post">
        <label for="minAccountID">Min Account ID:</label>
        <input type="number" name="minAccountID" id="minAccountID" value="<?php echo htmlspecialchars($minAccountID); ?>">

        <label for="maxAccountID">Max Account ID:</label>
        <input type="number" name="maxAccountID" id="maxAccountID" value="<?php echo htmlspecialchars($maxAccountID); ?>">

        <label for="usernameFilter">Username Contains:</label>
        <input type="text" name="usernameFilter" id="usernameFilter" value="<?php echo htmlspecialchars($usernameFilter); ?>">

        <input type="submit" value="Filter">
    </form>
</div>

<h3>Results</h3>
<?php if ($results && $results->num_rows > 0): ?>
    <table border="1">
        <tr>
            <th>AccountID</th>
            <th>Username</th>
            <th>Password</th>
        </tr>
        <?php while ($row = $results->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['AccountID']); ?></td>
                <td><?php echo htmlspecialchars($row['Username']); ?></td>
                <td><?php echo htmlspecialchars($row['Password']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No results found.</p>
<?php endif; ?>
</body>
</html>