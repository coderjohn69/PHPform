<?php
session_start();

include 'data.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);



// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['isAdmin']) {
    header("Location: login.php?redirect=createpoll.php");
    

    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    // split the options into an array
    // $options = explode(',', $_POST['options']);
    //split even if its entered with a space or a comma or new line
    $options = preg_split('/[\s,]+/', $_POST['options']);
    // remove any whitespace from the options
    $options = array_map('trim', $options);
    // remove any empty options
    $options = array_filter($options);

    $options = mysqli_real_escape_string($conn, $_POST['options']);
    $isMultiple = isset($_POST['isMultiple']) ? 1 : 0;
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    $createdAt = date('Y-m-d');
    $id = 1;
    $query = "SELECT id FROM polls ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'] + 1;
    }


    // Insert the poll into the database
    $sql = "INSERT INTO polls (id, description, question, options, isMultiple, createdAt, deadline, answers, voted)
            VALUES ($id, '$description', '$question', '$options', $isMultiple, '$createdAt', '$deadline', '', '')";
    mysqli_query($conn, $sql);

    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Poll Creation</title>
</head>
<body>
    <h1>Poll Creation</h1>
    <form method="post">
        <label for="description">Description:</label>
        <input type="text" id="description" name="description">
        <br>
        <label for="question">Question:</label>
        <input type="text" id="question" name="question" required>
        <br>
        <label for="options">Options:</label>
        <textarea id="options" name="options" required></textarea>
        <br>
        <label for="isMultiple">Allow multiple options to be selected:</label>
        <input type="radio" id="isMultiple" name="isMultiple" value="1"> Yes
        <input type="radio" id="isMultiple" name="isMultiple" value="0"> No
        <br>
        <label for="deadline">Voting Deadline:</label>
        <input type="date" id="deadline" name="deadline" required>
        <br>
        <input type="submit" value="Create Poll">
    </form>
</body>
</html>
