<?php
require_once 'config.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST['table'];
    $columns = $_POST['columns'];
    $primaryKeyValues = $_POST['primary_keys'];

    // Prepare the SQL statement for deletion
    $columnNames = array_keys($columns);
    $columnValues = array_map(function($value) use ($conn) {
        return "'" . $conn->real_escape_string($value) . "'";
    }, array_values($columns));

    $sql = "INSERT INTO $table (" . implode(", ", $columnNames) . ") VALUES (" . implode(", ", $columnValues) . ")";

    // Execute the deletion
    if ($conn->query($sql) === TRUE) {
        echo "Record(s) deleted successfully.";
    } else {
        echo "Error deleting record(s): " . $conn->error;
    }
}

$tables = [
    'Accounts' => ['AccountID', 'Username', 'Password'],
    'Game' => ['GameID', 'Title', 'Studio', 'Genre', 'Rating'],
    'PlatformRelease' => ['ReleaseID', 'GameID', 'Platform', 'Price', 'ReleaseDate'],
    'Item' => ['ItemID', 'Name', 'Description'],
    'Classes' => ['ClassName', 'Description'],
    'Characters' => ['AccountID', 'Studio', 'Title', 'CharName', 'Class'],
    'Inventory' => ['AccountID', 'Studio', 'Title', 'CharName', 'ItemID'],
    'GameStats' => ['Title', 'Studio'],
    'ItemUsage' => ['Title', 'Studio', 'ItemID', 'UsagePercent'],
    'ClassUsage' => ['Title', 'Studio', 'ClassName', 'UsagePercent'],
    'Builds' => ['BuildID', 'Description'],
    'PopularBuilds' => ['Title', 'Studio', 'BuildID'],
    'GameClass' => ['Title', 'Studio', 'Class'],
    'GameName' => ['Title', 'Studio', 'CharName'],
    'AccountStats' => ['AccountID', 'Title', 'Studio', 'Stats'],
    'StatTypes' => ['Studio', 'Title', 'StatName', 'Description'],
    'CharacterStats' => ['AccountID', 'Studio', 'Title', 'CharName', 'StatName', 'StatValue'],
    // Add other tables and their primary keys as needed
];

?>

<html>
<head>
    <title>Delete Data</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php require_once 'header.inc.php'; ?>
<div>
    <h2>Insert Data</h2>
    <form method="post" id="insertForm">
        <label for="table">Select Table:</label>
        <select name="table" id="table" onchange="updateColumnInputs()">
            <option value="">--Select a table--</option>
            <?php foreach ($tables as $tableName => $columns): ?>
                <option value="<?php echo $tableName; ?>"><?php echo $tableName; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <div id="columnInputs"></div>
        <br>
        <input type="button" value="Insert" onclick="submitForm()">
    </form>
</div>
<div id="tableDisplay"></div>

<script>
function updateColumnInputs() {
    const tableSelect = document.getElementById('table');
    const columnInputs = document.getElementById('columnInputs');
    const tableDisplay = document.getElementById('tableDisplay');
    columnInputs.innerHTML = '';
    tableDisplay.innerHTML = '';

    const selectedTable = tableSelect.value;
    const tableColumns = <?php echo json_encode($tables); ?>;

    if (selectedTable && tableColumns[selectedTable]) {
        // Generate input fields for the selected table
        tableColumns[selectedTable].forEach(column => {
            const inputDiv = document.createElement('div');
            inputDiv.innerHTML = `<label for="${column}">${column}:</label>
                                  <input type="text" name="columns[${column}]" id="${column}">`;
            columnInputs.appendChild(inputDiv);
        });

        // Fetch and display the table data
        fetchTableData(selectedTable);
    }
}

function fetchTableData(table) {
    const tableDisplay = document.getElementById('tableDisplay');

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
            tableDisplay.innerHTML = html;
        } else {
            tableDisplay.innerHTML = '<p>No data available for this table.</p>';
        }
    })
    .catch(error => {
        console.error('Error fetching table data:', error);
        tableDisplay.innerHTML = '<p>Error fetching table data.</p>';
    });
}

function submitForm() {
    const form = document.getElementById('insertForm');
    const formData = new FormData(form);
    const table = formData.get('table');

    if (!table) {
        alert('Please select a table.');
        return;
    }

    // Send an AJAX request to insert data
    fetch('insert_data.php', {
        method: 'POST',
        body: new URLSearchParams([...formData])
    })
    .then(response => response.text())
    .then(data => {
        fetchTableData(table); // Refresh the table display
    })
    .catch(error => {
        console.error('Error inserting data:', error);
        alert('Error inserting data.');
    });
}
</script>
</body>
</html>