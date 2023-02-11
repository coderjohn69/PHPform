<?php
include 'data.php';
session_start();
// In the delete_poll.php file

if(isset($_SESSION['user_id']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == true) {
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
if ($result->num_rows > 0) {
// output data of each row
while($row = $result->fetch_assoc()) {
echo "Poll ID: " . $row["id"]. "<br>";
echo "Description: " . $row["Description"]. "<br>";
echo "Question: " . $row["question"]. "<br>";
echo "Options: " . $row["options"]. "<br>";
echo "IsMultiple: " . $row["isMultiple"]. "<br>";
echo "CreatedAt: " . $row["createdAt"]. "<br>";
echo "Deadline: " . $row["deadline"]. "<br>";
echo "Answers: " . $row["answers"]. "<br>";
echo "Voted: " . $row["voted"]. "<br>";
}
} else {
echo "Poll not found";
}
echo '<form action="" method="post">
Please enter your password to confirm deletion: <input type="password" name="password">
<input type="submit" name="submit" value="Delete">
</form>';

    if(isset($_POST['submit'])){
        $password = $_POST['password'];
        $admin_password = "SELECT password FROM users WHERE username='admin'";
        $result = $conn->query($admin_password);
        $row = $result->fetch_assoc();
        if($password == $row["password"]) {
            $sql = "DELETE FROM polls WHERE id = $poll_id";
            if ($conn->query($sql) === TRUE) {
                echo "Record deleted successfully";
                header("Location: index.php");
            } else {
                echo "Error deleting record: " . $conn->error;
            }
        }else{
            echo "Wrong password";
        }
    }
    $conn->close();
} else {
    // Redirect to the login page
    header("Location: login.php");
}
?>