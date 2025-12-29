<?php
include 'db.php';
session_start();

$action = $_POST['action'] ?? '';

// Login
if ($action == 'login') {
    $email = $conn->real_escape_string($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];

            // Insert login record
            $stmt = $conn->prepare("INSERT INTO logins (user_id, login_time) VALUES (?, NOW())");
            $stmt->bind_param("i", $row['id']);
            $stmt->execute();

            // Store login ID in session
            $_SESSION['login_id'] = $stmt->insert_id;

            echo "Login successful.<br>";
        } else {
            echo "Wrong password.<br>";
        }
    } else {
        echo "User not found.<br>";
    }
    $stmt->close();
}

// Delete the current login record
elseif ($action == 'delete' && isset($_SESSION['user_id'], $_SESSION['login_id'])) {
    $login_id = $_SESSION['login_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM logins WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $login_id, $user_id);
    if ($stmt->execute()) {
        echo "Current login deleted.<br>";
        unset($_SESSION['login_id']); // clear stored ID
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

// Update the current login record
elseif ($action == 'update' && isset($_SESSION['user_id'], $_SESSION['login_id'])) {
    $login_id = $_SESSION['login_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE logins SET login_time=NOW() WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $login_id, $user_id);
    if ($stmt->execute()) {
        echo "Login time updated.<br>";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

// View current login only
if (isset($_SESSION['user_id'], $_SESSION['login_id'])) {
    $user_id = $_SESSION['user_id'];
    $login_id = $_SESSION['login_id'];

    $stmt = $conn->prepare("SELECT id, login_time FROM logins WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $login_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo "<h3>Your Current Login Record:</h3>";
        echo "<ul><li>ID: {$row['id']}, Time: {$row['login_time']} 
              <form method='post' style='display:inline;'>
                <input type='hidden' name='action' value='delete'>
                
              </form>
              <form method='post' style='display:inline;'>
                <input type='hidden' name='action' value='update'>
                
              </form>
              </li></ul>";
    } else {
        echo "<p>Login record not found.</p>";
    }
    $stmt->close();
} else {
    echo "<p>Please log in to view your current login record.</p>";
}

$conn->close();
?>