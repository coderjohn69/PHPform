<?php
session_start();

include 'data.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == true) {
    // Connect to the database
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check for errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the poll_id from the form
    $poll_id = $_GET['id'];

    $sql = "SELECT * FROM polls WHERE id = $poll_id";
    $result = $conn->query($sql);
    $poll = mysqli_fetch_assoc($result);


    // Check if the form was submitted
    if(isset($_POST['submit'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $question = mysqli_real_escape_string($conn, $_POST['question']);
        $options = mysqli_real_escape_string($conn, $_POST['options']);
        $isMultiple = isset($_POST['isMultiple']) ? 1 : 0;
        $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);

        // Update the poll in the database
        $sql = "UPDATE polls SET description = '$description', question = '$question', options = '$options', 
            isMultiple = $isMultiple, deadline = '$deadline' WHERE id = '$id'";
        mysqli_query($conn, $sql);

        header('Location: index.php');
        exit();
    }

} else {
    header('Location: login.php?redirect=editPoll.php');
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Poll</title>
</head>
<body>
    <h1>Edit Poll</h1>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo $poll['id']; ?>">
        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?php echo $poll['Description']; ?>">
        <br>
        <label for="question">Question:</label>
        <input type="text" id="question" name="question" value="<?php echo $poll['question']; ?>" required>
        <br>
        <label for="options">Options:</label>
        <textarea id="options" name="options" required><?php echo $poll['options']; ?></textarea>
        <br>
        <label for="isMultiple">Allow multiple options to be selected:</label>
        <input type="radio" id="isMultiple" name="isMultiple" value="1" <?php if ($poll['isMultiple']) echo 'checked'; ?>> Yes
        <input type="radio" id="isMultiple" name="isMultiple" value="0" <?php if (!$poll['isMultiple']) echo 'checked'; ?>> No
        <br>
        <label for="deadline">Deadline:</label>
        <input type="date" id="deadline" name="deadline" value="<?php echo $poll['deadline']; ?>" required>
        <br>
        <input type="submit" value="Save">
    </form>
</body>
</html>

