<?php
require_once 'config.inc.php';

// Define tables and their primary keys (copy from list_data.php)
$tables = [
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

// Get table and primary key values from GET
$table = $_GET['table'];
$primaryKeys = $tables[$table]; // $tables should be defined as in list_data.php
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
?>
<html>
<head><title>Show Data</title></head>
<body>
<h2>Show <?php echo htmlspecialchars($table); ?> Entry</h2>
<table>
<?php foreach ($row as $col => $val): ?>
    <tr><th><?php echo htmlspecialchars($col); ?></th><td><?php echo htmlspecialchars($val); ?></td></tr>
<?php endforeach; ?>
</table>
<?php
// Build query string for update link
$pk_query = [];
foreach ($primaryKeys as $pk) {
    $pk_query[] = urlencode($pk) . '=' . urlencode($row[$pk]);
}
$queryStr = implode('&', $pk_query);
?>
<a href="update_data.php?table=<?php echo urlencode($table); ?>&<?php echo $queryStr; ?>">Update</a>
</body>
</html>