<?php
    //start session
    session_start();
    //connect to database
    require_once "database.php";
    
    if(isset($_POST["Username"]) && isset($_POST["password"]))
    {
        //filter input to prevent XSS
        $username = filter_input(INPUT_POST, "Username", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $_POST["password"];

        // prepare statement to get user by username
        //prevents sql injection
        $stmt = $conn->prepare("SELECT UserId, password FROM Users WHERE Username = ?");
        if(!$stmt) //error handling
        {
            die("Prepare Failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        if(!$stmt->execute())
        {
            die("Execution Failed: " . $stmt->error);
        }
        $stmt->store_result();

        if($stmt->num_rows === 1)
        {
            $stmt->bind_result($userId, $hashedPasswordFromDB);
            $stmt->fetch();

            //verify password
            if(password_verify($password, $hashedPasswordFromDB))
            {               
                //start session (logged in)
                
                $_SESSION["Username"] = $username;
                $_SESSION["UserId"] = $userId;

                // create cookie
                setcookie("Username", $username, time()+3600);

                //redirect to home page
                header("Location: home.php");
                exit();
            }
            else
            {
                $_SESSION["error"] = "Invalid password";
                //redirect back to login page
                header("Location: login.php");
                exit();
            }

        }
        else
        {
            $_SESSION["error"] = "Username not found";
            //redirect to login page
            header("Location: login.php");
            exit();
        }

        $stmt->close();
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&display=swap" rel="stylesheet">

</head>
<body>

    <!--This is for the name of the library-->
    <div class="stripe">
        <p id="title" >Blooms</p>
    </div>
    
    <!--This is for the box containing the login-->
    <div class="container">

        <div class="box" >
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
            <p id="smalltxt">Log in to your account.</p>
                <?php
                if(isset($_SESSION["error"])) //if there is an error
                {
                    echo('<p style="color:red">Error:' . $_SESSION["error"]."</p>\n");
                    unset($_SESSION["error"]);
                }?>    
                <p class="label">Username:
                    <input type="text" name="Username" class="input" id="user" required>
                </p>
                <p class="label">Password:
                    <input type="password" name="password" class="input" id="password" required>
                </p>
                
            <!--Log In button-->
                <button type="submit" id="login" >Login</button>

            </form>

            <!--redirect to registration page -->
            <p id="other" >Don't have an account?: 
                <a href="register.php">Create an account</a>
            </p>
        </div>
    </div>

</body>
</html>



