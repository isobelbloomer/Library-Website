<?php
    //start session
    session_start();
    //connect to database
    require_once "database.php";

    //make sure user is still logged in
    if(!isset($_SESSION["UserId"]))
    {
        //redirect to log in page
        header("Location: login.php");
        exit();
    }

    if(isset($_POST["ISBN"]))
    {
        $isbn = $_POST["ISBN"];
        $userId = $_SESSION["UserId"];
        $username = $_SESSION["Username"];
        

        //check if book is still available 
        $stmt = $conn->prepare("SELECT Reserved FROM Books WHERE ISBN = ?");
        $stmt->bind_param("s", $isbn);
        $stmt->execute();
        $stmt->bind_result($reserved);
        $stmt->fetch();
        $stmt->close();

        if(!isset($reserved))
        {
            $_SESSION["message"] = "Book not found";
        }
        else if($reserved == 0)
        {
            // Insert reservation
            $username = $_SESSION["Username"];
            $stmt = $conn->prepare("INSERT INTO Reservations (Username, ISBN, ReservedDate) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $username, $isbn);
            $stmt->execute();
            $stmt->close();

            // Update book to reserved
            $stmt = $conn->prepare("UPDATE Books SET Reserved = 1 WHERE ISBN = ?");
            $stmt->bind_param("s", $isbn);
            $stmt->execute();
            $stmt->close();

            //success message
            $_SESSION['message'] = "Book reserved successfully!";

            $search = isset($_POST["search"]) ? $_POST["search"] : '';
            $category = isset($_POST["categories"]) ? $_POST["categories"] : '';
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

            //redirect back to results
            header("Location: searchResults.php?search=" . urlencode($search) . "&categories=" . urlencode($category)) . "&page=" . ($page);
            exit();

        }
        else
        {
            $_SESSION["message"] = "Sorry, this book is already reserved";
        }
        
        //redirect back to search results page
        header("Location: searchResults.php");
        exit();
    }


?>