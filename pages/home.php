<?php

    session_start();
    require_once "database.php";


    //store session data
    if(isset($_SESSION['views']))
        $_SESSION['views']=$_SESSION['views']+1;
    else
        $_SESSION['views']=1;
    //echo "Views=". $_SESSION['views'];
    

    //make sure user is logged in
    if (!isset($_SESSION['UserId']))
    {
        header("Location: login.php");
        exit();
    }

    //if the user selects logout
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout']))
    {
        session_destroy(); 
        //delete cookie
        setcookie("Username", "", time()-3600, "/");
        //go to login page
        header("Location: login.php");
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../css/home.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>
    <!--This is for the name of the library-->
    <div class="stripe">
        <p id="title">Blooms</p>
        <a href="reserved.php" id="menu-button">My Reserved Books</a>
            <!--Logout-->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            <input type="submit" name="logout" value="logout" class="button" id="logoutbtn" >
        </form>
    </div>


    <!--Welcome Message-->
    <?php if (isset($_SESSION['Username'])): ?>
        <p id="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['Username']); ?></p>
    <?php endif; ?>

    <p id="text" >Home<p>
    </p>

    <p id="searchfor">Search for Books:</p>
    <div class="container">
        <div class="box">
            <form method="get" action="searchResults.php">
                <label for="search" class="label">Search Title or Author: </label>
                <input type="text" name="search" id="search" class="input" />
                
                <br><br/>
                <!--Category drop down box-->
                <label for="categories" class="label"><strong>Category:</strong></label>
                <select name="categories" id="categories" class="input">
                    <option value="" disabled selected >Select Category</option>
                    <?php
                        //selects the categories from teh database
                        $categories = mysqli_query($conn, "SELECT DISTINCT CategoryID, CategoryDescription FROM Categories ORDER BY CategoryDescription ASC");
                        while($c = mysqli_fetch_array($categories))
                        {
                            echo '<option value="'.$c['CategoryID'].'">'.$c['CategoryDescription'].'</option>';
                        }
                    ?>
                </select>

                <br></br>
                <button type="submit" class="button" id="searchbtn" >Search</button>
            </form>
        </div>
    </div>
    
    <br>
    
</body>
</html>

