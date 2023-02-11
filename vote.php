<?php
include 'data.php';
session_start();

$conn = mysqli_connect($servername, $username, $password, $dbname);



if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    //give error message please login to vote and redirect to login page
    echo "Please login to vote";
    header('Location: login.php?redirect=poll.php?id=' . $_GET['id']);
    // header('Location: login.php?redirect=poll.php?id=' . $_GET['id']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get the poll data from the database
$id = $_GET['id'];
$query = "SELECT * FROM polls WHERE id = $id";
$result = mysqli_query($conn, $query);
$poll = mysqli_fetch_assoc($result);




// Check if the form has been submitted
if (isset($_POST['submit'])) {

    // Get the selected option
    $selected_option = $_POST['option'];
    
    if(empty($selected_option)) {
        $error_message = "Please select a valid option.";
    } else if (!in_array($selected_option, json_decode($poll['options']))) {
        $error_message = "Please select a valid option.";
    }
    else if (in_array($user_id, json_decode($poll['voted']))) {
        $error_message = "You have already voted.";
    } 
    else {

        //You need to update the answers and voted array to add the value in it
        $answers = json_decode($poll['answers'], true);
        $answers[$selected_option]++;
        $answers = json_encode($answers);

        $voted = json_decode($poll['voted'], true);
        $voted[] = $user_id;
        $voted = json_encode($voted);
        
        // Update the vote count for the selected option
        $query = "UPDATE polls SET answers = '$answers' , voted = '$voted' WHERE id = $id";
        $result = mysqli_query($conn, $query);

        // Check if the update was successful
        if ($result) {
            $success_message = "Your vote has been submitted successfully.";
        } else {
            $error_message = "An error occurred while submitting your vote. Please try again later.";
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vote</title>
</head>
<body>
    <h1><?php echo $poll['question']; ?></h1>
    <p>Description: <?php echo $poll['Description']; ?></p>
    <p>Options:
        <ul>
            <?php foreach (json_decode($poll['options']) as $option): ?>
            <li>
                <?php echo $option; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </p>
    <p>Created at: <?php echo $poll['createdAt']; ?></p>
    <p>Deadline: <?php echo $poll['deadline']; ?></p>
    <?php if (time() > strtotime($poll['deadline'])): ?>
    <p>Results:
        <ul>
            <?php foreach (json_decode($poll['answers']) as $option => $votes): ?>
                <li><?= $option ?>: <?= $votes ?></li>
            <?php endforeach; ?>
        </ul>
    </p>
    <p>This poll is closed</p>
<?php else: ?>
    <form action="" method="post">
    <label for="option">Select an option:</label>
    <select name="option" id="option">
        <option value="">Please select an option</option>
        <?php foreach (json_decode($poll['options']) as $option): ?>
        <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" name="submit" value="Submit">
</form>

<?php 
    if (isset($error_message)) {
        echo '<p style="color:red">' . $error_message . '</p>';
    }
    if (isset($success_message)) {
        echo '<p style="color:green">' . $success_message . '</p>';
    }
?>
<?php endif; ?>
</body>
</html>
