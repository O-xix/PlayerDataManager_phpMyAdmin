<?php
require_once 'config.inc.php';

if (isset($_POST['table'])) {
    $table = $_POST['table'];

    // Fetch data from the selected table
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(['error' => 'No table selected']);
}
?>