<?php
    session_start();
    require_once "database.php";

    // Make sure user is logged in
    if(!isset($_SESSION['UserId'])){
        header("Location: login.php");
        exit();
    }

    $username = $_SESSION["Username"];

    $limit = 5; // rows per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    // Fetch reserved books for this user
    $sql = "SELECT b.ISBN, b.BookTitle, b.Author, c.CategoryDescription, r.ReservedDate FROM Reservations r JOIN Books b ON r.ISBN = b.ISBN JOIN Categories c ON b.Category = c.CategoryID WHERE r.Username = ? ORDER BY r.ReservedDate DESC LIMIT ? OFFSET ?";

    //main search query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $username, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Count total rows for pagination
    $countSql = "SELECT COUNT(*) as total FROM Reservations r WHERE r.Username = ?";

    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("s", $username);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit );




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
<html>
<head>
    <title>My Reserved Books</title>
        <link rel="stylesheet" href="../css/reserved.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>

    <?php if(isset($_SESSION['message'])): ?>
        <p style="color:green;"><?php 
            echo htmlspecialchars($_SESSION['message']); 
            unset($_SESSION['message']); 
        ?></p>
    <?php endif; ?>


    <!--This is for the name of the library-->
    <div class="stripe">
        <p id="title" >Blooms</p>
        <a href="home.php" id="menu-button">Home</a>
            <!--Logout-->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            <input type="submit" name="logout" value="logout" id="button" >
        </form>
    </div>
    
    
    
<h2 id="reserved-title">My Reserved Books</h2>
<div id="reserved-container">


    <!--Table headings-->
    <?php if($result->num_rows > 0): ?>
        <table id="reserved-table">
            <tr class="reserved-header">
                <th class="r-title">Title</th>
                <th class="r-author">Author</th>
                <th class="r-category">Category</th>
                <th class="r-date">Reserved On</th>
                <th class="r-action">Action</th>
            </tr>
            <!--Table contents and cancel button-->
            <?php while($row = $result->fetch_assoc()): ?>
            <tr class="reserved-row">
                <td class="r-title"><?php echo htmlspecialchars($row["BookTitle"]); ?></td>
                <td class="r-author"><?php echo htmlspecialchars($row["Author"]); ?></td>
                <td class="r-category"><?php echo htmlspecialchars($row["CategoryDescription"]); ?></td>
                <td class="r-date"><?php echo date("d-m-Y", strtotime($row["ReservedDate"])); ?></td>
                <td class="r-action">
                    <form method="post" action="cancelReservation.php">
                        <input type="hidden" name="ISBN" value="<?php echo $row["ISBN"]; ?>">
                        <button type="submit">Cancel</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p id="no-reserved">You have not reserved any books yet.</p>
    <?php endif; ?>

    <div class="grid_pagination">
        <div id="pagination">
            <!-- Previous arrow -->
            <a class="pagination-arrow <?php echo ($page <= 1) ? 'disabled' : ''; ?>" href="<?php echo ($page > 1) ? '?page=' . ($page-1) : '#'; ?>">&larr;</a>

            <!-- Page indicator -->
            <span class="page-indicator">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

            <!-- Next arrow -->
            <a class="pagination-arrow <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>" href="<?php echo ($page < $totalPages) ? '?page=' . ($page+1) : '#'; ?>">&rarr;</a>
        </div>
    </div>

</div>

</body>
</html>
