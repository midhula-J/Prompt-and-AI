<?php
include 'db.php';

$action = $_POST['action'];

if ($action == 'insert') {
    $title = $_POST['title'];
    $desc = $_POST['description'];

    // Insert without user_id (it auto-increments now)
    $sql = "INSERT INTO complaints (title, description) VALUES ('$title', '$desc')";

    if ($conn->query($sql)) {
        $last_id = $conn->insert_id;
        echo "Complaint added. User ID = $last_id<br>";

        // Get the latest inserted row using the auto-incremented user_id
        $res = $conn->query("SELECT * FROM complaints WHERE user_id = $last_id");
    } else {
        echo "Error: " . $conn->error;
    }
}
elseif ($action == 'update') {
    $id = $_POST['complaint_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];

    $sql = "UPDATE complaints SET title='$title', description='$desc' WHERE user_id=$id";

    if ($conn->query($sql)) {
        echo "Updated complaint.<br>";
        $res = $conn->query("SELECT * FROM complaints WHERE user_id = $id");
    } else {
        echo "Error: " . $conn->error;
    }
}
elseif ($action == 'delete') {
    $id = $_POST['complaint_id'];

    $sql = "DELETE FROM complaints WHERE user_id=$id";

    if ($conn->query($sql)) {
        echo "Deleted complaint with User ID $id.<br>";
        $res = false; // No need to show deleted record
    } else {
        echo "Error: " . $conn->error;
    }
}

// Show only the last affected record
if ($res && $res->num_rows > 0) {
    echo "<h3>Complaint Record:</h3><ul>";
    while ($row = $res->fetch_assoc()) {
        echo "<li>User ID: {$row['user_id']}<br>Title: {$row['title']}<br>Description: {$row['description']}</li>";
    }
    echo "</ul>";
}

$conn->close();
?>