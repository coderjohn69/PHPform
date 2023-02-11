<?php
include 'data.php';

session_start();

// initialize variables to store form data
$usernameUser = "";
$emailUser = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameUser = $_POST['username'];
    $emailUser = $_POST['email'];
    $passwordUser = $_POST['password'];
    $password2 = $_POST['password2'];

    // error flag
    $error = false;

    // error messages
    $username_error = "";
    $email_error = "";
    $password_error = "";
    $password2_error = "";

    // validate the form fields
    if (empty($usernameUser)) {
        $error = true;
        $username_error = "Please enter a username";
    }

    if (empty($emailUser)) {
        $error = true;
        $email_error = "Please enter an email address";
    } else if (!filter_var($emailUser, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Please enter a valid email address";
    }

    if (empty($passwordUser)) {
        $error = true;
        $password_error = "Please enter a password";
    }

    if (empty($password2)) {
        $error = true;
        $password2_error = "Please confirm your password";
    } else if ($passwordUser != $password2) {
        $error = true;
        $password2_error = "Passwords do not match";
    }

    // if there are no errors, insert the new user into the database
    if (!$error) {
        // Connect to the database
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql1 = "SELECT MAX(id) as max_id FROM users WHERE id LIKE 'userid%'";
$result = $conn->query($sql1);
$row = $result->fetch_assoc();
$max_id = $row['max_id'];

// Extract the number from the max_id
preg_match('/\d+/', $max_id, $matches);
$num = $matches[0];

// Increment the number to get the new id
$num++;
$new_id = "userid" . $num;




        // Insert the new user into the users table
        $sql = "INSERT INTO users (id, username, email, password, isAdmin) VALUES ('$new_id', '$usernameUser', '$emailUser', '$passwordUser', 0)";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
            header("Location: login.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sign Up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body{
    font-family: Arial, sans-serif;
    text-align: center;
            background: rgb(238,174,202);
background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(148,187,233,1) 100%);
        
}

h1{
    margin-top: 50px;
}

form{
    margin: 0 auto;
    text-align: left;
    width: 300px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: white;
}

label{
    display: block;
    margin-bottom: 10px;
}

input[type="text"], input[type="email"], input[type="password"]{
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
}

input[type="submit"]{
    width: 100%;
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type="submit"]:hover{
    background-color: #45a049;
}

  </style>
</head>
<body>
  <h1>Sign Up</h1>
  <form action="signup.php" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <label for="password2">Confirm Password:</label>
    <input type="password" id="password2" name="password2" required>
    <br>
    <input type="submit" value="Sign Up">
  </form>
 
</body>
</html>

