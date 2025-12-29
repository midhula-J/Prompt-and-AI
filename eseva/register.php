<?php
include 'db.php';

$action = $_POST['action'];

if ($action == 'insert') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    
    if ($conn->query($sql)) {
        $user_id = $conn->insert_id;
        echo "Registered successfully.<br>";
        // Show only the newly registered user
        showUser($conn, $user_id);
    } else {
        echo "Error: " . $conn->error;
    }
}
elseif ($action == 'update') {
    $id = $_POST['user_id'];
    $name = $_POST['name'];
    $sql = "UPDATE users SET name='$name' WHERE id=$id";
    
    if ($conn->query($sql)) {
        echo "Updated successfully.<br>";
        // Show the updated user
        showUser($conn, $id);
    } else {
        echo "Error: " . $conn->error;
    }
}
elseif ($action == 'delete') {
    $id = $_POST['user_id'];
    $sql = "DELETE FROM users WHERE id=$id";
    echo $conn->query($sql) ? "Deleted successfully.<br>" : "Error: " . $conn->error;
}

// Function to display a single user
function showUser($conn, $user_id) {
    $res = $conn->query("SELECT id, name, email FROM users WHERE id = $user_id");
    
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo "<h3>Your Information:</h3>";
        echo "<ul>";
        echo "<li>ID: {$row['id']}</li>";
        echo "<li>Name: {$row['name']}</li>";
        echo "<li>Email: {$row['email']}</li>";
        echo "</ul>";
    } else {
        echo "<p>User not found.</p>";
    }
}

$conn->close();
?>