<?php
session_start();

// Connect to the MySQL database
include 'data.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username and password match a record in the "users" table
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Login successful, store the user's username in the session
        $_SESSION['user_id'] =  $result->fetch_assoc()['id'];
        
        

        // Redirect to the appropriate page based on the user's role (admin or regular user)
        $sql = "SELECT isAdmin FROM users WHERE username='$username'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $_SESSION['isAdmin'] = $row['isAdmin'];

        
       
            // Regular user
            if(isset($_GET['redirect'])) {
                $redirect = $_GET['redirect'];
                header("Location: $redirect");
            } else {
                header("Location: index.php");
            }
        
    } else {
        // Login failed, display an error message
        echo '<p style="color:red;">username or password is incorrect.</p>';

        // wait for 3 seconds before redirecting
        echo '<p style="color:green;">Redirecting in 3 seconds...</p>';
        header("refresh:3;url=login.php");
    }
} elseif (isset($_SESSION['user_id'])) {
    //If user is already logged in, redirect to the page that the user clicked from
    $referrer = $_SERVER['HTTP_REFERER'];
    $ref_data = "?ref_data=".$_SESSION['user_id'];
    if(!empty($referrer)) {
        header("Location: $referrer".$ref_data);
    }
    else {
        //If the referrer is not available, redirect to a default page
        header("Location: index.php".$ref_data);
    }
} else {
    // Display the login form
    echo '
    <!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style> 
        /* center the login form */
        body {
            font-family: Arial, sans-serif;
            background: rgb(238,174,202);
background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(148,187,233,1) 100%);
        }
        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            
        }
        /* add some space around the form */
        #form-container {
            margin: 50px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: white;
        }
        /* style the inputs and labels */
        input[type="text"], input[type="password"] {
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            width: 100%;
        }
        label {
            font-size: 16px;
            margin-bottom: 10px;
            display: block;
        }
        /* style the submit button */
        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        /* change the submit button color on hover */
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        /* style the signup link */
        a {
            color: blue;
            text-decoration: none;
            margin-top: 20px;
            display: block;
            text-align: center;
        }
        /* center the text */
        h1{
            text-align: center;
            margin-bottom: 20px;
        }
        /* make the form responsive on small screens */
        @media (max-width: 600px) {
            form {
                flex-direction: column;
            }
            #form-container {
                margin: 20px;
            }
            input[type="text"], input[type="password"] {
                width: 100%;
                margin-bottom: 15px;
            }
            label {
                text-align: left;
                width: 100%;
            }
            input[type="submit"] {
                margin-top: 20px;
            }
        }
        // on mobile screens, make the form responsive and make it occupy the whole screen
        @media (max-width: 400px) {
            form {
                height: 100%;
                flex-direction: column;

            }
            #form-container {
                margin: 0;
                border: none;
                border-radius: 0;
            }
            input[type="text"], input[type="password"] {
                width: 100%;
                margin-bottom: 15px;
            }
            label {
                text-align: left;
                width: 100%;
            }
            input[type="submit"] {
                margin-top: 20px;
            }
        }

    </style>
</head>
<body>
    <h1>Login</h1>
    <form action="login.php" method="post">
        <div id="form-container">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" value="Login">
        </div>
    </form>
    <a href="signup.php">Sign Up</a>
    <div id="error-message">
    <?php if (isset($errorMessage)):
        echo $errorMessage;
        endif; ?>
    </div>
    
</body>
</html>
';

}

?>
