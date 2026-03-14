<?php
    //start session
    session_start();
    //connect to database
    require_once "database.php";

    // Ensure user is logged in
    if(!isset($_SESSION["UserId"])) {
        header("Location: login.php");
        exit();
    }


    if(isset($_POST["ISBN"])) {
        $isbn = $_POST["ISBN"];
        $username = $_SESSION["Username"];

        // Delete reservation
        $stmt = $conn->prepare("DELETE FROM Reservations WHERE ISBN = ? AND Username = ?");
        $stmt->bind_param("ss", $isbn, $username);
        $stmt->execute();
        $stmt->close();

        // Update book availability
        $stmt = $conn->prepare("UPDATE Books SET Reserved = 0 WHERE ISBN = ?");
        $stmt->bind_param("s", $isbn);
        $stmt->execute();
        $stmt->close();

        $_SESSION["message"] = "Reservation cancelled successfully!";
    }

    //redirect to reserved.php
    header("Location: reserved.php");
    exit();
?>
