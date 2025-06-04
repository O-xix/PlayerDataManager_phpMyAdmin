<?php
/**
 * Created by PhpStorm.
 * User: MKochanski
 * Date: 7/24/2018
 * Time: 3:07 PM
 */
require_once 'config.inc.php';

function display_table($conn, $tableName, $columns, $primaryKeys = [], $title = null) {
    if (!$title) $title = $tableName;
    echo "<h2>" . htmlspecialchars($title) . "</h2>";
    $sql = "SELECT " . implode(", ", $columns) . " FROM $tableName";
    $stmt = $conn->stmt_init();
    if (!$stmt->prepare($sql)) {
        echo "<p>Failed to prepare $tableName query: " . htmlspecialchars($stmt->error) . "</p>";
        return;
    }
    $stmt->execute();
    // Dynamically bind result variables
    $bindParams = [];
    foreach ($columns as $col) {
        $bindParams[] = &$row[$col];
    }
    call_user_func_array([$stmt, 'bind_result'], $bindParams);

    echo "<table border='1'><tr>";
    foreach ($columns as $col) {
        echo "<th>" . htmlspecialchars($col) . "</th>";
    }
    echo "<th>Show</th>";
    echo "</tr>";
    while ($stmt->fetch()) {
        echo "<tr>";
        foreach ($columns as $col) {
            echo "<td>" . htmlspecialchars($row[$col]) . "</td>";
        }
        // Build query string for primary keys
        $pk_query = [];
        foreach ($primaryKeys as $pk) {
            $pk_query[] = urlencode($pk) . '=' . urlencode($row[$pk]);
        }
        $queryStr = implode('&', $pk_query);
        echo '<td><a href="show_data.php?table=' . urlencode($tableName) . '&' . $queryStr . '">Show</a></td>';
        echo "</tr>";
    }
    echo "</table>";
    $stmt->close();
}

// =====================
// Call display_table for each table
// =====================


$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Player Data Manager</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php
require_once 'header.inc.php';

// =================================================================
// 1) Open one shared connection at the top:
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// =================================================================
// ===== Character Stats =====
display_table($conn, 'Accounts', ['AccountID', 'Username', 'Password'], ['AccountID']);
display_table($conn, 'Game', ['GameID', 'Title', 'Studio', 'Genre', 'Rating'], ['GameID']);
display_table($conn, 'PlatformRelease', ['ReleaseID', 'GameID', 'Platform', 'Price', 'ReleaseDate'], ['ReleaseID']);
display_table($conn, 'GameStats', ['Title', 'Studio'], ['Title', 'Studio']);
display_table($conn, 'Item', ['ItemID', 'Name', 'Description'], ['ItemID']);
display_table($conn, 'ItemUsage', ['Title', 'Studio', 'ItemID', 'UsagePercent'], ['Title', 'Studio', 'ItemID']);
display_table($conn, 'Classes', ['ClassName', 'Description'], ['ClassName']);
display_table($conn, 'Characters', ['AccountID', 'Studio', 'Title', 'CharName', 'ClassName'], ['AccountID', 'Studio', 'Title', 'CharName']);
display_table($conn, 'Inventory', ['AccountID', 'Studio', 'Title', 'CharName', 'ItemID'], ['AccountID', 'Studio', 'Title', 'CharName', 'ItemID']);
display_table($conn, 'ClassUsage', ['Title', 'Studio', 'ClassName', 'UsagePercent'], ['Title', 'Studio', 'ClassName']);
display_table($conn, 'GameClass', ['Title', 'Studio', 'ClassName'], ['Title', 'Studio', 'ClassName']);
display_table($conn, 'GameName', ['Title', 'Studio', 'CharName'], ['Title', 'Studio', 'CharName']);
display_table($conn, 'AccountStats', ['AccountID', 'Title', 'Studio', 'Stats'], ['AccountID', 'Title', 'Studio']);
display_table($conn, 'PopularBuilds', ['Title', 'Studio', 'BuildID'], ['Title', 'Studio', 'BuildID']);
display_table($conn, 'StatTypes', ['Studio', 'Title', 'StatName', 'Description'], ['Studio', 'Title', 'StatName']);
display_table($conn, 'CharacterStats', ['AccountID', 'Studio', 'Title', 'CharName', 'StatName', 'StatValue'], ['AccountID', 'Studio', 'Title', 'CharName', 'StatName']);


// =================================================================
// 5) Close the shared connection once at the end:
$conn->close();
echo "</div>";
?>
</body>
</html>

