<?php
require_once 'config.inc.php';

// Define tables and their columns (expand as needed)
$tables = [
    'Accounts' => ['AccountID', 'Username', 'Password'],
    'Game' => ['GameID', 'Title', 'Studio', 'Genre', 'Rating'],
    'PlatformRelease' => ['ReleaseID', 'GameID', 'Platform', 'Price', 'ReleaseDate'],
    'GameStats' => ['Title', 'Studio'],
    'Item' => ['ItemID', 'Name', 'Description'],
    'ItemUsage' => ['Title', 'Studio', 'ItemID', 'UsagePercent'],
    'Classes' => ['ClassName', 'Description'],
    'Characters' => ['AccountID', 'Studio', 'Title', 'CharName', 'ClassName'],
    'Inventory' => ['AccountID', 'Studio', 'Title', 'CharName', 'ItemID'],
    // Add more tables as needed
];

$selectedTable = $_POST['table'] ?? '';
$filters = $_POST['filters'] ?? [];

$results = [];
$columns = [];

if ($selectedTable && isset($tables[$selectedTable])) {
    $columns = $tables[$selectedTable];
    // Build SQL
    $sql = "SELECT * FROM `$selectedTable`";
    $where = [];
    $params = [];
    $types = '';
    foreach ($columns as $col) {
        if (!empty($filters[$col])) {
            $where[] = "`$col` LIKE ?";
            $params[] = '%' . $filters[$col] . '%';
            $types .= 's';
        }
    }
    if ($where) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $stmt = $conn->prepare($sql);
    if ($where) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $results = $stmt->get_result();
}
?>

<html>
<head>
    <title>Search Data</title>
    <link rel="stylesheet" href="base.css">
    <script>
    function updateFilters() {
        const table = document.getElementById('table').value;
        const columns = <?php echo json_encode($tables); ?>;
        const filterDiv = document.getElementById('filterInputs');
        const resultsDiv = document.getElementById('results');
        filterDiv.innerHTML = '';
        resultsDiv.innerHTML = '';

        if (columns[table]) {
            // Generate filter inputs for the selected table
            columns[table].forEach(col => {
                const div = document.createElement('div');
                div.innerHTML = `<label for="filter_${col}">${col}:</label>
                                 <input type="text" name="filters[${col}]" id="filter_${col}">`;
                filterDiv.appendChild(div);
            });

            // Fetch and display all data for the selected table
            fetchTableData(table);
        }
    }

    function fetchTableData(table) {
        const resultsDiv = document.getElementById('results');

        // Send an AJAX request to fetch table data
        fetch('fetch_table_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `table=${table}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Create an HTML table to display the data
                let html = '<table border="1"><thead><tr>';
                Object.keys(data[0]).forEach(column => {
                    html += `<th>${column}</th>`;
                });
                html += '</tr></thead><tbody>';
                data.forEach(row => {
                    html += '<tr>';
                    Object.values(row).forEach(value => {
                        html += `<td>${value}</td>`;
                    });
                    html += '</tr>';
                });
                html += '</tbody></table>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<p>No data available for this table.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching table data:', error);
            resultsDiv.innerHTML = '<p>Error fetching table data.</p>';
        });
    }
    </script>
</head>
<body>
<?php require_once 'header.inc.php'; ?>
<div>
    <h2>Search Data</h2>
    <form method="post">
        <label for="table">Select Table:</label>
        <select name="table" id="table" onchange="updateFilters()">
            <option value="">--Select a table--</option>
            <?php foreach ($tables as $tableName => $cols): ?>
                <option value="<?php echo $tableName; ?>" <?php if ($selectedTable == $tableName) echo 'selected'; ?>>
                    <?php echo $tableName; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div id="filterInputs"></div>
        <br>
        <input type="submit" value="Search">
    </form>
</div>
<div id="results"></div>
<script>
    // Populate filters if table is already selected
    window.onload = function() {
        updateFilters();
        <?php if ($selectedTable && $filters): ?>
            <?php foreach ($filters as $col => $val): ?>
                document.getElementById('filter_<?php echo $col; ?>').value = "<?php echo htmlspecialchars($val); ?>";
            <?php endforeach; ?>
        <?php endif; ?>
    }
</script>
<?php if ($selectedTable && $results): ?>
    <h3>Results for <?php echo htmlspecialchars($selectedTable); ?></h3>
    <table border="1">
        <tr>
            <?php foreach ($columns as $col): ?>
                <th><?php echo htmlspecialchars($col); ?></th>
            <?php endforeach; ?>
        </tr>
        <?php while ($row = $results->fetch_assoc()): ?>
            <tr>
                <?php foreach ($columns as $col): ?>
                    <td><?php echo htmlspecialchars($row[$col]); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endwhile; ?>
    </table>
<?php elseif ($selectedTable): ?>
    <p>No results found.</p>
<?php endif; ?>
</body>
</html>