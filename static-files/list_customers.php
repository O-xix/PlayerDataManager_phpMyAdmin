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
    <title>Sample PHP Database Program</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php
require_once 'header.inc.php';
?>
<div>
    <h2>Customer List</h2>
    <?php
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

        // Prepare SQL Statement
    $sql = "SELECT CustomerNumber,CustomerName FROM customer ORDER BY CustomerName";
    $stmt = $conn->stmt_init();
    if (!$stmt->prepare($sql)) {
        echo "failed to prepare";
    }
    else {

                // Execute the Statement
        $stmt->execute();

                // Loop Through Result
        $stmt->bind_result($customerNumber,$customerName);
        echo "<ul>";
        while ($stmt->fetch()) {
            echo '<li><a href="show_customer.php?id='  . $customerNumber . '">' . $customerName . '</a></li>';
        }
        echo "</ul>";
    }

        // Close Connection
    $conn->close();

    ?>
</div>
</body>
</html>