<!-- This is  -->

<?php
/**
 * Created by PhpStorm.
 * User: MKochanski
 * Date: 7/24/2018
 * Time: 3:07 PM
 */
require_once 'config.inc.php';

?>
<html>
<head>
    <title>Insert</title>
    <link rel="stylesheet" href="base.css">
    <script>
        const tableAttributes = {
            Accounts: ['AccountID', 'Username', 'Password'],
            Game: ['GameID', 'Title', 'Studio', 'Genre', 'Rating'],
            PlatformRelease: ['ReleaseID', 'GameID', 'Platform', 'Price', 'ReleaseDate'],
            GameStats: ['Title', 'Studio'],
            Item: ['ItemID', 'Name', 'Description'],
            ItemUsage: ['Title', 'Studio', 'ItemID', 'UsagePercent'],
            Classes: ['ClassName', 'Description'],
            ClassUsage: ['Title', 'Studio', 'ClassName', 'UsagePercent'],
            Builds: ['BuildID', 'Description'],
            PopularBuilds: ['Title', 'Studio', 'BuildID'],
            Characters: ['AccountID', 'Studio', 'Title', 'CharName', 'Class'],
            GameClass: ['Title', 'Studio', 'Class'],
            GameName: ['Title', 'Studio', 'CharName'],
            AccountStats: ['AccountID', 'Title', 'Studio', 'Stats'],
            Inventory: ['AccountID', 'Studio', 'Title', 'CharName', 'ItemID'],
            StatTypes: ['Studio', 'Title', 'StatName', 'Description'],
            CharacterStats: ['AccountID', 'Studio', 'Title', 'CharName', 'StatName', 'StatValue']
        };

        function updateFormFields(tableName) {
            const attributes = tableAttributes[tableName] || [];
            const inputField = document.getElementById('data-input-field');

            // Remove existing fields
            inputField.innerHTML = '';

            // Create new fields based on selected table
            attributes.forEach(attr => {
                const label = document.createElement('label');
                label.textContent = attr + ':';
                label.setAttribute('for', attr);
                
                const input = document.createElement('input');
                input.type = 'text';
                input.name = attr;
                input.id = attr;

                form.appendChild(label);
                form.appendChild(input);
                form.appendChild(document.createElement('br'));
                form.a
            });
        }

        const insertMethodSelect = document.getElementById('insertMethod');
        insertMethodSelect.addEventListener('change', function() {
            updateFormFields(this.value);
        });

    </script>
</head>
<body>
<?php
require_once 'header.inc.php';
?>
<div>
    <h2>Insert Data into Database</h2>
    <form method="POST" action="insert.php">
        <label for="insertMethod">Choose an Insert Method:</label>
        <select name="insertMethod" id="insertMethod">
            <option value="Accounts">Accounts</option>
            <option value="Game">Game</option>
            <option value="PlatformRelease">PlatformRelease</option>
            <option value="GameStats">GameStats</option>
            <option value="Item">Item</option>
            <option value="ItemUsage">ItemUsage</option>
            <option value="Classes">Classes</option>
            <option value="ClassUsage">ClassUsage</option>
            <option value="Builds">Builds</option>
            <option value="PopularBuilds">PopularBuilds</option>
            <option value="Characters">Characters</option>
            <option value="GameClass">GameClass</option>
            <option value="GameName">GameName</option>
            <option value="AccountStats">AccountStats</option>
            <option value="Inventory">Inventory</option>
            <option value="StatTypes">StatTypes</option>
            <option value="CharacterStats">CharacterStats</option>
        </select>
        <br><br>
        <label for="data">Enter Data:</label>
        <div id = "data-input-field">
        </div>
        <br><br>
        <button type="submit">Submit</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create connection
        $conn = new mysqli($servername, $username, $password, $database, $port);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $insertMethod = $_POST['insertMethod'];

        // Get the table name and attributes based on the selected method
        $tableName = $insertMethod; // Assuming the dropdown value matches the table name
        $attributes = isset($tableAttributes[$tableName]) ? $tableAttributes[$tableName] : [];

        if (!empty($attributes)) {
            // Build the SQL query dynamically
            $columns = implode(", ", $attributes);
            $values = [];
            foreach ($attributes as $attribute) {
                if (isset($_POST[$attribute])) {
                    $values[] = "'" . $conn->real_escape_string($_POST[$attribute]) . "'";
                } else {
                    $values[] = "NULL"; // Handle missing fields
                }
            }
            $valuesString = implode(", ", $values);

            $sql = "INSERT INTO $tableName ($columns) VALUES ($valuesString)";

            // Execute the query
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully in $tableName.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Invalid table or attributes.";
        }

        // Close connection
        $conn->close();
    }
    ?>
</div>
</body>
</html>