
<?php
 include 'data.php';
 session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Polling Application</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: rgb(238,174,202);
background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(148,187,233,1) 100%);
        }

        header {
            text-align: center;
            padding: 20px;
            background-color: lightgray;
            border-bottom: 1px solid #ccc;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
        }

        section {
            flex: 1;
            padding: 20px;
        }

        .open-polls {
            background-color: #f9f9f9;
        }

        .closed-polls {
            background-color: #f2f2f2;
        }

        h2 {
            margin-top: 0;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* beautify the buttons with the class bttn"
        */
        .bttn {
            background-color: #2c92e8;
            /* Green */
            border: none;
            border-radius: 4px;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        /* //beautify item with id buttons */
        #buttons{
            white-space: nowrap; overflow-x: auto; overflow-y: hidden;
        }

        /* //beautify all items with tag button */
        button {
            background-color: purple;
            /* Green */
            border: none;
            border-radius: 4px;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        
        }

        a{
            text-decoration: none;
            color: white;
        }
    </style>
</head>

<body>
    <header>
        <h1>Welcome to Our Polling Application</h1>
        <p>This application allows you to vote on various polls and see the results.</p>
        <?php

if(isset($_SESSION['user_id']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
    // echo '<a href="createPoll.php">Create Poll</a>';
    // echo '<button href="createPoll.php">Create Poll</button>';
    //the above line is not working, it is not redirecting to createPoll.php
    echo '<button><a href="createPoll.php">Create Poll</a></button>';
            echo '     ';
    echo '<button><a href="admin.php">Admin Panel</a></button>';
            echo '     ';
            echo '<button><a href="logout.php">Logout</a></button>';

    // echo '<button href="logout.php">Logout</button>';

    // echo '<a href="logout.php">Logout</a>';
} else if(isset($_SESSION['user_id'])) {
    echo '<a href="logout.php">Logout</a>';
} else {
    echo '<button><a href="login.php">Login</a></button>';
            
}
?>

    </header>

    <?php 

    $openPolls = array();
    $closedPolls = array();
    
    // $servername = "sql11.freesqldatabase.com";
    // $username = "sql11591853";
    // $password = "jeV6AXr3uKFeF4cs";
    // $dbname = "sql11591853";

    $conn = mysqli_connect($servername, $username, $password, $dbname);
  
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM polls";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $poll = array(
            'id' => $row['id'],
            'question' => $row['question'],
            'options' => json_decode($row['options'], true),
            'isMultiple' => $row['isMultiple'],
            'createdAt' => $row['createdAt'],
            'deadline' => $row['deadline'],
            'answers' => json_decode($row['answers'], true),
            'voted' => $row['voted']
        );
        if (time() < strtotime($poll['deadline'])) {
            $openPolls[] = $poll;
        } else {
            $closedPolls[] = $poll;
        }
    }
   
    
    ?>


    <div class="container">
        <section class="open-polls">
            <h2>Open Polls</h2>
            <ul>
                <?php foreach ($openPolls as $poll): ?>
                <li>
                    <p>ID:
                        <?= $poll['id'] ?>
                    </p>
                    <p>Question:
                        <?= $poll['question'] ?>
                    </p>
                    <p>Created at:
                        <?= $poll['createdAt'] ?>
                    </p>
                    <p>Deadline:
                        <?= $poll['deadline'] ?>
                    </p>
                    <div id="buttons" >
                    <form action="vote.php?id=<?= $poll['id'] ?>" method="post">

                        <input type="hidden" name="poll_id" value="<?php echo $poll_id ?>">
                        <input type="submit" class="bttn" value="Vote">
                    </form>
                   <!-- make a edit button inline with vote button that will only show if the user is an admin , when
                     the edit button is clicked it will take the user to the editPoll.php page and pass the poll id as a get parameter -->

                    <form action="editPoll.php?id=<?=$poll['id'] ?>" method="post">
                        <input type="hidden" name="poll_id" value="<?php echo $poll_id; ?>">
                        <?php if(isset($_SESSION['user_id']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == true) { ?>
                        <input type="submit" class="bttn" value="Edit">
                        <?php } ?>
                    </form>
                    
                    
                    <!-- In the HTML file, inside the loop that displays the poll -->

    <form action="deletePoll.php?id=<?=$poll['id'] ?>" method="post">
        <input type="hidden" name="poll_id" value="<?php echo $poll_id; ?>">
        <?php if(isset($_SESSION['user_id']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == true) { ?>
            <input type="submit" class="bttn" value="Delete" class="button">
        <?php } ?>
    </form>
                    </div>


                    

                     
                </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="closed-polls">
            <h2>Closed Polls</h2>
            <ul>
                <?php foreach ($closedPolls as $poll): ?>
                <li>
                    <p>ID:
                        <?= $poll['id'] ?>
                    </p>
                    <p>Question:
                        <?= $poll['question'] ?>
                    </p>
                    <p>Created at:
                        <?= $poll['createdAt'] ?>
                    </p>
                    <p>Deadline:
                        <?= $poll['deadline'] ?>
                    </p>
                    <p>Results:
                        <?php foreach ($poll['answers'] as $option => $votes): ?>
                        <?= $option ?>:
                        <?= $votes ?><br>
                        <?php endforeach; ?>
                    </p>
                </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
</body>
<?php mysqli_close($conn); ?>
</html>