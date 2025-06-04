<?php
require_once 'config.inc.php';

// All columns for each table
$table_columns = [
    'Accounts' => ['AccountID', 'Username', 'Password'],
    'Game' => ['GameID', 'Title', 'Studio', 'Genre', 'Rating'],
    'PlatformRelease' => ['ReleaseID', 'GameID', 'Platform', 'Price', 'ReleaseDate'],
    'GameStats' => ['Title', 'Studio'],
    'Item' => ['ItemID', 'Name', 'Description'],
    'ItemUsage' => ['Title', 'Studio', 'ItemID', 'UsagePercent'],
    'Classes' => ['ClassName', 'Description'],
    'Characters' => ['AccountID', 'Studio', 'Title', 'CharName', 'ClassName'],
    'Inventory' => ['AccountID', 'Studio', 'Title', 'CharName', 'ItemID'],
    'ClassUsage' => ['Title', 'Studio', 'ClassName', 'UsagePercent'],
    'GameClass' => ['Title', 'Studio', 'ClassName'],
    'GameName' => ['Title', 'Studio', 'CharName'],
    'AccountStats' => ['AccountID', 'Title', 'Studio', 'Stats'],
    'PopularBuilds' => ['Title', 'Studio', 'BuildID'],
    'StatTypes' => ['Studio', 'Title', 'StatName', 'Description'],
    'CharacterStats' => ['AccountID', 'Studio', 'Title', 'CharName', 'StatName', 'StatValue'],
];

// Primary keys for each table
$table_primary_keys = [
    'Accounts' => ['AccountID'],
    'Game' => ['GameID'],
    'PlatformRelease' => ['ReleaseID'],
    'GameStats' => ['Title', 'Studio'],
    'Item' => ['ItemID'],
    'ItemUsage' => ['Title', 'Studio', 'ItemID'],
    'Classes' => ['ClassName'],
    'Characters' => ['AccountID', 'Studio', 'Title', 'CharName'],
    'Inventory' => ['AccountID', 'Studio', 'Title', 'CharName', 'ItemID'],
    'ClassUsage' => ['Title', 'Studio', 'ClassName'],
    'GameClass' => ['Title', 'Studio', 'ClassName'],
    'GameName' => ['Title', 'Studio', 'CharName'],
    'AccountStats' => ['AccountID', 'Title', 'Studio'],
    'PopularBuilds' => ['Title', 'Studio', 'BuildID'],
    'StatTypes' => ['Studio', 'Title', 'StatName'],
    'CharacterStats' => ['AccountID', 'Studio', 'Title', 'CharName', 'StatName'],
];

// Usage in your code:
$table = $_GET['table'];
$columns = $table_columns[$table];
$primaryKeys = $table_primary_keys[$table];

// Fetch current values
$where = [];
$params = [];
foreach ($primaryKeys as $pk) {
    $where[] = "$pk = ?";
    $params[] = $_GET[$pk];
}
$sql = "SELECT * FROM $table WHERE " . implode(' AND ', $where);
$stmt = $conn->prepare($sql);
$types = str_repeat('s', count($params));
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Build update statement
    $set = [];
    $updateParams = [];
    foreach ($columns as $col) {
        if (!in_array($col, $primaryKeys)) {
            $set[] = "$col = ?";
            $updateParams[] = $_POST[$col];
        }
    }
    $updateParams = array_merge($updateParams, $params); // Add PKs for WHERE
    $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $where);
    $stmt = $conn->prepare($sql);
    $types2 = str_repeat('s', count($updateParams));
    $stmt->bind_param($types2, ...$updateParams);
    $stmt->execute();
    echo "Update successful!";

    // Reload the updated row
    $sql = "SELECT * FROM $table WHERE " . implode(' AND ', $where);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}
?>
<html>
<head><title>Update Data</title></head>
<body>
<h2>Update <?php echo htmlspecialchars($table); ?> Entry</h2>
<form method="post">
<?php foreach ($columns as $col): ?>
    <label><?php echo htmlspecialchars($col); ?>:</label>
    <input type="text" name="<?php echo htmlspecialchars($col); ?>"
       value="<?php echo htmlspecialchars($row[$col]); ?>"
       <?php echo in_array($col, $primaryKeys) ? 'readonly' : ''; ?>><br>
<?php endforeach; ?>
<input type="submit" value="Update">
<a href="list_data.php" style="margin-left:10px;">Back to List</a>
</form>
</body>
</html>