<?php

    //connect to database
    require_once "database.php";
    //start session
    session_start();

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $u = trim($_POST['Username']);
        $pa = trim($_POST['password']);
        $f = trim($_POST['FirstName']);
        $s = trim($_POST['Surname']);
        $a1 = trim($_POST['AddressLine1']);
        $a2 = trim($_POST['AddressLine2']);
        $c = trim($_POST['City']);
        $ph = trim($_POST['Phone']);

        //validate phone number with regex
        if(!preg_match('/^[0-9]{10}$/', $ph))
        {
            $error = "Invalid Phone number";
        }
        else if(strlen($pa) < 6) //validate password length
        {
            $error = "Password must be 6 characters";
        }

        else
        {
            //check if username already exists
            $stmt = $conn->prepare("SELECT UserId FROM Users WHERE Username = ?");
            $stmt->bind_param("s", $u);
            $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows > 0)
            {
                //username already exists
                $error = "Username is taken";
            }
            else
            {
                // Hash the password
                $hashedPassword = password_hash($pa, PASSWORD_DEFAULT);
                
                //Prepared statement to prevent SQL injection
                $insertStmt = $conn->prepare("INSERT INTO Users (FirstName, Surname, AddressLine1, AddressLine2, City, Phone, Username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insertStmt->bind_param("ssssssss", $f, $s, $a1, $a2, $c, $ph, $u, $hashedPassword);

                if($insertStmt->execute())
                {
                    // set user as logged in
                    $_SESSION['UserId'] = $insertStmt->insert_id;
                    $_SESSION['Username'] = $u;

                    //redirect to homepage
                    header("Location: home.php");
                    exit();
                }
                else
                {
                    $error = "Database error: " . $insertStmt->error;
                }

                $insertStmt->close();
                //redirect to the same page to prevent resubmission
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

            $stmt->close();
            $conn->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>

    <!--This is for the name of the library-->
    <div class="stripe">
        <p id="title" >Blooms</p>
    </div>
    <div class="container">
        <div class="box">
            <p id="smalltxt">Register.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
                <p class="label">First name:
                    <input type="text" name="FirstName" class="input" required>
                </p>
                <p class="label">Surname:
                    <input type="text" name="Surname" class="input" required>
                </p>
                <p class="label">Address Line 1:
                    <input type="text" name="AddressLine1" class="input" required>
                </p>
                <p class="label">Address Line 2:
                    <input type="text" name="AddressLine2" class="input" required>
                </p>
                <p class="label">City:
                    <input type="text" name="City" class="input" required>
                </p>
                <p class="label">Phone:
                    <input type="text" name="Phone" class="input" required>
                </p>
                <!--Error message-->
                <?php if(!empty($error)): ?>
                    <p style="color:red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <p class="label">Username:
                    <input type="text" name="Username" id="username" class="input" required>
                    <span id="user-tick"></span>
                </p>
                <p class="label">Password:
                    <input type="password" name="password" id="password" class="input" required>
                </p>
                <p class="label">Confirm password:
                    <input type="password" name="confirm_password" id ="confirm_password" class="input" required>
                    <span id="password-tick"></span>
                </p>
                <p>
                    <button type="submit" id="confirm" class="btn">Confirm</button>
                </p>
            </form>
            <!--redirect to registration page -->
            <p id="other" >Already have an account?: 
                <a href="login.php">Login</a>
            </p>
        </div>
    </div>

    <!--Connect to JS file-->
    <script src="http://localhost/WebDProjectv1.0.0/register.js"></script>
    
</body>
</html>

