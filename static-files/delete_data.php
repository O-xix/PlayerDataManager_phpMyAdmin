<?php
require_once 'config.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST['table'];
    $primaryKeyValues = $_POST['primary_keys'];

    // Prepare the SQL statement for deletion
    $sql = "DELETE FROM `$table` WHERE ";

    // Build the WHERE clause based on primary key values
    $conditions = [];
    foreach ($primaryKeyValues as $key => $value) {
        if (!empty($value)) {
            $conditions[] = "`$key` = '" . $conn->real_escape_string($value) . "'";
        }
    }
    $sql .= implode(" AND ", $conditions);

    // Execute the deletion
    if ($conn->query($sql) === TRUE) {
        echo "Record(s) deleted successfully.";
    } else {
        echo "Error deleting record(s): " . $conn->error;
    }
}

$tables = [
    'Accounts' => ['AccountID'],
    'Game' => ['GameID'],
    'PlatformRelease' => ['ReleaseID'],
    'Item' => ['ItemID'],
    'Classes' => ['ClassName'],
    'Characters' => ['AccountID', 'Studio', 'Title', 'CharName'],
    'Inventory' => ['AccountID', 'Studio', 'Title', 'CharName', 'ItemID'],
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
    <h2>Delete Data</h2>
    <form id="deleteForm">
        <label for="table">Select Table:</label>
        <select name="table" id="table" onchange="updatePrimaryKeys()">
            <option value="">--Select a table--</option>
            <?php foreach ($tables as $tableName => $primaryKeys): ?>
                <option value="<?php echo $tableName; ?>"><?php echo $tableName; ?></option>
            <?php endforeach; ?>
        </select>
        <div id="primaryKeyInputs"></div>
        <br>
        <input type="button" value="Delete" onClick="submitDelete()">
    </form>
</div>
<div id = "tableDisplay"></div>

<script>
function updatePrimaryKeys() {
    const tableSelect = document.getElementById('table');
    const primaryKeyInputs = document.getElementById('primaryKeyInputs');
    const tableDisplay = document.getElementById('tableDisplay');
    primaryKeyInputs.innerHTML = '';
    tableDisplay.innerHTML = '';

    const selectedTable = tableSelect.value;
    const primaryKeys = <?php echo json_encode($tables); ?>;

    if (selectedTable && primaryKeys[selectedTable]) {
        // Generate input fields for the primary keys
        primaryKeys[selectedTable].forEach(key => {
            const inputDiv = document.createElement('div');
            inputDiv.innerHTML = `<label for="${key}">${key}:</label>
                                  <input type="text" name="primary_keys[${key}]" id="${key}">`;
            primaryKeyInputs.appendChild(inputDiv);
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

function submitDelete() {
    const form = document.getElementById('deleteForm');
    const formData = new FormData(form);
    const table = formData.get('table');

    if (!table) {
        alert('Please select a table.');
        return;
    }

    // Send an AJAX request to delete data
    fetch('delete_data.php', {
        method: 'POST',
        body: new URLSearchParams([...formData])
    })
    .then(response => response.text())
    .then(data => {
        fetchTableData(table); // Refresh the table display
    })
    .catch(error => {
        console.error('Error deleting data:', error);
        alert('Error deleting data.');
    });
}
</script>
</body>
</html>