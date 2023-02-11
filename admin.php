<?php
session_start();
include 'data.php';

// check if user is logged in and is an admin
if(!isset($_SESSION['user_id']) || !isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    header('Location: login.php');
    exit;
}

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// create group
if(isset($_POST['create_group'])) {
    $groupName = $_POST['group_name'];
    $userIds = implode(',', $_POST['user_ids']);
    $pollId = $_POST['poll_ids'];

    $query = "INSERT INTO groups (name, user_ids, poll_ids) VALUES ('$groupName', '$userIds', '$pollId')";
    $result = mysqli_query($conn, $query);
    if($result) {
        echo 'Group created successfully';
    } else {
        echo 'Error creating group: ' . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
</head>
<body>
    <h1>Admin Page</h1>
<form action="admin.php" method="post">
    <label for="group_name">Group Name:</label>
    <input type="text" id="group_name" name="group_name" required>
    <br>
    <label for="user_ids">Select Users:</label>
    <select name="user_ids[]" id="user_ids" multiple required>
        <?php
        $query = "SELECT id, username FROM users";
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['id'] . '">' . $row['username'] . '</option>';
        }
        ?>
    </select>
    <br><br>
    <label for="poll_ids">Select Poll:</label>
    <select name="poll_ids" id="poll_ids" required>
        <?php
        $query = "SELECT id,question FROM polls";
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['id'] . '">' . $row['question'] . '</option>';
        }
        ?>
    </select>
    <br><br>
    <input type="submit" value="Create Group" name="create_group">
</form>

<a href="index.php">Home</a>
<a href="logout.php">Logout</a>

</body>
</html>